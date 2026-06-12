<?php defined('\ABSPATH') || exit; ?>
<?php if (! empty($item['extra']['keySpecs'])): ?>
    <div class="cegg-features-box">
        <div class="mt-0 h5"><?php esc_html_e('Highlights', 'content-egg-tpl'); ?></div>
        <ul class="cegg-feature-list small">
            <?php foreach ($item['extra']['keySpecs'] as $spec): ?>
                <li><?php echo \esc_html($spec); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>