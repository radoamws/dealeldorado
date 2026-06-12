<?php

namespace ContentEgg\application\libs\qwant;

defined('\ABSPATH') || exit;

use ContentEgg\application\libs\RestClient;

/**
 * QwantApi class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'RestClient.php';

class QwantApi extends RestClient
{

    const API_URI_BASE = 'https://api.qwant.com/v3/search';

    protected $_responseTypes = array(
        'json'
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setUri(self::API_URI_BASE);
        $this->setResponseType('json');
    }

    /**
     * Image Search
     */
    public function images($query, $params = array())
    {
        $params['q'] = $query;
        $params['uiv'] = '4';
        $params['t'] = 'images';

        $response = $this->restGet('/images', $params);

        return $this->_decodeResponse($response);
    }

    public function restGet($path, array $query = null)
    {
        $headers = array(
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
        );

        $this->setCustomHeaders($headers);

        return parent::restGet($path, $query);
    }
}
