<?php

namespace ContentEgg\application\modules\GoogleNews;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataGoogleNews class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class ExtraDataGoogleNews extends ExtraData
{

	public $links = array();
}

class ExtraGoogleNewsLinks
{

	public $link;
	public $source;
	public $title;
}
