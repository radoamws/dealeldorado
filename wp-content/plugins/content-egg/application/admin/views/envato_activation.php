<?php defined('\ABSPATH') || exit; ?>
<h2>Content Egg <?php esc_html_e('activation', 'content-egg') ?></h2>
<?php settings_errors(); ?>

<p><?php esc_html_e('Activate your Content Egg license to unlock premium features, receive automatic updates, access your user dashboard, and get official support.', 'content-egg'); ?></p>
<form action="options.php" method="POST">
	<?php settings_fields($page_slug); ?>
	<table class="form-table">
		<?php do_settings_fields($page_slug, 'default'); ?>
	</table>
	<?php submit_button(); ?>
</form>