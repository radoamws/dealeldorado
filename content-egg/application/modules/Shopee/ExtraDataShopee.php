<?php

namespace ContentEgg\application\modules\Shopee;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataShopee class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 */
class ExtraDataShopee extends ExtraData
{
	public $shopId;
	public $locale;
	public $commissionRate;
	public $appExistRate;
	public $appNewRate;
	public $webExistRate;
	public $webNewRate;
	public $commission;
	public $sales;
	public $shopName;
	public $offerLink;
	public $periodStartTime;
	public $periodEndTime;
	public $productLink;
}
