<?php

use ContentEgg\application\helpers\TemplateHelper;

defined('\ABSPATH') || exit;
/*
  Name: Simple
 */

?>

<div class="container px-0 mb-5 mt-1" <?php $this->colorMode(); ?>>

    <?php foreach ($items as $item): ?>
        <?php $this->setItem($item); ?>
        <div class="d-flex mb-4">
            <?php if ($item['img']): ?>
                <div class="flex-shrink-0 me-3">
                    <img style="max-width: 225px;" class="img-thumbnail" src="<?php echo esc_url($item['img']); ?>" alt="<?php echo esc_attr($item['title']); ?>" />
                </div>
            <?php endif; ?>
            <div class="text-body">
                <?php if ($this->isVisible('title')): ?>
                    <?php TemplateHelper::title($item, 'h5', 'div', $params); ?>
                <?php endif; ?>
                <div class="small text-secondary">
                    <?php if ($item['extra']['publisher']): ?>
                        <?php echo esc_html($item['extra']['publisher']); ?>.
                    <?php endif; ?>
                    <?php if ($item['extra']['date']): ?>
                        <?php echo esc_html(date('Y', (int) $item['extra']['date'])); ?>
                    <?php endif; ?>
                    <a target="_blank" rel="nofollow" href="<?php echo esc_url_raw($item['url']); ?>">
                        <img src="<?php echo esc_url(plugins_url('res/gbs_preview.gif', __FILE__)); ?>" alt="<?php esc_attr_e('Preview', 'content-egg'); ?>" />
                    </a>
                </div>
                <div><?php echo wp_kses_post($item['description']); ?></div>
            </div>
        </div>
    <?php endforeach; ?>
</div>