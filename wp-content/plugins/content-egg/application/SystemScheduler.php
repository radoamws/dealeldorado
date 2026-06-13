<?php

namespace ContentEgg\application;

defined('\ABSPATH') || exit;

use ContentEgg\application\admin\LicConfig;
use ContentEgg\application\components\Scheduler;;

/**
 * SystemScheduler class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class SystemScheduler extends Scheduler
{
    const CRON_TAG = 'cegg_system_cron';

    public static function getCronTag()
    {
        return self::CRON_TAG;
    }

    public static function run()
    {
        self::checkStatus();
    }

    public static function checkStatus()
    {
        $key = LicConfig::getInstance()->option('license_key');
        if (empty($key))
        {
            return;
        }

        $resp = Plugin::apiRequest([
            'cmd' => 'status',
            'd'   => parse_url(site_url(), PHP_URL_HOST),
            'p'   => Plugin::product_id,
            'v'   => Plugin::version(),
            'key' => $key,
        ]);

        if (false === $resp || 200 !== $resp['code'])
        {
            return;
        }

        $data = json_decode($resp['body'], true);
        if (JSON_ERROR_NONE !== json_last_error() || ! is_array($data))
        {
            return;
        }

        if (isset($data['status']) && 'invalid' === $data['status'])
        {
            update_option(Plugin::getShortSlug() . '_sys_status', 'invalid');
            if (! get_option(Plugin::getShortSlug() . '_sys_deadline', false))
            {
                update_option(Plugin::getShortSlug() . '_sys_deadline', time() + 7 * DAY_IN_SECONDS);
            }
        }
        else
        {
            update_option(Plugin::getShortSlug() . '_sys_status', 'valid');
        }
    }
}
