<?php defined('ABSPATH') || exit; ?>
<div class="wrap ded-admin-wrap">
<div class="ded-admin-header">
    <h1><i class="fas fa-key me-2 text-primary"></i>Configuration des APIs Affiliées</h1>
    <p class="text-muted">Ces paramètres sont lus depuis votre fichier <code>.env</code> mais peuvent être modifiés manuellement ici.</p>
</div>

<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <?php wp_nonce_field('ded_save_settings'); ?>
    <input type="hidden" name="action" value="ded_save_settings">

    <!-- CJ Products -->
    <div class="ded-card mb-4">
        <div class="d-flex align-items-center mb-3 gap-3">
            <div class="ded-module-logo" style="background:#004b8d">CJ</div>
            <div>
                <h4 class="mb-0">Commission Junction (CJ Products)</h4>
                <small class="text-muted">Réseau affilié avec des milliers de marchands</small>
            </div>
            <div class="ms-auto">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="cj_active" name="cj_active" value="1"
                           <?php checked(!empty($cj_settings['is_active'])); ?>>
                    <label class="form-check-label fw-semibold" for="cj_active">Activer</label>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-12">
                <label class="form-label fw-semibold">Personal Access Token <span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="cj_access_token"
                       value="<?php echo esc_attr($cj_settings['access_token'] ?? ''); ?>"
                       placeholder="WV_xxxxxxxxxxxx">
                <div class="form-text">Générer sur <a href="https://developers.cj.com/account/personal-access-tokens" target="_blank">developers.cj.com</a></div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Company ID (CID) <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="cj_company_id"
                       value="<?php echo esc_attr($cj_settings['cid'] ?? ''); ?>"
                       placeholder="4476141">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Website ID (PID)</label>
                <input type="text" class="form-control" name="cj_website_id"
                       value="<?php echo esc_attr($cj_settings['website_id'] ?? ''); ?>"
                       placeholder="2507021532520678700">
            </div>
        </div>
    </div>

    <!-- Clickbank -->
    <div class="ded-card mb-4">
        <div class="d-flex align-items-center mb-3 gap-3">
            <div class="ded-module-logo" style="background:#d13f31">CB</div>
            <div>
                <h4 class="mb-0">Clickbank</h4>
                <small class="text-muted">Marketplace de produits digitaux et physiques</small>
            </div>
            <div class="ms-auto">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="cb_active" name="cb_active" value="1"
                           <?php checked(!empty($cb_settings['is_active'])); ?>>
                    <label class="form-check-label fw-semibold" for="cb_active">Activer</label>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Nickname <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="cb_nickname"
                       value="<?php echo esc_attr($cb_settings['nickname'] ?? ''); ?>"
                       placeholder="radonirina">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">API Key</label>
                <input type="password" class="form-control" name="cb_api_key"
                       value="<?php echo esc_attr($cb_settings['apiKey'] ?? ''); ?>"
                       placeholder="API-XXXXXXXXXX">
            </div>
        </div>
    </div>

    <!-- Sovrn / VigLink -->
    <div class="ded-card mb-4">
        <div class="d-flex align-items-center mb-3 gap-3">
            <div class="ded-module-logo" style="background:#1d4289">SV</div>
            <div>
                <h4 class="mb-0">Sovrn Commerce (VigLink)</h4>
                <small class="text-muted">Monétisation automatique des liens</small>
            </div>
            <div class="ms-auto">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="sv_active" name="sv_active" value="1"
                           <?php checked(!empty($sv_settings['is_active'])); ?>>
                    <label class="form-check-label fw-semibold" for="sv_active">Activer</label>
                </div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">API Key <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="sv_api_key"
                       value="<?php echo esc_attr($sv_settings['apiKey'] ?? ''); ?>"
                       placeholder="de86ae09...">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Secret Key <span class="text-danger">*</span></label>
                <input type="password" class="form-control" name="sv_secret_key"
                       value="<?php echo esc_attr($sv_settings['secretKey'] ?? ''); ?>"
                       placeholder="ab8a43d7...">
                <div class="form-text">Trouvez vos clés sur <a href="https://platform.sovrn.com/commerce/settings/site" target="_blank">platform.sovrn.com</a></div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-3">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-save me-2"></i>Sauvegarder la configuration
        </button>
        <a href="<?php echo esc_url(admin_url('admin.php?page=dealeldorado')); ?>" class="btn btn-outline-secondary btn-lg">
            Annuler
        </a>
    </div>
</form>
</div>
