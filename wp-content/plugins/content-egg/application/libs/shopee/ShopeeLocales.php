<?php

namespace ContentEgg\application\libs\shopee;

defined('\ABSPATH') || exit;

/**
 * ShopeeLocales class
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class ShopeeLocales
{
    static public $locales = array(
        'id' => array(
            'Indonesia',
            'shopee.co.id',
            'IDR',
        ),
        'tw' => array(
            'Taiwan',
            'shopee.tw',
            'TWD',
        ),
        'vn' => array(
            'Vietnam',
            'shopee.vn',
            'VND',
        ),
        'th' => array(
            'Thailand',
            'shopee.co.th',
            'THB',
        ),
        'ph' => array(
            'Philippines',
            'shopee.ph',
            'PHP',
        ),
        'pl' => array(
            'Polska',
            'shopee.pl',
            'PLN',
        ),
        'my' => array(
            'Malaysia',
            'shopee.com.my',
            'MYR',
        ),
        'sg' => array(
            'Singapore',
            'shopee.sg',
            'SGD',
        ),
        'br' => array(
            'Brazil',
            'shopee.com.br',
            'BRL',
        ),
        'mx' => array(
            'Mexico',
            'shopee.com.mx',
            'MXN',
        ),
        /*
        'co' => array(
            'Colombia',
            'shopee.com.co',
            'COP',
        ),
        */
        /*
        'cl' => array(
            'Chile',
            'shopee.cl',
            'CPL',
        ),
        */
    );

    static public function locales()
    {
        return self::$locales;
    }

    static public function getLocale($locale)
    {
        $locales = self::$locales;
        if (isset($locales[$locale]))
            return $locales[$locale];
        else
            throw new \Exception("Locale " . esc_html($locale) . " does not exist.");
    }

    static public function getApiHost($locale)
    {
        $data = self::getLocale($locale);

        return $data[1];
    }

    static public function getApiEndpoint($locale)
    {
        return 'https://open-api.affiliate.' . self::getApiHost($locale);
    }

    static public function getDomain($locale)
    {
        $data = self::getLocale($locale);

        return $data[1];
    }

    static public function getCurrencyCode($locale)
    {
        $data = self::getLocale($locale);

        return $data[2];
    }
}
