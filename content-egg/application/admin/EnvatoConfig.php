<?php

namespace ContentEgg\application\admin;

defined('\ABSPATH') || exit;

use ContentEgg\application\Plugin;

/**
 * EnvatoConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class EnvatoConfig extends LicConfig
{

	public function page_slug()
	{
		return Plugin::slug . '-lic';
	}

	protected function options()
	{
		$url = Plugin::pluginPricingUrl('ce_license_form', 'envato_options_page');

		return array(
			'email'       => array(
				'title'       => 'Email <span class="cegg_required">*</span>',
				'description' => sprintf(
					__('After activation, you’ll receive login details for the <a target="_blank" href="%s">User Panel</a>, where you can manage your licenses.', 'content-egg'),
					'https://www.keywordrush.com/panel'
				),
				'callback'    => array($this, 'render_input'),
				'default'     => '',
				'validator'   => array(
					'trim',
					array(
						'call'    => array('\\ContentEgg\\application\\helpers\\FormValidator', 'required'),
						'message' => sprintf(__('The field "%s" cannot be empty.', 'content-egg'), 'Email'),
					),
					array(
						'call'    => array('\\ContentEgg\\application\\helpers\\FormValidator', 'valid_email'),
						'message' => sprintf(__('The "%s" field contains an invalid email address.', 'content-egg'), 'Email'),
					),
				),
			),

			'license_key' => array(
				'title'       => 'Purchase Code <span class="cegg_required">*</span>',
				'description' => sprintf(
					__('Enter your Envato purchase code to activate Content Egg.', 'content-egg') .
						'<br>' .
						__('Not sure where to find your code?', 'content-egg') . ' <a target="_blank" href="%s">' . __('Click here for instructions', 'content-egg') . '</a>.' .
						'<br><br><strong>' . __('Don’t have a license yet?', 'content-egg') . '</strong> ' .
						__('Get yours from the', 'content-egg') . ' <a target="_blank" href="%s">' . __('official Content Egg website', 'content-egg') . '</a> ' .
						__('or', 'content-egg') . ' <a target="_blank" href="https://www.keywordrush.com/bundles?utm_source=cepro&utm_medium=referral&utm_campaign=ce_license_form&utm_content=bundle_offers">' . __('check our Bundle Offers', 'content-egg') . '</a>.',
					'https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-',
					esc_url($url)
				),
				'callback'    => array($this, 'render_input'),
				'default'     => '',
				'validator'   => array(
					'trim',
					array(
						'call'    => array('\\ContentEgg\\application\\helpers\\FormValidator', 'required'),
						'message' => sprintf(__('The field "%s" cannot be empty.', 'content-egg'), 'Purchase Code'),
					),
					array(
						'call'    => array($this, 'licFormat'),
						'message' => sprintf(__('The "%s" field contains an invalid code format.', 'content-egg'), 'Purchase Code'),
					),
					array(
						'call'    => array($this, 'activatingLicense'),
						'message' => __('Plugin activation failed. Please verify your Purchase Code and try again.', 'content-egg'),
						'Purchase code',
					),
				),
			),

			'subscribe'   => array(
				'title'       => '',
				'description' => __('Send me updates and special offers from Content Egg and KeywordRush.', 'content-egg'),
				'callback'    => array($this, 'render_checkbox'),
				'default'     => false,
				'validator'   => array(
					array(
						'call'    => array($this, 'envatoFlag'),
						'message' => __('An error occurred while processing your request.', 'content-egg'),
						'Purchase code',
					),
				),
			),
		);
	}

	public function settings_page()
	{
		PluginAdmin::render('envato_activation', array('page_slug' => $this->page_slug()));
	}

	public function activatingLicense($value)
	{
		return true;
		if (get_settings_errors())
		{
			return false;
		}

		$email = $this->get_submitted_value('email');
		if (empty($email))
		{
			return false;
		}

		$subscribe = (bool) $this->get_submitted_value('subscribe');

		$resp = Plugin::apiRequest([
			'cmd'       => 'envato_activate',
			'key'       => $value,
			'd'         => parse_url(site_url(), PHP_URL_HOST),
			'p'         => Plugin::product_id,
			'v'         => Plugin::version(),
			'email'     => $email,
			'subscribe' => $subscribe,
		]);

		if (false === $resp)
		{
			add_settings_error('license_key', 'license_key_error', __('Connection to the licensing server failed. Please try again later or contact support if the issue persists.', 'content-egg'));
			return false;
		}

		$result = json_decode($resp['body'], true);
		if (JSON_ERROR_NONE !== json_last_error() || ! is_array($result))
		{
			add_settings_error('license_key', 'license_key_error', __('Received an invalid response from the licensing server. Please try again later or contact support if the issue persists.', 'content-egg'));
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
			add_settings_error('license_key', 'license_key_error', $result['message']);
			return false;
		}

		add_settings_error('license_key', 'license_key_error', __('Unexpected response from the licensing server. Please try again later or contact support if this problem continues.', 'content-egg'));
		return false;
	}

	public function envatoFlag()
	{
		return false;
		if (\get_settings_errors())
		{
			return true;
		}

		\update_option(Plugin::slug . '_env_install', time());

		return true;
	}
}
