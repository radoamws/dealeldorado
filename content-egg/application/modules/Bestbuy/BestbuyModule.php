<?php

namespace ContentEgg\application\modules\Bestbuy;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModule;
use ContentEgg\application\components\ContentProduct;
use ContentEgg\application\admin\PluginAdmin;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\libs\bestbuy\BestbuyApi;
use ContentEgg\application\components\LinkHandler;
use ContentEgg\application\modules\Bestbuy\ExtraDataBestbuy;
use ContentEgg\application\helpers\ArrayHelper;;

/**
 * BestbuyModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class BestbuyModule extends AffiliateParserModule
{

    public function info()
    {
        return array(
            'name' => 'Bestbuy',
            'description' => sprintf(__('Adds products from %s.', 'content-egg'), 'bestbuy.com'),
            'docs_uri' => 'https://ce-docs.keywordrush.com/modules/affiliate/bestbuy',
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
        $client = $this->getApiClient();

        $options = array();

        if ($is_autoupdate)
            $limit = $this->config('entries_per_page_update');
        else
            $limit = $this->config('entries_per_page');

        $options['pageSize'] = $limit;
        $options['IPID'] = $this->config('ipid');
        $options['show'] = 'all';

        if ($items = $this->searchByEan($keyword, $options))
            $results = $items;
        elseif ($items = $this->searchById($keyword, $options))
            $results = $items;
        else
            $results = $client->search($keyword, $options);

        if (!isset($results['products']) || !is_array($results['products']))
            return array();

        return $this->prepareResults($results['products']);
    }

    private function prepareResults($results)
    {
        $data = array();
        foreach ($results as $r)
        {
            $data[] = $this->prepareResult($r);
        }

        return $data;
    }

    private function prepareResult($r)
    {
        $content = new ContentProduct;

        $content->unique_id = $r['sku'];
        $content->title = $r['name'];
        $content->url = $r['affiliateUrl'];
        $content->rating = TextHelper::ratingPrepare($r['customerReviewAverage']);
        if (isset($r['rating']))
            $content->ratingDecimal = round((float) $r['rating'], 2);
        $content->reviewsCount = (int) $r['customerReviewCount'];
        $content->price = (float) $r['salePrice'];
        if ((float) $r['regularPrice'] > $content->price)
            $content->priceOld = (float) $r['regularPrice'];
        $content->domain = 'bestbuy.com';
        $content->currencyCode = 'USD';
        $content->img = $r['image'];
        $content->manufacturer = $r['manufacturer'];
        $content->sku = $r['sku'];
        $content->upc = $r['upc'];

        if (TextHelper::isEan($r['upc']))
            $content->ean = TextHelper::fixEan($r['upc']);

        if (!empty($r['longDescription']))
            $content->description = $r['longDescription'];
        elseif (!empty($r['shortDescription']))
            $content->description = $r['shortDescription'];
        elseif (!empty($r['description']))
            $content->description = $r['description'];

        $content->description = strip_tags(html_entity_decode($content->description));
        if ($max_size = $this->config('description_size'))
            $content->description = TextHelper::truncateHtml($content->description, $max_size);

        if (!empty($r['categoryPath']) && is_array($r['categoryPath']))
        {
            foreach ($r['categoryPath'] as $c)
            {
                if ($c['name'] == 'Best Buy')
                    continue;

                $content->categoryPath[] = $c['name'];
            }

            $content->category = current($content->categoryPath);
        }

        $content->availability = $r['orderable'];

        if ($r['orderable'] == 'SoldOut')
            $content->stock_status = ContentProduct::STOCK_STATUS_OUT_OF_STOCK;
        else
            $content->stock_status = ContentProduct::STOCK_STATUS_IN_STOCK;

        $content->extra = new ExtraDataBestbuy();
        ExtraDataBestbuy::fillAttributes($content->extra, $r);

        if (isset($r['details']) && is_array($r['details']))
        {
            $content->features = array();
            foreach ($r['details'] as $d)
            {
                if ($d['name'] == 'Product Name')
                    continue;

                $feature = array(
                    'name' => \sanitize_text_field($d['name']),
                    'value' => \sanitize_text_field($d['value']),
                );
                $content->features[] = $feature;
            }
        }

        if (isset($r['features']) && is_array($r['features']))
        {
            $content->extra->keySpecs = array();
            foreach ($r['features'] as $f)
            {

                $content->extra->keySpecs[] = $f['feature'];
            }
        }

        return $content;
    }

    public function doRequestItems(array $items)
    {
        $client = $this->getApiClient();

        $options = array();
        $options['IPID'] = $this->config('ipid');
        //$options['show'] = 'all';

        $item_ids = array_map(function ($element)
        {
            return $element['unique_id'];
        }, $items);

        $results = $client->searchSku($item_ids, $options);
        if (!$results || !isset($results['products']))
            return $items;

        if (is_array($results['products']))
            $results = $results['products'];
        else
            $results = array();

        $results = $this->prepareResults($results);
        $new = array();
        foreach ($results as $r)
        {
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

            // all extra fields
            $items[$unique_id]['extra'] = ArrayHelper::object2Array($result->extra);
        }

        return $items;
    }

    private function getApiClient()
    {
        return new BestbuyApi($this->config('api_key'));
    }

    public function viewDataPrepare($data)
    {
        $ipid = $this->config('ipid');

        foreach ($data as $key => $d)
        {
            $data[$key]['url'] = \add_query_arg('IPID', $ipid, $d['url']);
        }

        return parent::viewDataPrepare($data);
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
        if (preg_match('~\/(\d+)\.p~', $url, $matches))
            return $matches[1];
        else
            return false;
    }

    private function searchByEan($keyword, $options)
    {
        if (!TextHelper::isEan($keyword))
            return false;

        $client = $this->getApiClient();

        if (strlen($keyword != 12))
        {
            $keyword = ltrim($keyword, '0');
            $keyword = str_pad($keyword, 12, '0', STR_PAD_LEFT);
        }

        try
        {
            $items = $client->searchEan($keyword, $options);
        }
        catch (\Exception $e)
        {
            return false;
        }

        if (!$items || !isset($items['products']))
            return false;

        return $items;
    }

    private function searchById($keyword, $options)
    {
        if ($pid = self::parsePidFromUrl($keyword))
            $keyword = $pid;

        if (!preg_match('~\d{7,12}~', $keyword))
            return false;

        $client = $this->getApiClient();
        try
        {
            $items = $client->searchSku($keyword, $options);
        }
        catch (\Exception $e)
        {
            return false;
        }

        if (!$items || !isset($items['products']))
            return false;

        return $items;
    }
}
