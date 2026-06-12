<?php

namespace ContentEgg\application\modules\LomadeeCoupons;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataLomadeeCoupons class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class ExtraDataLomadeeCoupons extends ExtraData
{

	public $category = array();
	public $store = array();
	public $discount;
}
