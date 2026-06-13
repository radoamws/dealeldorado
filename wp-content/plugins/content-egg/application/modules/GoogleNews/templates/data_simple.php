<?php
defined('\ABSPATH') || exit;
/*
  Name: Simple
 */
__('Simple', 'content-egg');

use ContentEgg\application\helpers\TemplateHelper;

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
                <small class="text-body-secondary">
                    <?php echo esc_html(TemplateHelper::formatDate($item['extra']['date'])); ?> -
                    <a target="_blank" rel="nofollow"
                        href="<?php echo esc_url_raw($item['url']); ?>">
                        <?php echo \esc_html($item['extra']['source'] ? $item['extra']['source'] : $item['domain']); ?>
                    </a>
                </small>
                <p><?php echo wp_kses_post($item['description']); ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>