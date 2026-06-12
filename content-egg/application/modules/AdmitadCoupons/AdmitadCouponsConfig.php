<?php

namespace ContentEgg\application\modules\AdmitadCoupons;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModuleConfig;

/**
 * AdmitadCouponsConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class AdmitadCouponsConfig extends AffiliateParserModuleConfig
{

	public function options()
	{
		$options = array(
			'url'                     => array(
				'title'       => __('URL for getting XML-file', 'content-egg') . ' ' . '<span class="cegg_required">*</span>',
				'description' => __('Go to Admitad: Tools -> Coupons -> Export -> Get link.', 'content-egg') .
					'<br>' . __('Don\'t forget to set flag "Only my programs" and other filters.', 'content-egg'),
				'callback'    => array($this, 'render_input'),
				'default'     => '',
				'validator'   => array(
					'trim',
					array(
						'call'    => array('\ContentEgg\application\helpers\FormValidator', 'required'),
						'when'    => 'is_active',
						'message' => __('The "URL for getting XML-file" can not be empty', 'content-egg'),
					),
				),
				'section'     => 'default',
			),
			'entries_per_page'        => array(
				'title'       => __('Results', 'content-egg'),
				'description' => __('Specify the number of results to display for a single search query.', 'content-egg'),
				'callback'    => array($this, 'render_input'),
				'default'     => 30,
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
		);
		$parent  = parent::options();
		unset($parent['featured_image']);

		$options = array_merge($parent, $options);
		return self::moveRequiredUp($options);
	}
}
