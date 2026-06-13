<?php
defined('ABSPATH') || exit;

/**
 * Shortcodes DealElDorado pour les pages et articles.
 *
 * [ded_compare keyword="iPhone 15"] - Affiche une comparaison de prix
 * [ded_search_bar] - Barre de recherche stylisée
 * [ded_top_deals count="6"] - Top deals du moment
 * [ded_price_box merchant="Amazon" price="499" url="https://..."] - Boîte prix
 */
class DED_Shortcodes {

    private static ?DED_Shortcodes $instance = null;

    public static function instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(): void {
        add_shortcode('ded_compare', array($this, 'compare_shortcode'));
        add_shortcode('ded_search_bar', array($this, 'search_bar_shortcode'));
        add_shortcode('ded_top_deals', array($this, 'top_deals_shortcode'));
        add_shortcode('ded_price_box', array($this, 'price_box_shortcode'));
        add_shortcode('ded_affiliate_disclaimer', array($this, 'disclaimer_shortcode'));
    }

    /**
     * Shortcode de comparaison - utilise Content Egg Pro si disponible.
     */
    public function compare_shortcode(array $atts): string {
        $atts = shortcode_atts(array(
            'keyword'  => '',
            'module'   => 'CjProducts',
            'template' => 'block_price_comparison',
            'limit'    => 10,
        ), $atts, 'ded_compare');

        if (empty($atts['keyword'])) {
            $atts['keyword'] = get_the_title();
        }

        ob_start();
        ?>
        <div class="ded-compare-widget card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h3 class="h6 fw-bold mb-0 text-primary">
                    <i class="fas fa-balance-scale me-2"></i>
                    Comparer les prix : <?php echo esc_html($atts['keyword']); ?>
                </h3>
            </div>
            <div class="card-body p-0 p-md-3">
                <?php if (function_exists('content_egg_shortcode')): ?>
                    <?php echo do_shortcode('[content-egg module=' . esc_attr($atts['module']) . ' keyword="' . esc_attr($atts['keyword']) . '" limit=' . intval($atts['limit']) . ']'); ?>
                    <?php echo do_shortcode('[content-egg-block template=' . esc_attr($atts['template']) . ']'); ?>
                <?php else: ?>
                    <div class="alert alert-info m-3">
                        <i class="fas fa-info-circle me-2"></i>
                        Activez le plugin <strong>Content Egg Pro</strong> pour afficher les comparaisons de prix.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Barre de recherche DealElDorado.
     */
    public function search_bar_shortcode(array $atts): string {
        $atts = shortcode_atts(array(
            'placeholder' => 'Rechercher un produit...',
            'button_text' => 'Comparer',
        ), $atts, 'ded_search_bar');

        ob_start();
        ?>
        <div class="ded-shortcode-search my-4">
            <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <div class="input-group input-group-lg shadow-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="search" name="s"
                           class="form-control border-start-0 border-end-0"
                           placeholder="<?php echo esc_attr($atts['placeholder']); ?>">
                    <button type="submit" class="btn btn-primary fw-semibold px-4">
                        <?php echo esc_html($atts['button_text']); ?>
                    </button>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Liste des top deals.
     */
    public function top_deals_shortcode(array $atts): string {
        $atts = shortcode_atts(array(
            'count' => 6,
        ), $atts, 'ded_top_deals');

        $posts = get_posts(array(
            'numberposts' => intval($atts['count']),
            'post_status' => 'publish',
        ));

        if (empty($posts)) {
            return '<p class="text-muted">Aucun deal disponible pour le moment.</p>';
        }

        ob_start();
        ?>
        <div class="ded-top-deals-widget row g-3 my-3">
            <?php foreach ($posts as $post): setup_postdata($post); ?>
            <div class="col-sm-6 col-md-4">
                <div class="card border-0 shadow-sm h-100 ded-product-card">
                    <?php if (has_post_thumbnail($post->ID)): ?>
                    <a href="<?php echo esc_url(get_permalink($post->ID)); ?>">
                        <?php echo get_the_post_thumbnail($post->ID, 'medium', array('class' => 'card-img-top', 'style' => 'height:160px;object-fit:cover')); ?>
                    </a>
                    <?php endif; ?>
                    <div class="card-body p-3">
                        <h5 class="card-title h6">
                            <a href="<?php echo esc_url(get_permalink($post->ID)); ?>" class="text-decoration-none text-dark">
                                <?php echo esc_html(get_the_title($post->ID)); ?>
                            </a>
                        </h5>
                        <a href="<?php echo esc_url(get_permalink($post->ID)); ?>" class="btn btn-primary btn-sm mt-2">
                            <i class="fas fa-balance-scale me-1"></i> Comparer
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Boîte de prix simple (affichage manuel d'une offre).
     */
    public function price_box_shortcode(array $atts): string {
        $atts = shortcode_atts(array(
            'merchant' => 'Marchand',
            'price'    => '',
            'url'      => '#',
            'badge'    => '',
            'shipping' => 'Livraison incluse',
        ), $atts, 'ded_price_box');

        ob_start();
        ?>
        <div class="ded-price-box card border-0 shadow-sm my-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold"><?php echo esc_html($atts['merchant']); ?></div>
                        <div class="text-muted small"><?php echo esc_html($atts['shipping']); ?></div>
                    </div>
                    <div class="text-end">
                        <?php if ($atts['price']): ?>
                        <div class="ded-price-best fs-5"><?php echo esc_html($atts['price']); ?> €</div>
                        <?php endif; ?>
                        <?php if ($atts['badge']): ?>
                        <span class="badge bg-success"><?php echo esc_html($atts['badge']); ?></span>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo esc_url($atts['url']); ?>"
                       class="btn btn-primary btn-sm"
                       target="_blank"
                       rel="nofollow noopener sponsored">
                        <i class="fas fa-external-link-alt me-1"></i>Voir l'offre
                    </a>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Disclaimer affiliation.
     */
    public function disclaimer_shortcode(): string {
        return '<div class="ded-disclaimer alert alert-secondary small py-2 px-3 mt-3">' .
               '<i class="fas fa-info-circle me-1"></i>' .
               'Ce site contient des liens affiliés. Nous percevons une commission si vous effectuez un achat, ' .
               'sans frais supplémentaires pour vous.' .
               '</div>';
    }
}
