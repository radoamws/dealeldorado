<?php
defined('ABSPATH') || exit;

/**
 * Handlers AJAX pour la recherche live et les alertes prix.
 */
class DED_Ajax {

    private static ?DED_Ajax $instance = null;

    public static function instance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(): void {
        add_action('wp_ajax_ded_search_suggestions', array($this, 'search_suggestions'));
        add_action('wp_ajax_nopriv_ded_search_suggestions', array($this, 'search_suggestions'));
        add_action('wp_ajax_ded_price_alert', array($this, 'price_alert'));
        add_action('wp_ajax_nopriv_ded_price_alert', array($this, 'price_alert'));
    }

    /**
     * Retourne des suggestions de recherche depuis les articles WordPress.
     */
    public function search_suggestions(): void {
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'ded_nonce')) {
            wp_send_json_error('Nonce invalide');
        }

        $query = sanitize_text_field($_POST['query'] ?? '');
        if (strlen($query) < 2) {
            wp_send_json_success(array());
        }

        $posts = get_posts(array(
            's'              => $query,
            'numberposts'    => 8,
            'post_status'    => 'publish',
            'post_type'      => array('post', 'page'),
        ));

        $suggestions = array();
        foreach ($posts as $post) {
            $suggestions[] = array(
                'title' => get_the_title($post->ID),
                'url'   => get_permalink($post->ID),
            );
        }

        // Also add the search URL as the last item
        $suggestions[] = array(
            'title' => 'Voir tous les résultats pour "' . esc_html($query) . '"',
            'url'   => home_url('/?s=' . urlencode($query)),
        );

        wp_send_json_success($suggestions);
    }

    /**
     * Enregistre une alerte prix (simple email log).
     */
    public function price_alert(): void {
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'ded_nonce')) {
            wp_send_json_error('Nonce invalide');
        }

        $email   = sanitize_email($_POST['email'] ?? '');
        $post_id = intval($_POST['post_id'] ?? 0);

        if (!is_email($email)) {
            wp_send_json_error('Email invalide');
        }

        // Store alert in wp_options as a simple list
        $alerts   = get_option('ded_price_alerts', array());
        $alerts[] = array(
            'email'      => $email,
            'post_id'    => $post_id,
            'post_title' => get_the_title($post_id),
            'created_at' => time(),
        );
        update_option('ded_price_alerts', $alerts);

        // Send confirmation email
        wp_mail(
            $email,
            '✅ Alerte prix créée - DealElDorado',
            sprintf(
                "Bonjour,\n\nVotre alerte prix pour \"%s\" a bien été créée.\n\nVous serez notifié dès que le prix baisse.\n\nL'équipe DealElDorado\nhttps://dealeldorado.com",
                get_the_title($post_id)
            )
        );

        wp_send_json_success(array('message' => 'Alerte créée avec succès !'));
    }
}
