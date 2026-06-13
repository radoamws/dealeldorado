<?php

namespace ContentEgg\application\modules\Trovaprezzi;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ExtraData;

/**
 * ExtraDataTrovaprezzi class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 *
 */
class ExtraDataTrovaprezzi extends ExtraData
{
	public $merchant;
	public $deliveryCost;
	public $totalCost;
	public $availability;
	public $availabilityDescr;
	public $categoryId;
	public $categoryName;
	public $merchantRating;
}
