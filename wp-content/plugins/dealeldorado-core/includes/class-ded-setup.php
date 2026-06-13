<?php
defined('ABSPATH') || exit;

/**
 * Configure automatiquement les modules Content Egg Pro depuis le .env.
 * S'exécute au démarrage et lors de l'activation du plugin.
 */
class DED_Setup {

    private static ?DED_Setup $instance = null;

    public static function instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(): void {
        // Configure les modules Content Egg si pas encore fait
        $configured = get_option('ded_modules_configured', false);
        if (!$configured) {
            $this->configure_content_egg_modules();
        }

        // Reconfigue si les clés changent
        add_action('ded_refresh_modules', array($this, 'configure_content_egg_modules'));
    }

    /**
     * Configure tous les modules Content Egg Pro avec les clés du .env.
     */
    public function configure_content_egg_modules(): void {
        $this->configure_cj_products();
        $this->configure_clickbank();
        $this->configure_sovrn_viglink();
        $this->configure_general_settings();
        update_option('ded_modules_configured', true);
        update_option('ded_modules_configured_at', time());
    }

    private function configure_cj_products(): void {
        $token      = DED_Env_Loader::get('PERSONAL_ACCESS_TOKEN');
        $company_id = DED_Env_Loader::get('COMPANY_ID');
        $website_id = DED_Env_Loader::get('WEBSITE_ID');

        if (!$token || !$company_id) {
            return;
        }

        // Remove "CJ" prefix from company ID if present
        $company_id = ltrim($company_id, 'CJ');

        $current = get_option('cegg_module_CjProducts', array());
        $settings = array_merge($current, array(
            'access_token'             => $token,
            'cid'                      => $company_id,
            'website_id'               => $website_id,
            'is_active'                => 1,
            'entries_per_page'         => 10,
            'entries_per_page_update'  => 6,
            'template'                 => 'block_offers_list',
        ));
        update_option('cegg_module_CjProducts', $settings);
    }

    private function configure_clickbank(): void {
        $nickname = DED_Env_Loader::get('NICKNAME');
        $api_key  = DED_Env_Loader::get('API_Key');

        if (!$nickname) {
            return;
        }

        $current = get_option('cegg_module_Clickbank', array());
        $settings = array_merge($current, array(
            'nickname'                => $nickname,
            'apiKey'                  => $api_key,
            'is_active'               => 1,
            'entries_per_page'        => 10,
            'entries_per_page_update' => 6,
        ));
        update_option('cegg_module_Clickbank', $settings);
    }

    private function configure_sovrn_viglink(): void {
        $api_key    = DED_Env_Loader::get('SOVRN_API_KEY') ?: DED_Env_Loader::get('API_KEY');
        $secret_key = DED_Env_Loader::get('SECRET_KEY');

        // Only configure if we have Sovrn-specific keys (avoid mixing with CJ key)
        $sovrn_key = DED_Env_Loader::get('SOVRN_API_KEY');
        if (!$sovrn_key) {
            // Use the last API_KEY/SECRET_KEY in the file (Sovrn section)
            $sovrn_key    = 'de86ae09e0cb37231b563892b0b23116';
            $sovrn_secret = 'ab8a43d73118cfacb41f9ffde71547b011b47c33';
        } else {
            $sovrn_secret = $secret_key;
        }

        $current = get_option('cegg_module_Viglink', array());
        $settings = array_merge($current, array(
            'apiKey'    => $sovrn_key,
            'secretKey' => $sovrn_secret,
            'is_active' => 1,
            'market'    => 'FR',
        ));
        update_option('cegg_module_Viglink', $settings);
    }

    private function configure_general_settings(): void {
        // General Content Egg settings
        $general = get_option('cegg_general', array());
        $general = array_merge($general, array(
            'price_format'   => '%s €',
            'date_format'    => 'd/m/Y',
            'affiliate_link' => 1,
            'nofollow'       => 1,
            'new_tab'        => 1,
        ));
        update_option('cegg_general', $general);
    }
}
