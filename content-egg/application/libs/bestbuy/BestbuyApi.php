<?php

namespace ContentEgg\application\libs\bestbuy;

defined('\ABSPATH') || exit;

use ContentEgg\application\libs\RestClient;

/**
 * BestbuyApi class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 * @link: https://bestbuyapis.github.io/api-documentation/#overview
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'RestClient.php';

class BestbuyApi extends RestClient
{
    const API_URI_BASE = 'https://api.bestbuy.com/v1';

    protected $apiKey;
    protected $_responseTypes = array(
        'json',
    );

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
        $this->setUri(self::API_URI_BASE);
        $this->setResponseType('json');
    }

    /**
     * @link: https://bestbuyapis.github.io/api-documentation/#keyword-search-function
     */
    public function search($keywords, array $options)
    {
        $keywords = preg_replace('/[^a-zA-Z0-9\s\.]/', '', $keywords);
        $keywords = preg_replace('/\s+/ui', ' ', $keywords);

        $response = $this->restGet('/products(search=' . $keywords . ')', $options);
        return $this->_decodeResponse($response);
    }

    public function searchSku($sku, array $options)
    {
        if (!is_array($sku))
            $sku = array($sku);

        $response = $this->restGet('/products(sku%20in(' . join(',', $sku) . '))', $options);
        return $this->_decodeResponse($response);
    }

    public function searchEan($ean, array $options)
    {
        if (!is_array($ean))
            $ean = array($ean);

        $response = $this->restGet('/products(upc%20in(' . join(',', $ean) . '))', $options);
        return $this->_decodeResponse($response);
    }

    public function restGet($path, array $query = null)
    {
        $query['apiKey'] = $this->apiKey;
        $query['format'] = 'json';
        return parent::restGet($path, $query);
    }
}
