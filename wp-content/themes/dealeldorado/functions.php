<?php
defined('ABSPATH') || exit;

define('DED_THEME_VERSION', '1.0.0');
define('DED_THEME_URI', get_template_directory_uri());
define('DED_THEME_PATH', get_template_directory());

function dealeldorado_setup() {
    load_theme_textdomain('dealeldorado', DED_THEME_PATH . '/languages');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
    add_theme_support('custom-logo', array(
        'height'      => 80,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    register_nav_menus(array(
        'primary' => __('Menu Principal', 'dealeldorado'),
        'categories' => __('Menu Catégories', 'dealeldorado'),
        'footer'  => __('Menu Footer', 'dealeldorado'),
    ));
}
add_action('after_setup_theme', 'dealeldorado_setup');

function dealeldorado_scripts() {
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3');
    wp_enqueue_style('fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', array(), '6.5.1');
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap', array(), null);
    wp_enqueue_style('dealeldorado-style', get_stylesheet_uri(), array('bootstrap'), DED_THEME_VERSION);
    wp_enqueue_style('dealeldorado-custom', DED_THEME_URI . '/assets/css/custom.css', array('dealeldorado-style'), DED_THEME_VERSION);

    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array(), '5.3.3', true);
    wp_enqueue_script('dealeldorado-main', DED_THEME_URI . '/assets/js/main.js', array('jquery', 'bootstrap-js'), DED_THEME_VERSION, true);

    wp_localize_script('dealeldorado-main', 'ded_vars', array(
        'ajax_url'   => admin_url('admin-ajax.php'),
        'nonce'      => wp_create_nonce('ded_nonce'),
        'search_url' => home_url('/?s='),
        'site_url'   => home_url('/'),
    ));
}
add_action('wp_enqueue_scripts', 'dealeldorado_scripts');

function dealeldorado_widgets_init() {
    register_sidebar(array(
        'name'          => __('Barre Latérale', 'dealeldorado'),
        'id'            => 'sidebar-main',
        'before_widget' => '<div class="ded-widget card mb-3 border-0 shadow-sm"><div class="card-body p-3">',
        'after_widget'  => '</div></div>',
        'before_title'  => '<h5 class="widget-title fw-bold mb-3 pb-2 border-bottom">',
        'after_title'   => '</h5>',
    ));
    register_sidebar(array(
        'name'          => __('Footer Zone 1', 'dealeldorado'),
        'id'            => 'footer-1',
        'before_widget' => '<div class="footer-widget mb-4">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="footer-widget-title text-white fw-bold mb-3">',
        'after_title'   => '</h5>',
    ));
    register_sidebar(array(
        'name'          => __('Footer Zone 2', 'dealeldorado'),
        'id'            => 'footer-2',
        'before_widget' => '<div class="footer-widget mb-4">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="footer-widget-title text-white fw-bold mb-3">',
        'after_title'   => '</h5>',
    ));
    register_sidebar(array(
        'name'          => __('Footer Zone 3', 'dealeldorado'),
        'id'            => 'footer-3',
        'before_widget' => '<div class="footer-widget mb-4">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="footer-widget-title text-white fw-bold mb-3">',
        'after_title'   => '</h5>',
    ));
}
add_action('widgets_init', 'dealeldorado_widgets_init');

function dealeldorado_body_classes($classes) {
    if (is_front_page()) $classes[] = 'ded-homepage';
    if (is_search()) $classes[] = 'ded-search-page';
    if (is_singular('post')) $classes[] = 'ded-article';
    return $classes;
}
add_filter('body_class', 'dealeldorado_body_classes');

// Pagination
function dealeldorado_pagination() {
    $args = array(
        'prev_text' => '<i class="fas fa-chevron-left"></i> Précédent',
        'next_text' => 'Suivant <i class="fas fa-chevron-right"></i>',
        'type'      => 'plain',
    );
    $pagination = paginate_links($args);
    if ($pagination) {
        echo '<nav class="ded-pagination d-flex justify-content-center my-4">' . $pagination . '</nav>';
    }
}

// Get popular categories with icons
function dealeldorado_get_categories() {
    $cat_icons = array(
        'electronique'      => 'fa-laptop',
        'telephone'         => 'fa-mobile-alt',
        'informatique'      => 'fa-desktop',
        'maison'            => 'fa-home',
        'mode'              => 'fa-tshirt',
        'sport'             => 'fa-running',
        'beaute'            => 'fa-spa',
        'jardin'            => 'fa-leaf',
        'auto'              => 'fa-car',
        'jouets'            => 'fa-gamepad',
        'cuisine'           => 'fa-utensils',
        'livres'            => 'fa-book',
        'musique'           => 'fa-music',
        'voyage'            => 'fa-plane',
        'default'           => 'fa-tag',
    );
    return $cat_icons;
}

// Remove WooCommerce default styles (we have our own)
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

// Excerpt length
add_filter('excerpt_length', function() { return 20; });
add_filter('excerpt_more', function() { return '...'; });

// Search redirect to results page
function dealeldorado_search_redirect() {
    if (is_search() && isset($_GET['s']) && empty($_GET['s'])) {
        wp_redirect(home_url('/'));
        exit;
    }
}
add_action('template_redirect', 'dealeldorado_search_redirect');
