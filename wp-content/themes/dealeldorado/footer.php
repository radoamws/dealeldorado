</div><!-- #page-content -->

<footer class="ded-footer mt-5">

    <!-- Newsletter Section -->
    <div class="ded-footer-newsletter py-4">
        <div class="container-xl">
            <div class="row align-items-center">
                <div class="col-md-6 mb-3 mb-md-0">
                    <h4 class="text-white fw-bold mb-1">
                        <i class="fas fa-bell me-2 text-warning"></i>
                        <?php esc_html_e('Alertes prix gratuites', 'dealeldorado'); ?>
                    </h4>
                    <p class="text-white-50 mb-0 small">
                        <?php esc_html_e('Recevez une alerte dès que le prix d\'un produit baisse', 'dealeldorado'); ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <form class="d-flex gap-2" onsubmit="return false;">
                        <input type="email"
                               class="form-control"
                               placeholder="<?php esc_attr_e('Votre adresse email', 'dealeldorado'); ?>">
                        <button type="submit" class="btn btn-warning fw-semibold text-nowrap px-4">
                            <?php esc_html_e("S'abonner", 'dealeldorado'); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Main -->
    <div class="ded-footer-main py-5">
        <div class="container-xl">
            <div class="row g-4">

                <!-- Brand Column -->
                <div class="col-lg-3 col-md-6">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="d-block mb-3">
                        <img src="<?php echo esc_url(DED_THEME_URI . '/assets/images/logo-white.svg'); ?>"
                             alt="<?php bloginfo('name'); ?>"
                             height="40"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                        <span class="ded-footer-brand-text" style="display:none">
                            <span style="color:#f4a261;font-weight:800;font-size:1.5rem">Deal</span><span style="color:white;font-weight:800;font-size:1.5rem">ElDorado</span>
                        </span>
                    </a>
                    <p class="text-white-50 small lh-lg">
                        <?php esc_html_e('Le comparateur de prix intelligent pour trouver les meilleures offres en ligne. Économisez sur vos achats préférés.', 'dealeldorado'); ?>
                    </p>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="ded-social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="ded-social-link" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                        <a href="#" class="ded-social-link" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="ded-social-link" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Column 2: Comparateur -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="footer-widget-title text-white fw-bold mb-3">
                        <?php esc_html_e('Comparateur', 'dealeldorado'); ?>
                    </h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo esc_url(home_url('/electronique')); ?>" class="ded-footer-link">Électronique</a></li>
                        <li><a href="<?php echo esc_url(home_url('/informatique')); ?>" class="ded-footer-link">Informatique</a></li>
                        <li><a href="<?php echo esc_url(home_url('/telephones')); ?>" class="ded-footer-link">Téléphones</a></li>
                        <li><a href="<?php echo esc_url(home_url('/maison')); ?>" class="ded-footer-link">Maison & Jardin</a></li>
                        <li><a href="<?php echo esc_url(home_url('/mode')); ?>" class="ded-footer-link">Mode & Sport</a></li>
                        <li><a href="<?php echo esc_url(home_url('/jeux-jouets')); ?>" class="ded-footer-link">Jeux & Jouets</a></li>
                    </ul>
                    <?php if (is_active_sidebar('footer-1')): ?>
                        <?php dynamic_sidebar('footer-1'); ?>
                    <?php endif; ?>
                </div>

                <!-- Column 3: Information -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white fw-bold mb-3"><?php esc_html_e('Information', 'dealeldorado'); ?></h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo esc_url(home_url('/a-propos')); ?>" class="ded-footer-link">À propos de nous</a></li>
                        <li><a href="<?php echo esc_url(home_url('/comment-ca-marche')); ?>" class="ded-footer-link">Comment ça marche</a></li>
                        <li><a href="<?php echo esc_url(home_url('/blog')); ?>" class="ded-footer-link">Blog & Conseils</a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact')); ?>" class="ded-footer-link">Contact</a></li>
                        <li><a href="<?php echo esc_url(home_url('/sitemap')); ?>" class="ded-footer-link">Plan du site</a></li>
                    </ul>
                    <?php if (is_active_sidebar('footer-2')): ?>
                        <?php dynamic_sidebar('footer-2'); ?>
                    <?php endif; ?>
                </div>

                <!-- Column 4: Legal -->
                <div class="col-lg-3 col-md-6">
                    <h5 class="text-white fw-bold mb-3"><?php esc_html_e('Légal', 'dealeldorado'); ?></h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo esc_url(home_url('/mentions-legales')); ?>" class="ded-footer-link">Mentions légales</a></li>
                        <li><a href="<?php echo esc_url(home_url('/politique-confidentialite')); ?>" class="ded-footer-link">Politique de confidentialité</a></li>
                        <li><a href="<?php echo esc_url(home_url('/cookies')); ?>" class="ded-footer-link">Gestion des cookies</a></li>
                        <li><a href="<?php echo esc_url(home_url('/cgu')); ?>" class="ded-footer-link">CGU</a></li>
                    </ul>
                    <div class="mt-3">
                        <p class="text-white-50 small mb-1">
                            <i class="fas fa-info-circle me-1 text-warning"></i>
                            <?php esc_html_e('Site affilié - Nous percevons une commission sur certains liens.', 'dealeldorado'); ?>
                        </p>
                    </div>
                    <?php if (is_active_sidebar('footer-3')): ?>
                        <?php dynamic_sidebar('footer-3'); ?>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer Bottom -->
    <div class="ded-footer-bottom py-3">
        <div class="container-xl">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
                <p class="text-white-50 small mb-0">
                    &copy; <?php echo date('Y'); ?> <strong class="text-white">DealElDorado</strong>.
                    <?php esc_html_e('Tous droits réservés.', 'dealeldorado'); ?>
                </p>
                <p class="text-white-50 small mb-0">
                    <?php esc_html_e('Domaine final:', 'dealeldorado'); ?>
                    <strong class="text-warning">dealeldorado.com</strong>
                </p>
            </div>
        </div>
    </div>

</footer>

<?php wp_footer(); ?>

<script type="text/javascript">
  var vglnk = {key: 'de86ae09e0cb37231b563892b0b23116'};
  (function(d, t) {var s = d.createElement(t);
    s.type = 'text/javascript';s.async = true;
    s.src = '//cdn.viglink.com/api/vglnk.js';
    var r = d.getElementsByTagName(t)[0];
    r.parentNode.insertBefore(s, r);
  }(document, 'script'));
</script>

</body>
</html>
