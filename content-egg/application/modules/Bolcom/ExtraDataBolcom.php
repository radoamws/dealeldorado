<?php

namespace ContentEgg\application\modules\Bolcom;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataBolcom class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 */
class ExtraDataBolcom extends ExtraData
{

	public $gpc;
	public $subtitle;
	public $summary;
	public $media = array();

	public $deliveryDescription;

	// offer method only
	public $condition;
	public $isPreOrder;
	public $ultimateOrderTime;
	public $minDeliveryDate;
	public $maxDeliveryDate;
	public $releaseDate;
}
