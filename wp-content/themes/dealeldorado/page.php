<?php get_header(); ?>

<div class="container-xl py-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <?php while (have_posts()): the_post(); ?>
            <article class="card border-0 shadow-sm">
                <?php if (has_post_thumbnail()): ?>
                <?php the_post_thumbnail('large', array('class' => 'card-img-top', 'style' => 'max-height:400px;object-fit:cover')); ?>
                <?php endif; ?>
                <div class="card-body p-4 p-lg-5">
                    <h1 class="fw-bold mb-4"><?php the_title(); ?></h1>
                    <div class="ded-article-content"><?php the_content(); ?></div>
                </div>
            </article>
            <?php endwhile; ?>
        </div>
        <div class="col-lg-4">
            <?php if (is_active_sidebar('sidebar-main')): ?>
                <?php dynamic_sidebar('sidebar-main'); ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
