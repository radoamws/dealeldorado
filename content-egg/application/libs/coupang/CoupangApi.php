<?php

namespace ContentEgg\application\libs\coupang;

defined('\ABSPATH') || exit;

use ContentEgg\application\libs\RestClient;

/**
 * CoupangApi class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 * @link: https://partners.coupang.com/#help/open-api
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'RestClient.php';

class CoupangApi extends RestClient
{
    const API_URI_BASE = 'https://api-gateway.coupang.com/v2/providers/affiliate_open_api/apis/openapi';

    private $access_key;
    private $secret_key;

    protected $_responseTypes = array(
        'json',
    );

    public function __construct($access_key, $secret_key)
    {
        $this->access_key = $access_key;
        $this->secret_key = $secret_key;
        $this->setResponseType('json');
        $this->setUri(self::API_URI_BASE);
    }

    public function search($keyword, array $options)
    {
        $options['keyword'] = $keyword;
        $response = $this->restGet('/products/search', $options);

        return $this->_decodeResponse($response);
    }

    /**
     * @link: https://partners.coupang.com/#help/open-api
     */
    public function restGet($path, array $query = null)
    {
        // Timestamp in UTC
        $datetime = gmdate('ymd\THis\Z');
        $method   = 'GET';

        // Coupang wants the full path (domain-stripped) in the signature:
        // e.g. "/v2/providers/affiliate_open_api/apis/openapi" + "/products/search"
        $basePath = rtrim(parse_url(self::API_URI_BASE, PHP_URL_PATH), '/');
        $fullPath = $basePath . $path;

        $queryString = $query ? http_build_query($query) : '';

        $message   = $datetime . $method . $fullPath . $queryString;
        $signature = hash_hmac('sha256', $message, $this->secret_key);

        $authorization = 'CEA algorithm=HmacSHA256, access-key=' . $this->access_key .
            ', signed-date=' . $datetime .
            ', signature=' . $signature;

        $this->addCustomHeaders(array(
            'Authorization' => $authorization,
            'Content-Type'  => 'application/json;charset=UTF-8',
        ));

        return parent::restGet($path, $query);
    }

    protected function myErrorHandler($response)
    {
        $response_code = (int) \wp_remote_retrieve_response_code($response);
        $raw_data = \wp_remote_retrieve_body($response);

        if ($response_code !== 200)
        {
            $data = $this->_decodeResponse($raw_data, $response_code);

            if ($data && isset($data['message']))
            {
                $mess = $data['message'];
                throw new \Exception(esc_html($mess));
            }
        }

        if (strpos($raw_data, 'Sorry! Access denied') !== false)
        {
            throw new \Exception('Coupang API server is not accessible from your IP address.', 403);
        }

        return parent::myErrorHandler($response);
    }
}
