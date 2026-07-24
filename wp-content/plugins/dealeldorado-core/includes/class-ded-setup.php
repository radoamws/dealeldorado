<?php
defined('ABSPATH') || exit;

/**
 * Configure automatiquement les modules Content Egg Pro depuis le .env.
 *
 * IMPORTANT: Content Egg stocke les options sous la clé `content-egg_{ModuleId}`
 * (plugin slug + underscore + module id). La config générale est `contentegg_options`.
 */
class DED_Setup {

    // Préfixe exact utilisé par Content Egg Pro
    const CE_OPTION_PREFIX = 'content-egg_';
    const CE_GENERAL_OPTION = 'contentegg_options';

    private static ?DED_Setup $instance = null;

    public static function instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(): void {
        $configured = get_option('ded_modules_configured', false);
        if (!$configured) {
            $this->configure_content_egg_modules();
        }
        add_action('ded_refresh_modules', array($this, 'configure_content_egg_modules'));
    }

    /**
     * Retourne la clé d'option WordPress pour un module Content Egg.
     * Format : content-egg_{ModuleId}
     */
    public static function ce_option(string $module_id): string {
        return self::CE_OPTION_PREFIX . $module_id;
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

        // Supprimer le préfixe "CJ" si présent (Content Egg attend un entier)
        $company_id_clean = ltrim($company_id, 'CJ');

        $key     = self::ce_option('CjProducts');
        $current = get_option($key, array());

        $settings = array_merge($current, array(
            'access_token'            => $token,
            'cid'                     => $company_id_clean,
            'website_id'              => $website_id,
            'is_active'               => 1,
            'entries_per_page'        => 10,
            'entries_per_page_update' => 6,
            'embed_at'                => 'shortcode',
            'priority'                => 10,
            'template'                => 'block_offers_list',
        ));
        update_option($key, $settings);
    }

    private function configure_clickbank(): void {
        $nickname = DED_Env_Loader::get('NICKNAME');
        $api_key  = DED_Env_Loader::get('API_Key');

        if (!$nickname) {
            return;
        }

        $key     = self::ce_option('Clickbank');
        $current = get_option($key, array());

        $settings = array_merge($current, array(
            'nickname'                => $nickname,
            'apiKey'                  => $api_key,
            'is_active'               => 1,
            'entries_per_page'        => 10,
            'entries_per_page_update' => 6,
            'embed_at'                => 'shortcode',
            'priority'                => 20,
        ));
        update_option($key, $settings);
    }

    private function configure_sovrn_viglink(): void {
        // Dans le .env, les clés Sovrn sont dans la section finale (API_KEY + SECRET_KEY)
        // On les lit directement depuis les valeurs connues du .env
        $env_lines  = @file(ABSPATH . '.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: array();
        $sovrn_key  = '';
        $sovrn_sec  = '';
        $in_sovrn   = false;

        foreach ($env_lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, '# Sovrn')) {
                $in_sovrn = true;
            }
            if ($in_sovrn && str_starts_with($line, 'API_KEY=')) {
                $sovrn_key = trim(substr($line, 8));
            }
            if ($in_sovrn && str_starts_with($line, 'SECRET_KEY=')) {
                $sovrn_sec = trim(substr($line, 11));
            }
        }

        // Fallback si la lecture directe échoue : utiliser les valeurs du .env
        if (!$sovrn_key) {
            $sovrn_key = 'de86ae09e0cb37231b563892b0b23116';
            $sovrn_sec = 'ab8a43d73118cfacb41f9ffde71547b011b47c33';
        }

        $key     = self::ce_option('Viglink');
        $current = get_option($key, array());

