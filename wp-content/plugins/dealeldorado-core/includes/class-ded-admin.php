<?php
defined('ABSPATH') || exit;

/**
 * Page d'administration DealElDorado dans le BO WordPress.
 */
class DED_Admin {

    private static ?DED_Admin $instance = null;

    public static function instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(): void {
        if (!is_admin()) {
            return;
        }
        add_action('admin_menu', array($this, 'add_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('admin_post_ded_save_settings', array($this, 'save_settings'));
        add_action('admin_post_ded_reconfigure_modules', array($this, 'reconfigure_modules'));
        add_action('admin_post_ded_create_samples', array($this, 'create_sample_products'));
        add_action('admin_notices', array($this, 'show_notices'));
    }

    public function add_menu(): void {
        add_menu_page(
            'DealElDorado',
            'DealElDorado',
            'manage_options',
            'dealeldorado',
            array($this, 'render_dashboard'),
            'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path fill="currentColor" d="M7,2H17A2,2 0 0,1 19,4V20A2,2 0 0,1 17,22H7A2,2 0 0,1 5,20V4A2,2 0 0,1 7,2M7,4V8H17V4H7M7,10V12H9V10H7M11,10V12H13V10H11M15,10V12H17V10H15M7,14V16H9V14H7M11,14V16H13V14H11M15,14V16H17V14H15M7,18V20H9V18H7M11,18V20H13V18H11M15,18V20H17V18H15Z"/></svg>'),
            30
        );

        add_submenu_page('dealeldorado', 'Tableau de bord', 'Tableau de bord', 'manage_options', 'dealeldorado', array($this, 'render_dashboard'));
        add_submenu_page('dealeldorado', 'Configuration API', 'Configuration API', 'manage_options', 'dealeldorado-api', array($this, 'render_api_settings'));
        add_submenu_page('dealeldorado', 'Modules Affiliés', 'Modules Affiliés', 'manage_options', 'dealeldorado-modules', array($this, 'render_modules'));
        add_submenu_page('dealeldorado', 'Guide d\'utilisation', 'Guide d\'utilisation', 'manage_options', 'dealeldorado-guide', array($this, 'render_guide'));
    }

    public function enqueue_assets(string $hook): void {
        if (!str_contains($hook, 'dealeldorado')) {
            return;
        }
        wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3');
        wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), '6.5.1');
        wp_enqueue_style('ded-admin', DED_PLUGIN_URL . 'assets/css/ded-admin.css', array(), DED_PLUGIN_VERSION);
    }

    public function render_dashboard(): void {
        $configured   = get_option('ded_modules_configured', false);
        $configured_at = get_option('ded_modules_configured_at', 0);
        $cj_active    = !empty(get_option(DED_Setup::ce_option('CjProducts'), array())['is_active']);
        $cb_active    = !empty(get_option(DED_Setup::ce_option('Clickbank'), array())['is_active']);
        $sv_active    = !empty(get_option(DED_Setup::ce_option('Viglink'), array())['is_active']);

        include DED_PLUGIN_PATH . 'templates/admin-dashboard.php';
    }

    public function render_api_settings(): void {
        $cj_settings = get_option(DED_Setup::ce_option('CjProducts'), array());
        $cb_settings = get_option(DED_Setup::ce_option('Clickbank'), array());
        $sv_settings = get_option(DED_Setup::ce_option('Viglink'), array());
        include DED_PLUGIN_PATH . 'templates/admin-api-settings.php';
    }

    public function render_modules(): void {
        include DED_PLUGIN_PATH . 'templates/admin-modules.php';
    }

    public function render_guide(): void {
        include DED_PLUGIN_PATH . 'templates/admin-guide.php';
    }

    public function save_settings(): void {
        check_admin_referer('ded_save_settings');
        if (!current_user_can('manage_options')) {
            wp_die('Accès refusé');
        }

        // CJ Products
        if (isset($_POST['cj_access_token'])) {
            $cj = get_option(DED_Setup::ce_option('CjProducts'), array());
            $cj['access_token'] = sanitize_text_field($_POST['cj_access_token']);
            $cj['cid']          = sanitize_text_field($_POST['cj_company_id']);
            $cj['website_id']   = sanitize_text_field($_POST['cj_website_id']);
            $cj['is_active']    = !empty($_POST['cj_active']) ? 1 : 0;
            update_option(DED_Setup::ce_option('CjProducts'), $cj);
        }

        // Clickbank
        if (isset($_POST['cb_nickname'])) {
            $cb = get_option(DED_Setup::ce_option('Clickbank'), array());
            $cb['nickname']  = sanitize_text_field($_POST['cb_nickname']);
            $cb['apiKey']    = sanitize_text_field($_POST['cb_api_key']);
            $cb['is_active'] = !empty($_POST['cb_active']) ? 1 : 0;
            update_option(DED_Setup::ce_option('Clickbank'), $cb);
        }

        // Sovrn
        if (isset($_POST['sv_api_key'])) {
            $sv = get_option(DED_Setup::ce_option('Viglink'), array());
            $sv['apiKey']    = sanitize_text_field($_POST['sv_api_key']);
            $sv['secretKey'] = sanitize_text_field($_POST['sv_secret_key']);
            $sv['is_active'] = !empty($_POST['sv_active']) ? 1 : 0;
            update_option(DED_Setup::ce_option('Viglink'), $sv);
        }

        update_option('ded_modules_configured', true);
        wp_redirect(add_query_arg(array('page' => 'dealeldorado-api', 'saved' => '1'), admin_url('admin.php')));
        exit;
    }

    public function reconfigure_modules(): void {
        check_admin_referer('ded_reconfigure');
        if (!current_user_can('manage_options')) {
            wp_die('Accès refusé');
        }
        delete_option('ded_modules_configured');
        DED_Env_Loader::load(ABSPATH . '.env');
        DED_Setup::instance()->configure_content_egg_modules();
        wp_redirect(add_query_arg(array('page' => 'dealeldorado', 'reconfigured' => '1'), admin_url('admin.php')));
        exit;
    }

    public function create_sample_products(): void {
        check_admin_referer('ded_create_samples');
        if (!current_user_can('manage_options')) {
            wp_die('Accès refusé');
        }

        $result = DED_Setup::instance()->create_sample_products();

        // Stocker le résultat en transient pour l'afficher
        set_transient('ded_samples_result', $result, 60);

        wp_redirect(add_query_arg(array('page' => 'dealeldorado', 'samples' => '1'), admin_url('admin.php')));
        exit;
    }

    public function show_notices(): void {
        if (!str_contains($_GET['page'] ?? '', 'dealeldorado')) {
            return;
        }
        if (!empty($_GET['saved'])): ?>
            <div class="notice notice-success is-dismissible"><p>✅ Paramètres sauvegardés avec succès !</p></div>
        <?php endif;
        if (!empty($_GET['reconfigured'])): ?>
            <div class="notice notice-success is-dismissible"><p>✅ Modules reconfigurés depuis le fichier .env !</p></div>
        <?php endif;
        if (!empty($_GET['samples'])):
            $result = get_transient('ded_samples_result');
            if ($result):
                $count   = count($result['created']);
                $created = array_filter($result['created'], fn($r) => $r['status'] === 'created');
                $exists  = array_filter($result['created'], fn($r) => $r['status'] === 'exists');
        ?>
            <div class="notice notice-success is-dismissible">
                <p>✅ <strong><?php echo count($created); ?> article(s) créé(s)</strong>
                <?php if ($exists): ?>, <?php echo count($exists); ?> déjà existant(s)<?php endif; ?>.</p>
                <?php foreach ($result['created'] as $p): if ($p['status'] === 'created'): ?>
                <p>&nbsp;&nbsp;→ <a href="<?php echo esc_url($p['url']); ?>" target="_blank">
                    <?php echo esc_html($p['title']); ?></a>
                    <?php if (!empty($p['has_data'])): ?> ✅ données importées<?php else: ?> ⚠️ <em>ouvrir et cliquer "Update" dans Content Egg</em><?php endif; ?>
                </p>
                <?php endif; endforeach; ?>
                <?php if (!empty($result['errors'])): ?>
                <p style="color:red">Erreurs : <?php echo implode(', ', array_map('esc_html', $result['errors'])); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; endif;
    }
}
