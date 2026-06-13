<?php
/**
 * Plugin Name: DealElDorado Core
 * Plugin URI: https://dealeldorado.com
 * Description: Plugin principal du comparateur DealElDorado. Configure automatiquement Content Egg Pro avec les APIs affiliés (CJ, Clickbank, Sovrn), ajoute des shortcodes et widgets de comparaison.
 * Version: 1.0.0
 * Author: DealElDorado Team
 * Author URI: https://dealeldorado.com
 * Text Domain: dealeldorado-core
 * License: GPLv2 or later
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

defined('ABSPATH') || exit;

define('DED_PLUGIN_VERSION', '1.0.0');
define('DED_PLUGIN_FILE', __FILE__);
define('DED_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('DED_PLUGIN_URL', plugin_dir_url(__FILE__));

// Autoload includes
require_once DED_PLUGIN_PATH . 'includes/class-ded-env-loader.php';
require_once DED_PLUGIN_PATH . 'includes/class-ded-setup.php';
require_once DED_PLUGIN_PATH . 'includes/class-ded-admin.php';
require_once DED_PLUGIN_PATH . 'includes/class-ded-shortcodes.php';
require_once DED_PLUGIN_PATH . 'includes/class-ded-ajax.php';

/**
 * Bootstrap the plugin after all plugins are loaded.
 */
add_action('plugins_loaded', function () {
    DED_Env_Loader::load(ABSPATH . '.env');
    DED_Setup::instance()->init();
    DED_Admin::instance()->init();
    DED_Shortcodes::instance()->init();
    DED_Ajax::instance()->init();
});

/**
 * Enqueue frontend assets.
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'ded-core-frontend',
        DED_PLUGIN_URL . 'assets/css/ded-frontend.css',
        array(),
        DED_PLUGIN_VERSION
    );
    wp_enqueue_script(
        'ded-core-frontend',
        DED_PLUGIN_URL . 'assets/js/ded-frontend.js',
        array('jquery'),
        DED_PLUGIN_VERSION,
        true
    );
});

/**
 * Activation hook.
 */
register_activation_hook(__FILE__, function () {
    DED_Env_Loader::load(ABSPATH . '.env');
    DED_Setup::instance()->configure_content_egg_modules();
    flush_rewrite_rules();
});
