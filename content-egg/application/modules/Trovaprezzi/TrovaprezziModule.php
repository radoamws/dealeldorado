<?php

namespace ContentEgg\application\modules\Trovaprezzi;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModule;
use ContentEgg\application\components\ContentProduct;
use ContentEgg\application\admin\PluginAdmin;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\libs\trovaprezzi\TrovaprezziApi;

/**
 * TrovaprezziModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class TrovaprezziModule extends AffiliateParserModule
{

    public function info()
    {
        return array(
            'name' => 'Trovaprezzi',
            'docs_uri' => 'https://ce-docs.keywordrush.com/modules/affiliate/trovaprezzi',
        );
    }

    public function releaseVersion()
    {
        return '11.4.0';
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
        return false;
    }

    public function isUrlSearchAllowed()
    {
        return true;
    }

    public function doRequest($keyword, $query_params = array(), $is_autoupdate = false)
    {
        $client = $this->getApiClient();

        if ($is_autoupdate)
            $limit = $this->config('entries_per_page_update');
        else
            $limit = $this->config('entries_per_page');

        $options = array();
        $options['sort'] = $this->config('sort');

        if ($this->config('one_offer'))
            $options['merchantUniqueness'] = 'true';

        if ($category_ids = $this->config('category_id'))
        {
            $category_ids = TextHelper::getArrayFromCommaList($category_ids);
            $options['categoryId'] = join(',', $category_ids);
        }

        if (!empty($query_params['min_price']))
            $options['minPrice'] = (float) $query_params['min_price'];
        elseif ($this->config('min_price'))
            $options['minPrice'] = (float) $this->config('min_price');

        if (!empty($query_params['max_price']))
            $options['maxPrice'] = (float) $query_params['max_price'];
        elseif ($this->config('max_price'))
            $options['maxPrice'] = (float) $this->config('max_price');

        if ($ku = self::parseKeywordFromUrl($keyword))
            $keyword = $ku;

        if (TextHelper::isEan($keyword))
            $results = $this->getApiClient()->searchEan($keyword, $options);
        else
        {
            // This string should not contains spaces, they should be replaced by underscore chars ( ‘_’ )
            $keyword = \sanitize_title($keyword);
            $keyword = str_replace('-', '_', $keyword);
            $results = $this->getApiClient()->search($keyword, $options);
        }

        $results = $client->search($keyword, $options);

        if (!isset($results['offers']) || !is_array($results['offers']))
            return array();

        return $this->prepareResults(array_slice($results['offers'], 0, $limit));
    }

    private function prepareResults($results)
    {

        $data = array();
        foreach ($results as $key => $r)
        {
            $content = new ContentProduct;

            $content->unique_id = $r['id'];
            $content->title = $r['name'];
            $content->url = $r['url'];
            $content->description = $r['description'];
            if ($max_size = $this->config('description_size'))
                $content->description = TextHelper::truncate($content->description, $max_size);
            $content->merchant = $r['merchantName'];
            $content->img = $r['bigImage'];
            $content->price = TextHelper::parsePriceAmount($r['price']);
            $content->priceOld = TextHelper::parsePriceAmount($r['listingPrice']);
            if ($content->priceOld == $content->price)
                $content->priceOld = 0;
            $content->currencyCode = TextHelper::parseCurrencyCode($r['currencyCode']);
            //$content->logo = $r['merchantLogo'];

            if ($domain = self::getMerchantDomain($r['merchantName']))
                $content->domain = $domain;
            else
            {
                $merchant = strtolower($r['merchantName']);
                if (TextHelper::isValidDomainName($merchant))
                    $content->domain = $merchant;
            }

            $content->extra = new ExtraDataTrovaprezzi();
            ExtraDataTrovaprezzi::fillAttributes($content->extra, $r);

            $data[] = $content;
        }

        return $data;
    }

    private function getApiClient()
    {
        return new TrovaprezziApi($this->config('partner_id'));
    }

    public function renderResults()
    {
        PluginAdmin::render('_metabox_results', array('module_id' => $this->getId()));
    }

    public function renderSearchResults()
    {
        PluginAdmin::render('_metabox_search_results', array('module_id' => $this->getId()));
    }

    public function renderSearchPanel()
    {
        $this->render('search_panel', array('module_id' => $this->getId()));
    }

    public function renderUpdatePanel()
    {
        $this->render('update_panel', array('module_id' => $this->getId()));
    }

    public static function getMerchantDomain($merchant)
    {
        $list = self::getMerchantDomains();
        if (isset($list[$merchant]))
            return $list[$merchant];
        else
            return false;
    }

    private function parseKeywordFromUrl($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL))
            return false;

        $url = strtok($url, '?');
        $parts = explode('/', $url);

        $keyword = end($parts);
        $keyword = str_replace('Fprezzo_', '', $keyword);
        $keyword = str_replace('prezzo_', '', $keyword);
        $keyword = str_replace('.aspx', '', $keyword);

        return $keyword;
    }

    public static function getMerchantDomains()
    {
        $m2d = array(
            'Ri Si Electronic' => 'risielectronic.it',
            'Jolly Shop' => 'jollyshop.it',
            'Interteria' => 'interteria.it',
            'Mondoshop' => 'mondoshop.it',
        );

        return \apply_filters('cegg_trovaprezzi_merchant2domain', $m2d);
    }

    public static function getPriceParamMap()
    {
        return [
            'min' => 'min_price',
            'max' => 'max_price',
        ];
    }
}
