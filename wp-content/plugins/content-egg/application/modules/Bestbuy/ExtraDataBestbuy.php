<?php

namespace ContentEgg\application\modules\Bestbuy;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataBestbuy class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 */
class ExtraDataBestbuy extends ExtraData
{
	public $type;
	public $startDate;
	public $lowPriceGuarantee;
	public $modelNumber;
	public $condition;
	public $height;
	public $weight;
	public $shippingWeight;
	public $warrantyLabor;
	public $warrantyParts;
	public $includedItemList = array();
	public $keySpecs = array();
}
