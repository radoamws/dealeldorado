<?php

namespace ContentEgg\application\modules\Coupang;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModuleConfig;

/**
 * CoupangConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class CoupangConfig extends AffiliateParserModuleConfig
{
    public function options()
    {
        $options = array(
            'access_key' => array(
                'title' => 'Access Key <span class="cegg_required">*</span>',
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => sprintf(__('The field "%s" can not be empty.', 'content-egg'), 'App ID'),
                    ),
                ),
            ),
            'secret_key' => array(
                'title' => 'Secret Key <span class="cegg_required">*</span>',
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
                'default' => 6,
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
            'stock_status' => array(
                'title'            => __('Stock Status', 'content-egg'),
                'description'      => __('Default stock status to use when a product is not found during a price update attempt.', 'content-egg'),
                'callback'         => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    'unknown'      => __('Unknown', 'content-egg'),
                    'out_of_stock' => __('Out of Stock', 'content-egg'),
                ),
                'default'          => 'out_of_stock',
            ),
            'image_size' => array(
                'title' => __('Image size', 'content-egg'),
                'description' => __('Select the image size to be retrieved from the API.', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    '1024x1024' => '1024x1024',
                    '512x512' => '512x512',
                    '256x256' => '256x256',
                    '128x128' => '128x128',
                ),
                'default' => '512x512',
            ),
            'save_img' => array(
                'title' => __('Save images', 'content-egg'),
                'description' => __('Save images to local server', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
            ),
        );

        $parent = parent::options();
        $parent['update_mode']['default'] = 'cron';
        $parent['ttl_items']['description'] .= '<br><i>' . __(
            'The price update feature for this module is experimental and may not always work as expected.',
            'content-egg'
        ) . '</i>';
        $options = array_merge($parent, $options);

        return self::moveRequiredUp($options);
    }
}
