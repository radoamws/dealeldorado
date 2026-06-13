<?php

namespace ContentEgg\application\components;

defined('\\ABSPATH') || exit;

use ContentEgg\application\Plugin;
use ContentEgg\application\admin\LicConfig;
use ContentEgg\application\helpers\TemplateHelper;

/**
 * LManager class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class LManager
{
    const CACHE_TTL = 86400;

    private $data = null;
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function adminInit()
    {
        \add_action('admin_notices', array($this, 'displayNotice'));
        $this->hideNotice();
    }

    public function getData($force = false)
    {
        if (!LicConfig::getInstance()->option('license_key'))
        {
            return array();
        }

        if (!$force && $this->data !== null)
        {
            return $this->data;
        }

        $this->data = $this->getCache();
        if ($this->data === false || $force)
        {

            $data = $this->remoteRetrieve();
            if (!$data || !is_array($data))
            {
                $data = array();
            }

            $this->data = $data;
            $this->saveCache($this->data);
        }

        return $this->data;
    }

    public function isExpired(): bool
    {
        $data = $this->getData();
        $expiry = $data['expiry_date'] ?? null;

        if (! is_numeric($expiry))
        {
            return false;
        }

        return time() >= (int) $expiry;
    }

    public function remoteRetrieve()
    {
        $response = Plugin::apiRequest($this->getRequestArray('license'));
        if (false === $response)
        {
            return false;
        }

        $result = json_decode($response['body'], true);
        if (null === $result)
        {
            return false;
        }

        return $result;
    }

    public function saveCache($data)
    {
        \set_transient(Plugin::getShortSlug() . '_' . 'ldata', $data, self::CACHE_TTL);
    }

    public function getCache()
    {
        return \get_transient(Plugin::getShortSlug() . '_' . 'ldata');
    }

    public function deleteCache()
    {
        \delete_transient(Plugin::getShortSlug() . '_' . 'ldata');
    }

    private function getRequestArray($cmd)
    {
        return array(
            'cmd' => $cmd,
            'd' => parse_url(\site_url(), PHP_URL_HOST),
            'p' => Plugin::product_id,
            'v' => Plugin::version(),
            'key' => LicConfig::getInstance()->option('license_key')
        );
    }

    public function isConfigPage()
    {
        if ($GLOBALS['pagenow'] == 'admin.php' && isset($_GET['page']) && $_GET['page'] == 'content-egg-lic')
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function displayNotice()
    {
        if (LManager::isNulled() && time() > 1634198091)
        {
            $notice_date = \get_option(Plugin::slug . '_nulled_notice_date', 0);
            if ($notice_date && time() > $notice_date + 86400 * 3)
            {
                LManager::deactivateLic();

                return;
            }

            //$this->displayNulledNotice();

            return;
        }

        if (!$data = LManager::getInstance()->getData())
        {
            return;
        }

        if ($data['activated_on'] && $data['activated_on'] != preg_replace('/^www\./', '', strtolower(parse_url(\site_url(), PHP_URL_HOST))))
        {
            $this->displayLicenseMismatchNotice();

            return;
        }

        if (time() >= $data['expiry_date'])
        {
            $this->displayExpiredNotice($data);

            return;
        }

        $days_left = floor(($data['expiry_date'] - time()) / 3600 / 24);
        if ($days_left >= 0 && $days_left <= 21)
        {
            $this->displayExpiresSoonNotice($data);

            return;
        }

        if ($this->isConfigPage())
        {
            $this->displayActiveNotice($data);

            return;
        }
    }

    public function displayActiveNotice(array $data)
    {
        $this->addInlineCss();

        $expiry_timestamp          = isset($data['expiry_date']) ? (int) $data['expiry_date'] : 0;
        $current_timestamp         = current_time('timestamp', true);
        $days_left                 = max(0, (int) floor(($expiry_timestamp - $current_timestamp) / DAY_IN_SECONDS));
        $days_left_i18n            = number_format_i18n($days_left);
        $support_expiry_timestamp  = isset($data['support_expiry_date']) ? (int) $data['support_expiry_date'] : 0;
        $is_lifetime               = $days_left > 365 * 50;

        $status        = isset($data['status']) ? sanitize_key($data['status']) : 'inactive';
        $status_class  = strtolower($status);
        $status_label  = strtoupper($status);

        $extend_discount = isset($data['extend_discount']) ? (int) $data['extend_discount'] : 0;

        $purchase_uri = '/product/purchase/1017';
        $purchase_url = Plugin::website . '/login?return=' . urlencode($purchase_uri);
        $date_format  = get_option('date_format');

        echo '<div class="notice notice-success egg-notice" style="padding:15px;">';

        // Keep HTML structure in code; translate only the text parts.
        printf(
            '%s <span class="egg-label egg-label-%s">%s</span>.',
            esc_html__('License status:', 'content-egg'),
            esc_attr($status_class),
            esc_html($status_label)
        );

        if ('active' === $status)
        {
            echo ' ' . esc_html__('You are receiving automatic updates.', 'content-egg');
        }

        if ($is_lifetime)
        {
            echo '<div style="color:green; margin-top:10px;">'
                . esc_html__('Lifetime updates included.', 'content-egg')
                . '</div>';

            $extend_btn_txt = __('Extend support', 'content-egg'); // Escaped on output below.
        }
        else
        {
            $expiry_local = date_i18n($date_format, $expiry_timestamp);

            printf(
                '<div style="margin-top:10px;">%s</div>',
                sprintf(
                    esc_html__('Expires on %1$s (%2$s days left).', 'content-egg'),
                    esc_html($expiry_local),
                    esc_html($days_left_i18n)
                )
            );

            $extend_btn_txt = __('Extend now', 'content-egg'); // Escaped on output below.
        }

        if ($is_lifetime && ! empty($support_expiry_timestamp))
        {
            $now             = current_time('timestamp');
            $expiry_display  = date_i18n($date_format, $support_expiry_timestamp);

            if ($support_expiry_timestamp < $now)
            {
                printf(
                    '<div class="support-expired" style="margin-top:10px;">%s</div>',
                    sprintf(
                        esc_html__('Support expired on %s.', 'content-egg'),
                        esc_html($expiry_display)
                    )
                );
            }
            else
            {
                printf(
                    '<div class="support-active" style="margin-top:10px;">%s</div>',
                    sprintf(
                        esc_html__('Support expires on %s.', 'content-egg'),
                        esc_html($expiry_display)
                    )
                );
            }
        }

        echo '<div style="margin-top:20px;">';
        $this->displayCheckAgainButton();

        printf(
            ' <a class="button-primary" target="_blank" rel="noopener noreferrer" href="%s">&#10003; %s</a>',
            esc_url($purchase_url),
            esc_html($extend_btn_txt)
        );

        if ($extend_discount > 0)
        {
            printf(
                ' <small>%s</small>',
                esc_html__('with a discount', 'content-egg')
            );
        }

        echo '</div></div>';
    }

    public function displayExpiresSoonNotice(array $data)
    {
        if (\get_transient('cegg_hide_notice_lic_expires_soon') && !$this->isConfigPage())
        {
            return;
        }

        $this->addInlineCss();
        $purchase_uri = '/product/purchase/1017';
        $days_left = floor(($data['expiry_date'] - time()) / 3600 / 24);
        echo '<div class="notice notice-warning egg-notice">';
        echo '<p>';
        if (!$this->isConfigPage())
        {
            $hide_notice_uri = \add_query_arg(array(
                'cegg_hide_notice' => 'lic_expires_soon',
                '_cegg_notice_nonce' => \wp_create_nonce('hide_notice')
            ), $_SERVER['REQUEST_URI']);
            printf(
                '<a href="%s" class="egg-notice-close notice-dismiss">%s</a>',
                esc_url($hide_notice_uri),
                esc_html__('Dismiss', 'content-egg')
            );
        }
        echo '<strong>' . esc_html__('License expires soon', 'content-egg') . '</strong><br />';
        $expiry_gmt = gmdate('F d, Y H:i', $data['expiry_date']) . ' GMT';
        printf(
            '%s',
            sprintf(
                esc_html__('Your %1$s license expires at %2$s (%3$d days left).', 'content-egg'),
                esc_html(Plugin::getName()),
                esc_html($expiry_gmt),
                (int) $days_left
            )
        );
        echo ' ' . esc_html__('You will not receive automatic updates, bug fixes, and technical support.', 'content-egg');
        echo '</p>';
        echo '<p>';
        $this->displayCheckAgainButton();
        $purchase_url = Plugin::website . '/login?return=' . urlencode($purchase_uri);
        printf(
            ' <a class="button-primary" target="_blank" href="%s">%s</a>',
            esc_url($purchase_url),
            '&#10003; ' . esc_html__('Extend now', 'content-egg')
        );
        if ((int) $data['extend_discount'])
        {
            printf(
                ' <span class="egg-label egg-label-success">%s</span>',
                sprintf(
                    esc_html__('with a %d%% discount', 'content-egg'),
                    (int) $data['extend_discount']
                )
            );
        }
        echo '</p>';
        echo '</div>';
    }

    public function displayExpiredNotice(array $data)
    {
        if (\get_transient('cegg_hide_notice_lic_expired') && !$this->isConfigPage())
        {
            return;
        }

        $this->addInlineCss();
        $purchase_uri = '/product/purchase/1017';
        echo '<div class="notice notice-error egg-notice">';
        echo '<p>';

        if (!$this->isConfigPage())
        {
            $hide_notice_uri = \add_query_arg(array(
                'cegg_hide_notice' => 'lic_expired',
                '_cegg_notice_nonce' => \wp_create_nonce('hide_notice')
            ), $_SERVER['REQUEST_URI']);
            printf(
                '<a href="%s" class="egg-notice-close notice-dismiss">%s</a>',
                esc_url($hide_notice_uri),
                esc_html__('Dismiss', 'content-egg')
            );
        }

        echo '<strong>' . esc_html__('License expired', 'content-egg') . '</strong><br />';
        printf(
            '%s',
            sprintf(
                esc_html__('Your %1$s license expired on %2$s.', 'content-egg'),
                esc_html(Plugin::getName()),
                esc_html(gmdate('F d, Y H:i', $data['expiry_date']) . ' GMT')
            )
        );
        echo ' ' . esc_html__('You are not receiving automatic updates, bug fixes, and technical support.', 'content-egg');
        echo '</p>';
        echo '<p>';
        $this->displayCheckAgainButton();
        $purchase_url = Plugin::website . '/login?return=' . urlencode($purchase_uri);
        printf(
            ' <a class="button-primary" target="_blank" href="%s">%s</a>',
            esc_url($purchase_url),
            '&#10003; ' . esc_html__('Renew now', 'content-egg')
        );
        echo '</p></div>';
    }

    public function displayLicenseMismatchNotice()
    {
        $this->addInlineCss();
        echo '<div class="notice notice-error egg-notice"><p>';
        printf('<img src="%s" width="40" />', esc_url(\ContentEgg\PLUGIN_RES . '/img/logo.png'));
        echo '<strong>' . esc_html__('License mismatch', 'content-egg') . '</strong><br />';
        printf(
            '%s',
            sprintf(
                esc_html__("Your %s license doesn't match your current domain.", 'content-egg'),
                esc_html(Plugin::getName())
            )
        );
        echo ' ';
        printf(
            wp_kses(
                __('If you wish to continue using the plugin then you must <a target="_blank" href="%1$s">revoke</a> the license and then <a href="%2$s">reactivate</a> it again or <a target="_blank" href="%3$s">buy a new license</a>.', 'content-egg'),
                array('a' => array('href' => array(), 'target' => array()))
            ),
            esc_url(Plugin::panelUri),
            esc_url(\get_admin_url(\get_current_blog_id(), 'admin.php?page=content-egg-lic')),
            esc_url('https://www.keywordrush.com/contentegg/pricing')
        );
        echo '</p></div>';
    }

    public function displayCheckAgainButton()
    {
        $action_url = \get_admin_url(\get_current_blog_id(), 'admin.php?page=content-egg-lic');
        printf('<form style="display: inline;" action="%s" method="POST">', esc_url($action_url));
        echo '<input type="hidden" name="cegg_cmd" id="cegg_cmd" value="refresh" />';
        echo '<input type="hidden" name="nonce_refresh" value="' . \esc_attr(\wp_create_nonce('license_refresh')) . '"/>';
        printf('<input type="submit" name="submit3" id="submit3" class="button" value="&#8635; %s" />', esc_attr__('Check again', 'content-egg'));
        echo '</form>';
    }

    public function hideNotice()
    {
        if (!isset($_GET['cegg_hide_notice']))
        {
            return;
        }

        if (!isset($_GET['_cegg_notice_nonce']) || !\wp_verify_nonce($_GET['_cegg_notice_nonce'], 'hide_notice'))
        {
            return;
        }

        $notice = \sanitize_text_field(wp_unslash($_GET['cegg_hide_notice']));

        if (!in_array($notice, array('lic_expires_soon', 'lic_expired')))
        {
            return;
        }

        if ($notice == 'lic_expires_soon')
        {
            $expiration = 7 * 24 * 3600;
        }
        elseif ($notice == 'lic_expired')
        {
            $expiration = 90 * 24 * 3600;
        }
        else
        {
            $expiration = 0;
        }

        \set_transient('cegg_hide_notice_' . $notice, true, $expiration);

        \wp_redirect(\remove_query_arg(array(
            'cegg_hide_notice',
            '_cegg_notice_nonce'
        ), \wp_unslash($_SERVER['REQUEST_URI'])));
        exit;
    }

    public function addInlineCss()
    {
        echo '<style>.egg-notice a.egg-notice-close {position:static;float:right;top:0;right0;padding:0;margin-top:-20px;line-height:1.23076923;text-decoration:none;}.egg-notice a.egg-notice-close::before{position: relative;top: 18px;left: -20px;}.egg-notice img {float:left;width:40px;padding-right:12px;}</style>';
    }

    public function displayNulledNotice()
    {
        $activation_date = \get_option(Plugin::slug . '_first_activation_date', false);
        if ($activation_date && $activation_date < time() + 86400 * 3)
        {
            return;
        }

        $notice_date = \get_option(Plugin::slug . '_nulled_notice_date');
        if (!$notice_date)
        {
            $notice_date = time();
            \update_option(Plugin::slug . '_nulled_notice_date', $notice_date);
        }
        $valid_date = $notice_date + 86400 * 2;

        $this->addInlineCss();
        echo '<div class="notice notice-error egg-notice" style="padding: 10px;">';
        printf('<img src="%s" width="40" />', esc_url(\ContentEgg\PLUGIN_RES . '/img/logo.png'));
        echo '<strong>Cracked WordPress Plugin Can Kill Your Site</strong><br />';
        printf(
            '<p>%s</p>',
            sprintf(
                /* translators: %s: Plugin name. */
                'You are using a cracked version of %s plugin. This is an illegal and dangerous copy of the plugin.',
                esc_html(Plugin::getName())
            )
        );
        echo '<br/>Cracked plugins often have backdoors and other malware injected into code that is used to get full third-party access to your site, distribute SEO spam, viruses and redirect site visitors. Your site will be probably blacklisted by Google.</p>';
        echo '<p>Please note: You can purchase Content Egg only on our <a target="_blank" href="https://www.keywordrush.com/?utm_source=cegg&utm_medium=referral&utm_campaign=legal">official site</a>. If you purchased your pirated copy on any other site, we recommend requesting a refund and reinstalling the plugin (your existing settings and plugin data are safe!).</p>';
        echo '<p>The official version includes <u>direct support, automatic updates</u> and a guarantee of proper work.</p>';

        if ($valid_date > time())
        {
            printf('Use code <b>LEGAL25</b> for a 25%% discount (valid until %s).', esc_html(TemplateHelper::dateFormatFromGmt($valid_date, false)));
            echo '<br><br><a target="_blank" class="button button-primary button-large" href="https://www.keywordrush.com/contentegg/pricing?ref=LEGAL25&utm_source=cegg&utm_medium=referral&utm_campaign=legal">Apply Coupon</a>';
        }
        else
        {
            echo '<a target="_blank" class="button button-primary button-large" href="https://www.keywordrush.com/contentegg/pricing?utm_source=cegg&utm_medium=referral&utm_campaign=legal">Buy Now</a>';
        }

        echo '</div>';
    }

    public static function isNulled()
    {
        if (!Plugin::isPro())
        {
            return false;
        }

        $l = LicConfig::getInstance()->option('license_key', false);

        if (!$l && Plugin::isEnvato())
        {
            return false;
        }

        if (!LManager::isValidLicFormat($l))
        {
            return true;
        }

        if (in_array(md5($l), LManager::getNulledLics()))
        {
            return true;
        }

        return false;
    }

    public static function isValidLicFormat($value)
    {
        if (preg_match('/[^0-9a-zA-Z_~\-]/', $value))
        {
            return false;
        }
        if (strlen($value) != 32 && strlen($value) != 36)
        {
            return false;
        }

        return true;
    }

    public static function getNulledLics()
    {
        return [];
    }

    public static function deactivateLic()
    {
        \update_option(Plugin::slug . '_nulled_key', LicConfig::getInstance()->option('license_key'));
        \update_option(Plugin::slug . '_nulled_deactiv_date', time());
        \delete_option(LicConfig::getInstance()->option_name());
    }
}
