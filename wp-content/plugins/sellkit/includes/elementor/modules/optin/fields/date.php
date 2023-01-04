<?php

defined( 'ABSPATH' ) || die();

class Sellkit_Elementor_Optin_Field_Date extends Sellkit_Elementor_Optin_Field_Base {

	public static function get_field_type() {
		return 'date';
	}

	public function get_input_type() {
		return $this->field['native_html5'] ? 'date' : 'text';
	}

	public function get_class() {
		return 'sellkit-field flatpickr';
	}

	public function get_style_depends() {
		return [ 'flatpickr' ];
	}

	public function get_script_depends() {
		return [ 'flatpickr' ];
	}

	public function add_field_render_attribute() {
		parent::add_field_render_attribute();

		$localization = $this->field['localization'];

		if ( 'yes' === $localization ) {
			$locale = $this->date_localization();
			$this->load_localize_script( $locale );
			$this->widget->add_render_attribute( 'field-' . $this->get_id(), 'data-locale', $locale );
		}

		$this->widget->add_render_attribute( 'field-' . $this->get_id(), [
			$this->field['native_html5'] ? 'min' : 'data-min-date' => $this->field['min_date'],
			$this->field['native_html5'] ? 'max' : 'data-max-date' => $this->field['max_date'],
		] );
	}

	public function render_content() {
		$attrs = $this->widget->get_render_attribute_string( 'field-' . $this->get_id() );

		?>
		<input <?php echo $attrs; ?>>
		<?php
	}

	public static function get_additional_controls() {
		$commons = parent::get_common_controls();

		return [
			'label' => $commons['label'],
			'field_value' => $commons['field_value'],
			'placeholder' => $commons['placeholder'],
			'min_date' => [
				'label'          => esc_html__( 'Min Date', 'sellkit' ),
				'type'           => 'date_time',
				'label_block'    => false,
				'conditions' => [ 'terms' => [ parent::get_type_condition() ] ],
				'picker_options' => [
					'enableTime' => false,
					'locale'     => [
						'firstDayOfWeek' => 1,
					],
				],
			],
			'max_date' => [
				'label'          => esc_html__( 'Max Date', 'sellkit' ),
				'type'           => 'date_time',
				'label_block'    => false,
				'conditions' => [ 'terms' => [ parent::get_type_condition() ] ],
				'picker_options' => [
					'enableTime' => false,
					'locale'     => [
						'firstDayOfWeek' => 1,
					],
				],
			],
			'localization' => [
				'label'        => esc_html__( 'Localization', 'sellkit' ),
				'type'         => 'switcher',
				'return_value' => 'yes',
				'conditions' => [ 'terms' => [ parent::get_type_condition() ] ],
			],
			'native_html5' => [
				'label'        => esc_html__( 'Native HTML5', 'sellkit' ),
				'type'         => 'switcher',
				'return_value' => 'true',
				'conditions' => [ 'terms' => [ parent::get_type_condition() ] ],
			],
			'required' => $commons['required'],
			'width_responsive' => $commons['width_responsive'],
		];
	}

