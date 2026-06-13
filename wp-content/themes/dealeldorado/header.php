<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#e85d04">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Top Bar -->
<div class="ded-topbar py-1">
    <div class="container-xl">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-white-50 small">
                <i class="fas fa-shield-alt me-1 text-warning"></i>
                <?php esc_html_e('Comparez des milliers de produits en toute confiance', 'dealeldorado'); ?>
            </span>
            <div class="d-flex gap-3 align-items-center">
                <a href="<?php echo esc_url(home_url('/deals')); ?>" class="text-warning small text-decoration-none fw-semibold">
                    <i class="fas fa-fire me-1"></i><?php esc_html_e('Top Deals', 'dealeldorado'); ?>
                </a>
                <?php if (is_user_logged_in()): ?>
                    <a href="<?php echo esc_url(admin_url()); ?>" class="text-white-50 small text-decoration-none">
                        <i class="fas fa-user-shield me-1"></i><?php esc_html_e('Admin', 'dealeldorado'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Header -->
<header class="ded-header sticky-top">
    <div class="container-xl">
        <div class="d-flex align-items-center gap-3 py-2">

            <!-- Logo -->
            <a href="<?php echo esc_url(home_url('/')); ?>" class="ded-logo-link flex-shrink-0">
                <?php if (has_custom_logo()): ?>
                    <?php the_custom_logo(); ?>
                <?php else: ?>
                    <img src="<?php echo esc_url(DED_THEME_URI . '/assets/images/logo.svg'); ?>"
                         alt="<?php bloginfo('name'); ?>"
                         height="48" class="ded-logo">
                <?php endif; ?>
            </a>

            <!-- Search Bar -->
            <div class="ded-search-wrapper flex-grow-1">
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="ded-search-form">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-white border-end-0 ps-3">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="search"
                               name="s"
                               value="<?php echo esc_attr(get_search_query()); ?>"
                               placeholder="<?php esc_attr_e('Rechercher un produit, une marque...', 'dealeldorado'); ?>"
                               class="form-control border-start-0 border-end-0 py-2"
                               autocomplete="off"
                               id="ded-search-input">
                        <button type="submit" class="btn ded-btn-search px-4">
                            <?php esc_html_e('Comparer', 'dealeldorado'); ?>
                        </button>
                    </div>
                    <!-- Autocomplete dropdown -->
                    <div class="ded-search-suggestions" id="ded-search-suggestions"></div>
                </form>
            </div>

            <!-- Header Actions -->
            <div class="ded-header-actions d-none d-md-flex align-items-center gap-2 flex-shrink-0">
                <a href="<?php echo esc_url(home_url('/alertes-prix')); ?>" class="btn btn-outline-light btn-sm" title="<?php esc_attr_e('Alertes Prix', 'dealeldorado'); ?>">
                    <i class="fas fa-bell"></i>
                    <span class="d-none d-lg-inline ms-1"><?php esc_html_e('Alertes', 'dealeldorado'); ?></span>
                </a>
            </div>

        </div>
    </div>

    <!-- Navigation Categories -->
    <nav class="ded-nav-categories">
        <div class="container-xl">
            <div class="d-flex align-items-center gap-1 overflow-x-auto py-1 ded-categories-scroll">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="ded-nav-cat-item <?php echo is_front_page() ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span><?php esc_html_e('Accueil', 'dealeldorado'); ?></span>
                </a>
                <?php
                $categories = get_terms(array(
                    'taxonomy'   => 'category',
                    'hide_empty' => false,
                    'number'     => 12,
                    'parent'     => 0,
                ));
                $cat_icons = array(
                    'electronique' => 'fa-laptop',
                    'téléphone'    => 'fa-mobile-alt',
                    'informatique' => 'fa-desktop',
                    'maison'       => 'fa-home',
                    'mode'         => 'fa-tshirt',
                    'sport'        => 'fa-running',
                    'beauté'       => 'fa-spa',
                    'auto'         => 'fa-car',
                    'jouets'       => 'fa-gamepad',
                    'cuisine'      => 'fa-utensils',
                );
                if (!empty($categories) && !is_wp_error($categories)):
                    foreach ($categories as $cat):
                        $slug = strtolower($cat->slug);
                        $icon = 'fa-tag';
                        foreach ($cat_icons as $key => $ico) {
                            if (strpos($slug, $key) !== false) { $icon = $ico; break; }
                        }
                ?>
                <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="ded-nav-cat-item">
                    <i class="fas <?php echo esc_attr($icon); ?>"></i>
                    <span><?php echo esc_html($cat->name); ?></span>
                </a>
                <?php endforeach; else: ?>
                <!-- Catégories par défaut si aucune n'est configurée -->
                <a href="#" class="ded-nav-cat-item"><i class="fas fa-laptop"></i><span>Électronique</span></a>
                <a href="#" class="ded-nav-cat-item"><i class="fas fa-mobile-alt"></i><span>Téléphones</span></a>
                <a href="#" class="ded-nav-cat-item"><i class="fas fa-desktop"></i><span>Informatique</span></a>
                <a href="#" class="ded-nav-cat-item"><i class="fas fa-home"></i><span>Maison</span></a>
                <a href="#" class="ded-nav-cat-item"><i class="fas fa-tshirt"></i><span>Mode</span></a>
                <a href="#" class="ded-nav-cat-item"><i class="fas fa-gamepad"></i><span>Jeux</span></a>
                <a href="#" class="ded-nav-cat-item"><i class="fas fa-car"></i><span>Auto</span></a>
                <?php endif; ?>
                <a href="<?php echo esc_url(home_url('/?s=')); ?>" class="ded-nav-cat-item ms-auto text-warning">
                    <i class="fas fa-fire"></i>
                    <span><?php esc_html_e('Deals', 'dealeldorado'); ?></span>
                </a>
            </div>
        </div>
    </nav>
</header>

<div id="page-content">
