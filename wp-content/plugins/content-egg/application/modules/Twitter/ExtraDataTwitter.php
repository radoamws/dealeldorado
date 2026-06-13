<?php

namespace ContentEgg\application\modules\Twitter;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataTwitter class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class ExtraDataTwitter extends ExtraData
{

	public $links = array();
}

class ExtraTwitterLinks
{

	public $userId;
	public $statusesCount;
	public $followersCount;
	public $friendsCount;
	public $media;
	public $profileImage;
}
