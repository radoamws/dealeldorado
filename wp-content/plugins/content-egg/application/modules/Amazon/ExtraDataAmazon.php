<?php

namespace ContentEgg\application\modules\Amazon;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataAmazon class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class ExtraDataAmazon extends ExtraData
{

	public $locale;
	public $associate_tag;
	public $primaryImages = array();
	public $imageSet = array();
	public $IsAmazonFulfilled;
	public $IsPrimeEligible;
	public $IsEligibleForSuperSaverShipping;
	public $IsBuyBoxWinner;
	public $IsPrimeExclusive;
	public $IsPrimePantry;
	public $Condition;
	public $MerchantName;
	public $PricePerUnit;
	public $DisplayAmount;
	public $ViolatesMAP;
	public $addToCartUrl;
	public $ASIN;
	public $itemAttributes = array();
	public $lowestNewPrice;
	public $lowestUsedPrice;
	public $totalNew;
	public $totalUsed;
	public $primePrice;
	public $UPCs = array();
	public $EANs = array();
}
