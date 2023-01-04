<?php
namespace JupiterX_Core\Raven\Modules\Post_Comments\Widgets;

use JupiterX_Core\Raven\Base\Base_Widget;
use JupiterX_Core\Raven\Modules\Post_Comments\Skins;
use Elementor\Plugin;

defined( 'ABSPATH' ) || die();

class Post_Comments extends Base_Widget {

	protected $_has_template_content = false;

	public function get_name() {
		return 'raven-post-comments';
	}

	public function get_title() {
		return __( 'Post Comments', 'jupiterx-core' );
	}

	public function get_icon() {
		return 'raven-element-icon raven-element-icon-post-comments';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_settings',
			[
				'label' => __( 'Settings', 'jupiterx-core' ),
				'description' => __( 'No settings available.', 'jupiterx-core' ),
			]
		);

		$this->add_control(
			'help',
			[
				'type' => 'raw_html',
				'raw' => __( 'No settings available', 'elementor' ),
			]
		);

		$this->end_controls_section();
	}

	protected function render_warning() {
		?>
		<div class="elementor-alert elementor-alert-danger" role="alert">
			<span class="elementor-alert-title"> <?php esc_html_e( 'Comments are closed. Switch on comments from', 'jupiterx-core' ); ?></span><br />
			<div class="elementor-alert-description">
				<ul>
					<li> <?php esc_html_e( 'WordPress Customizer or', 'jupiterx-core' ); ?> </li>
					<li> <?php esc_html_e( 'Discussion box on the WordPress post edit screen or', 'jupiterx-core' ); ?> </li>
					<li> <?php esc_html_e( 'WordPress discussion settings or', 'jupiterx-core' ); ?> </li>
					<li> <?php esc_html_e( 'Page/post meta fields.', 'jupiterx-core' ); ?> </li>
				</ul>
			</div>
		</div>
		<?php
	}

	protected function render() {
		$is_preview_or_edit = Plugin::instance()->preview->is_preview_mode() || Plugin::instance()->editor->is_edit_mode();

		$post_type = get_post_type();

		if ( function_exists( 'jupiterx_post_element_enabled' ) && ! jupiterx_post_element_enabled( 'comments', $post_type ) && $is_preview_or_edit ) {
			$this->render_warning();

			return;
		}

		if ( ! comments_open() && $is_preview_or_edit ) {
			$this->render_warning();

			return;
		}

		if ( function_exists( 'jupiterx_comments_template' ) ) {
			jupiterx_comments_template();

			return;
		}

		comments_template();
	}
}
