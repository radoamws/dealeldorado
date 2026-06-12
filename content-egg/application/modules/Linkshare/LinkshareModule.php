<?php

namespace ContentEgg\application\modules\Linkshare;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModule;
use ContentEgg\application\libs\linkshare\RakutenApiClient;
use ContentEgg\application\libs\linkshare\LinkshareProductsRest;
use ContentEgg\application\components\ContentProduct;
use ContentEgg\application\admin\PluginAdmin;
use ContentEgg\application\helpers\TextHelper;

/**
 * LinkshareModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class LinkshareModule extends AffiliateParserModule
{

    private $api_client = null;

    public function info()
    {
        return array(
            'name' => 'Rakuten (Linkshare)',
        );
    }

    public function getParserType()
    {
        return self::PARSER_TYPE_PRODUCT;
    }

    public function defaultTemplateName()
    {
        return 'grid';
    }

    public function isItemsUpdateAvailable()
    {
        return true;
    }

    public function doRequest($keyword, $query_params = array(), $is_autoupdate = false)
    {
        $options = array();

        if ($is_autoupdate)
        {
            $options['max'] = $this->config('entries_per_page_update');
        }
        else
        {
            $options['max'] = $this->config('entries_per_page');
        }

        $query = self::_sanitizeKeyword($keyword);

        if ($this->config('cat'))
        {
            $options['cat'] = $this->config('cat');
        }
        if ($this->config('mid'))
        {
            $options['mid'] = $this->config('mid');
        }
        if ($this->config('sort'))
        {
            $options['sort'] = $this->config('sort');
            $options['sorttype'] = $this->config('sorttype');
        }

        $results = $this->getApiClient()->search($query, $options, $this->config('search_logic'));
        if (!is_array($results) || !isset($results['item']))
        {
            return array();
        }

        if (!isset($results['item'][0]) && isset($results['item']['mid']))
        {
            $results['item'] = array($results['item']);
        }

        return $this->prepareResults($results['item']);
    }

    public function doRequestItems(array $items)
    {
        foreach ($items as $key => $item)
        {
            $options = array();
            $options['max'] = 10;

            if (!empty($item['extra']['mid']))
            {
                $options['mid'] = $item['extra']['mid'];
            }

            $keyword = $item['title'];
            $query = self::_sanitizeKeyword($keyword);

            try
            {
                $results = $this->getApiClient()->search($query, $options, 'AND');
            }
            catch (\Exception $e)
            {
                continue;
            }

            if (!is_array($results) || !isset($results['item']))
            {
                continue;
            }

            if (!isset($results['item'][0]) && isset($results['item']['mid']))
            {
                $results['item'] = array($results['item']);
            }

            $results = $this->prepareResults($results['item']);

            $product = null;
            foreach ($results as $i => $r)
            {
                if ($this->isProductsMatch($item, $r))
                {
                    $product = $r;
                    break;
                }
            }
            if (!$product)
            {
                if ($this->config('stock_status') == 'out_of_stock')
                {
                    $items[$key]['stock_status'] = ContentProduct::STOCK_STATUS_OUT_OF_STOCK;
                }
                else
                {
                    $items[$key]['stock_status'] = ContentProduct::STOCK_STATUS_UNKNOWN;
                }
                continue;
            }
            // assign new price
            $items[$key]['stock_status'] = ContentProduct::STOCK_STATUS_IN_STOCK;
            $items[$key]['price'] = $product->price;
            $items[$key]['url'] = $product->url;
        }

        return $items;
    }

    public function isProductsMatch(array $p1, ContentProduct $p2)
    {
        $p2 = json_decode(json_encode($p2), true);

        if ($p1['url'] == $p2['url'])
        {
            return true;
        }

        if ($p1['domain'] && $p1['domain'] != $p2['domain'])
        {
            return false;
        }

        if ($p1['sku'] && $p1['sku'] == $p2['sku'])
        {
            return true;
        }
        if ($p1['ean'] && $p1['ean'] == $p2['ean'])
        {
            return true;
        }
        if ($p1['img'] && $p1['img'] == $p2['img'])
        {
            return true;
        }
        if ($p1['title'] == $p2['title'] && $p1['domain'] == $p2['domain'])
        {
            return true;
        }

        return false;
    }

    private function prepareResults($results)
    {
        $data = array();

        // filter sku dublicates
        if ($this->config('filter_duplicate'))
        {
            $urls = array();
            foreach ($results as $key => $r)
            {
                if ($murl = TextHelper::parseOriginalUrl($r['linkurl'], 'murl'))
                {
                    foreach ($urls as $url)
                    {
                        if ($murl == $url)
                        {
                            unset($results[$key]);
                            break;
                        }
                    }
                    $urls[] = $murl;
                }
            }
        }

        foreach ($results as $key => $r)
        {
            $content = new ContentProduct;
            $content->unique_id = $r['linkid'];
            $content->stock_status = ContentProduct::STOCK_STATUS_IN_STOCK;
            $content->title = trim($r['productname']);

            if (is_array($r['saleprice']))
            {
                $content->price = (float) $r['saleprice'][0];
                $content->currencyCode = $r['saleprice']['@attributes']['currency'];
            }
            else
            {
                $content->price = (float) $r['saleprice'];
                $content->currencyCode = $r['currency'];
            }

            if (is_array($r['price']))
            {
                $content->priceOld = (float) $r['price'][0];
            }
            else
            {
                $content->priceOld = (float) $r['price'];
            }

            if ($content->priceOld && $content->price && $content->price > $content->priceOld)
            {
                $old_price = $content->priceOld;
                $content->priceOld = $content->price;
                $content->price = $old_price;
            }

            if ($content->price == $content->priceOld)
                $content->priceOld = 0;

            $content->currency = TextHelper::currencyTyping($content->currencyCode);
            $content->url = $r['linkurl'];
            $content->orig_url = TextHelper::parseOriginalUrl($r['linkurl'], 'murl');
            $content->img = (!empty($r['imageurl'])) ? $r['imageurl'] : '';
            $content->merchant = ($r['merchantname']) ? $r['merchantname'] : '';
            $content->domain = TextHelper::parseDomain($content->url, 'murl');

            if (!empty($r['description']['long']))
            {
                $content->description = $r['description']['long'];
            }
            elseif (!empty($r['description']['short']) && trim($r['description']['short']) != $content->title)
            {
                $content->description = $r['description']['short'];
            }
            if ($content->description)
            {
                $content->description = trim(strip_tags($content->description));
                if ($max_size = $this->config('description_size'))
                {
                    $content->description = TextHelper::truncate($content->description, $max_size);
                }
            }
            if (!empty($r['category']['primary']))
            {
                $content->category = $r['category']['primary'];
            }

            if (!$content->unique_id)
            {
                $content->unique_id = md5($content->title . $content->price);
            }

            $content->extra = new ExtraDataLinkshare;
            $content->extra->mid = ($r['mid']) ? $r['mid'] : '';
            $content->extra->createdon = ($r['createdon']) ? strtotime(str_replace('/', ' ', $r['createdon'])) : '';
            $content->sku = $content->extra->sku = ($r['sku']) ? $r['sku'] : '';
            $content->upc = $content->extra->upccode = ($r['upccode']) ? $r['upccode'] : '';
            $content->extra->keywords = ($r['keywords']) ? $r['keywords'] : '';

            $data[] = $content;
        }

        return $data;
    }

    private function getApiClient()
    {
        if ($this->api_client !== null)
        {
            return $this->api_client;
        }

        $clientId = $this->config('client_id');
        $clientSecret = $this->config('client_secret');
        $sid = $this->config('sid');
        $token = $this->config('token'); // Deprecated

        if ($clientId && $clientSecret && $sid)
        {
            $this->api_client = new RakutenApiClient($clientId, $clientSecret, $sid);
            $this->api_client->setAccessToken($this->getAccessToken());
        }
        elseif ($token)
        {
            // Deprecated fallback
            $this->api_client = new LinkshareProductsRest($token);
        }
        else
        {
            throw new \RuntimeException(
                esc_html__(
                    'Linkshare module is not configured. Please set Client ID, Client Secret and SID in module settings.',
                    'content-egg'
                )
            );
        }

        return $this->api_client;
    }

    public function requestAccessToken()
    {
        $api_client = $this->getApiClient();
        $response = $api_client->requestAccessToken();

        if (!$response || empty($response['access_token']) || empty($response['expires_in']))
        {
            throw new \Exception('Rakuten API: Invalid Response Format.');
        }

        return array($response['access_token'], (int) $response['expires_in']);
    }

    public function renderResults()
    {
        PluginAdmin::render('_metabox_results', array('module_id' => $this->getId()));
    }

    public function renderSearchResults()
    {
        PluginAdmin::render('_metabox_search_results', array('module_id' => $this->getId()));
    }

    private static function _sanitizeKeyword($keyword)
    {
        $invalidCharacters = [
            '&',
            '=',
            '?',
            '{',
            '}',
            '\\',
            '(',
            ')',
            '[',
            ']',
            '-',
            ';',
            '~',
            '|',
            '$',
            '!',
            '>',
            '*',
            '%'
        ];

        return str_replace($invalidCharacters, '', $keyword);
    }
}
