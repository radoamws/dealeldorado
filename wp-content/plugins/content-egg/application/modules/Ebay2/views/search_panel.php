<?php defined('\ABSPATH') || exit; ?>

<?php
$module = \ContentEgg\application\components\ModuleManager::factory($module_id);
$config = $module->getConfigInstance();

$locales = \ContentEgg\application\modules\Ebay2\Ebay2Config::getLocalesList();
$default_locale = $config->option('locale');
?>

<select class="form-control form-control-sm" ng-model="query_params.<?php echo esc_attr($module_id); ?>.locale" ng-init="query_params.<?php echo esc_attr($module_id); ?>.locale = '<?php echo esc_attr($default_locale); ?>'">
    <?php foreach ($locales as $value => $name) : ?>
        <option value="<?php echo \esc_attr($value); ?>"><?php echo \esc_html($name); ?></option>
    <?php endforeach; ?>
</select>

<input type="text" class="form-control form-control-sm" ng-model="query_params.<?php echo esc_attr($module_id); ?>.min_price" ng-init="query_params.<?php echo esc_attr($module_id); ?>.min_price = ''" placeholder="<?php esc_html_e('Min. price', 'content-egg') ?>" />
<input type="text" class="form-control form-control-sm" ng-model="query_params.<?php echo esc_attr($module_id); ?>.max_price" ng-init="query_params.<?php echo esc_attr($module_id); ?>.max_price = ''" placeholder="<?php esc_html_e('Max. price', 'content-egg') ?>" />