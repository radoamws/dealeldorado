<?php

namespace ContentEgg\application\modules\AvantlinkProducts;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataAvantlinkProducts class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 */
class ExtraDataAvantlinkProducts extends ExtraData
{

	public $lngSubCategoryId;
	public $lngCategoryId;
	public $lngDepartmentId;
	public $strProductSKU;
	public $strSubCategoryName;
	public $lngDatafeedId;
	public $lngMerchantId;
	public $intSearchResultScore;
	public $intSearchResultPrecision;
	public $strActionCommission;
}
