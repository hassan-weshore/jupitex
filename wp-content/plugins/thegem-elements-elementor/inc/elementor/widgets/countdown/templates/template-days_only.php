<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

switch ( $settings['position'] ) {
	case 'flex-start':
		$style = 'text-align:left';
		break;
	case 'center':
		$style = 'text-align: center';
		break;
	case 'flex-end':
		$style = 'text-align:right';
		break;
}
$this->add_render_attribute( 'countdown_container', [
	'data-eventdate' => strtotime( $settings['due_date'] ),
	'class'          => [ 'countdown-container', 'countdown-style-7' ],
	'style'          => $style
] );
?>
<div <?php echo $this->get_render_attribute_string( 'countdown_container' ) ?>>
	<div class="countdown-wrapper countdown-info" style="<?php echo $style ?>">
		<div class='countdown-item'>
			<div class="wrap">
				<?php if ( $show_link ): ?><a <?php echo $this->get_render_attribute_string( $countdown_wrap_link ) ?>></a><?php endif ?>
				<span class="item-count countdown-days title-xlarge"></span></div>
			<?php if ( $settings['content_labels_days_label_days_only'] ): ?>
				<div class="countdown-text styled-subtitle"><?php echo $settings['content_labels_days_label_days_only'] ?></div>
			<?php endif ?>
		</div>
	</div>
</div>
