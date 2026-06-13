<?php

namespace ContentEgg\application\libs\ozon;

defined('\ABSPATH') || exit;

use ContentEgg\application\libs\RestClient;

/**
 * OzonRest class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *

 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'RestClient.php';

class OzonRest extends RestClient
{

	const API_URI_BASE = '';

	/**
	 * @var array required login and pass for any request
	 */
	protected $_loginParams = array();

	/**
	 * @var array Response Format Types
	 */
	protected $_responseTypes = array(
		'xml',
		'json',
	);

	/**
	 * Constructor
	 *
	 * @param string $responseType
	 */
	public function __construct($login, $pass)
	{
		$this->_loginParams['login']    = $login;
		$this->_loginParams['password'] = $pass;
		$this->setResponseType('json');
		$this->setUri(self::API_URI_BASE);
	}
}
