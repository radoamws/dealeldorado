<?php

namespace ContentEgg\application\modules\Zanox;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataZanox class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class ExtraDataZanox extends ExtraData
{

	public $modified;
	public $deliveryTime;
	public $shippingCosts;
	public $shipping;
	public $merchantCategory;
	public $merchantProductId;
	public $trackingImg;
	public $programId;
}
