<?php

namespace ContentEgg\application\modules\Shopee;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModuleConfig;
use ContentEgg\application\libs\shopee\ShopeeLocales;

/**
 * ShopeeConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class ShopeeConfig extends AffiliateParserModuleConfig
{
    public function options()
    {
        $options = array(
            'app_id' => array(
                'title' => 'App ID <span class="cegg_required">*</span>',
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
            'api_key' => array(
                'title' => 'API Key <span class="cegg_required">*</span>',
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
            'affiliate_id' => array(
                'title' => 'Affiliate ID (deprecated)',
                'description' => '',
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                ),
                'section' => 'default',
            ),
            'locale' => array(
                'title' => __('Locale', 'content-egg') . '<span class="cegg_required">*</span>',
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => self::getLocalesList(),
                'default'  => self::getDefaultLocale(),
                'section'  => 'default',
            ),
            'deeplink' => array(
                'title' => 'Deeplink',
                'description' => sprintf(__('Set <a target="_blank" href="%s">your deeplink</a> if you want to send clicks through one of the affiliate networks with Shopee support.', 'content-egg'), 'https://ce-docs.keywordrush.com/modules/deeplink-settings'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                ),
                'section' => 'default',
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
                        'arg' => 50,
                        'message' => sprintf(__('The field "%s" can not be more than %d.', 'content-egg'), 'Results', 50),
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
                        'arg' => 50,
                        'message' => sprintf(__('The field "%s" can not be more than %d.', 'content-egg'), 'Results for updates', 50),
                    ),
                ),
            ),
            'sort_type' => array(
                'title' => __('Sort type', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    '1.' => __('Sort by relevance', 'content-egg'),
                    '2.' => __('Sort by sold count from high to low', 'content-egg'),
                    '3.' => __('Sort by price from high to low', 'content-egg'),
                    '4.' => __('Sort by price from low to high', 'content-egg'),
                    '5.' => __('Sort by commission rate from high to low', 'content-egg'),
                ),
                'default' => '1.',
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
        $parent['ttl']['default'] = 259200;
        $options = array_merge($parent, $options);

        return self::moveRequiredUp($options);
    }

    public static function getLocalesList()
    {
        $locales = array_keys(self::locales());
        sort($locales);

        return array_combine($locales, array_map('strtoupper', $locales));
    }

    public static function getDefaultLocale()
    {
        return 'vn';
    }

    public static function getDomainByLocale($locale)
    {
        return ShopeeLocales::getDomain($locale);
    }

    public static function locales()
    {
        return ShopeeLocales::locales();
    }
}
