<?php
/**
 * Apply filters button
 */

$show_apply_button = ! empty( $settings['apply_button'] ) ? filter_var( $settings['apply_button'], FILTER_VALIDATE_BOOLEAN ) : false;

if ( ! $show_apply_button ) {
	return;
}

$apply_button_text = $settings['apply_button_text'];

if ( empty( $apply_button_text ) ) {
	return;
}

$use_tabindex = filter_var( jet_smart_filters()->settings->get( 'use_tabindex', false ), FILTER_VALIDATE_BOOLEAN );
$tabindex     = $use_tabindex ? 'tabindex="0"' : '';

?>
<div class="apply-filters"<?php if ( ! empty( $data_atts ) ) {
	echo ' ' . $data_atts;
} ?>>
	<button
		type="button"
		class="apply-filters__button"
		<?php echo $tabindex; ?>
	><?php echo $apply_button_text; ?></button>
</div>