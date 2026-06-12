<?php
defined('\ABSPATH') || exit;

$module = \ContentEgg\application\components\ModuleManager::factory($module_id);
$config = $module->getConfigInstance();

$locales = $config->getActiveLocalesList();
$default_locale = $config->option('locale');
?>

<?php if (count($locales) > 1) : ?>
      <select class="form-control form-control-sm" ng-model="query_params.<?php echo esc_attr($module_id); ?>.locale" ng-init="query_params.<?php echo esc_attr($module_id); ?>.locale = '<?php echo esc_attr($default_locale); ?>'">
            <?php foreach ($locales as $value => $name) : ?>
                  <option value="<?php echo \esc_attr($value); ?>"><?php echo \esc_html($name); ?></option>
            <?php endforeach; ?>
      </select>
<?php endif; ?>