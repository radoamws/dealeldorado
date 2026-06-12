<?php

namespace ContentEgg\application\modules\Amazon;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModule;
use ContentEgg\application\libs\amazon\AmazonApi;
use ContentEgg\application\components\ContentProduct;
use ContentEgg\application\admin\PluginAdmin;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\helpers\ArrayHelper;
use ContentEgg\application\helpers\TemplateHelper;
use ContentEgg\application\libs\amazon\AmazonLocales;

/**
 * AmazonModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class AmazonModule extends AffiliateParserModule
{

    private $api_client = null;

    public function info()
    {
        return array(
            'name' => 'Amazon',
            'docs_uri' => 'https://ce-docs.keywordrush.com/modules/affiliate/amazon',
        );
    }

    public function getParserType()
    {
        return self::PARSER_TYPE_PRODUCT;
    }

    public function defaultTemeName()
    {
        return 'data_item';
    }

    public function isItemsUpdateAvailable()
    {
        return true;
    }

    public function isFree()
    {
        return true;
    }

    public function isUrlSearchAllowed()
    {
        return true;
    }

    public function doRequest($keyword, $query_params = array(), $is_autoupdate = false)
    {
        if (!empty($query_params['locale']) && AmazonLocales::getLocale($query_params['locale']))
        {
            $locale = $query_params['locale'];
        }
        else
        {
            $locale = $this->config('locale');
        }

        $associate_tag = $this->getAssociateTagForLocale($locale);

        if ($is_autoupdate)
        {
            $total = $this->config('entries_per_page_update');
        }
        else
        {
            $total = $this->config('entries_per_page');
        }

        $client = $this->getAmazonClient();
        $client->setLocale($locale);
        $client->setAssociateTag($associate_tag);

        $options = $this->getCommonRequestParameters();
        $options['PartnerTag'] = $associate_tag;

        if ($asin = AmazonModule::parseAsinFromUrl($keyword))
        {
            $keyword = $asin;
        }

        if ($node = self::extractNodeId($keyword))
        {
            $options['BrowseNodeId'] = $node;
            $payload['Actor'] = "*";
            $payload['SearchIndex'] = "All";
            return $this->searchByKeyword($client, $options, $query_params, $total);
        }

        if (TextHelper::isAsin($keyword) && !TextHelper::isEan($keyword))
        {
            $options['ItemIds'] = TextHelper::splitAsins($keyword);

            return $this->searchByASINs($client, $options);
        }
        else
        {
            if ($this->config('title'))
            {
                $options['Title'] = $keyword;
            }
            else
            {
                $options['Keywords'] = $keyword;
            }

            return $this->searchByKeyword($client, $options, $query_params, $total);
        }
    }

    protected function getCommonRequestParameters()
    {
        $options = array();
        $options['Resources'] = self::getResources();

        if ($v = $this->config('CurrencyOfPreference'))
        {
            $options['CurrencyOfPreference'] = $v;
        }
        if ($v = $this->config('LanguagesOfPreference'))
        {
            $options['LanguagesOfPreference'] = TextHelper::getArrayFromCommaList($v);
        }

        return $options;
    }

    protected function searchByASINs($client, array $options)
    {
        $options['Resources'][] = "OffersV2.Listings.Price";

        $data = $client->GetItems($options);
        if (!is_array($data) || !isset($data['ItemsResult']['Items']))
        {
            return array();
        }
        $items = $data['ItemsResult']['Items'];
        $this->addVariationSummary($client, $options, $items);

        return $this->prepareResults($items, $client->getLocale(), $client->getAssociateTag());
    }

    protected function addVariationSummary($client, array $options, array &$items)
    {
        if (isset($options['ItemIds']))
        {
            unset($options['ItemIds']);
        }
        $options['Resources'] = array('VariationSummary.Price.LowestPrice');

        // check for parent ASINs
        foreach ($items as $i => $item)
        {
            // child or parent?
            if (isset($item['ParentASIN']))
            {
                continue;
            }

            $options['ASIN'] = $item['ASIN'];

            if (\apply_filters('cegg_amazon_one_request_per_second', true))
                sleep(1);
            try
            {
                $data = $client->GetVariations($options);
            }
            catch (\Exception $e)
            {
                continue;
            }
            if (isset($data['VariationsResult']['VariationSummary']))
            {
                $items[$i]['VariationSummary'] = $data['VariationsResult']['VariationSummary'];
            }
        }
    }

    protected function searchByKeyword($client, array $options, array $query_params, $total)
    {
        if (!empty($query_params['minimum_price']))
        {
            $options['MinPrice'] = TextHelper::pricePenniesDenomination($query_params['minimum_price']);
        }
        elseif ($this->config('minimum_price'))
        {
            $options['MinPrice'] = TextHelper::pricePenniesDenomination($this->config('minimum_price'));
        }
        if (!empty($query_params['maximum_price']))
        {
            $options['MaxPrice'] = TextHelper::pricePenniesDenomination($query_params['maximum_price']);
        }
        elseif ($this->config('maximum_price'))
        {
            $options['MaxPrice'] = TextHelper::pricePenniesDenomination($this->config('maximum_price'));
        }
        if (!empty($query_params['min_percentage_off']))
        {
            $options['MinSavingPercent'] = (int) $query_params['min_percentage_off'];
        }
        elseif ($this->config('min_percentage_off'))
        {
            $options['MinSavingPercent'] = (int) $this->config('min_percentage_off');
        }

        // from autoblog
        if (isset($query_params['product_condition']) && in_array($query_params['product_condition'], array(
            'new',
            'used',
            'refurbished'
        )))
        {
            $options['Condition'] = ucfirst($query_params['product_condition']);
        }
        elseif ($v = $this->config('Condition'))
        {
            $options['Condition'] = $v;
        }

        // v4 module options mapping
        if (empty($options['SearchIndex']) && $v = $this->config('search_index'))
        {
            $options['SearchIndex'] = $v;
        }
        if (empty($options['BrowseNodeId']) && $v = $this->config('brouse_node'))
        {
            $options['BrowseNodeId'] = $v;
        }

        // DeliveryFlags
        $flags = array('AmazonGlobal', 'FreeShipping', 'FulfilledByAmazon', 'Prime');
        $DeliveryFlags = array();
        foreach ($flags as $flag)
        {
            if ($this->config($flag))
            {
                $DeliveryFlags[] = $flag;
            }
        }
        if ($DeliveryFlags)
        {
            $options['DeliveryFlags'] = $DeliveryFlags;
        }

        if ($v = $this->config('MinReviewsRating'))
        {
            $options['MinReviewsRating'] = (int) $v;
        }
        if ($v = $this->config('SortBy'))
        {
            $options['SortBy'] = $this->config('SortBy');
        }

        // Guard: nothing to fetch
        $total = (int) $total;
        if ($total <= 0)
        {
            return [];
        }

        // Determine per-page (1..10). If $total <= 10, request exactly that many per page.
        $perPage = 10;
        if ($total <= 10)
        {
            $perPage = max(1, $total);
            $options['ItemCount'] = $perPage + 1; // looks like a PA API bug — it returns one fewer item than requested
        }

        $maxApiPages   = 10; // hard limit for SearchItems
        $pagesToFetch  = (int) ceil($total / $perPage);
        $pagesToFetch  = min($pagesToFetch, $maxApiPages);

        $results = [];
        for ($i = 0; $i < $pagesToFetch; $i++)
        {
            if ($i > 0 && \apply_filters('cegg_amazon_one_request_per_second', true))
            {
                sleep(1);
            }

            $options['ItemPage'] = $i + 1;

            $resp = $client->SearchItems($options);
            if (!is_array($resp) || empty($resp['SearchResult']['Items']))
            {
                break;
            }

            // Derive available pages from API response (and cap it)
            if (isset($resp['SearchResult']['TotalResultCount']))
            {
                $availablePages = (int) ceil(((int) $resp['SearchResult']['TotalResultCount']) / $perPage);
                $availablePages = min($availablePages, $maxApiPages);
            }
            else
            {
                $availablePages = 1;
            }

            // Don’t exceed requested total
            $items     = $resp['SearchResult']['Items'];
            $remaining = $total - count($results);
            $items     = array_slice($items, 0, max(0, $remaining));

            $results = array_merge(
                $results,
                $this->prepareResults($items, $client->getLocale(), $client->getAssociateTag())
            );

            // Stop if we hit our goal or the last available page
            if (count($results) >= $total || ($i + 1) >= $availablePages)
            {
                break;
            }
        }

        return $results;
    }

    public function doRequestItems(array $items)
    {
        $locales = array();
        $default_locale = $this->config('locale');

        // find all locales
        foreach ($items as $item)
        {
            if (!empty($item['extra']['locale']))
            {
                $locale = $item['extra']['locale'];
            }
            else
            {
                $locale = $default_locale;
                $item['extra']['locale'] = $locale;
            }

            if (!in_array($locale, $locales))
            {
                $locales[] = $locale;
            }
        }

        // request by locale
        $results = array();
        foreach ($locales as $locale)
        {
            $request = array();
            foreach ($items as $item)
            {
                if ($item['extra']['locale'] == $locale)
                {
                    $request[] = $item;
                }
            }

            // This parameter can have a maximum of 10 values.
            $pages_count = ceil(count($request) / 10);
            for ($i = 0; $i < $pages_count; $i++)
            {
                // If your application is submitting requests faster than once per second per IP address, you may receive error messages from the Product Advertising API until you decrease the rate of your requests.
                if ($i > 0 && \apply_filters('cegg_amazon_one_request_per_second', true))
                    sleep(1);

                $request10 = array_slice($request, $i * 10, 10);
                try
                {
                    $results = array_merge($results, $this->requestItems($request10, $locale));
                }
                catch (\Exception $e)
                {
                    // API error. Do not update.
                    return $items;
                }
            }
        }

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
                'currency',
                'currencyCode',
                'availability',
                'orig_url',
                'stock_status',
                'url'
            );
            foreach ($fields as $field)
            {
                $items[$unique_id][$field] = $result->$field;
            }
            if (!$this->config('save_img'))
            {
                $items[$unique_id]['img'] = $result->img;
            }

            // all extra fields for API data only
            $items[$unique_id]['extra'] = ArrayHelper::object2Array($result->extra);
        }

        return $items;
    }

    private function requestItems(array $items, $locale)
    {
        $associate_tag = $this->getAssociateTagForLocale($locale);

        $client = $this->getAmazonClient();
        $client->setLocale($locale);
        $client->setAssociateTag($associate_tag);

        $options = $this->getCommonRequestParameters();

        $options['PartnerTag'] = $associate_tag;

        $item_ids = array();
        foreach ($items as $item)
        {
            $item_ids[] = $item['extra']['ASIN'];
        }

        $options['ItemIds'] = $item_ids;

        return $this->searchByASINs($client, $options);
    }

    private function prepareResults($results, $locale, $associate_tag)
    {
        $data = array();
        foreach ($results as $key => $r)
        {
            $data[] = $this->prepareResult($r, $locale, $associate_tag);
        }

        return $data;
    }

    private function prepareResult($r, $locale, $associate_tag)
    {
        $content = new ContentProduct;
        $extra = new ExtraDataAmazon;

        $content->unique_id = $locale . '-' . $r['ASIN'];
        $content->url = $r['DetailPageURL'];
        $content->orig_url = strtok($r['DetailPageURL'], '?');
        $content->title = $r['ItemInfo']['Title']['DisplayValue'];

        if (isset($r['Images']['Primary']))
        {
            $content->img = $r['Images']['Primary']['Large']['URL'];
            $extra->primaryImages = $r['Images']['Primary'];
        }
        elseif (isset($r['Images']['Variants'][0]))
        {
            $content->img = $r['Images']['Variants'][0]['Large']['URL'];
        }
        $content->domain = AmazonConfig::getDomainByLocale($locale);
        $content->merchant = ucfirst($content->domain);
        $extra->ASIN = $r['ASIN'];
        $extra->locale = $locale;
        $extra->associate_tag = $associate_tag;
        $extra->addToCartUrl = $this->generateAddToCartUrl($locale, $extra->ASIN);
        if (isset($r['ItemInfo']['ByLineInfo']['Manufacturer']))
        {
            $content->manufacturer = $r['ItemInfo']['ByLineInfo']['Manufacturer']['DisplayValue'];
        }

        if (isset($r['ItemInfo']['Classifications']['Binding']))
        {
            $content->category = $r['ItemInfo']['Classifications']['Binding']['DisplayValue'];
        }

        if (isset($r['ItemInfo']['ExternalIds']['EANs']))
        {
            $content->ean = $r['ItemInfo']['ExternalIds']['EANs']['DisplayValues'][0];
            $extra->EANs = $r['ItemInfo']['ExternalIds']['EANs']['DisplayValues'];
        }
        if (isset($r['ItemInfo']['ExternalIds']['UPCs']))
        {
            $content->upc = $r['ItemInfo']['ExternalIds']['UPCs']['DisplayValues'][0];
            $extra->UPCs = $r['ItemInfo']['ExternalIds']['UPCs']['DisplayValues'];
        }
        if ($this->config('link_type') == 'add_to_cart')
        {
            $content->orig_url = $content->url;
            $content->url = $extra->addToCartUrl;
        }
        /*
        if (isset($r['ItemInfo']['Features']['DisplayValues']))
        {
            $extra->itemAttributes['Feature'] = $r['ItemInfo']['Features']['DisplayValues'];
        }
        */

        if (isset($r['ItemInfo']['Features']['DisplayValues']))
        {
            $features = $r['ItemInfo']['Features']['DisplayValues'];
            if ($features && is_array($features))
                $content->description = "<ul>\n<li>" . join("</li>\n<li>", $features) . "</li>\n</ul>";

            $extra->itemAttributes['Feature'] = array();
        }

        if (!empty($r['Images']['Variants']))
        {
            $extra->imageSet = array();
            foreach ($r['Images']['Variants'] as $v)
            {
                $image = array(
                    'ThumbnailImage' => $v['Small']['URL'],
                    'MediumImage' => $v['Medium']['URL'],
                    'LargeImage' => $v['Large']['URL'],
                );
                $extra->imageSet[] = $image;
            }
        }

        $content->images = array();
        if ($extra->imageSet && is_array($extra->imageSet))
        {
            foreach ($extra->imageSet as $key => $image)
            {
                $content->images[] = $image['LargeImage'];
            }
        }

        $content->features = array();
        $infos = array();
        if (isset($r['ItemInfo']['ManufactureInfo']))
        {
            $infos = $r['ItemInfo']['ManufactureInfo'];
        }
        if (isset($r['ItemInfo']['ProductInfo']))
        {
            $infos = array_merge($infos, $r['ItemInfo']['ProductInfo']);
        }
        if (isset($r['ItemInfo']['ContentInfo']))
        {
            $infos = array_merge($infos, $r['ItemInfo']['ContentInfo']);
        }
        if (isset($r['ItemInfo']['TechnicalInfo']))
        {
            $infos = array_merge($infos, $r['ItemInfo']['TechnicalInfo']);
        }
        foreach ($infos as $info)
        {
            if (!isset($info['Label']) || in_array($info['Label'], array('UnitCount', 'NumberOfItems')))
            {
                continue;
            }
            if (isset($info['DisplayValue']))
            {
                $value = $info['DisplayValue'];
            }
            elseif (isset($info['DisplayValues'][0]['DisplayValue']))
            {
                $value = $info['DisplayValues'][0]['DisplayValue'];
            }
            elseif (isset($info['DisplayValues'][0]) && !is_array($info['DisplayValues'][0]))
            {
                $value = $info['DisplayValues'][0];
            }
            else
            {
                continue;
            }

            $feature = array(
                'name' => TemplateHelper::splitAttributeName($info['Label']),
                'value' => $value,
            );
            $content->features[] = $feature;
        }

        self::fillOfferVars($r, $content, $extra);
        $content->extra = $extra;

        return $content;
    }

    private function getAmazonClient()
    {
        if ($this->api_client === null)
        {
            $access_key_id = $this->config('access_key_id');
            $secret_access_key = $this->config('secret_access_key');
            $associate_tag = $this->config('associate_tag');
            $this->api_client = new AmazonApi($access_key_id, $secret_access_key, $associate_tag);
        }

        return $this->api_client;
    }

    static private function fillOfferVars($r, $content, $extra)
    {
        $offer = null;
        $content->price = 0;
        $content->priceOld = 0;
        $content->stock_status = ContentProduct::STOCK_STATUS_IN_STOCK;

        if (isset($r['OffersV2']['Listings'][0]))
        {
            $offer2 = $r['OffersV2']['Listings'][0];
            if (isset($offer2['Price']))
            {
                $content->price = $offer2['Price']['Money']['Amount'];
                $content->currencyCode = $offer2['Price']['Money']['Currency'];
            }
            if (isset($offer2['Price']['SavingBasis']))
            {
                $content->priceOld = $offer2['Price']['SavingBasis']['Money']['Amount'];
            }
        }

        if (isset($r['Offers']['Listings'][0]))
        {
            $offer = $r['Offers']['Listings'][0];
            if (!$content->price)
            {
                if (isset($offer['Availability']['Message']))
                {
                    $content->availability = $offer['Availability']['Message'];
                }

                if (isset($offer['Price']))
                {
                    $content->price = $offer['Price']['Amount'];
                    $content->currencyCode = $offer['Price']['Currency'];
                }
                if (isset($offer['SavingBasis']))
                {
                    $content->priceOld = $offer['SavingBasis']['Amount'];
                }
            }
        }

        if (!isset($r['OffersV2']['Listings'][0]) && !isset($r['Offers']['Listings'][0]))
        {
            $content->stock_status = ContentProduct::STOCK_STATUS_OUT_OF_STOCK;
        }

        if (isset($r['Offers']['Listings'][1]) && isset($r['Offers']['Listings'][1]['ProgramEligibility']['IsPrimeExclusive']) && filter_var($r['Offers']['Listings'][1]['ProgramEligibility']['IsPrimeExclusive'], FILTER_VALIDATE_BOOLEAN))
        {
            $extra->primePrice = (float) $r['Offers']['Listings'][1]['Price']['Amount'];
            if ($offer)
                $offer['ProgramEligibility']['IsPrimeExclusive'] = true;
        }

        if (isset($r['Offers']['Summaries']))
        {
            foreach ($r['Offers']['Summaries'] as $s)
            {
                if ($s['Condition']['Value'] == 'New' && isset($s['LowestPrice']))
                {
                    $extra->lowestNewPrice = $s['LowestPrice']['Amount'];
                    $extra->totalNew = (int) $s['OfferCount'];
                }
                elseif ($s['Condition']['Value'] == 'Used' && isset($s['LowestPrice']))
                {
                    $extra->lowestUsedPrice = $s['LowestPrice']['Amount'];
                    $extra->totalUsed = (int) $s['OfferCount'];
                }
            }
        }

        if ($offer)
        {
            $extra->IsEligibleForSuperSaverShipping = filter_var($offer['DeliveryInfo']['IsFreeShippingEligible'], FILTER_VALIDATE_BOOLEAN);
            if (isset($offer['DeliveryInfo']['IsAmazonFulfilled']))
            {
                $extra->IsAmazonFulfilled = filter_var($offer['DeliveryInfo']['IsAmazonFulfilled'], FILTER_VALIDATE_BOOLEAN);
            }
            $extra->IsPrimeEligible = filter_var($offer['DeliveryInfo']['IsPrimeEligible'], FILTER_VALIDATE_BOOLEAN);
            $extra->IsBuyBoxWinner = filter_var($offer['IsBuyBoxWinner'], FILTER_VALIDATE_BOOLEAN);
            $extra->IsPrimeExclusive = filter_var($offer['ProgramEligibility']['IsPrimeExclusive'], FILTER_VALIDATE_BOOLEAN);
            $extra->IsPrimePantry = filter_var($offer['ProgramEligibility']['IsPrimePantry'], FILTER_VALIDATE_BOOLEAN);
            $extra->ViolatesMAP = filter_var($offer['ViolatesMAP'], FILTER_VALIDATE_BOOLEAN);
            $extra->Condition = $offer['Condition']['Value'];
            $extra->MerchantName = $offer['MerchantInfo']['Name'];
            if (isset($offer['Price']['PricePerUnit']))
            {
                $extra->PricePerUnit = $offer['Price']['PricePerUnit'];
            }
            if (isset($offer['Price']['DisplayAmount']))
            {
                $extra->DisplayAmount = $offer['Price']['DisplayAmount'];
                if (preg_match('/\((.+?)\)/', $extra->DisplayAmount, $matches))
                {
                    $extra->pricePerUnitDisplay = $matches[1];
                }
            }
        }

        // fix for parent ASIN
        if (isset($r['VariationSummary']) && isset($r['VariationSummary']['Price']))
        {
            $content->stock_status = ContentProduct::STOCK_STATUS_IN_STOCK;
            $content->price = $r['VariationSummary']['Price']['LowestPrice']['Amount'];
            $content->currencyCode = $r['VariationSummary']['Price']['LowestPrice']['Currency'];
            $content->priceOld = 0;
        }

        // fix for used prices
        if ($content->price == $extra->lowestUsedPrice && $extra->lowestNewPrice)
        {
            $content->price = $extra->lowestNewPrice;
        }
    }

    public function getAssociateTagForLocale($locale)
    {
        if ($locale == $this->config('locale'))
        {
            return $this->config('associate_tag');
        }
        else
        {
            return $this->config('associate_tag_' . $locale);
        }
    }

    /**
     * Add to shopping cart url
     * @link: https://webservices.amazon.com/paapi5/documentation/add-to-cart-form.html
     * @link: https://affiliate-program.amazon.com/help/node/topic/G9SMD8TQHFJ7728F
     */
    private function getAmazonAddToCartUrl($locale)
    {
        return 'https://www.' . AmazonConfig::getDomainByLocale($locale) . '/gp/aws/cart/add.html';
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

    private static function pricePenniesDenomination($amount)
    {
        return TextHelper::pricePenniesDenomination($amount, false);
    }

    public static function parseAsinFromUrl($url)
    {

        $regex = '~/(?:exec/obidos/ASIN/|o/|gp/product/|gp/offer-listing/|(?:(?:[^"\'/]*)/)?dp/|)([0-9A-Z]{10})(?:(?:/|\?|\#)(?:[^"\'\s]*))?~isx';
        if (preg_match($regex, $url, $matches))
        {
            return $matches[1];
        }
        else
        {
            return false;
        }
    }

    private function generateAddToCartUrl($locale, $asin)
    {
        return $this->getAmazonAddToCartUrl($locale) .
            '?ASIN.1=' . $asin . '&Quantity.1=1' .
            '&AssociateTag=' . $this->getAssociateTagForLocale($locale);
    }

    public function viewDataPrepare($data)
    {
        foreach ($data as $key => $d)
        {
            if (empty($data[$key]['merchant']) || $data[$key]['merchant'] == 'Amazon')
            {
                $data[$key]['merchant'] = ucfirst($d['domain']);
            }

            if ($this->config('link_type') == 'product' && strstr($d['url'], 'AssociateTag=') && !empty($d['orig_url']))
            {
                $data[$key]['url'] = $d['orig_url'];
            }
            elseif ($this->config('link_type') == 'add_to_cart' && !strstr($d['url'], 'AssociateTag=') && !empty($d['extra']['addToCartUrl']))
            {
                $data[$key]['url'] = $d['extra']['addToCartUrl'];
            }

            // forced URL update
            if ($this->config('forced_urls_update'))
            {
                if ($d['extra']['locale'])
                {
                    $tag_id = $this->getAssociateTagForLocale($d['extra']['locale']);
                }
                else
                {
                    $tag_id = $this->getAssociateTagForLocale($this->config('locale'));
                }

                if (strstr($data[$key]['url'], 'AssociateTag='))
                {
                    $data[$key]['url'] = TextHelper::addUrlParam($data[$key]['url'], 'AssociateTag', $tag_id);
                }
                else
                {
                    $data[$key]['url'] = TextHelper::addUrlParam($data[$key]['url'], 'tag', $tag_id);
                }
            }

            // forced Associate Tag
            if ($forced_tag = $this->config('forced_tag'))
            {
                $data[$key]['url'] = TextHelper::addUrlParam($data[$key]['url'], 'tag', $forced_tag);
            }

            // hide prices?
            $hp = $this->config('hide_prices');
            if ($hp == 'hide_24')
            {
                $last_update = TemplateHelper::getLastUpdate($this->getId());
                if ($last_update)
                    $lt = time() - $last_update;
                else
                    $lt = 0;
            }
            else
                $lt = 0;

            if ($hp == 'hide' || ($hp == 'hide_24' && $lt > \DAY_IN_SECONDS))
            {
                $data[$key]['price'] = '';
                $data[$key]['priceOld'] = '';
                $data[$key]['stock_status'] = 0;
                $data[$key]['extra']['totalNew'] = '';
                $data[$key]['extra']['totalUsed'] = '';
                $data[$key]['extra']['lowestNewPrice'] = '';
                $data[$key]['extra']['lowestUsedPrice'] = '';
                $data[$key]['extra']['PricePerUnit'] = '';
            }
        }

        return parent::viewDataPrepare($data);
    }

    public static function getResources()
    {
        return array(
            "CustomerReviews.Count",
            "CustomerReviews.StarRating",
            "Images.Primary.Small",
            "Images.Primary.Medium",
            "Images.Primary.Large",
            "Images.Variants.Small",
            "Images.Variants.Medium",
            "Images.Variants.Large",
            "ItemInfo.ByLineInfo",
            "ItemInfo.ContentInfo",
            "ItemInfo.ContentRating",
            "ItemInfo.Classifications",
            "ItemInfo.ExternalIds",
            "ItemInfo.Features",
            "ItemInfo.ManufactureInfo",
            "ItemInfo.ProductInfo",
            "ItemInfo.TechnicalInfo",
            "ItemInfo.Title",
            "ItemInfo.TradeInInfo",
            "Offers.Listings.Availability.MaxOrderQuantity",
            "Offers.Listings.Availability.Message",
            "Offers.Listings.Availability.MinOrderQuantity",
            "Offers.Listings.Availability.Type",
            "Offers.Listings.Condition",
            "Offers.Listings.Condition.SubCondition",
            "Offers.Listings.DeliveryInfo.IsAmazonFulfilled",
            "Offers.Listings.DeliveryInfo.IsFreeShippingEligible",
            "Offers.Listings.DeliveryInfo.IsPrimeEligible",
            "Offers.Listings.DeliveryInfo.ShippingCharges",
            "Offers.Listings.IsBuyBoxWinner",
            "Offers.Listings.LoyaltyPoints.Points",
            "Offers.Listings.MerchantInfo",
            "Offers.Listings.Price",
            "Offers.Listings.ProgramEligibility.IsPrimeExclusive",
            "Offers.Listings.ProgramEligibility.IsPrimePantry",
            "Offers.Listings.Promotions",
            "Offers.Listings.SavingBasis",
            "Offers.Summaries.HighestPrice",
            "Offers.Summaries.LowestPrice",
            "Offers.Summaries.OfferCount",
            "ParentASIN"
        );
    }

    public static function extractNodeId($url)
    {
        $parsedUrl = parse_url($url);
        if (!$parsedUrl || !isset($parsedUrl['query']))
        {
            return null;
        }

        parse_str($parsedUrl['query'], $queryParams);
        return isset($queryParams['node']) ? $queryParams['node'] : null;
    }

    public static function getPriceParamMap()
    {
        return [
            'min' => 'minimum_price',
            'max' => 'maximum_price',
        ];
    }

    public static function getLocaleParamMap()
    {
        return 'locale';
    }

    public static function getSortByNewestParamMap()
    {
        return [
            'SortBy' => 'NewestArrivals',
        ];
    }
}
