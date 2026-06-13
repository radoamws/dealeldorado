<?php

namespace ContentEgg\application\modules\AmazonNoApi;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataAmazonNoApi class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class ExtraDataAmazonNoApi extends ExtraData
{

	public $ASIN;
	public $locale;
	public $associate_tag;
	public $addToCartUrl;
	public $Rating;
	public $TotalReviews;
	public $IsPrimeEligible;
	public $comments = array();
	public $data = array();
}
