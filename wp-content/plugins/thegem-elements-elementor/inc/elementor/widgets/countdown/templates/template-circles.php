<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$this->add_render_attribute( 'countdown-container', [
	'data-eventdate'            => strtotime( $settings['due_date'] ),
	'data-starteventdate'       => strtotime( $settings['starting_date'] ),
	'data-days-circles-size'    => isset( $settings['days_style_circles_size']['size'] ) ? $settings['days_style_circles_size']['size'] / 2 : $settings['circles_size']['size'] / 2,
	'data-hours-circles-size'   => isset( $settings['hours_style_circles_size']['size'] ) ? $settings['hours_style_circles_size']['size'] / 2 : $settings['circles_size']['size'] / 2,
	'data-minutes-circles-size' => isset( $settings['minutes_style_circles_size']['size'] ) ? $settings['minutes_style_circles_size']['size'] / 2 : $settings['circles_size']['size'] / 2,
	'data-seconds-circles-size' => isset( $settings['seconds_style_circles_size']['size'] ) ? $settings['seconds_style_circles_size']['size'] / 2 : $settings['circles_size']['size'] / 2,
	'data-weightnumber'         => $settings['circles_weight']['size'],
	'class'                     => [ 'countdown-container', 'countdown-style-5' ],
] );

?>

<div <?php echo $this->get_render_attribute_string( 'countdown-container' ) ?>>
	<div class="countdown-wrapper countdown-info">
		<?php if ( 'yes' === $settings['content_labels_show_days'] ): ?>
			<div class="countdown-item count-1">
				<div class="circle-raphael-days"></div>
				<div class="wrap">
					<?php if ( $show_link ): ?><a <?php echo $this->get_render_attribute_string( $countdown_wrap_link ) ?>></a><?php endif ?>
					<span class="item-count countdown-days title-h1"></span>
				</div>
				<span class="item-title styled-subtitle"><?php echo $settings['content_labels_days_label'] ?></span>
			</div>
		<?php endif ?>
		<?php if ( 'yes' === $settings['content_labels_show_hours'] ): ?>
			<div class="countdown-item count-2">
				<div class="circle-raphael-hours"></div>
				<div class="wrap">
					<?php if ( $show_link ): ?><a <?php echo $this->get_render_attribute_string( $countdown_wrap_link ) ?>></a><?php endif ?>
					<span class="item-count countdown-hours title-h1"></span>
				</div>
				<span class="item-title styled-subtitle"><?php echo $settings['content_labels_hours_label'] ?></span>
			</div>
		<?php endif ?>
		<?php if ( 'yes' === $settings['content_labels_show_minutes'] ): ?>
			<div class="countdown-item count-3">
				<div class="circle-raphael-minutes"></div>
				<div class="wrap">
					<?php if ( $show_link ): ?><a <?php echo $this->get_render_attribute_string( $countdown_wrap_link ) ?>></a><?php endif ?>
					<span class="item-count countdown-minutes title-h1"></span>
				</div>
				<span class="item-title styled-subtitle"><?php echo $settings['content_labels_minutes_label'] ?></span>
			</div>
		<?php endif ?>
		<?php if ( 'yes' === $settings['content_labels_show_seconds'] ): ?>
			<div class="countdown-item count-4">
				<div class="circle-raphael-seconds"></div>
				<div class="wrap">
					<?php if ( $show_link ): ?><a <?php echo $this->get_render_attribute_string( $countdown_wrap_link ) ?>></a><?php endif ?>
					<span class="item-count countdown-seconds title-h1"></span>
				</div>
				<span class="item-title styled-subtitle"><?php echo $settings['content_labels_seconds_label'] ?></span>
			</div>
		<?php endif ?>
	</div>
</div>
