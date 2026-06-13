<?php

namespace ContentEgg\application\modules\Ozon;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModule;
use ContentEgg\application\admin\PluginAdmin;

/**
 * OzonModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class OzonModule extends AffiliateParserModule
{

	public function info()
	{
		return array(
			'name'        => 'Ozon',
		);
	}

	public function getParserType()
	{
		return self::PARSER_TYPE_PRODUCT;
	}

	public function defaultTemplateName()
	{
		return 'item';
	}

	public function isItemsUpdateAvailable()
	{
		return false;
	}

	public function isDeprecated()
	{
		return true;
	}

	public function doRequest($keyword, $query_params = array(), $is_autoupdate = false)
	{
		return array();
	}

	public function doRequestItems(array $items)
	{
		return array();
	}

	public function renderResults()
	{
		PluginAdmin::render('_metabox_results', array('module_id' => $this->getId()));
	}

	public function renderSearchResults()
	{
		PluginAdmin::render('_metabox_search_results', array('module_id' => $this->getId()));
	}
}
