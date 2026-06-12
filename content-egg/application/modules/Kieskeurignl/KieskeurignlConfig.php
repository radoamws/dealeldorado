<?php

namespace ContentEgg\application\modules\Kieskeurignl;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModuleConfig;

/**
 * KieskeurignlConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class KieskeurignlConfig extends AffiliateParserModuleConfig
{
    public function options()
    {
        $options = array(
            'token' => array(
                'title' => 'API key <span class="cegg_required">*</span>',
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => sprintf(__('The field "%s" can not be empty.', 'content-egg'), 'API key'),
                    ),
                ),
            ),
            'affiliate_id' => array(
                'title' => 'Affiliate ID <span class="cegg_required">*</span>',
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => sprintf(__('The field "%s" can not be empty.', 'content-egg'), 'Affiliate ID'),
                    ),
                ),
            ),
            'entries_per_page' => array(
                'title' => __('Results', 'content-egg'),
                'description' => __('Specify the number of results to display for a single search query.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => 10,
                'validator' => array(
                    'trim',
                    'absint',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'less_than_equal_to'),
                        'arg' => 100,
                        'message' => sprintf(__('The field "%s" can not be more than %d.', 'content-egg'), 'Results', 100),
                    ),
                ),
            ),
            'entries_per_page_update' => array(
                'title' => __('Results for updates', 'content-egg'),
                'description' => __('Specify the number of results for automatic updates and autoblogging.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => 10,
                'validator' => array(
                    'trim',
                    'absint',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'less_than_equal_to'),
                        'arg' => 100,
                        'message' => sprintf(__('The field "%s" can not be more than %d.', 'content-egg'), 'Results for updates', 100),
                    ),
                ),
            ),

            'exclude_domains' => array(
                'title' => __('Exclude domains', 'content-egg'),
                'description' => __('List of domains separated by commas that should be excluded from the search results.', 'content-egg') . ' ' . sprintf(__('For example: %s', 'content-egg'), 'amazon.nl, bol.com'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',

                ),
            ),
            'save_img' => array(
                'title' => __('Save images', 'content-egg'),
                'description' => __('Save images to local server', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
                'section' => 'default',
            ),
        );

        $parent = parent::options();
        $parent['ttl']['default'] = 432000;
        $parent['ttl']['ttl_items'] = 259200;
        $options = array_merge($parent, $options);

        return self::moveRequiredUp($options);
    }
}
