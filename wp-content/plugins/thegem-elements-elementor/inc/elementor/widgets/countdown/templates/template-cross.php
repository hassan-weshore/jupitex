<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

switch ( $settings['position'] ) {
	case 'flex-start':
		$style = 'margin:0;';
		break;
	case 'flex-end':
		$style = 'margin:0;float:right;';
		break;
	case 'center':
		$style = 'margin:auto;';
		break;
}

$this->add_render_attribute( 'countdown_container', [
	'data-eventdate' => strtotime( $settings['due_date'] ),
	'class'          => [ 'countdown-container', 'countdown-style-6' ],
	'style'          => $style
] );

$border_days_margin    = isset( $settings['days_style_separator_width']['size'] ) ? - $settings['days_style_separator_width']['size'] / 2 : - $settings['separator_width']['size'] / 2;
$border_hours_margin   = isset( $settings['hours_style_separator_width']['size'] ) ? - $settings['hours_style_separator_width']['size'] / 2 : - $settings['separator_width']['size'] / 2;
$border_minutes_margin = isset( $settings['minutes_style_separator_width']['size'] ) ? - $settings['minutes_style_separator_width']['size'] / 2 : - $settings['separator_width']['size'] / 2;
$border_seconds_margin = isset( $settings['seconds_style_separator_width']['size'] ) ? - $settings['seconds_style_separator_width']['size'] / 2 : - $settings['separator_width']['size'] / 2;
?>
<div <?php echo $this->get_render_attribute_string( 'countdown_container' ) ?>>
	<div class="countdown-wrapper countdown-info">
		<div class="countdown-item count-1">
			<div class="wrap">
				<?php if ( $show_link ): ?><a <?php echo $this->get_render_attribute_string( $countdown_wrap_link ) ?>></a><?php endif ?>
				<div class="countdown-item-border countdown-item-border-1" style="margin-bottom: <?php echo $border_days_margin ?>px"></div>
				<?php if ( 'yes' === $settings['content_labels_show_days'] ): ?>
					<span class="item-title"><?php echo $settings['content_labels_days_label'] ?></span><span class="item-count countdown-days title-xlarge"></span>
				<?php endif ?>
			</div>
		</div>
		<div class="countdown-item count-2">
			<div class="wrap">
				<?php if ( $show_link ): ?><a <?php echo $this->get_render_attribute_string( $countdown_wrap_link ) ?>></a><?php endif ?>
				<div class="countdown-item-border countdown-item-border-2" style="margin-left: <?php echo $border_hours_margin ?>px"></div>
				<?php if ( 'yes' === $settings['content_labels_show_hours'] ): ?>
					<span class="item-title"><?php echo $settings['content_labels_hours_label'] ?></span><span class="item-count countdown-hours title-xlarge"></span>
				<?php endif ?>
			</div>
		</div>
		<div class="countdown-item count-3">
			<div class="wrap">
				<?php if ( $show_link ): ?><a <?php echo $this->get_render_attribute_string( $countdown_wrap_link ) ?>></a><?php endif ?>
				<div class="countdown-item-border countdown-item-border-3" style="margin-right: <?php echo $border_minutes_margin ?>px"></div>
				<?php if ( 'yes' === $settings['content_labels_show_minutes'] ): ?>
					<span class="item-title"><?php echo $settings['content_labels_minutes_label'] ?></span><span class="item-count countdown-minutes title-xlarge"></span>
				<?php endif ?>
			</div>
		</div>
		<div class="countdown-item count-4">
			<div class="wrap">
				<?php if ( $show_link ): ?><a <?php echo $this->get_render_attribute_string( $countdown_wrap_link ) ?>></a><?php endif ?>
				<div class="countdown-item-border countdown-item-border-4" style="margin-top: <?php echo $border_seconds_margin ?>px"></div>
				<?php if ( 'yes' === $settings['content_labels_show_seconds'] ): ?>
					<span class="item-title"><?php echo $settings['content_labels_seconds_label'] ?></span><span class="item-count countdown-seconds title-xlarge"></span>
				<?php endif ?>
			</div>
		</div>
	</div>
</div>
<div style="clear: both"></div>