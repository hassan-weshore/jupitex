<?php

namespace Sellkit_Pro\Alerts;

use Sellkit\Database;

defined( 'ABSPATH' ) || die();

/**
 * Class Dynamic Discount.
 *
 * @since 1.1.0
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Alerts {

	/**
	 * Default alert pages.
	 *
	 * @var string[] All pages which can show alert.
	 * @since 1.1.0
	 */
	public $possible_alert_pages = [
		'checkout',
		'cart',
		'product_single',
		'catalog',
		'order_received',
		'my_account',
		'order_received',
	];

	/**
	 * Today minimum timestamp.
	 *
	 * @since 1.1.0
	 * @var string Today min time.
	 */
	public $today_min_time;

	/**
	 * Rule id.
	 *
	 * @since 1.1.0
	 * @var string Rule id
	 */
	public $rule_id;

	/**
	 * Alerts constructor.
	 *
	 * @since 1.1.0
	 */
	public function __construct() {
		add_action( 'wp_ajax_sellkit_smart_alert_send_click_log', [ $this, 'send_click_log' ] );
		add_action( 'wp_ajax_nopriv_sellkit_smart_alert_send_click_log', [ $this, 'send_click_log' ] );

		add_filter( 'sellkit_pro_frontend_scripts_args', [ $this, 'enqueue_alert_frontend_script' ] );

		add_action( 'wp', function () {
			add_shortcode( 'sellkit-smart-alert', [ $this, 'show_shortcode_alert' ] );
		} );

		if ( is_admin() ) {
			return;
		}

		add_action( 'template_redirect', function() {
			if ( ! sellkit_pro()->has_valid_dependencies() ) {
				return;
			}

			if ( is_checkout() && empty( is_wc_endpoint_url( 'order-received' ) ) ) {
				return;
			}

			$this->init();
		} );

		if ( wp_doing_ajax( 'wc_ajax_update_order_review' ) ) {
			add_action( 'wp_loaded', [ $this, 'init' ], 20 );
		}

		$this->today_min_time = strtotime( date( 'Y-m-d 0:0:0' ) );
	}

	/**
	 * Initialize alert process.
	 *
	 * @since 1.1.0
	 */
	public function init() {
		if ( ! in_array( $this->get_current_page(), $this->possible_alert_pages, true ) ) {
			return;
		}

		if ( ! has_shortcode( get_the_content(), 'sellkit-smart-alert' ) ) {
			$this->maybe_show_alert();
		}
	}

	/**
	 * It check the conditions and show alert if all conditions were valid.
	 *
	 * @since 1.1.0
	 * @param string $display_type Alert display type.
	 */
	public function maybe_show_alert( $display_type = false ) {
		if ( ! sellkit_pro()->has_valid_dependencies() ) {
			return;
		}

		$rule_ids = $this->get_alert_rules_by_conditions( $display_type );

		if ( empty( $rule_ids ) ) {
			return;
		}

		foreach ( $rule_ids as $rule_id ) {
			$this->show_notice( $rule_id, $display_type );
		}
	}

	/**
	 * Shows smart notice.
	 *
	 * @since 1.2.6
	 * @param string $rule_id Rule id.
	 * @param string $display_type Alert display type.
	 */
	public function show_notice( $rule_id, $display_type ) {
		$alert_meta = get_post_meta( $rule_id );
		$alert_skin = ! empty( $alert_meta['alert_content_skin'][0] ) ? $alert_meta['alert_content_skin'][0] : 'message';

		set_query_var( 'alert_id', $rule_id );

		if ( 'custom_location' !== $display_type ) {
			wc_add_notice(
				$this->get_alert( $rule_id ),
				$alert_skin,
				[
					'source' => 'sellkit_smart_alert_notice',
					'rule_id' => $rule_id,
				]
			);
		}

		if ( self::is_thankyou_page() ) {
			add_action( 'woocommerce_before_thankyou', function() use ( $rule_id ) {
				wc_print_notices();
			} );
		}

		if ( 'custom_location' === $display_type ) {
			wc_add_notice(
				$this->get_alert( $rule_id ),
				$alert_skin,
				[
					'source' => 'sellkit_smart_alert_notice',
					'rule_id' => $rule_id,
				]
			);

			wc_print_notices();
		}

		$this->add_log( $rule_id );
	}

	/**
	 * Short code callback.
	 *
	 * @since 1.1.0
	 */
	public function show_shortcode_alert() {
		if ( ! is_user_logged_in() ) {
			return '';
		}

		ob_start();

		$this->maybe_show_alert( 'custom_location' );

		return ob_get_clean();
	}

	/**
	 * Gets alert text.
	 *
	 * @since 1.1.0
	 * @return string
	 * @param string $rule_id Rule id.
	 */
	public function get_alert( $rule_id ) {
		$alert_meta  = get_post_meta( $rule_id );
		$button_text = ! empty( $alert_meta['button_text'][0] ) ? $alert_meta['button_text'][0] : '';
		$button_url  = ! empty( $alert_meta['button_url'][0] ) ? $alert_meta['button_url'][0] : '';
		$content     = ! empty( $alert_meta['sellkit_content'][0] ) ? $alert_meta['sellkit_content'][0] : '';

		ob_start();
		if ( ! empty( $button_text ) ) :
		?>

			<a href="<?php echo esc_url_raw( $button_url ); ?>" tabindex="1" class="button wc-forward sellkit-smart-alert-button" target="_blank"><?php echo esc_html( $button_text ); ?></a>

			<?php
		endif;

		echo wp_kses_post( do_shortcode( $content ) );

		return ob_get_clean();
	}

	/**
	 * Gets alert rule id.
	 *
	 * @since 1.1.0
	 * @param string $display_type Display type.
	 * @return false|array
	 */
	public function get_alert_rules_by_conditions( $display_type = false ) {
		$rules = [];
		$args  = [
			'post_type' => 'sellkit-alert',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby'  => [ 'meta_value_num' => 'ASC' ],
			'meta_key' => 'sellkit_usage_limit', // phpcs:ignore
		];

		if ( 'custom_location' === $display_type ) {
			$args['meta_query'] = [ // phpcs:ignore
				[
					'key'     => 'display_position',
					'value'   => 's:15:"custom_location"',
					'compare' => 'LIKE',
				],
			];
		}

		if ( false === $display_type ) {
			$args[ 'meta_query' ][] = [ // phpcs:ignore
				'key'     => 'display_position',
				'value'   => $this->get_current_page(),
				'compare' => 'LIKE',
			];
		}

		$query = new \WP_Query( $args );

		foreach ( $query->posts as $post ) {
			$conditions     = get_post_meta( $post->ID, 'conditions', true );
			$is_valid       = true;
			$condition_type = ! empty( $conditions[1]['type'] ) ? $conditions[1]['type'] : 'and';

			if ( 'or' === $condition_type ) {
				$is_valid = false;
			}

			if ( empty( $conditions ) ) {
				$rules[]    = $post->ID;
				$conditions = [];
			}

			foreach ( $conditions as $condition ) {
				if ( is_array( $condition['condition_value'] ) && ! empty( $condition['condition_value'][0]['value'] ) ) {
					$condition['condition_value'] = sellkit_get_multi_select_values( $condition['condition_value'] );
				}

				$result = sellkit_condition_match( $condition['condition_subject'], $condition['condition_operator'], $condition['condition_value'] );

				if ( is_wp_error( $result ) ) {
					continue;
				}

				if ( ! $result ) {
					$is_valid = false;
				}

				if ( $result && 'or' === $condition_type ) {
					$is_valid = true;
					break;
				}
			}

			if ( ! empty( $conditions ) && true === $is_valid ) {
				$rules[] = $post->ID;
			}

			if ( count( $rules ) > 2 ) {
				return $rules;
			}
		}

		if ( count( $rules ) > 0 ) {
			return $rules;
		}

		return false;
	}

	/**
	 * Checks if current page can show errors or not.
	 *
	 * @since 1.1.0
	 */
	private function get_current_page() {
		if ( ! function_exists( 'WC' ) ) {
			return;
		}

		$is_checkout = is_checkout();

		if ( ! empty( $_GET['wc-ajax'] ) ) { //phpcs:ignore
			$action      = sellkit_htmlspecialchars( INPUT_GET, 'wc-ajax' );
			$is_checkout = 'update_order_review' === $action ? true : false;
		}

		if ( $is_checkout && empty( is_wc_endpoint_url( 'order-received' ) ) ) {
			return 'checkout';
		}

		if ( is_cart() ) {
			return 'cart';
		}

		if ( is_singular( 'product' ) ) {
			return 'product_single';
		}

		if ( is_shop() ) {
			return 'catalog';
		}

		if ( self::is_thankyou_page() ) {
			return 'order_received';
		}

		if ( is_user_logged_in() && is_account_page() && ! is_wc_endpoint_url() ) {
			return 'my_account';
		}

		return false;
	}

	/**
	 * Checks if current page is thank you page.
	 *
	 * @since 1.1.0
	 * @return bool
	 */
	public static function is_thankyou_page() {
		if ( is_checkout() && ! empty( is_wc_endpoint_url( 'order-received' ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Inserts applied alert log.
	 *
	 * @since 1.1.0
	 * @param string $rule_id Rule id.
	 */
	private function insert_applied_alert_log( $rule_id ) {
		if ( ! sellkit_pro()->has_valid_dependencies() ) {
			return;
		}

		sellkit()->db->insert( 'applied_alert', [
			'click' => 0,
			'impression' => 1,
			'rule_id' => $rule_id,
			'applied_at' => time(),
		] );
	}

	/**
	 * Checks if has applied alert or not.
	 *
	 * @since 1.1.0
	 * @param string $rule_id Rule id.
	 */
	private function has_applied_alert_data( $rule_id ) {
		global $current_user, $wpdb;

		$database_prefix = Database::DATABASE_PREFIX;

		//phpcs:disable
		$result = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * from {$wpdb->prefix}{$database_prefix}applied_alert
				WHERE rule_id = %s and applied_at > %s",
				$rule_id,
				$this->today_min_time
			)
		, ARRAY_A );
		//phpcs:enable

		if ( empty( $result[0] ) ) {
			return false;
		}

		return $result[0];
	}

	/**
	 * Adds logs.
	 *
	 * @since 1.2.6
	 * @param string $rule_id Rule id.
	 */
	private function add_log( $rule_id ) {
		global $current_user;

		$data = $this->has_applied_alert_data( $rule_id );

		if ( $data ) {
			global $wpdb;

			$table = $wpdb->prefix . Database::DATABASE_PREFIX . 'applied_alert';

			$sql = "UPDATE `$table` SET impression = %d WHERE rule_id = %d AND applied_at > %s";

			$this->check_current_query = false;

			//phpcs:disable
			$wpdb->query(
				$wpdb->prepare( $sql, $data['impression'] + 1, $rule_id, $this->today_min_time )
			);
			//phpcs:enable

			return;
		}

		$this->insert_applied_alert_log( $rule_id );
	}

	/**
	 * Adding args to js var.
	 *
	 * @since 1.1.0
	 * @param array $args Args data.
	 */
	public function enqueue_alert_frontend_script( $args ) {
		if (
			! has_shortcode( get_the_content(), 'sellkit-smart-alert' ) &&
			! in_array( $this->get_current_page(), $this->possible_alert_pages, true )
		) {
			return $args;
		}

		$args['pages'] = array_merge( $args['pages'], [ 'smart-alert' ] );

		return $args;
	}

	/**
	 * Sending click log.
	 *
	 * @since 1.1.0
	 */
	public function send_click_log() {
		check_ajax_referer( 'sellkit_frontend_nonce', 'nonce' );

		global $current_user;

		$rule_id              = sellkit_htmlspecialchars( INPUT_POST, 'rule_id' );
		$this->today_min_time = strtotime( date( 'Y-m-d 0:0:0' ) );

		$data = $this->has_applied_alert_data( $rule_id );

		if ( empty( $data ) ) {
			return;
		}

		global $wpdb;

		$table = $wpdb->prefix . Database::DATABASE_PREFIX . 'applied_alert';
		$sql   = "UPDATE `$table` SET click = %s WHERE rule_id = %d AND applied_at > %s";

		$this->check_current_query = false;

		//phpcs:disable
		$wpdb->query(
			$wpdb->prepare( $sql, $data['click'] + 1, $rule_id, $this->today_min_time )
		);
		//phpcs:enable

		wp_send_json_success( esc_html__( 'The click log has been added.', 'sellkit-pro' ) );
	}
}

new Alerts();
