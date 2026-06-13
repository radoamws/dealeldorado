<?php

namespace ContentEgg\application\modules\Bestbuy;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModuleConfig;

/**
 * BestbuyConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class BestbuyConfig extends AffiliateParserModuleConfig
{

    public function options()
    {
        $options = array(
            'api_key' => array(
                'title' => 'API Key <span class="cegg_required">*</span>',
                'description' => sprintf(__('You can find your API Key <a target="_blank" href="%s">here</a>.', 'content-egg'), 'https://developer.bestbuy.com/login'),
                'callback' => array($this, 'render_password'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => sprintf(__('The field "%s" can not be empty.', 'content-egg'), 'API Key'),
                    ),
                ),
            ),
            'ipid' => array(
                'title' => 'Impact Partner ID <span class="cegg_required">*</span>',
                'description' => __('As an affiliate, you must add the Impact Partner ID to your queries to receive credit for a customer purchase.', 'content-egg') .
                    ' ' . sprintf(__('Please read more <a target="_blank" href="%s">here</a>.', 'content-egg'), 'https://developer.bestbuy.com/affiliate-program'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => sprintf(__('The field "%s" can not be empty.', 'content-egg'), 'Impact Partner ID'),
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
                'default' => 6,
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
            'description_size' => array(
                'title' => __('Truncate description', 'content-egg'),
                'description' => __('Description size in characters (0 - do not truncate)', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '0',
                'validator' => array(
                    'trim',
                    'absint',
                ),
                'section' => 'default',
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
        $parent['ttl_items']['default'] = 432000; // Bestbuy response links will expire after seven days
        $options = array_merge($parent, $options);

        return self::moveRequiredUp($options);
    }
}
