<?php
if ( ! class_exists( 'SellKit_Personalised_Coupons' ) ) {
	/**
	 * Settings.
	 *
	 * @since 1.1.0
	 */
	class SellKit_Personalised_Coupons {

		/**
		 * Constructor.
		 *
		 * @since 1.1.0
		 */
		public function __construct() {
			add_action( 'wp_ajax_sellkit_create_personalised_coupons', [ $this, 'create_new_personalised_coupons' ] );
		}

		/**
		 * Creating new Personalised Coupons.
		 *
		 * @since 1.1.0
		 */
		public function create_new_personalised_coupons() {
			check_ajax_referer( 'sellkit_control_panel', 'nonce' );

			if ( isset( $_POST['type'] ) && 'save' === $_POST['type'] ) { // phpcs:ignore WordPress.Security
				$fields           = wp_unslash( $_POST['fields'] ); // phpcs:ignore WordPress.Security
				$sanitized_fields = $this->sanitize_fields( $fields );

				$this->validate_fields( $sanitized_fields );

				$result = $this->save_new_coupon( $sanitized_fields );
			}

			if ( empty( $result ) ) {
				wp_send_json_error( [ 'message' => __( 'something went wrong', 'sellkit' ) ] );
				die();
			}

			wp_send_json_success(
				[
					'message' => __( 'The new coupon was added', 'sellkit' ),
					'redirect_url' => admin_url( 'admin.php?page=personalised-coupons' ),
				]
			);
			die();
		}

		/**
		 * Saving new Personalised Coupons.
		 *
		 * @param array $coupon_data Coupon data.
		 *
		 * @return bool
		 *
		 * @since 1.1.0
		 */
		private function save_new_coupon( $coupon_data ) {
			$title         = sanitize_text_field( $coupon_data['sellkit_personalised_coupons_name'] );
			$active_coupon = filter_var( $coupon_data['sellkit_personalised_coupons_active_immediately'], FILTER_VALIDATE_BOOLEAN );
			$post_status   = $active_coupon ? 'publish' : 'draft';

			$post_args = [
				'post_title' => $title,
				'post_type' => 'sk-personalised-coupons',
				'post_status' => $post_status,
			];

			$new_coupon_id = wp_insert_post( $post_args );

			if ( ! empty( $new_coupon_id ) && ! is_wp_error( $new_coupon_id ) ) {
				update_post_meta( $new_coupon_id, 'sellkit_personalised_coupon_data', $coupon_data );
				return true;
			}

			return false;
		}

		/**
		 * Sanitize fields.
		 *
		 * @param array $fields Fields.
		 *
		 * @return array
		 *
		 * @since 1.1.0
		 */
		private function sanitize_fields( $fields ) {
			$new_fields = [];

			foreach ( $fields as $key => $field ) {
				if ( is_array( $fields[ $key ] ) ) {
					$new_fields[ $key ] = $this->sanitize_fields( $fields[ $key ] );
				}

				$field_value = sanitize_text_field( $field );

				if ( ! is_array( $fields[ $key ] ) && ! empty( $field_value ) ) {
					$new_fields[ $key ] = $field_value;
				}
			}

			return $new_fields;
		}

		/**
		 * Validate fields.
		 *
		 * @param array $fields Fields.
		 *
		 * @return void
		 *
		 * @since 1.1.0
		 */
		public function validate_fields( $fields ) {
			if ( ! condition_row_validation( $fields['condition_row'] ) ) {
				wp_send_json_error( [ 'message' => 'Your condition was not completed.' ] );
			}
		}

		/**
		 * Check Personalised Coupon is empty Or not.
		 *
		 * @since 1.1.0
		 */
		public static function personalised_coupon_is_empty() {
			$args = array(
				'post_type' => 'sk-personalised-coupons',
				'posts_per_page' => 1,
				'post_status' => [ 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash' ],
			);

			$coupon_query = new WP_Query( $args );

			if ( $coupon_query->have_posts() ) {
				while ( $coupon_query->have_posts() ) {
					$coupon_query->the_post();

					return false;
				}

				wp_reset_postdata();
			}

			return true;
		}
	}

	new SellKit_Personalised_Coupons();
}
