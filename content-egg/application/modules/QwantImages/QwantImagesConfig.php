<?php

namespace ContentEgg\application\modules\QwantImages;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\ParserModuleConfig;

/**
 * QwantImagesConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link https://www.keywordrush.com
 * @copyright Copyright &copy; 2025 keywordrush.com
 */
class QwantImagesConfig extends ParserModuleConfig
{

	public function options()
	{
		$options = array(
			'entries_per_page'        => array(
				'title'       => __('Results', 'content-egg'),
				'description' => __('Specify the number of results to display for a single search query.', 'content-egg'),
				'callback'    => array($this, 'render_input'),
				'default'     => 10,
				'validator'   => array(
					'trim',
					'absint',
					array(
						'call'    => array('\ContentEgg\application\helpers\FormValidator', 'less_than_equal_to'),
						'arg'     => 100,
						'message' => sprintf(__('The field "%s" can not be more than %d.', 'content-egg'), __('Results', 'content-egg'), 150),
					),
				),
				'section'     => 'default',
			),
			'entries_per_page_update' => array(
				'title'       => __('Results for autoupdates ', 'content-egg'),
				'description' => __('Maximum number of results returned for keyword autoupdates and other automatic searches.', 'content-egg'),
				'callback'    => array($this, 'render_input'),
				'default'     => 6,
				'validator'   => array(
					'trim',
					'absint',
					array(
						'call'    => array('\ContentEgg\application\helpers\FormValidator', 'less_than_equal_to'),
						'arg'     => 100,
						'message' => sprintf(__('The field "%s" can not be more than %d.', 'content-egg'), __('Results', 'content-egg'), 150),
					),
				),
				'section'     => 'default',
			),
			'locale'                  => array(
				'title'            => __('Locale', 'content-egg'),
				'callback'         => array($this, 'render_dropdown'),
				'dropdown_options' => self::locales(),
				'default'          => 'en_US',
				'section'          => 'default',
			),
			'safesearch'              => array(
				'title'            => __('Safe search', 'content-egg'),
				'description'      => __('Filter images for adult content.', 'content-egg'),
				'callback'         => array($this, 'render_dropdown'),
				'dropdown_options' => array(
					''   => __('Return images with adult content', 'content-egg'),
					'on' => __('Do not return images with adult content', 'content-egg'),
				),
				'default'          => 'on',
			),
			'save_img'                => array(
				'title'       => __('Save images', 'content-egg'),
				'description' => __('Save images on server', 'content-egg'),
				'callback'    => array($this, 'render_checkbox'),
				'default'     => false,
				'section'     => 'default',
			),
		);

		$options = array_merge(parent::options(), $options);
		return self::moveRequiredUp($options);
	}

	static public function marketCodes()
	{
		$codes = array(
			'es-AR',
			'en-AU',
			'de-AT',
			'nl-BE',
			'fr-BE',
			'pt-BR',
			'en-CA',
			'fr-CA',
			'es-CL',
			'da-DK',
			'fi-FI',
			'fr-FR',
			'de-DE',
			'zh-HK',
			'en-IN',
			'en-ID',
			'en-IE',
			'it-IT',
			'ja-JP',
			'ko-KR',
			'en-MY',
			'es-MX',
			'nl-NL',
			'en-NZ',
			'no-NO',
			'zh-CN',
			'pl-PL',
			'pt-PT',
			'en-PH',
			'ru-RU',
			'ar-SA',
			'en-ZA',
			'es-ES',
			'sv-SE',
			'fr-CH',
			'de-CH',
			'zh-TW',
			'tr-TR',
			'en-GB',
			'en-US',
			'es-US'
		);

		return array_combine($codes, $codes);
	}

	static public function locales()
	{
		$locales = [
			"bg_bg",
			"br_fr",
			"ca_ad",
			"ca_es",
			"ca_fr",
			"co_fr",
			"cs_cz",
			"cy_gb",
			"da_dk",
			"de_at",
			"de_ch",
			"de_de",
			"ec_ca",
			"el_gr",
			"en_au",
			"en_ca",
			"en_gb",
			"en_ie",
			"en_my",
			"en_nz",
			"en_us",
			"es_ad",
			"es_ar",
			"es_cl",
			"es_co",
			"es_es",
			"es_mx",
			"es_pe",
			"et_ee",
			"eu_es",
			"eu_fr",
			"fc_ca",
			"fi_fi",
			"fr_ad",
			"fr_be",
			"fr_ca",
			"fr_ch",
			"fr_fr",
			"gd_gb",
			"he_il",
			"hu_hu",
			"it_ch",
			"it_it",
			"ko_kr",
			"nb_no",
			"nl_be",
			"nl_nl",
			"pl_pl",
			"pt_ad",
			"pt_pt",
			"ro_ro",
			"sv_se",
			"th_th",
			"zh_cn",
			"zh_hk"
		];
		return array_combine($locales, $locales);
	}
}
