<?php

namespace ContentEgg\application\modules\Kieskeurignl;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataKieskeurignl class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 */
class ExtraDataKieskeurignl extends ExtraData
{
	public $productid;
	public $link;
	public $deliveryText;
	public $deliveryCode;
	public $textAd;

	public $productgroupcode;
	public $type;
	public $type_extra;
	public $metakeywords;
	public $pricecount;
	public $shopcount;
	public $pricemin;
	public $pricemax;
	public $cpc_rate;
	public $pricedrop;
	public $blackfriday;
	public $lowest_ever_price;
	public $clicks;
	public $lifecycle;
	public $online;
	public $pricetotal;
	public $pricetotal_pickup;
	public $text_ad;
	public $stock;
	public $delivery_time;
	public $delivery_time_popup_text;
	public $most_clicked;
	public $rating;
	public $certificates = array();
	public $productreviewrate = array();
	public $product_certificates = array();
}