        $settings = array_merge($current, array(
            'apiKey'    => $sovrn_key,
            'secretKey' => $sovrn_sec,
            'is_active' => 1,
            'market'    => 'FR',
            'embed_at'  => 'shortcode',
            'priority'  => 30,
        ));
        update_option($key, $settings);
    }

    private function configure_general_settings(): void {
        $key     = self::CE_GENERAL_OPTION;
        $current = get_option($key, array());
        // Content Egg initialise l'option avec '' — forcer un tableau
        if (!is_array($current)) {
            $current = array();
        }

        // S'assurer que le type de post 'post' (articles) est activé pour la métabox
        $post_types = $current['post_types'] ?? array();
        if (!is_array($post_types)) {
            $post_types = array();
        }
        if (!in_array('post', $post_types)) {
            $post_types[] = 'post';
        }

        $settings = array_merge($current, array(
            'post_types'     => $post_types,
            'price_format'   => '%s €',
            'date_format'    => 'd/m/Y',
            'affiliate_link' => 1,
            'nofollow'       => 1,
            'new_tab'        => 1,
        ));
        update_option($key, $settings);
    }

    /**
     * Crée 5 articles exemples et déclenche l'import Content Egg pour chacun.
     * Retourne un array avec ['created' => [...], 'errors' => [...]]
     */
    public function create_sample_products(): array {
        $products = array(
            array(
                'title'    => 'iPhone 15 Pro 256Go Titane',
                'keyword'  => 'iPhone 15 Pro 256Go',
                'category' => 'Téléphones',
                'modules'  => array('CjProducts', 'Viglink'),
            ),
            array(
                'title'    => 'Samsung Galaxy S24 Ultra 512Go',
                'keyword'  => 'Samsung Galaxy S24 Ultra',
                'category' => 'Téléphones',
                'modules'  => array('CjProducts', 'Viglink'),
            ),
            array(
                'title'    => 'MacBook Pro 14" M3 - 16Go RAM 512Go SSD',
                'keyword'  => 'MacBook Pro 14 M3',
                'category' => 'Informatique',
                'modules'  => array('CjProducts', 'Viglink'),
            ),
            array(
                'title'    => 'PlayStation 5 Slim - Console de jeu',
                'keyword'  => 'PlayStation 5 Slim',
                'category' => 'Jeux & Consoles',
                'modules'  => array('CjProducts', 'Clickbank'),
            ),
            array(
                'title'    => 'Nike Air Max 270 Homme - Running',
                'keyword'  => 'Nike Air Max 270',
                'category' => 'Sport & Mode',
                'modules'  => array('CjProducts', 'Viglink'),
            ),
        );

        $created = array();
        $errors  = array();

        foreach ($products as $product) {
            // Vérifier si l'article existe déjà
            $existing = get_page_by_title($product['title'], OBJECT, 'post');
            if ($existing) {
                $post_id = $existing->ID;
                $created[] = array('id' => $post_id, 'title' => $product['title'], 'status' => 'exists');
                continue;
            }

            // Créer l'article
            $cat_id  = wp_create_category($product['category']);
            $post_id = wp_insert_post(array(
                'post_title'    => $product['title'],
                'post_status'   => 'publish',
                'post_type'     => 'post',
                'post_category' => array($cat_id),
                'post_content'  => sprintf(
                    '<p>Comparez les meilleurs prix pour <strong>%s</strong>. Retrouvez toutes les offres des marchands partenaires ci-dessous.</p>',
                    esc_html($product['title'])
                ),
            ));

            if (is_wp_error($post_id)) {
                $errors[] = $product['title'] . ': ' . $post_id->get_error_message();
                continue;
            }

            // Sauvegarder le mot-clé Content Egg pour chaque module
            foreach ($product['modules'] as $module_id) {
                // Clé de keyword Content Egg : _cegg_keyword{ModuleId}
                update_post_meta($post_id, '_cegg_keyword' . $module_id, $product['keyword']);
                // Marquer pour auto-update
                update_post_meta($post_id, '_cegg_global_autoupdate_keyword', $product['keyword']);
            }

            // Tenter d'appeler l'API Content Egg si le plugin est actif
            $this->fetch_content_egg_data($post_id, $product['keyword'], $product['modules']);

            $created[] = array(
                'id'     => $post_id,
                'title'  => $product['title'],
                'url'    => get_permalink($post_id),
                'status' => 'created',
            );
        }

        return array('created' => $created, 'errors' => $errors);
    }

    /**
     * Appelle Content Egg pour récupérer les données produit via les modules actifs.
     */
    private function fetch_content_egg_data(int $post_id, string $keyword, array $module_ids): void {
        if (!class_exists('\ContentEgg\application\components\ModuleManager')) {
            return;
        }

        foreach ($module_ids as $module_id) {
            // Vérifier si le module est actif
            $opts = get_option(self::ce_option($module_id), array());
            if (empty($opts['is_active'])) {
                continue;
            }

            try {
                $module = \ContentEgg\application\components\ModuleManager::factory($module_id);
                if (!$module) {
                    continue;
                }

                $data = $module->doRequest($keyword);

                if (!empty($data)) {
                    \ContentEgg\application\components\ContentManager::updateData(
                        $post_id,
                        $module_id,
                        $data
                    );
                    // Marquer la date de mise à jour
                    update_post_meta($post_id, '_cegg_last_bykeyword_update' . $module_id, time());
                }
            } catch (\Exception $e) {
                // Log sans bloquer - l'utilisateur pourra relancer manuellement
                error_log('[DED] Fetch error for ' . $module_id . ' / ' . $keyword . ': ' . $e->getMessage());
            }
        }
    }
}
