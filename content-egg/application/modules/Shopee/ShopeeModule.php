<?php

namespace ContentEgg\application\modules\Shopee;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModule;
use ContentEgg\application\components\ContentProduct;
use ContentEgg\application\admin\PluginAdmin;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\libs\shopee\ShopeeApi;
use ContentEgg\application\modules\Shopee\ExtraDataShopee;
use ContentEgg\application\libs\shopee\ShopeeLocales;
use ContentEgg\application\components\LinkHandler;
use ContentEgg\application\helpers\ArrayHelper;;

/**
 * ShopeeModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class ShopeeModule extends AffiliateParserModule
{

    public function info()
    {
        return array(
            'name' => 'Shopee',
            'description' => sprintf(__('Adds products from %s.', 'content-egg'), 'Shopee'),
            'docs_uri' => 'https://ce-docs.keywordrush.com/modules/affiliate/shopee',
        );
    }

    public function releaseVersion()
    {
        return '11.2.0';
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

    private static function getProductFields()
    {
        return array(
            'itemId',
            'shopId',
            'commissionRate',
            'appExistRate',
            'appNewRate',
            'webExistRate',
            'webNewRate',
            'commission',
            'price',
            'priceMax',
            'priceMin',
            'ratingStar',
            'priceDiscountRate',
            'sales',
            'imageUrl',
            'productName',
            'shopName',
            'productLink',
            'offerLink',
            'periodStartTime',
            'periodEndTime',
        );
    }

    public function doRequest($keyword, $query_params = array(), $is_autoupdate = false)
    {
        if ($is_autoupdate)
            $limit = $this->config('entries_per_page_update');
        else
            $limit = $this->config('entries_per_page');

        if ($id = self::parseIdFromUrl($keyword))
            $keyword = $id;

        if (self::isProductId($keyword))
        {
            $offer = sprintf('itemId: %d', $keyword);
            $offer .= ' limit: 1';
        }
        else
        {
            $keyword = str_replace('"', '', $keyword);
            $keyword = str_replace('\'', '', $keyword);
            $keyword = str_replace('\\', '', $keyword);
            $offer = sprintf('keyword: \"%s\"', $keyword);
            $offer .= sprintf(' limit: %d', $limit);
            $offer .= sprintf(' sortType: %d', (int) $this->config('sort_type'));
        }

        $node = join(', ', self::getProductFields());

        $query = 'query {productOfferV2(' . $offer . ') {nodes {' . $node . '}}}';
        $payload = '{"query":"' . $query . '"}';

        $results = $this->getApiClient()->search($payload);

        if (!$results || !isset($results['data']['productOfferV2']['nodes']) || !is_array($results['data']['productOfferV2']['nodes']))
            return array();

        return $this->prepareResults($results['data']['productOfferV2']['nodes'], $this->config('locale'));
    }

    public function doRequestItems(array $items)
    {
        $node = join(', ', self::getProductFields());

        foreach ($items as $unique_id => $item)
        {
            $offer = sprintf('itemId: %d', $unique_id);
            $offer .= ' limit: 1';
            $query = 'query {productOfferV2(' . $offer . ') {nodes {' . $node . '}}}';
            $payload = '{"query":"' . $query . '"}';

            $results = $this->getApiClient()->search($payload);
            if (!$results || !isset($results['data']['productOfferV2']['nodes']) || !is_array($results['data']['productOfferV2']['nodes']))
                continue;

            $products = $this->prepareResults($results['data']['productOfferV2']['nodes'], $this->config('locale'));
            if (!$products)
                continue;

            $result = reset($products);

            // assign new data
            $fields = array(
                'url',
                'price',
                'priceOld',
                'availability',
                'stock_status',
            );

            foreach ($fields as $field)
            {
                $items[$unique_id][$field] = $result->$field;
            }

            $items[$unique_id]['extra'] = ArrayHelper::object2Array($result->extra);
        }

        return $items;
    }

    private function prepareResults($results, $locale)
    {
        $data = array();
        foreach ($results as $r)
        {
            $data[] = $this->prepareResult($r, $locale);
        }

        return $data;
    }

    private function prepareResult($r, $locale)
    {
        $content = new ContentProduct;
        $content->unique_id = $r['itemId'];
        $content->title = $r['productName'];
        $content->orig_url = $r['productLink'];

        if ($deeplink = $this->config('deeplink'))
            $content->url = LinkHandler::createAffUrl($content->orig_url, $deeplink);
        else
            $content->url = $r['offerLink'];

        if (isset($r['price']))
            $content->price = (float) $r['price'];
        elseif (isset($r['priceMin']))
            $content->price = (float) $r['priceMin'];
        elseif (isset($r['priceMax']))
            $content->price = (float) $r['priceMax'];

        $discount_rate = (int)$r['priceDiscountRate'];
        if ($discount_rate > 0 && $discount_rate < 100 && $content->price)
            $content->priceOld = number_format($content->price * 100 / (100 - $discount_rate), 2, '.', '');

        if (isset($r['ratingStar']))
        {
            $content->ratingDecimal = (float) $r['ratingStar'];
            $content->rating = TextHelper::ratingPrepare($r['ratingStar']);
        }

        $content->img = $r['imageUrl'];
        $content->currencyCode = ShopeeLocales::getCurrencyCode($locale);
        $content->domain = TextHelper::getHostName($content->orig_url);
        $content->merchant = 'Shopee';

        $content->extra = new ExtraDataShopee();
        $content->extra->locale = $locale;
        ExtraDataShopee::fillAttributes($content->extra, $r);
        return $content;
    }

    private function generateAffiliateUrl($url)
    {
        if ($deeplink = $this->config('deeplink'))
            return LinkHandler::createAffUrl($url, $deeplink);

        return $url;
    }

    public function viewDataPrepare($data)
    {
        foreach ($data as $key => $d)
        {
            if ($this->config('deeplink'))
            {
                $data[$key]['url'] = $this->generateAffiliateUrl($d['orig_url']);
            }
            elseif (isset($d['extra']['offerLink']))
            {
                $data[$key]['url'] = $d['extra']['offerLink'];
            }
        }

        return parent::viewDataPrepare($data);
    }

    private function getApiClient()
    {
        return new ShopeeApi($this->config('app_id'), $this->config('api_key'), $this->config('locale'));
    }

    public function renderResults()
    {
        PluginAdmin::render('_metabox_results', array('module_id' => $this->getId()));
    }

    public function renderSearchResults()
    {
        PluginAdmin::render('_metabox_search_results', array('module_id' => $this->getId()));
    }

    private static function parseIdFromUrl($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL))
            return '';

        $url = strtok($url, '?');
        $url = trim($url, '/');
        $parts = explode('/', $url);
        $id = end($parts);
        if (is_numeric($id))
            return (int) $id;

        $parts = explode('.', $url);
        $id = end($parts);
        if (is_numeric($id))
            return (int) $id;

        return '';
    }

    private static function isProductId($str)
    {
        if (preg_match('/[0-9]{10,}/', $str))
            return true;
        else
            return false;
    }
}
