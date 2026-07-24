<?php get_header(); ?>

<!-- Hero Section -->
<section class="ded-hero">
    <div class="container-xl">
        <div class="ded-hero-content text-center py-5">
            <h1 class="ded-hero-title">
                <?php esc_html_e('Trouvez le ', 'dealeldorado'); ?>
                <span class="text-warning"><?php esc_html_e('meilleur prix', 'dealeldorado'); ?></span>
                <?php esc_html_e(' en 1 clic', 'dealeldorado'); ?>
            </h1>
            <p class="ded-hero-subtitle">
                <?php esc_html_e('Comparez des milliers de produits chez les meilleurs marchands', 'dealeldorado'); ?>
            </p>

            <!-- Big Search Form -->
            <div class="ded-hero-search-wrapper">
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="ded-hero-search">
                    <div class="input-group input-group-lg shadow-lg">
                        <span class="input-group-text bg-white border-0 ps-4">
                            <i class="fas fa-search text-muted fs-5"></i>
                        </span>
                        <input type="search"
                               name="s"
                               placeholder="<?php esc_attr_e('Ex: iPhone 15, Samsung 4K, Nike Air Max...', 'dealeldorado'); ?>"
                               class="form-control border-0 fs-5 py-3"
                               autocomplete="off">
                        <button type="submit" class="btn ded-btn-search-hero px-5">
                            <i class="fas fa-search me-2"></i>
                            <?php esc_html_e('Comparer', 'dealeldorado'); ?>
                        </button>
                    </div>
                </form>

                <!-- Popular Searches -->
                <div class="ded-popular-searches mt-3">
                    <span class="text-white-50 small me-2"><?php esc_html_e('Populaire :', 'dealeldorado'); ?></span>
                    <?php
                    $popular = array('iPhone 15', 'PS5', 'AirPods', 'RTX 4090', 'MacBook Pro', 'Samsung S24');
                    foreach ($popular as $term):
                    ?>
                    <a href="<?php echo esc_url(home_url('/?s=' . urlencode($term))); ?>"
                       class="ded-popular-tag"><?php echo esc_html($term); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Stats Bar -->
            <div class="ded-stats-bar d-flex justify-content-center gap-5 mt-4 flex-wrap">
                <div class="ded-stat-item">
                    <div class="ded-stat-number">500K+</div>
                    <div class="ded-stat-label"><?php esc_html_e('Produits', 'dealeldorado'); ?></div>
                </div>
                <div class="ded-stat-divider d-none d-md-block"></div>
                <div class="ded-stat-item">
                    <div class="ded-stat-number">1000+</div>
                    <div class="ded-stat-label"><?php esc_html_e('Marchands', 'dealeldorado'); ?></div>
                </div>
                <div class="ded-stat-divider d-none d-md-block"></div>
                <div class="ded-stat-item">
                    <div class="ded-stat-number">100%</div>
                    <div class="ded-stat-label"><?php esc_html_e('Gratuit', 'dealeldorado'); ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="ded-section-categories py-5 bg-white">
    <div class="container-xl">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="ded-section-title mb-0">
                <i class="fas fa-th-large me-2 text-primary"></i>
                <?php esc_html_e('Catégories populaires', 'dealeldorado'); ?>
            </h2>
            <a href="<?php echo esc_url(home_url('/categories')); ?>" class="btn btn-outline-primary btn-sm">
                <?php esc_html_e('Tout voir', 'dealeldorado'); ?> <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-3">
            <?php
            $homepage_categories = array(
                array('icon' => 'fa-laptop-code',  'name' => 'Électronique',  'color' => '#4361ee', 'count' => '45K+'),
                array('icon' => 'fa-mobile-alt',   'name' => 'Téléphones',    'color' => '#7209b7', 'count' => '12K+'),
                array('icon' => 'fa-desktop',      'name' => 'Informatique',  'color' => '#3a0ca3', 'count' => '28K+'),
                array('icon' => 'fa-tv',           'name' => 'TV & Audio',    'color' => '#560bad', 'count' => '8K+'),
                array('icon' => 'fa-home',         'name' => 'Maison',        'color' => '#f4a261', 'count' => '32K+'),
                array('icon' => 'fa-tshirt',       'name' => 'Mode',          'color' => '#e85d04', 'count' => '55K+'),
                array('icon' => 'fa-dumbbell',     'name' => 'Sport',         'color' => '#2d6a4f', 'count' => '18K+'),
                array('icon' => 'fa-gamepad',      'name' => 'Jeux & Jouets', 'color' => '#d62828', 'count' => '10K+'),
                array('icon' => 'fa-car',          'name' => 'Auto & Moto',   'color' => '#457b9d', 'count' => '25K+'),
                array('icon' => 'fa-spa',          'name' => 'Beauté',        'color' => '#f72585', 'count' => '20K+'),
                array('icon' => 'fa-utensils',     'name' => 'Cuisine',       'color' => '#f77f00', 'count' => '15K+'),
                array('icon' => 'fa-book',         'name' => 'Livres',        'color' => '#606c38', 'count' => '80K+'),
            );
            foreach ($homepage_categories as $cat):
            ?>
            <div class="col-6 col-md-4 col-lg-2">
                <a href="<?php echo esc_url(home_url('/?s=' . urlencode($cat['name']))); ?>"
                   class="ded-cat-card text-center text-decoration-none d-block p-3 rounded-3 h-100">
                    <div class="ded-cat-icon mb-2" style="color: <?php echo esc_attr($cat['color']); ?>">
                        <i class="fas <?php echo esc_attr($cat['icon']); ?> fa-2x"></i>
                    </div>
                    <div class="ded-cat-name fw-semibold small"><?php echo esc_html($cat['name']); ?></div>
                    <div class="ded-cat-count text-muted" style="font-size:0.7rem"><?php echo esc_html($cat['count']); ?> produits</div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Top Deals Section -->
