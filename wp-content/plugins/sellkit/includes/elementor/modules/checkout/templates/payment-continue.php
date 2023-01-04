<section class="place-order sellkit-one-page-checkout-place-order">
	<noscript>
		<?php
		//phpcs:disable
		/* translators: $1 and $2 opening and closing emphasis tags respectively */
		printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), '<em>', '</em>' );
		//phpcs:enable
		?>
		<br/><button type="submit" class="button alt sellkit-checkout-widget-primary-button" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
	</noscript>

	<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

	<?php do_action( 'sellkit-checkout-multistep-third-step-back-btn' ); ?>

	<?php echo apply_filters( 'woocommerce_order_button_html', '<button type="submit" class="button alt sellkit-checkout-widget-primary-button" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

	<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

	<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
	<?php /* Hide place order button container after ajax call update. we already have it at bottom  */ ?>
	<script>
		jQuery( document ).ajaxComplete( function() {
			jQuery( '#payment > .place-order' ).css( 'display', 'none' ).hide();
		} );
	</script>
</section>