	private function date_localization() {
		$wp_locale = get_locale();

		$locales = [
			'af'             => '',   // 'Afrikaans'
			'ar'             => 'ar', // 'Arabic'
			'ary'            => 'ar', // 'Moroccan Arabic'
			'as'             => '',   // 'Assamese'
			'azb'            => 'az', // 'South Azerbaijani'
			'az'             => 'az', // 'Azerbaijani'
			'bel'            => 'be', // 'Belarusian'
			'bg_BG'          => 'bg', // 'Bulgarian'
			'bn_BD'          => 'bn', // 'Bengali (Bangladesh)'
			'bo'             => '',   // 'Tibetan'
			'bs_BA'          => 'bs', // 'Bosnian'
			'ca'             => 'cat', // 'Catalan'
			'ceb'            => '',   // 'Cebuano'
			'cs_CZ'          => 'cs', // 'Czech'
			'cy'             => 'cy', // 'Welsh'
			'da_DK'          => 'da', // 'Danish'
			'de_CH_informal' => 'de', // 'German (Switzerland, Informal)'
			'de_CH'          => 'de', // 'German (Switzerland)'
			'de_DE'          => 'de', // 'German'
			'de_DE_formal'   => 'de', // 'German (Formal)'
			'de_AT'          => 'de', // 'German (Austria)'
			'dzo'            => '',   // 'Dzongkha'
			'el'             => 'gr', // 'Greek'
			'en_GB'          => 'en', // 'English (UK)'
			'en_AU'          => 'en', // 'English (Australia)'
			'en_CA'          => 'en', // 'English (Canada)'
			'en_ZA'          => 'en', // 'English (South Africa)'
			'en_NZ'          => 'en', // 'English (New Zealand)'
			'eo'             => 'eo', // 'Esperanto'
			'es_CL'          => 'es', // 'Spanish (Chile)'
			'es_ES'          => 'es', // 'Spanish (Spain)'
			'es_MX'          => 'es', // 'Spanish (Mexico)'
			'es_GT'          => 'es', // 'Spanish (Guatemala)'
			'es_CR'          => 'es', // 'Spanish (Costa Rica)'
			'es_CO'          => 'es', // 'Spanish (Colombia)'
			'es_PE'          => 'es', // 'Spanish (Peru)'
			'es_VE'          => 'es', // 'Spanish (Venezuela)'
			'es_AR'          => 'es', // 'Spanish (Argentina)'
			'et'             => 'et', // 'Estonian'
			'eu'             => 'es', // 'Basque'
			'fa_IR'          => 'fa', // 'Persian'
			'fi'             => 'fi', // 'Finnish'
			'fr_CA'          => 'fr', // 'French (Canada)'
			'fr_FR'          => 'fr', // 'French (France)'
			'fr_BE'          => 'fr', // 'French (Belgium)'
			'fur'            => '',   // 'Friulian'
			'gd'             => 'ga', // 'Scottish Gaelic'
			'gl_ES'          => 'es', // 'Galician'
			'gu'             => '',   // 'Gujarati'
			'haz'            => '',   // 'Hazaragi'
			'he_IL'          => 'he', // 'Hebrew'
			'hi_IN'          => 'hi', // 'Hindi'
			'hr'             => 'hr', // 'Croatian'
			'hsb'            => '',   // 'Upper Sorbian'
			'hu_HU'          => 'hu', // 'Hungarian'
			'hy'             => '',   // 'Armenian'
			'id_ID'          => 'id', // 'Indonesian'
			'is_IS'          => 'is', // 'Icelandic'
			'it_IT'          => 'it', // 'Italian'
			'ja'             => 'ja', // 'Japanese'
			'jv_ID'          => '',   // 'Javanese'
			'ka_GE'          => 'ka', // 'Georgian'
			'kab'            => '',   // 'Kabyle'
			'kk'             => 'kz', // 'Kazakh'
			'km'             => 'km', // 'Khmer'
			'kn'             => '',   // 'Kannada'
			'ko_KR'          => 'ko', // 'Korean'
			'ckb'            => '',   // 'Kurdish (Sorani)'
			'lo'             => '',   // 'Lao'
			'lt_LT'          => 'lt', // 'Lithuanian'
			'lv'             => 'lv', // 'Latvian'
			'mk_MK'          => 'mk', // 'Macedonian'
			'ml_IN'          => '',   // 'Malayalam'
			'mn'             => 'mn', // 'Mongolian'
			'mr'             => '',   // 'Marathi'
			'ms_MY'          => 'ms', // 'Malay'
			'my_MM'          => 'my', // 'Myanmar (Burmese)'
			'nb_NO'          => 'no', // 'Norwegian (Bokmål)'
			'ne_NP'          => '',   // 'Nepali'
			'nl_NL'          => 'nl', // 'Dutch'
			'nl_NL_formal'   => 'nl', // 'Dutch (Formal)'
			'nl_BE'          => 'nl', // 'Dutch (Belgium)'
			'nn_NO'          => 'no', // 'Norwegian (Nynorsk)'
			'oci'            => '',   // 'Occitan'
			'pa_IN'          => 'pa', // 'Punjabi'
			'pl_PL'          => 'pl', // 'Polish'
			'ps'             => '',   // 'Pashto'
			'pt_BR'          => 'pt', // 'Portuguese (Brazil)'
			'pt_AO'          => 'pt', // 'Portuguese (Angola)'
			'pt_PT'          => 'pt', // 'Portuguese (Portugal)'
			'pt_PT_ao90'     => 'pt', // 'Portuguese (Portugal, AO90)'
			'rhg'            => '',   // 'Rohingya'
			'ro_RO'          => 'ro', // 'Romanian'
			'ru_RU'          => 'ru', // 'Russian'
			'sah'            => '',   // 'Sakha'
			'si_LK'          => 'si', // 'Sinhala'
			'sk_SK'          => 'sk', // 'Slovak'
			'skr'            => '',   // 'Saraiki'
			'sl_SI'          => 'sl', // 'Slovenian'
			'sq'             => 'sq', // 'Albanian'
			'sr_RS'          => 'sr', // 'Serbian'
			'sv_SE'          => 'sv', // 'Swedish'
			'sw'             => '',   // 'Swahili'
			'szl'            => '',   // 'Silesian'
			'ta_IN'          => '',   // 'Tamil'
			'te'             => '',   // 'Telugu'
			'th'             => 'th', // 'Thai'
			'tl'             => '',   // 'Tagalog'
			'tr_TR'          => 'tr', // 'Turkish'
			'tt_RU'          => '',   // 'Tatar'
			'tah'            => '',   // 'Tahitian'
			'ug_CN'          => '',   // 'Uighur'
			'uk'             => 'uk', // 'Ukrainian'
			'ur'             => '',   // 'Urdu'
			'uz_UZ'          => '',   // 'Uzbek'
			'vi'             => 'vn', // 'Vietnamese'
			'zh_HK'          => 'zh', // 'Chinese (Hong Kong)'
			'zh_TW'          => 'zh-tw', // 'Chinese (Taiwan)'
			'zh_CN'          => 'zh', // 'Chinese (China)'
		];

		$result = array_key_exists( $wp_locale, $locales ) ? $locales[ $wp_locale ] : 'default';

		if ( 'en' === $result || '' === $result ) {
			$result = 'default';
		}

		return $result;
	}

	private function load_localize_script( $locale ) {
		if ( 'default' === $locale ) {
			return;
		}

		wp_enqueue_script( 'sellkit_flatpickr_localize',
			sellkit()->plugin_url() . 'assets/lib/flatpickr-locale/' . $locale . '.js',
			[ 'flatpickr' ],
			'4.1.4',
			true
		);
	}

}
