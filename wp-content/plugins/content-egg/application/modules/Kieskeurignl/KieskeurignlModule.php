<?php

namespace ContentEgg\application\modules\Kieskeurignl;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModule;
use ContentEgg\application\components\ContentProduct;
use ContentEgg\application\admin\PluginAdmin;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\libs\kieskeurignl\KieskeurignlApi;
use ContentEgg\application\modules\Kieskeurignl\ExtraDataKieskeurignl;
use ContentEgg\application\helpers\ArrayHelper;



/**
 * KieskeurignlModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class KieskeurignlModule extends AffiliateParserModule
{

    public function info()
    {
        return array(
            'name' => 'Kieskeurignl',
            'docs_uri' => 'https://ce-docs.keywordrush.com/modules/affiliate/kieskeurignl',
        );
    }

    public function releaseVersion()
    {
        return '11.0.0';
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

    public function isUrlSearchAllowed()
    {
        return true;
    }

    public function doRequest($keyword, $query_params = array(), $is_autoupdate = false)
    {
        if (!$product_id = $this->findProductId($keyword))
            return array();

        if (!$result = $this->getOffers($product_id))
            return array();

        if ($is_autoupdate)
            $limit = $this->config('entries_per_page_update');
        else
            $limit = $this->config('entries_per_page');

        return $this->prepareResults($result, $product_id, $limit);
    }

    private function getOffers($product_id)
    {
        $options = array(
            'AffiliateId' => $this->config('affiliate_id'),
        );

        $client = $this->getApiClient();
        $response = $client->getOffers($product_id, $options);

        if (!$response || !is_array($response))
            return array();

        return $response;
    }

    private function prepareResults(array $offers, $product_id, $limit = 999)
    {
        $content = new ContentProduct;

        if (!$product = $this->getApiClient()->product($product_id))
            return array();

        $content->title = trim($product['title']);
        $content->description = $product['description'];
        $content->manufacturer = $product['brand'];
        if (isset($product['media'][0]))
            $content->img = $product['media'][0];

        $content->currencyCode = 'EUR';

        $features = array();
        if (isset($product['specifications']) && is_array($product['specifications']))
        {
            $content->features = array();
            foreach ($product['specifications'] as $section_name => $attributes)
            {
                foreach ($attributes as $attr_name => $attr_value)

                    if (strstr($attr_value, 'images.kieskeurig'))
                        continue;

                $feature = array(
                    'name' => \sanitize_text_field(ucfirst($attr_name)),
                    'value' => \sanitize_text_field($attr_value),
                );
                $features[] = $feature;
            }
        }

        $content->extra = new ExtraDataKieskeurignl();
        $content->extra->productid = $product['id']; // for price updates
        ExtraDataKieskeurignl::fillAttributes($content->extra, $product);

        $exclude_domains = TextHelper::getArrayFromCommaList(strtolower($this->config('exclude_domains')));
        $data = array();
        $i = 0;
        foreach ($offers as $r)
        {
            $domain = self::getMerchantDomain($r['shop']['name']);
            if (!$domain)
            {
                $merchant = strtolower($r['shop']['name']);
                if (TextHelper::isValidDomainName($merchant))
                    $domain = $merchant;
            }

            if ($exclude_domains && in_array($domain, $exclude_domains))
                continue;

            $c = clone $content;
            $c->domain = $domain;
            $c->unique_id = $r['shop']['id'] . '-' . $product['id'];
            $c->logo = $r['shop']['logo'];
            $c->url = $r['link'];
            $c->price = $r['amount'];
            if (!empty($r['productDescription']))
                $c->description = $r['productDescription'];

            $c->merchant = $r['shop']['name'];

            $c->stock_status = ContentProduct::STOCK_STATUS_IN_STOCK;

            if ($i == 0 && $features)
                $c->features = $features;

            ExtraDataKieskeurignl::fillAttributes($c->extra, $r);

            $data[] = $c;
            $i++;

            if (count($data) >= $limit)
                break;
        }

        return $data;
    }

    public function doRequestItems(array $items)
    {
        $product_ids = array_map(function ($element)
        {
            return $element['extra']['productid'];
        }, $items);

        $product_ids = array_unique($product_ids);
        $results = array();
        foreach ($product_ids as $product_id)
        {
            if (!$offers = $this->getOffers($product_id))
                continue;

            $results = array_merge($results, $this->prepareResults($offers, $product_id));
        }

        $new = array();
        foreach ($results as $r)
        {
            // fix for old api. find item by domain
            if (!isset($items[$r->unique_id]))
            {
                foreach ($items as $item)
                {
                    if ($item['domain'] == $r->domain && $item['title'] == $r->title)
                    {
                        $new[$item['unique_id']] = $r;
                        break;
                    }
                }
            }

            $new[$r->unique_id] = $r;
        }

        // assign new data
        foreach ($items as $unique_id => $item)
        {
            if (!isset($new[$unique_id]))
            {
                $items[$unique_id]['stock_status'] = ContentProduct::STOCK_STATUS_OUT_OF_STOCK;
                continue;
            }

            $result = $new[$unique_id];

            $fields = array(
                'price',
                'priceOld',
                'availability',
                'stock_status',
                'url',
            );
            foreach ($fields as $field)
            {
                $items[$unique_id][$field] = $result->$field;
            }

            $items[$unique_id]['extra'] = ArrayHelper::object2Array($result->extra);
        }

        return $items;
    }

    private function findProductId($keyword)
    {
        if ($pid = self::parsePidFromUrl($keyword))
            $keyword = $pid;

        if (self::isPid($keyword))
            return $keyword;

        $client = $this->getApiClient();
        $options = array();
        $options['Limit'] = 1;

        if (TextHelper::isEan($keyword))
            $results = $client->searchEan($keyword, $options);
        else
            $results = $client->search($keyword, $options);

        if (!$results || !is_array($results))
            return false;

        $result = reset($results);
        if (!isset($result['id']))
            return false;

        return $result['id'];
    }

    public function viewDataPrepare($data)
    {
        if ($exclude_domains = TextHelper::getArrayFromCommaList(strtolower($this->config('exclude_domains'))))
        {
            foreach ($data as $key => $d)
            {
                if (in_array($d['domain'], $exclude_domains))
                    unset($data[$key]);
            }
        }

        return parent::viewDataPrepare($data);
    }

    private function getApiClient()
    {
        return new KieskeurignlApi($this->config('token'));
    }

    public function renderResults()
    {
        PluginAdmin::render('_metabox_results', array('module_id' => $this->getId()));
    }

    public function renderSearchResults()
    {
        PluginAdmin::render('_metabox_search_results', array('module_id' => $this->getId()));
    }

    public static function parsePidFromUrl($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL))
            return false;

        $url = strtok($url, '?');
        if (preg_match('~product\/(\d+)-~', $url, $matches))
            return $matches[1];
        else
            return false;
    }

    static function isPid($pid)
    {
        if (preg_match('~\d{6,12}~', $pid))
            return true;
        else
            return false;
    }

    public static function getMerchantDomain($merchant)
    {
        $list = self::getMerchantDomains();
        if (isset($list[$merchant]))
            return $list[$merchant];
        else
            return false;
    }

    public static function getMerchantDomains()
    {
        $m2d = array(
            'Amazon' => 'amazon.nl',
            'BCC' => 'bcc.nl',
            'Bol.com' => 'bol.com',
            'Blokker connect' => 'blokker.nl',
            'Bol.com Plaza' => 'bol.com',
            'bol. Plaza' => 'bol.com',
            'Midimedia' => 'midimedia.nl',
            'Bemmel en Kroon' => 'bemmelenkroon.nl',
            'Keukenloods' => 'keukenloods.nl',
            'Beterwitgoed' => 'beterwitgoed.nl',
            'HelloTV' => 'hellotv.nl',
            'Expert' => 'expert.nl',
            'Art & Craft' => 'artencraft.nl',
            'Philips DA' => 'philips.nl',
            'Amazon Marketplace' => 'amazon.nl',
            'Tummers' => 'eptummers.nl',
        );

        return \apply_filters('cegg_kieskeurignl_merchant2domain', $m2d);
    }
}
