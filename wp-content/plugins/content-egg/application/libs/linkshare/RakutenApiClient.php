<?php

namespace ContentEgg\application\libs\linkshare;

defined('\ABSPATH') || exit;

use ContentEgg\application\libs\RestClient;;

/**
 * RakutenApiClient class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 * @link: https://developers.rakutenadvertising.com/guides/product_search
 * @link: https://developers.rakutenadvertising.com/documentation/en-US/affiliate_apis#/Product%20Search/get_productsearch_1_0
 * @link: https://developers.rakutenadvertising.com/guides/access_tokens
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'RestClient.php';

class RakutenApiClient extends RestClient
{

    const API_URI_BASE = 'https://api.linksynergy.com';

    protected $client_id;
    protected $client_secret;
    protected $sid;
    protected $access_token;

    protected $_responseTypes = array(
        'xml',
    );

    public function __construct($client_id, $client_secret, $sid)
    {
        $this->setResponseType('xml');
        $this->setUri(self::API_URI_BASE);
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->sid = $sid;
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }

    public function requestAccessToken()
    {
        $body = [
            'grant_type' => 'password',
            'scope' => $this->sid,
        ];

        $tokenKey = base64_encode($this->client_id . ':' . $this->client_secret);

        $this->setCustomHeaders([
            'Authorization' => 'Bearer ' . $tokenKey,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);

        $response = $this->restPost('https://api.linksynergy.com/token', $body);

        if (!$response)
        {
            throw new \RuntimeException('No response received from Rakuten token exchange.');
        }

        return json_decode($response, true);
    }

    public function search($keyword, array $params = array(), $search_logic = 'AND')
    {
        switch ($search_logic)
        {
            case 'AND':
                $params['keyword'] = $keyword;
                break;
            case 'OR':
                $params['one'] = $keyword;
                break;
            case 'EXACT':
                $params['exact'] = $keyword;
                break;
            default:
                $params['keyword'] = $keyword;
        }

        $response = $this->restGet('/productsearch/1.0', $params);

        $response = preg_replace('/(<price\scurrency=[\'"]([A-Z]+?)[\'"]>[0-9\.]+?<\/price>)/', '$1<currency>$2</currency>', $response);

        $result = $this->_decodeResponse($response);

        if ($result && isset($result['Errors']['ErrorText']))
        {
            throw new \RuntimeException(
                'Rakuten API Error: ' . esc_html($result['Errors']['ErrorText'])
            );
        }

        return $result;
    }

    public function restGet($path, array $query = null)
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->access_token,
        ];

        $this->addCustomHeaders($headers);

        return parent::restGet($path, $query);
    }
}
