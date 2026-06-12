<?php

namespace ContentEgg\application\admin;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\Config;
use ContentEgg\application\Plugin;
use ContentEgg\application\components\LManager;;

/**
 * LicConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class LicConfig extends Config
{

	public function page_slug()
	{
		if ($this->option('license_key') || \get_option(Plugin::slug . '_env_install'))
		{
			return Plugin::slug . '-lic';
		}
		else
		{
			return Plugin::slug;
		}
	}

	public function option_name()
	{
		return Plugin::slug . '_lic';
	}

	public function add_admin_menu()
	{
		$this->resetLicense();
		$this->refreshLicense();
		\add_submenu_page(Plugin::slug, __('License', 'content-egg') . ' &lsaquo; Content Egg', __('License', 'content-egg'), 'manage_options', $this->page_slug(), array(
			$this,
			'settings_page'
		));

		global $submenu;
		if (!Plugin::isTooMuchNicheActive())
			$submenu['content-egg'][] = array('<b style="color: #75b798;">TMN Plugin</b>', 'manage_options', 'https://www.keywordrush.com/toomuchniche?utm_source=cegg&utm_medium=referral&utm_campaign=tmnplugin');
	}

	protected function options()
	{
		return array(
			'license_key' => array(
				'title'       => __('License key', 'content-egg'),
				'description' => __('Please enter your license key.', 'content-egg') . ' ' . sprintf(__('You can find your key on the %s page.', 'content-egg'), '<a href="' . \esc_url(Plugin::panelUri) . '" target="_blank">My Account</a>') . ' ' .
					sprintf(__("If you don't have one yet, you can buy it from our <a target='_blank' href='%s'>official website</a>.", 'content-egg'), Plugin::pluginSiteUrl()),
				'callback'    => array($this, 'render_input'),
				'default'     => '',
				'validator'   => array(
					'trim',
					array(
						'call'    => array('\ContentEgg\application\helpers\FormValidator', 'required'),
						'message' => __('The "License key" can not be empty', 'content-egg'),
					),
					array(
						'call'    => array($this, 'licFormat'),
						'message' => __('Invalid license key', 'content-egg'),
					),
					array(
						'call'    => array($this, 'activatingLicense'),
						'message' => __(
							'The license key could not be validated. Please double-check that it was entered correctly. If the key is valid and the issue persists, your server may be blocking outgoing connections to keywordrush.com. In that case, please contact your hosting provider or <a href="http://www.keywordrush.com/contact" target="_blank" rel="noopener noreferrer">our support team</a> for help.',
							'content-egg'
						),
					),

					array(
						'call' => array($this, 'resetLicInfo'),
					),
				),
				'section'     => 'default',
			),
		);
	}

	public function settings_page()
	{
		PluginAdmin::render('lic_settings', array('page_slug' => $this->page_slug()));
	}

	public function licFormat($value)
	{
		return true;
		if (preg_match('/[^0-9a-zA-Z_~\-]/', $value))
		{
			return false;
		}
		if (strlen($value) !== 32 && !preg_match('/^\w{8}-\w{4}-\w{4}-\w{4}-\w{12}$/', $value))
		{
			return false;
		}

		return true;
	}

	public function activatingLicense($value)
	{
		return true;
		$resp = Plugin::apiRequest([
			'cmd' => 'activate',
			'key' => $value,
			'd'   => parse_url(site_url(), PHP_URL_HOST),
			'p'   => Plugin::product_id,
			'v'   => Plugin::version(),
		]);

		if (false === $resp)
		{
			add_settings_error(
				'license_key',
				'license_key',
				__('Could not connect to licensing server.', 'content-egg')
			);
			return false;
		}

		$result = json_decode($resp['body'], true);
		if (JSON_ERROR_NONE !== json_last_error() || ! is_array($result))
		{
			add_settings_error(
				'license_key',
				'license_key',
				__('Invalid response from licensing server.', 'content-egg')
			);
			return false;
		}

		if (isset($result['status']) && 'valid' === $result['status'])
		{
			delete_option(Plugin::getShortSlug() . '_sys_status');
			delete_option(Plugin::getShortSlug() . '_sys_deadline');
			return true;
		}

		if (isset($result['status']) && 'error' === $result['status'])
		{
			add_settings_error(
				'license_key',
				'license_key',
				$result['message']
			);
			return false;
		}

		add_settings_error(
			'license_key',
			'license_key',
			__('Unexpected response from licensing server.', 'content-egg')
		);
		return false;
	}

	private function resetLicense()
	{
		if ($GLOBALS['pagenow'] != 'admin.php' || empty($_GET['page']) || $_GET['page'] != 'content-egg-lic')
		{
			return;
		}

		if (isset($_POST['cmd']) && $_POST['cmd'] == 'lic_reset')
		{
			return true;
			if (!\current_user_can('delete_plugins') || empty($_POST['nonce_reset']) || !\wp_verify_nonce(sanitize_key($_POST['nonce_reset']), 'license_reset'))
			{
				\wp_die('You don\'t have access to this page.');
			}

			if (Plugin::isEnvato())
			{
				$redirect_url = \get_admin_url(\get_current_blog_id(), 'admin.php?page=content-egg-lic');
			}
			else
			{
				$redirect_url = \get_admin_url(\get_current_blog_id(), 'plugins.php');
			}

			$response = Plugin::apiRequest([
				'cmd' => 'deactivate',
				'key' => $this->option('license_key'),
				'd'   => parse_url(site_url(), PHP_URL_HOST),
				'p'   => Plugin::product_id,
				'v'   => Plugin::version(),
			]);
			if (!$response)
			{
				$redirect_url = AdminNotice::add2Url($redirect_url, 'license_reset_error', 'error');
			}
			$result = json_decode($response, true);

			if ($result && !empty($result['status']) && $result['status'] === 'valid')
			{
				\delete_option(LicConfig::getInstance()->option_name());
				$redirect_url = AdminNotice::add2Url($redirect_url, 'license_reset_success', 'warning');
			}
			else
			{
				$redirect_url = AdminNotice::add2Url($redirect_url, 'license_reset_error', 'error');
			}

			\wp_safe_redirect($redirect_url);
			exit;
		}
	}

	private function refreshLicense()
	{
		if ($GLOBALS['pagenow'] != 'admin.php' || empty($_GET['page']) || $_GET['page'] != 'content-egg-lic')
		{
			return;
		}

		if (isset($_POST['cegg_cmd']) && $_POST['cegg_cmd'] == 'refresh')
		{
			if (!\current_user_can('delete_plugins') || empty($_POST['nonce_refresh']) || !\wp_verify_nonce(sanitize_key($_POST['nonce_refresh']), 'license_refresh'))
			{
				\wp_die('You don\'t have access to this page.');
			}

			LManager::getInstance()->deleteCache();
		}
	}

	public function resetLicInfo()
	{
		LManager::getInstance()->deleteCache();

		return true;
	}
}
