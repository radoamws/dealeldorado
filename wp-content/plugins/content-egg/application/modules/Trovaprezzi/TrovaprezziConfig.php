<?php

namespace ContentEgg\application\modules\Trovaprezzi;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModuleConfig;

/**
 * TrovaprezziConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class TrovaprezziConfig extends AffiliateParserModuleConfig
{
    public function options()
    {
        $options = array(
            'partner_id' => array(
                'title' => 'Partner ID <span class="cegg_required">*</span>',
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => sprintf(__('The field "%s" can not be empty.', 'content-egg'), 'Partner ID'),
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
                        'arg' => 10,
                        'message' => sprintf(__('The field "%s" can not be more than %d.', 'content-egg'), 'Results', 10),
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
                        'arg' => 10,
                        'message' => sprintf(__('The field "%s" can not be more than %d.', 'content-egg'), 'Results for updates', 10),
                    ),
                ),
            ),
            'sort' => array(
                'title' => __('Sorting', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    'popularity' => __('Popularity', 'content-egg'),
                    'price' => __('Price', 'content-egg'),
                    'totalPrice' => __('Total price', 'content-egg'),
                ),
                'default' => 'popularity',
            ),
            'category_id'                  => array(
                'title'       => __('Category ID', 'content-egg'),
                'description' => sprintf(__('Specify one or more <a target="_blank" href="%s">category IDs</a> separated by comma.', 'content-egg'), 'https://quickshop.shoppydoo.it/categories.aspx'),
                'callback'    => array($this, 'render_input'),
                'default'     => '',
                'validator'   => array(
                    'trim',
                ),
            ),
            'min_price' => array(
                'title' => __('Minimum price', 'content-egg'),
                'description' => __('Example, 10.98', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                ),
                'metaboxInit' => true,
            ),
            'max_price' => array(
                'title' => __('Maximum price', 'content-egg'),
                'description' => __('Example, 300.50', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                ),
                'metaboxInit' => true,
            ),
            'one_offer' => array(
                'title' => __('One offer for merchant', 'content-egg'),
                'description' => __('Obtain just the first offer of each merchant', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
                'section' => 'default',
            ),
            'description_size'        => array(
                'title'       => __('Trim description', 'content-egg'),
                'description' => __('Description size in characters (0 - do not trim)', 'content-egg'),
                'callback'    => array($this, 'render_input'),
                'default'     => '300',
                'validator'   => array(
                    'trim',
                    'absint',
                ),
                'section'     => 'default',
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

        /**
         * Note: tracking links include a token in order to ensure that offers are updated as much as possible.
         * This token expires in 12 hours!  Therefore   you need to set your script to update your feed at least
         * once each 11 hours, in order to guarantee the correct click tracking!
         */
        $parent['ttl']['default'] = 39600;
        $parent['ttl']['description'] .= '<br />Note: Tracking links include a token in order to ensure that offers are updated as much as possible. This token expires in 12 hours! Therefore you need to set your script to update your feed at least once each 11 hours, in order to guarantee the correct click tracking.';

        $parent['update_mode']['default'] = 'visit_cron';

        $options = array_merge($parent, $options);

        return self::moveRequiredUp($options);
    }
}
