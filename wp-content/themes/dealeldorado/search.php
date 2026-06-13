<?php get_header(); ?>

<div class="container-xl py-4">

    <!-- Search Header -->
    <div class="ded-search-header mb-4">
        <div class="card border-0 shadow-sm p-4" style="background: linear-gradient(135deg, #1a1a2e 0%, #2d2d5e 100%);">
            <h1 class="text-white h4 mb-2">
                <i class="fas fa-search me-2 text-warning"></i>
                <?php printf(esc_html__('Résultats pour : "%s"', 'dealeldorado'), '<span class="text-warning">' . esc_html(get_search_query()) . '</span>'); ?>
            </h1>
            <p class="text-white-50 mb-3 small">
                <?php global $wp_query; printf(esc_html__('%d résultat(s) trouvé(s)', 'dealeldorado'), $wp_query->found_posts); ?>
            </p>
            <!-- Refined search form -->
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="ded-search-form">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="search" name="s"
                           value="<?php echo esc_attr(get_search_query()); ?>"
                           class="form-control border-0"
                           placeholder="<?php esc_attr_e('Affiner la recherche...', 'dealeldorado'); ?>">
                    <button type="submit" class="btn btn-warning fw-semibold px-4">
                        <?php esc_html_e('Rechercher', 'dealeldorado'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (have_posts()): ?>

    <!-- Content Egg Live Search Results -->
    <?php if (function_exists('content_egg_shortcode') && get_search_query()): ?>
    <div class="ded-live-comparison mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h2 class="h5 fw-bold mb-0 text-primary">
                    <i class="fas fa-balance-scale me-2"></i>
                    <?php printf(esc_html__('Comparer les prix pour "%s"', 'dealeldorado'), esc_html(get_search_query())); ?>
                </h2>
            </div>
            <div class="card-body p-0">
                <?php echo do_shortcode('[content-egg module=CjProducts keyword="' . esc_attr(get_search_query()) . '"]'); ?>
                <?php echo do_shortcode('[content-egg module=Clickbank keyword="' . esc_attr(get_search_query()) . '"]'); ?>
                <?php echo do_shortcode('[content-egg-block template=offers_list]'); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h5 fw-bold mb-0">
                    <i class="fas fa-file-alt me-2 text-muted"></i>
                    <?php esc_html_e('Articles & Guides', 'dealeldorado'); ?>
                </h3>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-secondary btn-sm active" id="view-grid"><i class="fas fa-th"></i></button>
                    <button class="btn btn-outline-secondary btn-sm" id="view-list"><i class="fas fa-list"></i></button>
                </div>
            </div>

            <div class="row g-3" id="results-container">
                <?php while (have_posts()): the_post(); ?>
                <div class="col-sm-6 col-lg-6 result-item">
                    <div class="ded-product-card card border-0 shadow-sm h-100">
                        <?php if (has_post_thumbnail()): ?>
                        <div class="ded-product-img-wrapper">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium', array('class' => 'card-img-top ded-product-img')); ?>
                            </a>
                        </div>
                        <?php endif; ?>
                        <div class="card-body p-3">
                            <h5 class="ded-product-title mb-2">
                                <a href="<?php the_permalink(); ?>" class="text-decoration-none text-dark">
                                    <?php the_title(); ?>
                                </a>
                            </h5>
                            <p class="text-muted small mb-3"><?php the_excerpt(); ?></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted" style="font-size:.75rem">
                                    <i class="fas fa-calendar me-1"></i><?php echo get_the_date('d/m/Y'); ?>
                                </span>
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-balance-scale me-1"></i>Voir
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="mt-4"><?php dealeldorado_pagination(); ?></div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="ded-widget card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <h5 class="fw-bold mb-3"><i class="fas fa-filter me-2 text-primary"></i>Filtres</h5>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Fourchette de prix</label>
                        <div class="d-flex gap-2">
                            <input type="number" class="form-control form-control-sm" placeholder="Min €">
                            <input type="number" class="form-control form-control-sm" placeholder="Max €">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Catégorie</label>
                        <?php wp_dropdown_categories(array('show_option_all' => 'Toutes catégories', 'class' => 'form-select form-select-sm')); ?>
                    </div>
                    <button class="btn btn-primary btn-sm w-100">Appliquer les filtres</button>
                </div>
            </div>
            <?php if (is_active_sidebar('sidebar-main')): ?>
                <?php dynamic_sidebar('sidebar-main'); ?>
            <?php endif; ?>
        </div>
    </div>

    <?php else: ?>

    <div class="ded-no-results text-center py-5">
        <i class="fas fa-search fa-4x text-muted mb-4 d-block"></i>
        <h3><?php esc_html_e('Aucun résultat trouvé', 'dealeldorado'); ?></h3>
        <p class="text-muted">
            <?php printf(esc_html__('Aucun résultat pour "%s". Essayez avec d\'autres mots-clés.', 'dealeldorado'), esc_html(get_search_query())); ?>
        </p>

        <!-- Popular suggestions -->
        <div class="mt-4">
            <p class="fw-semibold mb-2">Suggestions populaires :</p>
            <?php
            $popular = array('iPhone 15', 'Samsung Galaxy', 'PlayStation 5', 'Nike Air Max', 'Dyson', 'Apple Watch');
            foreach ($popular as $term):
            ?>
            <a href="<?php echo esc_url(home_url('/?s=' . urlencode($term))); ?>"
               class="btn btn-outline-primary btn-sm m-1">
                <?php echo esc_html($term); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <?php endif; ?>
</div>

<?php get_footer(); ?>
