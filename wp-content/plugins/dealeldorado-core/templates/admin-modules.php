<?php defined('ABSPATH') || exit; ?>
<div class="wrap ded-admin-wrap">
<div class="ded-admin-header">
    <h1><i class="fas fa-puzzle-piece me-2 text-warning"></i>Modules Affiliés Content Egg Pro</h1>
    <p class="text-muted">Statut et configuration des modules affiliés disponibles</p>
</div>

<?php if (class_exists('\ContentEgg\application\Plugin')): ?>
<div class="alert alert-success d-flex align-items-center gap-2">
    <i class="fas fa-check-circle fa-lg"></i>
    <div><strong>Content Egg Pro est actif !</strong> Vous pouvez configurer les modules ci-dessous ou depuis
    <a href="<?php echo esc_url(admin_url('admin.php?page=content-egg')); ?>">l'interface Content Egg</a>.</div>
</div>
<?php else: ?>
<div class="alert alert-danger d-flex align-items-center gap-2">
    <i class="fas fa-times-circle fa-lg"></i>
    <div><strong>Content Egg Pro n'est pas activé.</strong>
    <a href="<?php echo esc_url(admin_url('plugins.php')); ?>">Activez-le dans Extensions</a>.</div>
</div>
<?php endif; ?>

<div class="row g-3">
<?php
$modules_info = array(
    'CjProducts'  => array('label' => 'CJ Products',         'color' => '#004b8d', 'icon' => 'store',         'type' => 'Produits'),
    'Clickbank'   => array('label' => 'Clickbank',           'color' => '#d13f31', 'icon' => 'dollar-sign',   'type' => 'Digital/Physique'),
    'Viglink'     => array('label' => 'Sovrn / VigLink',     'color' => '#1d4289', 'icon' => 'link',          'type' => 'Monétisation'),
    'Amazon'      => array('label' => 'Amazon PA',           'color' => '#ff9900', 'icon' => 'shopping-cart', 'type' => 'Produits'),
    'Ebay'        => array('label' => 'eBay',                'color' => '#e53238', 'icon' => 'tag',           'type' => 'Enchères'),
    'AE'          => array('label' => 'AliExpress',          'color' => '#e62117', 'icon' => 'globe',         'type' => 'Produits'),
    'Walmart'     => array('label' => 'Walmart',             'color' => '#0071ce', 'icon' => 'store',         'type' => 'Produits'),
    'Shareasale'  => array('label' => 'ShareASale',          'color' => '#f05a23', 'icon' => 'handshake',     'type' => 'Réseau affilié'),
);

foreach ($modules_info as $module_id => $info):
    $settings  = get_option('cegg_module_' . $module_id, array());
    $is_active = !empty($settings['is_active']);
    $has_config = !empty($settings);
?>
<div class="col-md-6 col-lg-4">
    <div class="ded-card h-100">
        <div class="d-flex align-items-center gap-3 mb-3">
            <div class="ded-module-logo" style="background:<?php echo esc_attr($info['color']); ?>">
                <i class="fas fa-<?php echo esc_attr($info['icon']); ?>"></i>
            </div>
            <div class="flex-grow-1">
                <div class="fw-bold"><?php echo esc_html($info['label']); ?></div>
                <div class="text-muted small"><?php echo esc_html($info['type']); ?></div>
            </div>
            <span class="badge <?php echo $is_active ? 'bg-success' : 'bg-secondary'; ?>">
                <?php echo $is_active ? 'Actif' : 'Inactif'; ?>
            </span>
        </div>
        <?php if ($has_config): ?>
        <div class="small text-success mb-2"><i class="fas fa-check me-1"></i>Configuré</div>
        <?php else: ?>
        <div class="small text-muted mb-2"><i class="fas fa-minus me-1"></i>Pas encore configuré</div>
        <?php endif; ?>
        <?php if (in_array($module_id, array('CjProducts', 'Clickbank', 'Viglink'))): ?>
        <a href="<?php echo esc_url(admin_url('admin.php?page=dealeldorado-api')); ?>" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-cog me-1"></i>Configurer
        </a>
        <?php elseif (class_exists('\ContentEgg\application\Plugin')): ?>
        <a href="<?php echo esc_url(admin_url('admin.php?page=content-egg-' . strtolower($module_id))); ?>" class="btn btn-outline-secondary btn-sm">
            Configurer via CE Pro
        </a>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>
</div>
</div>
