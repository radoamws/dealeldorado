<?php get_header(); ?>

<div class="container-xl py-5">
    <div class="text-center py-5">
        <div class="ded-404-icon mb-4">
            <span style="font-size:8rem;line-height:1">🔍</span>
        </div>
        <h1 class="display-1 fw-black text-primary">404</h1>
        <h2 class="fw-bold mb-3"><?php esc_html_e('Page introuvable', 'dealeldorado'); ?></h2>
        <p class="text-muted fs-5 mb-4">
            <?php esc_html_e('Oops ! La page que vous cherchez n\'existe pas ou a été déplacée.', 'dealeldorado'); ?>
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary btn-lg">
                <i class="fas fa-home me-2"></i><?php esc_html_e('Retour à l\'accueil', 'dealeldorado'); ?>
            </a>
            <a href="<?php echo esc_url(home_url('/?s=')); ?>" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-search me-2"></i><?php esc_html_e('Rechercher', 'dealeldorado'); ?>
            </a>
        </div>
        <div class="mt-5">
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>" class="d-flex gap-2 justify-content-center">
                <input type="search" name="s" class="form-control form-control-lg w-auto"
                       placeholder="<?php esc_attr_e('Rechercher un produit...', 'dealeldorado'); ?>">
                <button type="submit" class="btn btn-primary btn-lg"><?php esc_html_e('Chercher', 'dealeldorado'); ?></button>
            </form>
        </div>
    </div>
</div>

<?php get_footer(); ?>
