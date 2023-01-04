<?php
/**
 * Checkbox list item template
 */

$checked_icon = apply_filters( 'jet-smart-filters/templates/checkboxes-item/checked-icon', 'fa fa-check' );

$use_tabindex = filter_var( jet_smart_filters()->settings->get( 'use_tabindex', false ), FILTER_VALIDATE_BOOLEAN );
$tabindex     = $use_tabindex ? 'tabindex="0"' : '';

?>
<div class="jet-checkboxes-list__row jet-filter-row<?php echo $extra_classes; ?>">
	<label class="jet-checkboxes-list__item" <?php echo $tabindex; ?>>
		<input
			type="checkbox"
			class="jet-checkboxes-list__input"
			name="<?php echo $query_var; ?>"
			value="<?php echo $value; ?>"
			data-label="<?php echo $label; ?>"
			<?php echo $checked; ?>
		>
		<div class="jet-checkboxes-list__button">
			<?php if ( $show_decorator ) : ?>
				<span class="jet-checkboxes-list__decorator"><i class="jet-checkboxes-list__checked-icon <?php echo $checked_icon ?>"></i></span>
			<?php endif; ?>
			<span class="jet-checkboxes-list__label"><?php echo $label; ?></span>
			<?php do_action('jet-smart-filter/templates/counter', $args ); ?>
		</div>
	</label>
</div>