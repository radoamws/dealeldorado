<?php

use ContentEgg\application\helpers\TemplateHelper;

defined('\ABSPATH') || exit;
/*
  Name: Simple
 */

?>

<div class="container px-0 mb-5 mt-1" <?php $this->colorMode(); ?>>

    <div class="row g-3<?php TemplateHelper::rowCols($params, 'row-cols-1 row-cols-md-3'); ?>">
        <?php foreach ($items as $item) : ?>
            <?php $this->setItem($item); ?>
            <div class="col text-center">
                <?php if ($this->isVisible('img')): ?>
                    <div class="position-relative">
                        <div class="ratio<?php TemplateHelper::imgRatio($params, 'ratio-1x1'); ?>">
                            <?php TemplateHelper::displayImage($item, 190, 170, array('class' => 'object-fit-scale rounded')); ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="text-center">
                    <?php if ($this->isVisible('title')): ?>
                        <?php TemplateHelper::title($item, 'card-title fs-6 fw-normal lh-base cegg-hover-title cegg-text-truncate-2 mt-1 small', 'div', $params); ?>
                    <?php endif; ?>
                    <?php if ($this->isVisible('description', false)): ?>
                        <div class="cegg-desc-small card-text small lh-sm pt-3"><?php echo \wp_kses_post($item['description']); ?></div>
                    <?php endif; ?>
                    <div class="small text-secondary">
                        <?php
                        printf(
                            wp_kses(
                                /* translators: %s: link to the photo on Flickr */
                                __('Photo %s on Flickr', 'content-egg-tpl'),
                                array('a' => array('href' => array(), 'target' => array(), 'rel' => array()))
                            ),
                            '<a href="' . esc_url($item['url']) . '" target="_blank" rel="nofollow">' . esc_html($item['extra']['author']) . '</a>'
                        );
                        ?>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>