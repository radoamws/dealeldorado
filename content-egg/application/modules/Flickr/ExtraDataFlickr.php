<?php

namespace ContentEgg\application\modules\Flickr;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataFlickr class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class ExtraDataFlickr extends ExtraData
{

	public $tags;
	public $id;
	public $secret;
	public $server;
	public $farm;
}
