<?php

namespace ContentEgg\application\libs\bolcom;

defined('\ABSPATH') || exit;

use ContentEgg\application\libs\RestClient;;

/**
 * BolcomJwtApi class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 * @link: https://affiliate.bol.com/nl/handleiding/handleiding-toegang-api
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'RestClient.php';

class BolcomJwtApi extends RestClient
{

    const API_URI_BASE = 'https://api.bol.com/marketing/catalog/v1';

    protected $lang;
    protected $client_id;
    protected $client_secret;
    protected $access_token;
    protected $_responseTypes = array(
        'json',
    );

    public function __construct($client_id, $client_secret)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->setUri(self::API_URI_BASE);
        $this->setResponseType('json');
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }

    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * Search for products
     * @link: https://api.bol.com/marketing/docs/api-reference/catalog-api-v1.html#tag/Products/operation/searchProducts
     */
    public function search($keyword, array $options)
    {
        $options['search-term'] = $keyword;
        $response = $this->restGet('/products/search', $options);

        return $this->_decodeResponse($response);
    }

    /**
     * Get the best offer for a product
     * @link: https://api.bol.com/marketing/docs/api-reference/catalog-api-v1.html#tag/Products/operation/getProductBestOffer
     */
    public function offer($ean, $options = array())
    {
        $response = $this->restGet('/products/' . urlencode($ean) . '/offers/best', $options);
        return $this->_decodeResponse($response);
    }

    /**
     * Get the best offer for a product
     * @link: https://api.bol.com/marketing/docs/api-reference/catalog-api-v1.html#tag/Products/operation/getProduct
     */
    public function product($ean, $options = array())
    {
        $response = $this->restGet('/products/' . urlencode($ean), $options);
        return $this->_decodeResponse($response);
    }

    /**
     * Get the media for a product
     * @link: https://api.bol.com/marketing/docs/api-reference/catalog-api-v1.html#tag/Products/operation/getProductMedia
     */
    public function media($ean, $options = array())
    {
        $response = $this->restGet('/products/' . urlencode($ean) . '/media', $options);
        return $this->_decodeResponse($response);
    }

    /**
     * Get the categories the product is placed in.
     * @link: https://api.bol.com/marketing/docs/api-reference/catalog-api-v1.html#tag/Products/operation/getProductCategories
     */
    public function categories($ean, $options = array())
    {
        $response = $this->restGet('/products/' . urlencode($ean) . '/categories', $options);
        return $this->_decodeResponse($response);
    }

    /**
     * Converts a given bolProductId to EAN
     * @link: https://api.bol.com/marketing/docs/api-reference/catalog-api-v1.html#tag/Converter
     */
    public function converter($product_id, $options = array())
    {
        $response = $this->restGet('/products/' . urlencode($product_id) . '/to-ean', $options);
        return $this->_decodeResponse($response);
    }

    /**
     * Get the most popular products
     * @link: https://api.bol.com/marketing/docs/api-reference/catalog-api-v1.html#tag/Products/operation/listPopularProducts
     */
    public function popular($category_id, $options = array())
    {
        $options['category-id'] = $category_id;
        $response = $this->restGet('/products/lists/popular', $options);
        return $this->_decodeResponse($response);
    }

    public function requestAccessToken()
    {
        $query = array(
            'grant_type' => 'client_credentials',
        );
        $this->setCustomHeaders(array('Authorization' => 'Basic ' . base64_encode($this->client_id . ":" . $this->client_secret)));
        $response = $this->restPost('https://login.bol.com/token', $query);
        return $this->_decodeResponse($response);
    }

    public function restGet($path, array $query = null)
    {
        $headers = array(
            'Accept-Language' => $this->lang,
            'Authorization' => 'Bearer ' . $this->access_token,
        );

        $this->addCustomHeaders($headers);
        return parent::restGet($path, $query);
    }

    protected function myErrorHandler($response)
    {
        $response_code = (int) \wp_remote_retrieve_response_code($response);
        $data = $this->_decodeResponse(\wp_remote_retrieve_body($response));

        if ($response_code == 404 && isset($data['title']) && $data['title'] == 'No data could be found.')
            return json_encode([]);

        return parent::myErrorHandler($response);
    }
}
