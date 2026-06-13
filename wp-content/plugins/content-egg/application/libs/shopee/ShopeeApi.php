<?php

namespace ContentEgg\application\libs\shopee;

defined('\ABSPATH') || exit;

use ContentEgg\application\libs\RestClient;

/**
 * ShopeeApi class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'RestClient.php';

class ShopeeApi extends RestClient
{
    private $appId;
    private $apiKey;
    private $locale;

    protected $_responseTypes = array(
        'json',
    );

    public function __construct($appId, $apiKey, $locale = 'vn')
    {
        $this->appId = $appId;
        $this->apiKey = $apiKey;
        $this->setLocale($locale);
        $this->setResponseType('json');
    }

    public function setLocale($locale)
    {
        $this->locale = $locale;
        $this->setUri(ShopeeLocales::getApiEndpoint($this->locale) . '/graphql');
    }

    public function search($payload)
    {
        $response = $this->restPost('/', $payload);
        return $this->_decodeResponse($response);
    }

    public function restPost($path, $payload = null, $enctype = null, $opts = array())
    {
        $app_id = $this->appId;
        $secret = $this->apiKey;
        $timestamp = time();
        $factor = $app_id . $timestamp . $payload . $secret;

        $signature = hash("sha256", $factor);

        $this->addCustomHeaders(array(
            'Authorization' => 'SHA256 Credential=' . $app_id . ',Timestamp=' . $timestamp . ',Signature=' . $signature,
            'Content-Type' => 'application/json',
        ));

        return parent::restPost($path, $payload);
    }

    protected function myErrorHandler($response)
    {
        $response_code = (int) \wp_remote_retrieve_response_code($response);
        $data = $this->_decodeResponse(\wp_remote_retrieve_body($response));

        if ($response_code != 200 || !$data)
            return parent::myErrorHandler($response);

        if (isset($data['errors'][0]['message']))
        {
            $mess = $data['errors'][0]['message'];
            throw new \Exception(esc_html($mess));
        }

        return parent::myErrorHandler($response);
    }
}
