<?php defined('ABSPATH') || exit; ?>
<div class="wrap ded-admin-wrap">
<div class="ded-admin-header">
    <img src="<?php echo esc_url(get_template_directory_uri() . '/assets/images/logo.svg'); ?>" height="50" alt="DealElDorado" onerror="this.style.display='none'">
    <div>
        <h1>DealElDorado <span class="ded-version-badge">v<?php echo DED_PLUGIN_VERSION; ?></span></h1>
        <p class="text-muted mb-0">Comparateur de prix intelligent - Tableau de bord</p>
    </div>
</div>

<!-- Status Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="ded-stat-card <?php echo $configured ? 'status-ok' : 'status-warn'; ?>">
            <div class="ded-stat-icon"><i class="fas fa-cog fa-2x"></i></div>
            <div>
                <div class="ded-stat-value"><?php echo $configured ? 'Configuré' : 'Non configuré'; ?></div>
                <div class="ded-stat-label">Statut des modules</div>
                <?php if ($configured_at): ?>
                <div style="font-size:0.7rem;opacity:.6"><?php echo human_time_diff($configured_at); ?> ago</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="ded-stat-card <?php echo $cj_active ? 'status-ok' : 'status-off'; ?>">
            <div class="ded-stat-icon"><i class="fas fa-store fa-2x"></i></div>
            <div>
                <div class="ded-stat-value">CJ Products</div>
                <div class="ded-stat-label"><?php echo $cj_active ? '✅ Actif' : '❌ Inactif'; ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="ded-stat-card <?php echo $cb_active ? 'status-ok' : 'status-off'; ?>">
            <div class="ded-stat-icon"><i class="fas fa-dollar-sign fa-2x"></i></div>
            <div>
                <div class="ded-stat-value">Clickbank</div>
                <div class="ded-stat-label"><?php echo $cb_active ? '✅ Actif' : '❌ Inactif'; ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="ded-stat-card <?php echo $sv_active ? 'status-ok' : 'status-off'; ?>">
            <div class="ded-stat-icon"><i class="fas fa-link fa-2x"></i></div>
            <div>
                <div class="ded-stat-value">Sovrn/VigLink</div>
                <div class="ded-stat-label"><?php echo $sv_active ? '✅ Actif' : '❌ Inactif'; ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="ded-card">
            <h4><i class="fas fa-rocket me-2 text-primary"></i>Actions rapides</h4>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?php echo esc_url(admin_url('post-new.php')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i> Nouvel article produit
                </a>
                <a href="<?php echo esc_url(admin_url('admin.php?page=dealeldorado-api')); ?>" class="btn btn-outline-primary">
                    <i class="fas fa-key me-1"></i> Configurer les APIs
                </a>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline">
                    <?php wp_nonce_field('ded_reconfigure'); ?>
                    <input type="hidden" name="action" value="ded_reconfigure_modules">
                    <button type="submit" class="btn btn-outline-warning">
                        <i class="fas fa-sync me-1"></i> Reconfigurer depuis .env
                    </button>
                </form>
                <?php if (class_exists('\ContentEgg\application\Plugin')): ?>
                <a href="<?php echo esc_url(admin_url('admin.php?page=content-egg')); ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-egg me-1"></i> Content Egg Pro
                </a>
                <?php endif; ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-outline-secondary" target="_blank">
                    <i class="fas fa-eye me-1"></i> Voir le site
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="ded-card">
            <h4><i class="fas fa-bell me-2 text-warning"></i>Alertes prix</h4>
            <?php $alerts = get_option('ded_price_alerts', array()); ?>
            <div class="ded-stat-value"><?php echo count($alerts); ?></div>
            <div class="ded-stat-label">alertes enregistrées</div>
        </div>
    </div>
</div>

<!-- Usage Guide Summary -->
<div class="ded-card">
    <h4><i class="fas fa-book me-2 text-info"></i>Guide d'utilisation rapide</h4>
    <div class="row g-3">
        <div class="col-md-6">
            <h6 class="fw-bold">1. Créer un article produit</h6>
            <p class="small text-muted">Créez un article WordPress normalement. Le bloc Content Egg Pro dans l'éditeur vous permet d'importer des produits depuis CJ, Clickbank, Sovrn.</p>
            <h6 class="fw-bold">2. Rechercher des produits</h6>
            <p class="small text-muted">Dans la métabox Content Egg, entrez un mot-clé (ex: "iPhone 15") et cliquez "Update" pour importer les offres affiliées.</p>
        </div>
        <div class="col-md-6">
            <h6 class="fw-bold">Shortcodes disponibles</h6>
            <code class="d-block mb-1">[ded_compare keyword="iPhone 15"]</code>
            <code class="d-block mb-1">[ded_search_bar]</code>
            <code class="d-block mb-1">[ded_top_deals count="6"]</code>
            <code class="d-block mb-1">[ded_price_box merchant="Amazon" price="499" url="..."]</code>
            <code class="d-block">[ded_affiliate_disclaimer]</code>
        </div>
    </div>
    <a href="<?php echo esc_url(admin_url('admin.php?page=dealeldorado-guide')); ?>" class="btn btn-outline-info btn-sm mt-3">
        Lire le guide complet <i class="fas fa-arrow-right ms-1"></i>
    </a>
</div>
</div>
