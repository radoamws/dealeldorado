<?php
defined('\ABSPATH') || exit;
/*
  Name: Simple
 */


use ContentEgg\application\helpers\TemplateHelper;

?>

<div class="container px-0 mb-5 mt-1" <?php $this->colorMode(); ?>>
    <?php if ($title): ?>
        <h3><?php echo esc_html($title); ?></h3>
    <?php endif; ?>
    <?php foreach ($items as $item): ?>
        <div class="row">
            <?php if ($item['img']): ?>
                <div class="col-md-2">
                    <img class="media-object img-thumbnail" src="<?php echo esc_url($item['img']); ?>"
                        alt="<?php echo esc_attr($item['title']); ?>" />
                </div>
            <?php endif; ?>
            <div class="col-md-10">
                <p>
                    <?php echo wp_kses_post($item['description']); ?>
                    <br>
                    <small class="text-muted">
                        <?php echo esc_html(TemplateHelper::formatDate($item['extra']['date'])); ?> -
                        <a<?php TemplateHelper::printRel(); ?> target="_blank"
                            href="<?php echo esc_url_raw($item['url']); ?>">@<?php echo esc_html($item['extra']['author']); ?></a>
                    </small>
                </p>
            </div>
        </div>
    <?php endforeach; ?>
</div>