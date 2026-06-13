<?php get_header(); ?>

<div class="container-xl py-4">
    <div class="row g-4">

        <!-- Main Content -->
        <div class="col-lg-8">
            <?php while (have_posts()): the_post(); ?>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo esc_url(home_url('/')); ?>">Accueil</a></li>
                    <?php $cats = get_the_category(); if (!empty($cats)): ?>
                    <li class="breadcrumb-item">
                        <a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>">
                            <?php echo esc_html($cats[0]->name); ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="breadcrumb-item active" aria-current="page"><?php the_title(); ?></li>
                </ol>
            </nav>

            <article class="ded-article-card card border-0 shadow-sm mb-4">
                <?php if (has_post_thumbnail()): ?>
                <div class="ded-article-img-wrapper">
                    <?php the_post_thumbnail('large', array('class' => 'card-img-top ded-article-img')); ?>
                </div>
                <?php endif; ?>

                <div class="card-body p-4">
                    <!-- Title & Meta -->
                    <h1 class="ded-article-title h2 fw-bold mb-3"><?php the_title(); ?></h1>

                    <div class="d-flex flex-wrap gap-3 mb-4 text-muted small">
                        <span><i class="fas fa-calendar me-1"></i><?php echo get_the_date('d/m/Y'); ?></span>
                        <span><i class="fas fa-user me-1"></i><?php the_author(); ?></span>
                        <?php $cats = get_the_category(); if (!empty($cats)): ?>
                        <span>
                            <i class="fas fa-tag me-1"></i>
                            <a href="<?php echo esc_url(get_category_link($cats[0]->term_id)); ?>" class="text-muted">
                                <?php echo esc_html($cats[0]->name); ?>
                            </a>
                        </span>
                        <?php endif; ?>
                    </div>

                    <!-- Content Egg Price Comparison Block -->
                    <?php if (function_exists('content_egg_shortcode')): ?>
                    <div class="ded-comparison-block mb-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-balance-scale text-primary me-2 fs-5"></i>
                            <h3 class="h5 fw-bold mb-0 text-primary"><?php esc_html_e('Comparer les prix', 'dealeldorado'); ?></h3>
                        </div>
                        <?php echo do_shortcode('[content-egg-block template=offers_list]'); ?>
                        <?php echo do_shortcode('[content-egg-block template=price_comparison]'); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Article Content -->
                    <div class="ded-article-content">
                        <?php the_content(); ?>
                    </div>

                    <!-- Content Egg Additional Blocks -->
                    <?php if (function_exists('content_egg_shortcode')): ?>
                    <div class="mt-4 pt-4 border-top">
                        <h4 class="fw-bold mb-3">
                            <i class="fas fa-store me-2 text-success"></i>
                            <?php esc_html_e('Toutes les offres', 'dealeldorado'); ?>
                        </h4>
                        <?php echo do_shortcode('[content-egg-block template=offers_list_groups]'); ?>
                    </div>

                    <div class="mt-4 pt-4 border-top">
                        <h4 class="fw-bold mb-3">
                            <i class="fas fa-chart-line me-2 text-primary"></i>
                            <?php esc_html_e('Historique des prix', 'dealeldorado'); ?>
                        </h4>
                        <?php echo do_shortcode('[content-egg-block template=price_history]'); ?>
                    </div>
                    <?php endif; ?>

                    <!-- Tags -->
                    <?php the_tags('<div class="mt-4 pt-3 border-top"><i class="fas fa-hashtag me-2 text-muted"></i>', ' ', '</div>'); ?>
                </div>
            </article>

            <!-- Author Box -->
            <div class="ded-author-box card border-0 shadow-sm p-4 mb-4">
                <div class="d-flex gap-3 align-items-start">
                    <?php echo get_avatar(get_the_author_meta('ID'), 64, '', '', array('class' => 'rounded-circle')); ?>
                    <div>
                        <h5 class="fw-bold mb-1"><?php the_author(); ?></h5>
                        <p class="text-muted small mb-0"><?php the_author_meta('description'); ?></p>
                    </div>
                </div>
            </div>

            <!-- Post Navigation -->
            <div class="d-flex justify-content-between gap-3 mb-4">
                <?php $prev = get_previous_post(); if ($prev): ?>
                <a href="<?php echo esc_url(get_permalink($prev)); ?>" class="btn btn-outline-secondary flex-grow-1 text-start">
                    <i class="fas fa-chevron-left me-2"></i>
                    <span class="d-block text-truncate small"><?php echo esc_html(get_the_title($prev)); ?></span>
                </a>
                <?php endif; ?>
                <?php $next = get_next_post(); if ($next): ?>
                <a href="<?php echo esc_url(get_permalink($next)); ?>" class="btn btn-outline-secondary flex-grow-1 text-end">
                    <span class="d-block text-truncate small"><?php echo esc_html(get_the_title($next)); ?></span>
                    <i class="fas fa-chevron-right ms-2"></i>
                </a>
                <?php endif; ?>
            </div>

            <?php endwhile; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Compare Widget -->
            <div class="ded-widget card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <h5 class="widget-title fw-bold mb-3 pb-2 border-bottom">
                        <i class="fas fa-search me-2 text-primary"></i>
                        <?php esc_html_e('Recherche rapide', 'dealeldorado'); ?>
                    </h5>
                    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="input-group">
                            <input type="search" name="s" class="form-control"
                                   placeholder="<?php esc_attr_e('Produit...', 'dealeldorado'); ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Price Alert Widget -->
            <div class="ded-widget card border-0 shadow-sm mb-3" style="background: linear-gradient(135deg, #e85d04, #f4a261);">
                <div class="card-body p-3 text-white">
                    <h5 class="fw-bold mb-2">
                        <i class="fas fa-bell me-2"></i>
                        <?php esc_html_e('Alerte prix', 'dealeldorado'); ?>
                    </h5>
                    <p class="small mb-3 text-white-75">
                        <?php esc_html_e('Soyez notifié quand le prix baisse', 'dealeldorado'); ?>
                    </p>
                    <?php if (function_exists('content_egg_shortcode')): ?>
                        <?php echo do_shortcode('[cegg_price_alert]'); ?>
                    <?php else: ?>
                    <input type="email" class="form-control form-control-sm mb-2"
                           placeholder="<?php esc_attr_e('Votre email', 'dealeldorado'); ?>">
                    <button class="btn btn-light btn-sm w-100 fw-semibold text-primary">
                        <?php esc_html_e('Créer une alerte', 'dealeldorado'); ?>
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar Widgets -->
            <?php if (is_active_sidebar('sidebar-main')): ?>
                <?php dynamic_sidebar('sidebar-main'); ?>
            <?php endif; ?>

            <!-- Related Posts -->
            <?php
            $cats = get_the_category();
            if (!empty($cats)):
                $related = get_posts(array(
                    'numberposts'      => 3,
                    'category__in'     => array($cats[0]->term_id),
                    'post__not_in'     => array(get_the_ID()),
                    'post_status'      => 'publish',
                ));
                if (!empty($related)):
            ?>
            <div class="ded-widget card border-0 shadow-sm">
                <div class="card-body p-3">
                    <h5 class="widget-title fw-bold mb-3 pb-2 border-bottom">
                        <i class="fas fa-link me-2 text-primary"></i>
                        <?php esc_html_e('Articles similaires', 'dealeldorado'); ?>
                    </h5>
                    <?php foreach ($related as $rel): ?>
                    <div class="d-flex gap-2 mb-3">
                        <?php if (has_post_thumbnail($rel->ID)): ?>
                        <a href="<?php echo esc_url(get_permalink($rel->ID)); ?>" class="flex-shrink-0">
                            <?php echo get_the_post_thumbnail($rel->ID, array(60, 60), array('class' => 'rounded', 'style' => 'width:60px;height:60px;object-fit:cover')); ?>
                        </a>
                        <?php endif; ?>
                        <div>
                            <a href="<?php echo esc_url(get_permalink($rel->ID)); ?>" class="text-dark text-decoration-none small fw-semibold lh-sm">
                                <?php echo esc_html(get_the_title($rel->ID)); ?>
                            </a>
                            <div class="text-muted" style="font-size:0.7rem">
                                <?php echo get_the_date('d/m/Y', $rel->ID); ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; endif; ?>
        </div>

    </div>
</div>

<?php get_footer(); ?>
