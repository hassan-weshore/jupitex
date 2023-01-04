<?php

namespace Sellkit\Contact_Segmentation\Conditions;

use Sellkit\Contact_Segmentation\Conditions\Condition_Base;

defined( 'ABSPATH' ) || die();

/**
 * Class Referral Source Internal Post.
 *
 * @package Sellkit\Contact_Segmentation\Conditions
 * @since 1.1.0
 */
class Referral_Source_Internal_Post extends Condition_Base {

	/**
	 * Condition name.
	 *
	 * @since 1.1.0
	 */
	public function get_name() {
		return 'referral-source-internal-post';
	}

	/**
	 * Condition title.
	 *
	 * @since 1.1.0
	 */
	public function get_title() {
		return __( 'Referral Source (Internal Page/Post)', 'sellkit' );
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
	 * Get the options
	 *
	 * @since 1.1.0
	 * @return array
	 */
	public function get_options() {
		$input_value = sellkit_htmlspecialchars( INPUT_GET, 'input_value' );

		$posts = [];
		$args  = [
			'post_type' => [ 'post', 'page' ],
			'post_status' => 'any',
			's' => sanitize_text_field( $input_value ),
		];

		$query = new \WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$posts[ get_the_ID() ] = htmlspecialchars_decode( get_the_title() );
			}
		}

		return $posts;
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
	 * All the conditions are not searchable by default.
	 *
	 * @return false
	 * @since 1.1.0
	 */
	public function is_searchable() {
		return true;
	}

	/**
	 * Check if the condition is active or not.
	 *
	 * @return bool
	 */
	public function is_active() {
		return false;
	}
}
