<?php

namespace ContentEgg\application\libs\vk;

defined('\ABSPATH') || exit;

use ContentEgg\application\libs\RestClient;

/**
 * VkApi class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'RestClient.php';

class VkApi extends RestClient
{

	const API_URI_BASE = '';


	/**
	 * @var array Response Format Types
	 */
	protected $_responseTypes = array(
		'json',
	);

	/**
	 * Constructor
	 *
	 * @param string API Key
	 * @param string $responseType
	 */
	public function __construct($key = null)
	{
		$this->setUri(self::API_URI_BASE);
		$this->setApiKey($key);
		$this->setResponseType('json');
	}

	public function setApiKey($key)
	{
	}


	public function newsfeedSearch($query, $params)
	{
		return array();
	}
}
