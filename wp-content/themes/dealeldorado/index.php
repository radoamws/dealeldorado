<?php get_header(); ?>

<div class="container-xl py-4">
    <div class="row g-4">
        <div class="col-lg-8">

            <?php if (have_posts()): ?>

            <?php if (is_archive()): ?>
            <div class="ded-archive-header mb-4">
                <h1 class="h3 fw-bold">
                    <?php the_archive_title('<i class="fas fa-folder-open me-2 text-primary"></i>'); ?>
                </h1>
                <?php the_archive_description('<p class="text-muted">', '</p>'); ?>
            </div>
            <?php endif; ?>

            <div class="row g-3">
                <?php while (have_posts()): the_post(); ?>
                <div class="col-sm-6">
                    <div class="ded-product-card card border-0 shadow-sm h-100">
                        <?php if (has_post_thumbnail()): ?>
                        <div class="ded-product-img-wrapper">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium', array('class' => 'card-img-top ded-product-img')); ?>
                            </a>
                        </div>
                        <?php else: ?>
                        <div class="ded-product-img-placeholder d-flex align-items-center justify-content-center bg-light" style="height:160px">
                            <i class="fas fa-image fa-2x text-muted"></i>
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
                                <span class="text-muted small">
                                    <i class="fas fa-calendar me-1"></i><?php echo get_the_date('d/m/Y'); ?>
                                </span>
                                <a href="<?php the_permalink(); ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-balance-scale me-1"></i>
                                    <?php esc_html_e('Comparer', 'dealeldorado'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                <?php dealeldorado_pagination(); ?>
            </div>

            <?php else: ?>
            <div class="ded-empty-state text-center py-5">
                <i class="fas fa-search fa-4x text-muted mb-3 d-block"></i>
                <h3 class="text-muted"><?php esc_html_e('Aucun résultat trouvé', 'dealeldorado'); ?></h3>
                <p class="text-muted"><?php esc_html_e('Essayez avec d\'autres mots-clés.', 'dealeldorado'); ?></p>
                <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="d-flex gap-2 mt-3 justify-content-center">
                    <input type="search" name="s" class="form-control w-auto"
                           placeholder="<?php esc_attr_e('Nouvelle recherche...', 'dealeldorado'); ?>">
                    <button type="submit" class="btn btn-primary"><?php esc_html_e('Chercher', 'dealeldorado'); ?></button>
                </form>
            </div>
            <?php endif; ?>

        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="ded-widget card border-0 shadow-sm mb-3">
                <div class="card-body p-3">
                    <h5 class="widget-title fw-bold mb-3 pb-2 border-bottom">
                        <i class="fas fa-search me-2 text-primary"></i>
                        <?php esc_html_e('Rechercher', 'dealeldorado'); ?>
                    </h5>
                    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <div class="input-group">
                            <input type="search" name="s" class="form-control"
                                   placeholder="<?php esc_attr_e('Produit...', 'dealeldorado'); ?>"
                                   value="<?php echo esc_attr(get_search_query()); ?>">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <?php if (is_active_sidebar('sidebar-main')): ?>
                <?php dynamic_sidebar('sidebar-main'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
