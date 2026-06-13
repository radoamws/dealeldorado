<?php

namespace ContentEgg\application\modules\Coupang;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModule;
use ContentEgg\application\components\ContentProduct;
use ContentEgg\application\admin\PluginAdmin;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\libs\coupang\CoupangApi;
use ContentEgg\application\modules\Coupang\ExtraDataCoupang;

/**
 * CoupangModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class CoupangModule extends AffiliateParserModule
{

    public function info()
    {
        return array(
            'name' => 'Coupang',
            'docs_uri' => 'https://ce-docs.keywordrush.com/modules/affiliate/coupang-module',
        );
    }

    public function releaseVersion()
    {
        return '18.1.0';
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
        return false;
    }

    public function doRequest($keyword, $query_params = array(), $is_autoupdate = false)
    {
        $limit = (int) ($is_autoupdate
            ? $this->config('entries_per_page_update')
            : $this->config('entries_per_page'));

        $options = array();

        if (!empty($this->config('image_size')))
        {
            $options['imageSize'] = $this->config('image_size');
        }

        if ($limit > 0 && $limit <= 10)
        {
            $options['limit'] = $limit;
        }

        $results = $this->getApiClient()->search($keyword, $options);

        if (
            !$results
            || !isset($results['data']['productData'])
            || !is_array($results['data']['productData'])
        )
        {
            return array();
        }

        $items = $results['data']['productData'];

        $items = array_slice($items, 0, $limit);

        return $this->prepareResults($items);
    }

    public function doRequestItems(array $items): array
    {
        $defaultStock = $this->config('stock_status') === 'out_of_stock'
            ? ContentProduct::STOCK_STATUS_OUT_OF_STOCK
            : ContentProduct::STOCK_STATUS_UNKNOWN;

        $options = ['limit' => 10];

        foreach ($items as $key => $item)
        {

            // Attempt 1: title, Attempt 2: extra.keyword (max two attempts)
            $keywords = [];
            foreach ([$item['title'] ?? null, $item['extra']['keyword'] ?? null] as $kw)
            {
                if (is_string($kw))
                {
                    $kw = trim($kw);
                    if ($kw !== '' && !in_array($kw, $keywords, true))
                    {
                        $keywords[] = $kw;
                    }
                }
            }

            // If no usable keyword, mark default and continue
            if (!$keywords)
            {
                $items[$key]['stock_status'] = $defaultStock;
                continue;
            }

            $matchedProduct = null;

            foreach ($keywords as $keyword)
            {
                try
                {
                    $resp = $this->getApiClient()->search($keyword, $options);
                }
                catch (\Throwable $e)
                {
                    // Try next keyword if this attempt errored
                    continue;
                }

                $rows = $resp['data']['productData'] ?? [];
                if (!is_array($rows) || !$rows)
                {
                    // Try next keyword if no results
                    continue;
                }

                $products = $this->prepareResults($rows);

                foreach ($products as $product)
                {
                    if ($this->isProductsMatch($item, $product))
                    {
                        $matchedProduct = $product;
                        break 2; // found → stop both loops
                    }
                }
                // else: try next keyword
            }

            if ($matchedProduct)
            {
                $items[$key]['stock_status'] = ContentProduct::STOCK_STATUS_IN_STOCK;
                $items[$key]['price']        = $matchedProduct->price;
                $items[$key]['url']          = $matchedProduct->url;
            }
            else
            {
                $items[$key]['stock_status'] = $defaultStock;
            }
        }

        return $items;
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
        $content->unique_id = $r['productId'];
        $content->title = $r['productName'];
        $content->url = $r['productUrl'];
        $content->price = (float)$r['productPrice'];
        $content->img = $r['productImage'];
        $content->category = $r['categoryName'];
        $content->stock_status = ContentProduct::STOCK_STATUS_IN_STOCK;

        $isFreeShipping = !empty($r['isFreeShipping']) && in_array($r['isFreeShipping'], [1, '1', true, 'true'], true);
        if ($isFreeShipping)
        {
            $content->shipping_cost = 0;
        }

        $content->currencyCode = 'KRW';
        $content->currency = TextHelper::currencyTyping($content->currencyCode);
        $content->domain = 'coupang.com';
        $content->merchant = 'Coupang';

        $content->extra = new ExtraDataCoupang();
        ExtraDataCoupang::fillAttributes($content->extra, $r);
        return $content;
    }

    private function getApiClient()
    {
        return new CoupangApi($this->config('access_key'), $this->config('secret_key'));
    }

    public function renderResults()
    {
        PluginAdmin::render('_metabox_results', array('module_id' => $this->getId()));
    }

    public function renderSearchResults()
    {
        PluginAdmin::render('_metabox_search_results', array('module_id' => $this->getId()));
    }

    public function isProductsMatch(array $p1, ContentProduct $p2)
    {
        $p2 = json_decode(json_encode($p2), true);

        if ((string)$p1['unique_id'] == (string)$p2['unique_id'])
        {
            return true;
        }

        return false;
    }
}