<section class="ded-section-deals py-5">
    <div class="container-xl">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="ded-section-title mb-0">
                <i class="fas fa-fire me-2 text-danger"></i>
                <?php esc_html_e('Top Deals du moment', 'dealeldorado'); ?>
            </h2>
            <a href="<?php echo esc_url(home_url('/?s=')); ?>" class="btn btn-outline-danger btn-sm">
                <?php esc_html_e('Tous les deals', 'dealeldorado'); ?> <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        <?php
        $recent_posts = get_posts(array(
            'numberposts' => 6,
            'post_status' => 'publish',
        ));

        if (!empty($recent_posts)):
        ?>
        <div class="row g-3">
            <?php foreach ($recent_posts as $post): setup_postdata($post); ?>
            <div class="col-sm-6 col-lg-4">
                <div class="ded-product-card card border-0 shadow-sm h-100">
                    <?php if (has_post_thumbnail()): ?>
                    <div class="ded-product-img-wrapper">
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('medium', array('class' => 'card-img-top ded-product-img')); ?>
                        </a>
                        <span class="ded-badge-deal">DEAL</span>
                    </div>
                    <?php else: ?>
                    <div class="ded-product-img-placeholder d-flex align-items-center justify-content-center">
                        <i class="fas fa-image fa-3x text-muted"></i>
                    </div>
                    <?php endif; ?>
                    <div class="card-body p-3">
                        <h5 class="ded-product-title">
                            <a href="<?php the_permalink(); ?>" class="text-decoration-none text-dark stretched-link">
                                <?php the_title(); ?>
                            </a>
                        </h5>
                        <p class="text-muted small mb-3"><?php the_excerpt(); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="ded-price-label">
                                <i class="fas fa-tag me-1 text-primary"></i>
                                <?php esc_html_e('Comparer les prix', 'dealeldorado'); ?>
                            </span>
                            <span class="badge bg-primary"><?php esc_html_e('Voir le deal', 'dealeldorado'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
        <?php else: ?>
        <!-- Empty State: Guide to add products -->
        <div class="ded-empty-state text-center py-5">
            <div class="mb-4">
                <i class="fas fa-box-open fa-4x text-muted"></i>
            </div>
            <h3 class="text-muted"><?php esc_html_e('Aucun produit pour le moment', 'dealeldorado'); ?></h3>
            <p class="text-muted">
                <?php esc_html_e('Commencez par créer des articles dans WordPress et utiliser Content Egg Pro pour importer des produits.', 'dealeldorado'); ?>
            </p>
            <?php if (is_user_logged_in()): ?>
            <a href="<?php echo esc_url(admin_url('post-new.php')); ?>" class="btn btn-primary mt-2">
                <i class="fas fa-plus me-2"></i><?php esc_html_e('Ajouter un produit', 'dealeldorado'); ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Content Egg Comparison Block (si Content Egg actif) -->
<?php if (shortcode_exists('content-egg-block')): ?>
<section class="ded-section-comparison py-5 bg-white">
    <div class="container-xl">
        <h2 class="ded-section-title text-center mb-4">
            <i class="fas fa-chart-bar me-2 text-primary"></i>
            <?php esc_html_e('Comparez les prix maintenant', 'dealeldorado'); ?>
        </h2>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow p-4">
                    <p class="text-center text-muted mb-3">
                        <?php esc_html_e('Entrez un mot-clé dans la barre de recherche pour comparer les prix instantanément.', 'dealeldorado'); ?>
                    </p>
                    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="d-flex gap-2">
                        <input type="search" name="s"
                               placeholder="<?php esc_attr_e('Ex: iPhone 15 Pro 256Go...', 'dealeldorado'); ?>"
                               class="form-control form-control-lg">
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            <i class="fas fa-balance-scale me-2"></i>
                            <?php esc_html_e('Comparer', 'dealeldorado'); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Features Section -->
<section class="ded-section-features py-5">
    <div class="container-xl">
        <h2 class="ded-section-title text-center mb-5"><?php esc_html_e('Pourquoi DealElDorado ?', 'dealeldorado'); ?></h2>
        <div class="row g-4">
            <div class="col-md-3 col-sm-6 text-center">
                <div class="ded-feature-icon mb-3">
                    <i class="fas fa-bolt fa-2x"></i>
                </div>
                <h5 class="fw-bold"><?php esc_html_e('Rapide & Gratuit', 'dealeldorado'); ?></h5>
                <p class="text-muted small"><?php esc_html_e('Comparaison instantanée sans inscription requise', 'dealeldorado'); ?></p>
            </div>
            <div class="col-md-3 col-sm-6 text-center">
                <div class="ded-feature-icon mb-3">
                    <i class="fas fa-shield-alt fa-2x"></i>
                </div>
                <h5 class="fw-bold"><?php esc_html_e('Fiable & Sécurisé', 'dealeldorado'); ?></h5>
                <p class="text-muted small"><?php esc_html_e('Marchands vérifiés et prix mis à jour en temps réel', 'dealeldorado'); ?></p>
            </div>
            <div class="col-md-3 col-sm-6 text-center">
                <div class="ded-feature-icon mb-3">
                    <i class="fas fa-history fa-2x"></i>
                </div>
                <h5 class="fw-bold"><?php esc_html_e('Historique des prix', 'dealeldorado'); ?></h5>
                <p class="text-muted small"><?php esc_html_e('Suivez l\'évolution des prix et achetez au bon moment', 'dealeldorado'); ?></p>
            </div>
            <div class="col-md-3 col-sm-6 text-center">
                <div class="ded-feature-icon mb-3">
                    <i class="fas fa-bell fa-2x"></i>
                </div>
                <h5 class="fw-bold"><?php esc_html_e('Alertes personnalisées', 'dealeldorado'); ?></h5>
                <p class="text-muted small"><?php esc_html_e('Soyez notifié quand le prix baisse', 'dealeldorado'); ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Recent Articles -->
<?php
$blog_posts = get_posts(array('numberposts' => 3, 'post_status' => 'publish'));
if (!empty($blog_posts)):
?>
<section class="ded-section-blog py-5 bg-white">
    <div class="container-xl">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="ded-section-title mb-0">
                <i class="fas fa-newspaper me-2 text-primary"></i>
                <?php esc_html_e('Conseils & Guides', 'dealeldorado'); ?>
            </h2>
            <a href="<?php echo esc_url(home_url('/blog')); ?>" class="btn btn-outline-primary btn-sm">
                <?php esc_html_e('Tous les articles', 'dealeldorado'); ?>
            </a>
        </div>
        <div class="row g-4">
            <?php foreach ($blog_posts as $post): setup_postdata($post); ?>
            <div class="col-md-4">
                <div class="ded-blog-card card border-0 shadow-sm h-100">
                    <?php if (has_post_thumbnail()): ?>
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('medium_large', array('class' => 'card-img-top', 'style' => 'height:180px;object-fit:cover')); ?>
                    </a>
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="text-muted small mb-2">
                            <i class="fas fa-calendar me-1"></i>
                            <?php echo get_the_date('d/m/Y', $post); ?>
                        </div>
                        <h5><a href="<?php the_permalink(); ?>" class="text-decoration-none text-dark"><?php the_title(); ?></a></h5>
                        <p class="text-muted small"><?php the_excerpt(); ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
