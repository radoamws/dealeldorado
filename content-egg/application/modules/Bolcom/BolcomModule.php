<?php

namespace ContentEgg\application\modules\Bolcom;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModule;
use ContentEgg\application\components\ContentProduct;
use ContentEgg\application\admin\PluginAdmin;
use ContentEgg\application\helpers\TextHelper;
use ContentEgg\application\libs\bolcom\BolcomJwtApi;
use ContentEgg\application\components\LinkHandler;
use ContentEgg\application\Plugin;;

/**
 * BolcomModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class BolcomModule extends AffiliateParserModule
{

    public function info()
    {
        return array(
            'name' => 'Bolcom',
            'description' => sprintf(__('Adds products from %s.', 'content-egg'), 'Bol.com'),
        );
    }

    public function releaseVersion()
    {
        return '4.1.0';
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

        if ($limit > 50)
            $limit = 50;

        $options['page-size'] = $limit;
        $options['country-code'] = $this->config('country');

        $sort = $this->config('sort');
        if (in_array($sort, array('RELEVANCE', 'POPULARITY', 'PRICE_ASC', 'PRICE_DESC', 'RELEASE_DATE', 'RATING')))
            $options['sort'] = $sort;

        if ($this->config('ids'))
            $options['category-id'] = (int) $this->config('ids');

        $options['include-categories'] = 'true';
        $options['include-image'] = 'true';
        $options['include-offer'] = 'true';
        $options['include-rating'] = 'true';

        if ($id = self::getEanFromUrl($keyword))
            $keyword = $id;

        if ($id = $this->getCategoryId($keyword))
            $results = $client->popular($id, $options);
        else
            $results = $client->search($keyword, $options);

        if (!isset($results['results']) || !is_array($results['results']))
            return array();

        $products = $this->prepareResults($results['results']);

        $gallery = $this->config('gallery');
        $category = $this->config('category');
        foreach ($products as $product)
        {
            $this->applaySpecsAndImage($product);

            if ($gallery === 'enabled')
            {
                $this->applayGallery($product);
            }
            if ($category === 'enabled')
            {
                $this->applayCategory($product);
            }
        }

        return $products;
    }

    private function applaySpecsAndImage(ContentProduct $product)
    {
        if (!$product->ean)
            return $product;

        $options = array();
        $options['country-code'] = $this->config('country');
        $options['include-specifications'] = 'true';
        $options['include-image'] = 'true';

        try
        {
            $r = $this->getApiClient()->product($product->ean, $options);
        }
        catch (\Exception $e)
        {
            return $product;
        }

        // large image
        if (isset($r['image']['url']))
            $product->img = $r['image']['url'];

        $features = array();
        if (isset($r['specificationGroups']) && is_array($r['specificationGroups']))
        {
            foreach ($r['specificationGroups'] as $spec_group)
            {

                foreach ($spec_group['specifications'] as $spec)
                {
                    $value = \strip_tags(join(', ', $spec['values']));

                    if (strstr($value, 'Deze informatie volgt nog'))
                        continue;

                    $feature = array(
                        'group' => \sanitize_text_field($spec_group['title']),
                        'name' => \sanitize_text_field($spec['name']),
                        'value' => $value,
                    );
                    $features[] = $feature;
                }
            }
        }

        $product->features = $features;

        return $product;
    }

    private function applayGallery(ContentProduct $product)
    {
        if (!$product->ean)
            return $product;

        $options = array();
        $options['country-code'] = $this->config('country');

        try
        {
            $r = $this->getApiClient()->media($product->ean, $options);
        }
        catch (\Exception $e)
        {
            return $product;
        }

        $gallery = [];

        if (!empty($r['images']) && is_array($r['images']))
        {
            foreach ($r['images'] as $i => $image)
            {
                if ($i === 0)
                {
                    continue;
                }

                foreach ($image['renditions'] as $rendition)
                {
                    if ((int)$rendition['width'] === 1200 || (int)$rendition['height'] === 1200)
                    {
                        $gallery[] = $rendition['url'];
                    }
                }
            }
        }

        $product->images = $gallery;
        return $product;
    }

    private function applayCategory(ContentProduct $product)
    {
        if (!$product->ean)
            return $product;

        $options = array();
        $options['country-code'] = $this->config('country');

        try
        {
            $r = $this->getApiClient()->categories($product->ean, $options);
        }
        catch (\Exception $e)
        {
            return $product;
        }

        if (empty($r['categories'] || !is_array($r['categories'])))
        {
            return $product;
        }
        $categoryPath = self::getBolCategoryPath($r['categories']);

        if (empty($categoryPath))
        {
            return $product;
        }

        $product->category = end($categoryPath);
        $product->category = (string) \apply_filters('cegg_bolcom_category', $product->category, $categoryPath);
        $product->categoryPath = $categoryPath;

        return $product;
    }

    private function getBolCategoryPath(array $categories)
    {
        $path = [];

        if (!empty($categories))
        {
            // Take the first category in the array
            $category = $categories[0];
            $path[] = $category['categoryName'];

            // If there are subcategories, recurse into them
            if (!empty($category['subcategories']))
            {
                $path = array_merge($path, self::getBolCategoryPath($category['subcategories']));
            }
        }

        return $path;
    }

    private function prepareResults($results)
    {
        $data = array();

        foreach ($results as $key => $r)
        {
            if (empty($r['image']))
                continue;

            $content = new ContentProduct;

            $content->unique_id = $r['bolProductId'];
            $content->title = $r['title'];
            $content->orig_url = $r['url'];
            $content->domain = 'bol.com';
            $content->currencyCode = 'EUR';
            $content->url = $this->createAffUrl($content->orig_url, (array) $content);

            if (!empty($r['ean']))
                $content->ean = $r['ean'];

            if (!empty($r['description']))
            {
                $content->description = $r['description'];
                if ($max_size = $this->config('description_size'))
                    $content->description = TextHelper::truncateHtml($content->description, $max_size);
            }

            $content->img = $r['image']['url'];
            if (!empty($r['offer']['price']))
            {
                $content->price = $r['offer']['price'];
                $content->stock_status = ContentProduct::STOCK_STATUS_IN_STOCK;
            }
            else
                $content->stock_status = ContentProduct::STOCK_STATUS_OUT_OF_STOCK;

            if (isset($r['offer']['strikethroughPrice']))
                $content->priceOld = $r['offer']['strikethroughPrice'];

            if (!empty($r['rating']))
            {
                $content->rating = TextHelper::ratingPrepare($r['rating']);
                $content->ratingDecimal = (float) $r['rating'];
            }

            $content->extra = new ExtraDataBolcom();
            if (!empty($r['offer']['deliveryDescription']))
                $content->extra->deliveryDescription = $r['offer']['deliveryDescription'];
            ExtraDataBolcom::fillAttributes($content->extra, $r);

            $data[] = $content;
        }

        return $data;
    }

    public function doRequestItems(array $items)
    {
        $client = $this->getApiClient();

        $options = array();
        $options['country-code'] = $this->config('country');

        foreach ($items as $i => $item)
        {
            if (empty($item['ean']))
                continue;

            $offer = array();

            try
            {
                $offer = $client->offer($item['ean'], $options);
            }
            catch (\Exception $e)
            {
                if ($e->getCode() == 404)
                {
                    $items[$i]['stock_status'] = ContentProduct::STOCK_STATUS_OUT_OF_STOCK;
                    continue;
                }

                continue;
            }

            if (!$offer)
                continue;

            // assign new data
            if (!empty($offer['price']))
            {
                $items[$i]['price'] = $offer['price'];
                $items[$i]['stock_status'] = ContentProduct::STOCK_STATUS_IN_STOCK;
            }
            else
            {
                $items[$i]['price'] = '';
                $items[$i]['stock_status'] = ContentProduct::STOCK_STATUS_OUT_OF_STOCK;
            }

            if (!empty($offer['strikethroughPrice']))
                $items[$i]['priceOld'] = $offer['strikethroughPrice'];
            else
                $items[$i]['priceOld'] = 0;

            if (!empty($offer['url']))
            {
                $items[$i]['orig_url'] = $offer['url'];
                $items[$i]['url'] = $this->createAffUrl($items[$i]['orig_url'], $items[$i]);
            }

            $extra_fields = array('deliveryDescription', 'condition', 'isPreOrder', 'ultimateOrderTime', 'minDeliveryDate', 'maxDeliveryDate', 'releaseDate');
            foreach ($extra_fields as $field)
            {
                if (!empty($offer[$field]))
                    $items[$i][$field] = $offer[$field];
            }

            $refreshEnabled = (bool) apply_filters('cegg_refresh_bol_images', false);
            $randomHit      = mt_rand(1, 10) === 1;

            // update images ~10% of the time or when the filter forces it
            if (!empty($item['ean']) && ($refreshEnabled || $randomHit))
            {
                try
                {
                    $r = $this->getApiClient()->product($item['ean'], array('country-code' => $this->config('country'), 'include-image' => 'true'));
                }
                catch (\Exception $e)
                {
                    continue;
                }

                if (isset($r['image']['url']))
                    $items[$i]['img'] = $r['image']['url'];
            }
        }

        return $items;
    }

    private function getApiClient()
    {
        $api_client = new BolcomJwtApi($this->config('client_id'), $this->config('client_secret'));
        $api_client->setLang($this->config('language'));
        $api_client->setAccessToken($this->getAccessToken());
        return $api_client;
    }

    public function viewDataPrepare($data)
    {
        foreach ($data as $key => $d)
        {
            $data[$key]['url'] = $this->createAffUrl($d['orig_url'], $d);
        }

        return parent::viewDataPrepare($data);
    }

    private function createAffUrl($url, $item)
    {
        // @link: https://affiliate.bol.com/nl/handleiding/handleiding-productfeed#:~:text=De%C2%A0productfeed%20koppelen,dat%20alles%20werkt

        $deeplink = 'https://partner.bol.com/click/click?p=1&t=url&s=' . urlencode($this->config('SiteId')) . '&url={{url_encoded}}&f=TXL';

        // replacement patterns can be applied
        if ($this->config('subId'))
            $deeplink .= '&subid=' . $this->config('subId');

        $deeplink .= '&name=' . urlencode(\apply_filters('cegg_bolcom_link_name_param', 'cegg'));

        return LinkHandler::createAffUrl($url, $deeplink, $item);
    }

    public function renderResults()
    {
        PluginAdmin::render('_metabox_results', array('module_id' => $this->getId()));
    }

    public function renderSearchResults()
    {
        PluginAdmin::render('_metabox_search_results', array('module_id' => $this->getId()));
    }

    public function requestAccessToken()
    {
        $api_client = new BolcomJwtApi($this->config('client_id'), $this->config('client_secret'));

        $response = $api_client->requestAccessToken();

        if (empty($response['access_token']) || empty($response['expires_in']))
        {
            throw new \Exception('Bolcom JWT API: Invalid Response Format.');
        }

        return array($response['access_token'], (int) $response['expires_in']);
    }

    private function getEanFromUrl($url)
    {
        if (!$id = self::parseId($url))
            return '';

        $client = $this->getApiClient();

        try
        {
            $result = $client->converter($id);
        }
        catch (\Exception $e)
        {
            return '';
        }

        if (!empty($result['ean']))
            return $result['ean'];
        else
            return '';
    }

    private static function parseId($input)
    {
        if (is_numeric($input) && strlen(strval($input)) == 16)
            return (int) $input;

        $url = $input;
        if (!filter_var($url, FILTER_VALIDATE_URL))
            return '';

        $url = strtok($url, '?');
        $url = strtok($url, '#');
        $url = trim($url, '/');
        $parts = explode('/', $url);
        $id = end($parts);
        if (is_numeric($id))
            return (int) $id;

        return '';
    }

    private function getCategoryId($url)
    {
        $parsedUrl = parse_url($url);

        if (!$parsedUrl || !isset($parsedUrl['path']))
            return false;

        $path = $parsedUrl['path'];

        // Check if this is a product URL
        if (strpos($path, '/p/') !== false)
            return false;

        if (preg_match("#/l/[^/]+/(\d+)#", $path, $matches))
            return $matches[1];

        return false;
    }

    public static function getSortByNewestParamMap()
    {
        return [
            'sort' => 'RELEASE_DATE',
        ];
    }
}
