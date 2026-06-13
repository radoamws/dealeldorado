<?php

namespace ContentEgg\application\libs\trovaprezzi;

defined('\ABSPATH') || exit;

use ContentEgg\application\libs\RestClient;

/**
 * TrovaprezziApi class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 * @link: https://affiliate.bol.com/nl/api-documentatie
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'RestClient.php';

class TrovaprezziApi extends RestClient
{
    const API_URI_BASE = 'https://feeds.trovaprezzi.it';

    protected $partnerId;
    protected $_responseTypes = array(
        'json',
    );

    public function __construct($partnerId)
    {
        $this->partnerId = $partnerId;
        $this->setUri(self::API_URI_BASE);
        $this->setResponseType('json');
    }

    public function search($keyword, array $options)
    {
        $slug = urlencode($keyword);
        $response = $this->restGet('/' . $slug . '.aspx', $options);

        return $this->_decodeResponse($response);
    }

    public function searchEan($ean, array $options)
    {
        $options['eanCode'] = $ean;
        $response = $this->restGet('/.aspx', $options);
        return $this->_decodeResponse($response);
    }

    public function restGet($path, array $query = null)
    {

        $path = '/' . $this->partnerId . $path;
        $query['format'] = 'json';

        return parent::restGet($path, $query);
    }
}
