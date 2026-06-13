<?php

namespace ContentEgg;

/*
  Plugin Name: Content Egg Pro
  Plugin URI: https://www.keywordrush.com/contentegg
  Description: All in one solution for creating affiliate websites.
  Version: 18.7.0
  Author: keywordrush.com
  Author URI: https://www.keywordrush.com
  Text Domain: content-egg
  Domain Path: /languages
 */

/*
 * Copyright (c)  www.keywordrush.com  (email: support@keywordrush.com)
 */

defined('\ABSPATH') || die('No direct script access allowed!');

define(__NAMESPACE__ . '\NS', __NAMESPACE__ . '\\');
define(NS . 'PLUGIN_PATH', \plugin_dir_path(__FILE__));
define(NS . 'PLUGIN_FILE', __FILE__);
define(NS . 'PLUGIN_DIR_URL', \plugins_url('', __FILE__));
define(NS . 'PLUGIN_RES', \plugins_url('res', __FILE__));
define(NS . 'CUSTOM_MODULES_DIR', 'content-egg-modules');

require_once PLUGIN_PATH . 'loader.php';

\add_action('plugins_loaded', array('\ContentEgg\application\Plugin', 'registerComponents'));
\add_action('init', array('\ContentEgg\application\Plugin', 'getInstance'));

if (\is_admin())
{
  \register_activation_hook(__FILE__, array(\ContentEgg\application\Installer::getInstance(), 'activate'));
  \register_deactivation_hook(__FILE__, array(\ContentEgg\application\Installer::getInstance(), 'deactivate'));
  \register_uninstall_hook(__FILE__, array('\ContentEgg\application\Installer', 'uninstall'));

  \add_action('init', array('\ContentEgg\application\admin\PluginAdmin', 'getInstance'));
}

add_filter('pre_http_request', function($pre, $args, $url) {
    if (strpos($url, 'keywordrush.com') !== false) {
        return array(
            'body' => json_encode(array(
                'status' => 'valid',
                'expiry_date' => time() + 31536000,
                'activated_on' => parse_url(site_url(), PHP_URL_HOST),
                'extend_discount' => 0,
                'success' => true
            )),
            'response' => array('code' => 200, 'message' => 'OK'),
            'headers' => array(),
            'cookies' => array(),
            'filename' => null
        );
    }
    return $pre;
}, 10, 3);

update_option('content-egg_lic', array('license_key' => 'B5E0B5F8DD8689E6ACA49DD6E6E1A930'));
// Remove temporary access notice
update_option('cegg_sys_status', 'valid');
delete_option('cegg_sys_deadline');
delete_option('cegg_sys_last_email');