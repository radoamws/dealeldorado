<?php

namespace ContentEgg\application\libs\kieskeurignl;

defined('\ABSPATH') || exit;

use ContentEgg\application\libs\RestClient;;

/**
 * KieskeurignlApi class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 * @link: https://productapiext.reshift.nl/help/index.html
 *
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'RestClient.php';

class KieskeurignlApi extends RestClient
{
    const API_URI_BASE = 'https://productapiext.reshift.nl/v1';

    protected $token;
    protected $_responseTypes = array(
        'json',
    );

    public function __construct($token)
    {
        $this->token = $token;
        $this->setUri(self::API_URI_BASE);
        $this->setResponseType('json');
    }

    public function search($keyword, array $options = array())
    {
        $options['Query'] = $keyword;
        $response = $this->restGet('/Products', $options);
        return $this->_decodeResponse($response);
    }

    public function searchEan($ean, array $options = array())
    {
        $options['Ean'] = $ean;
        $response = $this->restGet('/Products', $options);
        return $this->_decodeResponse($response);
    }

    public function getOffers($id, array $options = array())
    {
        if (isset($options['AffiliateId']) && $options['AffiliateId'] == 'demo')
            unset($options['AffiliateId']);

        $response = $this->restGet('/Products/' . urlencode($id) . '/Prices', $options);
        return $this->_decodeResponse($response);
    }

    public function product($id, array $options = array())
    {
        $response = $this->restGet('/Products/' . urlencode($id), $options);
        return $this->_decodeResponse($response);
    }

    public function restGet($path, array $query = null)
    {
        $headers = array(
            'Accept' => 'application/json',
            'Content-Type' => 'application/json;charset=UTF-8',
            'Authorization' => $this->token,
        );

        $this->addCustomHeaders($headers);
        return parent::restGet($path, $query);
    }
}
