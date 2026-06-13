<?php

namespace ContentEgg\application\libs\yandex;

defined('\ABSPATH') || exit;

use ContentEgg\application\libs\RestClient;

/**
 * MarketContentApi class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'RestClient.php';

class MarketContentApi extends RestClient implements MarketContentInterface
{

	const API_URI_BASE = '';

	private $apiKey;

	/**
	 * @var array Response Format Types
	 */
	protected $_responseTypes = array(
		'json',
		'xml'
	);

	/**
	 * Constructor
	 *
	 * @param string API Key
	 * @param string $responseType
	 */
	public function __construct($key, $rp = 'json')
	{
		$this->setUri(self::API_URI_BASE);
		$this->setApiKey($key);
		$this->setResponseType($rp);
	}

	public function setApiKey($key)
	{
		$this->apiKey = $key;
	}

	public function search($query, $params)
	{
		return array();
	}
}
