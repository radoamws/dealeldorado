<?php

namespace ContentEgg\application\modules\VkNews;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ParserModule;
use ContentEgg\application\admin\PluginAdmin;

/**
 * VkNewsModule class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class VkNewsModule extends ParserModule
{

	public function info()
	{
		return array(
			'name'          => 'VK News',
		);
	}

	public function isDeprecated()
	{
		return true;
	}

	public function getParserType()
	{
		return self::PARSER_TYPE_CONTENT;
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
