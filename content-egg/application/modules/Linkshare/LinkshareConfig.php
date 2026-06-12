<?php

namespace ContentEgg\application\modules\Linkshare;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModuleConfig;
use ContentEgg\application\Plugin;

/**
 * LinkshareConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class LinkshareConfig extends AffiliateParserModuleConfig
{
	public function options()
	{
		$options = array(
			'sid'                   => array(
				'title'       => 'SID <span class="cegg_required">*</span>',
				'description' => sprintf(__('Enter your SID (your account ID) provided by Rakuten. <a target="_blank" href="%s">Learn more</a>.', 'content-egg'), 'https://ce-docs.keywordrush.com/modules/affiliate/linkshare'),
				'callback'    => array($this, 'render_input'),
				'default'     => '',
				'validator'   => array(
					'trim',
					array(
						'call'    => array('\ContentEgg\application\helpers\FormValidator', 'required'),
						'when'    => 'is_active',
						'message' => sprintf(__('The field "%s" can not be empty.', 'content-egg'), 'SID'),
					),
				),
			),
			'client_id' 	  => array(
				'title'       => 'Client ID <span class="cegg_required">*</span>',
				'description' => sprintf(__('Generate your Client ID and Client Secret by logging into your Rakuten Developers account. <a target="_blank" href="%s">Learn more</a>.', 'content-egg'), 'https://ce-docs.keywordrush.com/modules/affiliate/linkshare'),
				'callback'    => array($this, 'render_input'),
				'default'     => '',
				'validator'   => array(
					'trim',
					array(
						'call'    => array('\ContentEgg\application\helpers\FormValidator', 'required'),
						'when'    => 'is_active',
						'message' => sprintf(__('The field "%s" can not be empty.', 'content-egg'), 'Client ID'),
					),
				),
			),
			'client_secret' 	=> array(
				'title'       => 'Client Secret <span class="cegg_required">*</span>',
				'callback'    => array($this, 'render_password'),
				'default'     => '',
				'validator'   => array(
					'trim',
					array(
						'call'    => array('\ContentEgg\application\helpers\FormValidator', 'required'),
						'when'    => 'is_active',
						'message' => sprintf(__('The field "%s" can not be empty.', 'content-egg'), 'Client Secret'),
					),
				),
			),

			'token'                   => array(
				'title'       => 'Web Services Token' . ' ' . '(deprecated)',
				'description' => __('Linkshare access key. Go to your account in Linkshare and follow "LINKS -> Web Service".', 'content-egg'),
				'callback'    => array($this, 'render_password'),
				'default'     => '',
				'validator'   => array(
					'trim',
					array(
						'call'    => array('\ContentEgg\application\helpers\FormValidator', 'required'),
						'when'    => 'is_active',
						'message' => __('The field "Web Services Token" can not be empty.', 'content-egg'),
					),
				),
				'section'     => 'default',
			),
			'entries_per_page'        => array(
				'title'       => __('Results', 'content-egg'),
				'description' => __('Specify the number of results to display for a single search query.', 'content-egg'),
				'callback'    => array($this, 'render_input'),
				'default'     => 10,
				'validator'   => array(
					'trim',
					'absint',
				),
				'section'     => 'default',
			),
			'entries_per_page_update' => array(
				'title'       => __('Results for Updates and Autoblogging', 'content-egg'),
				'description' => __('Specify the number of results for automatic updates and autoblogging.', 'content-egg'),
				'callback'    => array($this, 'render_input'),
				'default'     => 6,
				'validator'   => array(
					'trim',
					'absint',
				),
				'section'     => 'default',
			),
			'mid'                     => array(
				'title'       => 'Advertiser ID',
				'description' => __('Limit search results by Advertiser ID. Only results from the specified advertiser will be shown.', 'content-egg'),
				'callback'    => array($this, 'render_input'),
				'default'     => '',
				'validator'   => array(
					'trim',
				),
				'section'     => 'default',
			),
			'search_logic'            => array(
				'title'            => __('Searching logic', 'content-egg'),
				'description'      => '',
				'callback'         => array($this, 'render_dropdown'),
				'dropdown_options' => array(
					'AND'   => __('Match all words (AND)', 'content-egg'),
					'OR'    => __('Match any word (OR)', 'content-egg'),
					'EXACT' => __('Exact match only (EXACT)', 'content-egg'),
				),
				'default'          => 'AND',
				'section'          => 'default',
			),
			'sort'                    => array(
				'title'            => __('Sorting', 'content-egg'),
				'description'      => '',
				'callback'         => array($this, 'render_dropdown'),
				'dropdown_options' => array(
					''             => __('Default', 'content-egg'),
					'retailprice'  => 'Price',
					'productname'  => 'Product Name',
					'categoryname' => 'Primary Category',
					'mid'          => 'Merchant ID',
				),
				'default'          => '',
				'section'          => 'default',
			),
			'sorttype'                => array(
				'title'            => __('Sorting order', 'content-egg'),
				'description'      => '',
				'callback'         => array($this, 'render_dropdown'),
				'dropdown_options' => array(
					'asc' => 'Ascending',
					'dsc' => 'Descending',
				),
				'default'          => 'asc',
				'section'          => 'default',
			),
			'cat'                     => array(
				'title'       => __('Category ', 'content-egg'),
				'description' => __('Limit search by category. Each partner has own categories', 'content-egg'),
				'callback'    => array($this, 'render_input'),
				'default'     => '',
				'validator'   => array(
					'trim',
				),
				'section'     => 'default',
			),
			'filter_duplicate'        => array(
				'title'       => __('Filter duplicates', 'content-egg'),
				'description' => __('Filter similar entries', 'content-egg'),
				'callback'    => array($this, 'render_checkbox'),
				'default'     => true,
			),
			'save_img'                => array(
				'title'       => __('Save images', 'content-egg'),
				'description' => __('Save images on server', 'content-egg'),
				'callback'    => array($this, 'render_checkbox'),
				'default'     => false,
				'section'     => 'default',
			),
			'description_size'        => array(
				'title'       => __('Trim description', 'content-egg'),
				'description' => __('Maximum description length in characters. Set to 0 to disable trimming.', 'content-egg'),
				'callback'    => array($this, 'render_input'),
				'default'     => '300',
				'validator'   => array(
					'trim',
					'absint',
				),
				'section'     => 'default',
			),
			'stock_status' => array(
				'title'            => __('Stock Status', 'content-egg'),
				'description'      => __('Select the stock status to use when a product is not found.', 'content-egg'),
				'callback'         => array($this, 'render_dropdown'),
				'dropdown_options' => array(
					'unknown'      => __('Unknown', 'content-egg'),
					'out_of_stock' => __('Out of Stock', 'content-egg'),
				),
				'default'          => 'unknown',
			),

		);

		$parent                             = parent::options();
		$parent['ttl_items']['default']     = 0;
		$parent['ttl_items']['description'] = __('Time interval in seconds for updating prices. Set to 0 to disable updates.', 'content-egg')
			. ' ' . __('This is an experimental feature of the module.', 'content-egg');

		$options = array_merge($parent, $options);
		return self::moveRequiredUp($options);
	}

	public function deleteToken()
	{
		$id = 'Linkshare';
		$transient_name = Plugin::slug() . '-' . $id . '-access_token';
		\delete_transient($transient_name);
		return true;
	}
}
