<?php

namespace ContentEgg\application\modules\CjProducts;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModuleConfig;

/**
 * CjProductsConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class CjProductsConfig extends AffiliateParserModuleConfig
{

    public function options()
    {
        $options = array(
            'access_token' => array(
                'title' => 'Personal access token <span class="cegg_required">*</span>',
                'description' => __('A Personal Access Token is a unique identification string for your account. You can get it <a target="_blank" href="https://developers.cj.com/account/personal-access-tokens">here</a>.', 'content-egg'),
                'callback' => array($this, 'render_password'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => sprintf(__('The field  "%s" can not be empty', 'content-egg'), 'Personal access token'),
                    ),
                ),
            ),
            'cid' => array(
                'title' => 'Company ID <span class="cegg_required">*</span>',
                'description' => __('CID or Company ID is your account number. This number is located on the top right side of your screen next to your name.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => sprintf(__('The field  "%s" can not be empty', 'content-egg'), 'Company ID'),
                    ),
                ),
            ),
            'website_id' => array(
                'title' => 'Website ID <span class="cegg_required">*</span>',
                'description' => __('PID, also known as your Publisher Website ID. To find your PID, navigate to your Account tab -> Site Settings.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => sprintf(__('The field  "%s" can not be empty', 'content-egg'), 'Website ID'),
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
                ),
            ),
            'entries_per_page_update' => array(
                'title' => __('Results for Updates and Autoblogging', 'content-egg'),
                'description' => __('Specify the number of results for automatic updates and autoblogging.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => 6,
                'validator' => array(
                    'trim',
                    'absint',
                ),
            ),
            'advertiser_ids' => array(
                'title' => __('Advertiser IDs', 'content-egg'),
                'description' => __('A comma separated list of Advertiser IDs (CID). Restrict search results based on these IDs.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                ),
            ),
            'partner_status' => array(
                'title' => __('Advertiser status', 'content-egg'),
                'description' => __('Restricts results to advertisers you have (or do not have) an active relationship with.', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    'ALL' => 'All',
                    'JOINED' => 'Joined',
                    'NOT_JOINED' => 'Not joined',
                ),
                'default' => 'JOINED',
            ),
            'currency' => array(
                'title' => __('Currency', 'content-egg'),
                'description' => __('Restrict search results based on the type of currency (for example: EUR).', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                ),
            ),
            'save_img' => array(
                'title' => __('Save images', 'content-egg'),
                'description' => __('Save images on server', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
            ),
            'description_size' => array(
                'title' => __('Trim description', 'content-egg'),
                'description' => __('Description size in characters (0 - do not cut)', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '300',
                'validator' => array(
                    'trim',
                    'absint',
                ),
            ),
        );
        $parent = parent::options();
        $parent['update_mode']['default'] = 'cron';

        $options = array_merge($parent, $options);

        $options['deeplink'] = array(
            'title' => __('Deeplink', 'content-egg'),
            'description' => sprintf(__('You can set the <a target="_blank" href="%s">deeplink</a> to generate custom affiliate URLs, but usually, this option does not need to be configured.', 'content-egg'), 'https://ce-docs.keywordrush.com/modules/deeplink-settings'),
            'callback' => array($this, 'render_input'),
            'default' => '',
            'validator' => array(
                'trim',
            ),
            'section' => 'default',
        );

        return self::moveRequiredUp($options);
    }
}
