<?php

namespace Sellkit_Pro\Contact_Segmentation\Conditions;

use Sellkit_Pro\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Visitor Country.
 *
 * @package Sellkit_Pro\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Visitor_Country extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'visitor-country';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'Visitor Country', 'sellkit-pro' );
	}

	/**
	 * Condition type.
	 *
	 * @since 1.1.0
	 */
	public function get_type() {
		return self::SELLKIT_MULTISELECT_CONDITION_VALUE;
	}

	/**
	 * Gets options.
	 *
	 * @since 1.1.0
	 * @return string[]
	 */
	public function get_options() {
		$countries = [
			'afghanistan'                      => 'Afghanistan',
			'albania'                          => 'Albania',
			'algeria'                          => 'Algeria',
			'american samoa'                   => 'American Samoa',
			'andorra'                          => 'Andorra',
			'angola'                           => 'Angola',
			'anguilla'                         => 'Anguilla',
			'antigua and barbuda'              => 'Antigua And Barbuda',
			'argentina'                        => 'Argentina',
			'armenia'                          => 'Armenia',
			'aruba'                            => 'Aruba',
			'australia'                        => 'Australia',
			'austria'                          => 'Austria',
			'azerbaijan'                       => 'Azerbaijan',
			'bahamas'                          => 'Bahamas',
			'bahrain'                          => 'Bahrain',
			'bangladesh'                       => 'Bangladesh',
			'barbados'                         => 'Barbados',
			'belarus'                          => 'Belarus',
			'belgium'                          => 'Belgium',
			'belize'                           => 'Belize',
			'benin'                            => 'Benin',
			'bermuda'                          => 'Bermuda',
			'bhutan'                           => 'Bhutan',
			'bolivia'                          => 'Bolivia',
			'bosnia and herzegovina'           => 'Bosnia And Herzegovina',
			'botswana'                         => 'Botswana',
			'brazil'                           => 'Brazil',
			'british indian ocean territory'   => 'British Indian Ocean Territory',
			'brunei'                           => 'Brunei',
			'bulgaria'                         => 'Bulgaria',
			'burkina faso'                     => 'Burkina Faso',
			'burundi'                          => 'Burundi',
			'cambodia'                         => 'Cambodia',
			'cameroon'                         => 'Cameroon',
			'canada'                           => 'Canada',
			'cape verde'                       => 'Cape Verde',
			'cayman islands'                   => 'Cayman Islands',
			'central african republic'         => 'Central African Republic',
			'chad'                             => 'Chad',
			'chile'                            => 'Chile',
			'china'                            => 'China',
			'colombia'                         => 'Colombia',
			'congo'                            => 'Congo',
			'cook islands'                     => 'Cook Islands',
			'costa rica'                       => 'Costa Rica',
			'cote d\'ivoire'                   => 'Cote D\'ivoire',
			'croatia'                          => 'Croatia',
			'cuba'                             => 'Cuba',
			'cyprus'                           => 'Cyprus',
			'czech republic'                   => 'Czech Republic',
			'democratic republic of the congo' => 'Democratic Republic of the Congo',
			'denmark'                          => 'Denmark',
			'djibouti'                         => 'Djibouti',
			'dominica'                         => 'Dominica',
			'dominican republic'               => 'Dominican Republic',
			'ecuador'                          => 'Ecuador',
			'egypt'                            => 'Egypt',
			'el salvador'                      => 'El Salvador',
			'equatorial guinea'                => 'Equatorial Guinea',
			'eritrea'                          => 'Eritrea',
			'estonia'                          => 'Estonia',
			'ethiopia'                         => 'Ethiopia',
			'faroe islands'                    => 'Faroe Islands',
			'federated states of micronesia'   => 'Federated States Of Micronesia',
			'fiji'                             => 'Fiji',
			'finland'                          => 'Finland',
			'france'                           => 'France',
			'french guiana'                    => 'French Guiana',
			'french polynesia'                 => 'French Polynesia',
			'gabon'                            => 'Gabon',
			'gambia'                           => 'Gambia',
			'georgia'                          => 'Georgia',
			'germany'                          => 'Germany',
			'ghana'                            => 'Ghana',
			'gibraltar'                        => 'Gibraltar',
			'greece'                           => 'Greece',
			'greenland'                        => 'Greenland',
			'grenada'                          => 'Grenada',
			'guadeloupe'                       => 'Guadeloupe',
			'guam'                             => 'Guam',
			'guatemala'                        => 'Guatemala',
			'guinea'                           => 'Guinea',
			'guinea bissau'                    => 'Guinea Bissau',
			'guyana'                           => 'Guyana',
			'haiti'                            => 'Haiti',
			'honduras'                         => 'Honduras',
			'hong kong'                        => 'Hong Kong',
			'hungary'                          => 'Hungary',
			'iceland'                          => 'Iceland',
			'india'                            => 'India',
			'indonesia'                        => 'Indonesia',
			'iran'                             => 'Iran',
			'ireland'                          => 'Ireland',
			'israel'                           => 'Israel',
			'italy'                            => 'Italy',
			'jamaica'                          => 'Jamaica',
			'japan'                            => 'Japan',
			'jordan'                           => 'Jordan',
			'kazakhstan'                       => 'Kazakhstan',
			'kenya'                            => 'Kenya',
			'kuwait'                           => 'Kuwait',
			'kyrgyzstan'                       => 'Kyrgyzstan',
			'laos'                             => 'Laos',
			'latvia'                           => 'Latvia',
			'lebanon'                          => 'Lebanon',
			'lesotho'                          => 'Lesotho',
			'libyan arab jamahiriya'           => 'Libyan Arab Jamahiriya',
			'liechtenstein'                    => 'Liechtenstein',
			'lithuania'                        => 'Lithuania',
			'luxembourg'                       => 'Luxembourg',
			'macedonia'                        => 'Macedonia',
			'madagascar'                       => 'Madagascar',
			'malawi'                           => 'Malawi',
			'malaysia'                         => 'Malaysia',
			'maldives'                         => 'Maldives',
			'mali'                             => 'Mali',
			'malta'                            => 'Malta',
			'martinique'                       => 'Martinique',
			'mauritania'                       => 'Mauritania',
			'mauritius'                        => 'Mauritius',
			'mexico'                           => 'Mexico',
			'monaco'                           => 'Monaco',
			'mongolia'                         => 'Mongolia',
			'montenegro'                       => 'Montenegro',
			'morocco'                          => 'Morocco',
			'mozambique'                       => 'Mozambique',
			'myanmar'                          => 'Myanmar',
			'namibia'                          => 'Namibia',
			'nepal'                            => 'Nepal',
			'netherlands'                      => 'Netherlands',
			'netherlands antilles'             => 'Netherlands Antilles',
			'new caledonia'                    => 'New Caledonia',
			'new zealand'                      => 'New Zealand',
			'nicaragua'                        => 'Nicaragua',
			'niger'                            => 'Niger',
			'nigeria'                          => 'Nigeria',
			'norfolk island'                   => 'Norfolk Island',
			'northern mariana islands'         => 'Northern Mariana Islands',
			'norway'                           => 'Norway',
			'oman'                             => 'Oman',
			'pakistan'                         => 'Pakistan',
			'palau'                            => 'Palau',
			'panama'                           => 'Panama',
			'papua new guinea'                 => 'Papua New Guinea',
			'paraguay'                         => 'Paraguay',
			'peru'                             => 'Peru',
			'philippines'                      => 'Philippines',
			'poland'                           => 'Poland',
			'portugal'                         => 'Portugal',
			'puerto rico'                      => 'Puerto Rico',
			'qatar'                            => 'Qatar',
			'republic of moldova'              => 'Republic Of Moldova',
			'reunion'                          => 'Reunion',
			'romania'                          => 'Romania',
			'russia'                           => 'Russia',
			'rwanda'                           => 'Rwanda',
			'saint kitts and nevis'            => 'Saint Kitts And Nevis',
			'saint lucia'                      => 'Saint Lucia',
			'saint vincent and the grenadines' => 'Saint Vincent And The Grenadines',
			'samoa'                            => 'Samoa',
			'san marino'                       => 'San Marino',
			'sao tome and principe'            => 'Sao Tome And Principe',
			'saudi arabia'                     => 'Saudi Arabia',
			'senegal'                          => 'Senegal',
			'serbia'                           => 'Serbia',
			'seychelles'                       => 'Seychelles',
			'singapore'                        => 'Singapore',
			'slovakia'                         => 'Slovakia',
			'slovenia'                         => 'Slovenia',
			'solomon islands'                  => 'Solomon Islands',
			'south africa'                     => 'South Africa',
			'south korea'                      => 'South Korea',
			'spain'                            => 'Spain',
			'sri lanka'                        => 'Sri Lanka',
			'sudan'                            => 'Sudan',
			'suriname'                         => 'Suriname',
			'swaziland'                        => 'Swaziland',
			'sweden'                           => 'Sweden',
			'switzerland'                      => 'Switzerland',
			'syrian arab republic'             => 'Syrian Arab Republic',
			'taiwan'                           => 'Taiwan',
			'tajikistan'                       => 'Tajikistan',
			'tanzania'                         => 'Tanzania',
			'thailand'                         => 'Thailand',
			'togo'                             => 'Togo',
			'tonga'                            => 'Tonga',
			'trinidad and tobago'              => 'Trinidad And Tobago',
			'tunisia'                          => 'Tunisia',
			'turkey'                           => 'Turkey',
			'turkmenistan'                     => 'Turkmenistan',
			'uganda'                           => 'Uganda',
			'ukraine'                          => 'Ukraine',
			'united arab emirates'             => 'United Arab Emirates',
			'united kingdom of great britain and northern ireland' => 'United Kingdom',
			'united states of america'         => 'United States',
			'uruguay'                          => 'Uruguay',
			'uzbekistan'                       => 'Uzbekistan',
			'vanuatu'                          => 'Vanuatu',
			'venezuela'                        => 'Venezuela',
			'vietnam'                          => 'Vietnam',
			'virgin islands british'           => 'Virgin Islands British',
			'virgin islands u.s.'              => 'Virgin Islands U.S.',
			'yemen'                            => 'Yemen',
			'zambia'                           => 'Zambia',
			'zimbabwe'                         => 'Zimbabwe',
		];

		$input_value = sellkit_htmlspecialchars( INPUT_GET, 'input_value' );

		return sellkit_filter_array( $countries, $input_value );
	}

	/**
	 * It is pro feature or not.
	 *
	 * @since 1.1.0
	 */
	public function is_pro() {
		return true;
	}

	/**
	 * It searchable.
	 *
	 * @since 1.1.0
	 */
	public function is_searchable() {
		return true;
	}
}
