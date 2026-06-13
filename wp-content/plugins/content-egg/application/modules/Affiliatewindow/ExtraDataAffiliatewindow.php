<?php

namespace ContentEgg\application\modules\Affiliatewindow;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataAffiliatewindow class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class ExtraDataAffiliatewindow extends ExtraData
{

	public $bHotPick;
	public $sSpecification;
	public $sPromotion;
	public $sModel;
	public $sMerchantImageUrl;
	public $sDeliveryTime;
	public $fStorePrice;
	public $fDeliveryCost;
	public $sWarranty;
	public $iMerchantId;
	public $iCategoryId;
	public $iAdult;
}
