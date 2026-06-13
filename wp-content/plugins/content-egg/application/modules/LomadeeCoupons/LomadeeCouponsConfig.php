<?php

namespace ContentEgg\application\modules\LomadeeCoupons;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModuleConfig;

/**
 * LomadeeCouponsCouponsConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class LomadeeCouponsConfig extends AffiliateParserModuleConfig
{

	public function options()
	{
		$options = array(
			'sourceId'                => array(
				'title'       => 'Source ID <span class="cegg_required">*</span>',
				'description' => __('You can find your Source ID <a target="_blank" href="https://www.lomadee.com/dashboard/#/toolkit">here</a>.', 'content-egg'),
				'callback'    => array($this, 'render_input'),
				'default'     => '',
				'validator'   => array(
					'trim',
					array(
						'call'    => array('\ContentEgg\application\helpers\FormValidator', 'required'),
						'when'    => 'is_active',
						'message' => sprintf(__('The field "%s" can not be empty.', 'content-egg'), 'Source ID'),
					),
				),
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
				'title'       => __('Results for updates', 'content-egg'),
				'description' => __('Specify the number of results for automatic updates and autoblogging.', 'content-egg'),
				'callback'    => array($this, 'render_input'),
				'default'     => 6,
				'validator'   => array(
					'trim',
					'absint',
				),
				'section'     => 'default',
			),
			'categoryId'              => array(
				'title'     => 'Category ID',
				'callback'  => array($this, 'render_input'),
				'default'   => '',
				'validator' => array(
					'trim',
				),
			),
			'storeId'                 => array(
				'title'     => __('Store ID', 'content-egg'),
				'callback'  => array($this, 'render_input'),
				'default'   => '',
				'validator' => array(
					'trim',
				),
			),
		);

		$options = array_merge(parent::options(), $options);
		return self::moveRequiredUp($options);
	}
}
