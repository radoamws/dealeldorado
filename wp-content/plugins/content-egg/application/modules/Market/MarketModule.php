<?php

namespace ContentEgg\application\modules\Market;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ParserModule;
use ContentEgg\application\admin\PluginAdmin;


/**
 * MarketModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class MarketModule extends ParserModule
{
	public function info()
	{
		return array(
			'name'          => 'Market',
		);
	}

	public function getParserType()
	{
		return self::PARSER_TYPE_CONTENT;
	}

	public function isDeprecated()
	{
		return true;
	}

	public function defaultTemplateName()
	{
		return 'data_item';
	}

	public function doRequest($keyword, $query_params = array(), $is_autoupdate = false)
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
