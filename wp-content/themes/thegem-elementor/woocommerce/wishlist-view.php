<?php
/**
 * Wishlist page template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 2.0.12
 */
?>

<?php
	wp_enqueue_script('thegem-woocommerce');

	do_action( 'yith_wcwl_before_wishlist_form', $wishlist );
?>

<form id="yith-wcwl-form" action="<?php echo esc_attr( $form_action ); ?>" method="post" class="woocommerce<?php echo (thegem_checkout_get_type() == 'one-page-modern' ? ' woocommerce-wishlist-modern' : ''); ?>">

	<?php wp_nonce_field( 'yith-wcwl-form', 'yith_wcwl_form_nonce' ) ?>

	<!-- TITLE -->
	<?php
	do_action( 'yith_wcwl_before_wishlist_title' ); ?>

	<div class="woocommerce-before-cart clearfix">
		<?php do_action( 'yith_wcwl_before_wishlist' ); ?>
		<div class="cart-short-info">
			<?php
				echo sprintf(wp_kses(__('You Have <span class="items-count">%d Items</span> In Wishlist:', 'thegem'), array('span' => array('class' => array()))), $count);
			?>
		</div>
	</div>

	<div class="gem-table wishlist-content">
		<!-- WISHLIST TABLE -->
		<table class="shop_table cart wishlist_table" data-pagination="<?php echo esc_attr( $pagination )?>" data-per-page="<?php echo esc_attr( $per_page )?>" data-page="<?php echo esc_attr( $current_page )?>" data-id="<?php echo esc_attr( $wishlist_id ); ?>" data-token="<?php echo esc_attr( $wishlist_token ); ?>">

			<?php $column_count = 2; ?>

			<thead>
			<tr>
				<?php if( $show_cb ) : ?>

					<th class="product-checkbox">
						<input type="checkbox" class="gem-checkbox" value="" name="" id="bulk_add_to_cart"/>
					</th>

				<?php
					$column_count ++;
				endif;
				?>

				<?php if( $is_user_owner ): ?>
					<th class="product-remove"></th>
				<?php
					$column_count ++;
				endif;
				?>

				<th class="product-name" colspan="2">
					<span class="nobr"><?php echo apply_filters( 'yith_wcwl_wishlist_view_name_heading', esc_html__( 'Product', 'thegem' ) ) ?></span>
				</th>

				<?php if( $show_price ) : ?>

					<th class="product-price">
						<span class="nobr">
							<?php echo apply_filters( 'yith_wcwl_wishlist_view_price_heading', esc_html__( 'Price', 'thegem' ) ) ?>
						</span>
					</th>

				<?php
					$column_count ++;
				endif;
				?>

				<?php if( $show_stock_status ) : ?>

					<th class="product-stock-stauts">
						<span class="nobr">
							<?php echo apply_filters( 'yith_wcwl_wishlist_view_stock_heading', esc_html__( 'Stock Status', 'thegem' ) ) ?>
						</span>
					</th>

				<?php
					$column_count ++;
				endif;
				?>

				<?php if( $show_last_column ) : ?>

					<th class="product-add-to-cart"></th>

				<?php
					$column_count ++;
				endif;
				?>
			</tr>
			</thead>

			<tbody>
			<?php
			if( count( $wishlist_items ) > 0 ) :
				foreach( $wishlist_items as $item ) :
					global $product;
					if( function_exists( 'wc_get_product' ) ) {
						$product = wc_get_product( $item['prod_id'] );
					}
					else{
						$product = get_product( $item['prod_id'] );
					}

					if( $product !== false && $product->exists() ) :
						$availability = $product->get_availability();
						$stock_status = $availability['class'];
						?>
						<tr id="yith-wcwl-row-<?php echo $item['prod_id'] ?>" class="cart_item" data-row-id="<?php echo $item['prod_id'] ?>">
							<?php if( $show_cb ) : ?>
								<td class="product-checkbox">
									<input type="checkbox" class="gem-checkbox" value="<?php echo esc_attr( $item['prod_id'] ) ?>" name="add_to_cart[]" <?php echo ( $product->get_type() != 'simple' ) ? 'disabled="disabled"' : '' ?>/>
								</td>
							<?php endif ?>

							<?php if( $is_user_owner ): ?>
							<td class="product-remove <?php echo $show_cb ? 'with-cb' : ''; ?>">
								<div>
									<a href="<?php echo esc_url( add_query_arg( 'remove_from_wishlist', $item['prod_id'] ) ) ?>" class="remove remove_from_wishlist" title="<?php esc_attr_e( 'Remove this product', 'thegem' ) ?>">&times;</a>
								</div>
							</td>
							<?php endif; ?>

							<td class="product-thumbnail">
								<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item['prod_id'] ) ) ) ?>">
									<?php echo $product->get_image() ?>
								</a>
							</td>

							<td class="product-name">
								<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item['prod_id'] ) ) ) ?>"><?php echo apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ) ?></a>
								<?php do_action( 'yith_wccl_table_after_product_name', $item ); ?>
							</td>

							<?php if( $show_price ) : ?>
								<td class="product-price">
									<?php echo $product->get_price() ? $product->get_price_html() : apply_filters( 'yith_free_text', __( 'Free!', 'yith-woocommerce-wishlist' ) ); ?>
								</td>
							<?php endif ?>

							<?php if( $show_stock_status ) : ?>
								<td class="product-stock-status">
									<?php
									if( $stock_status == 'out-of-stock' ) {
										$stock_status = "Out";
										echo '<span class="wishlist-out-of-stock">' . esc_html__( 'Out of Stock', 'thegem' ) . '</span>';
									} else {
										$stock_status = "In";
										echo '<span class="wishlist-in-stock">' . esc_html__( 'In Stock', 'thegem' ) . '</span>';
									}
									?>
								</td>
							<?php endif ?>

							<?php if( $show_last_column ): ?>
							<td class="product-add-to-cart">
								<!-- Date added -->
								<?php
								if( $show_dateadded && isset( $item['dateadded'] ) ):
									echo '<span class="dateadded">' . sprintf( esc_html__( 'Added on : %s', 'thegem' ), date_i18n( get_option( 'date_format' ), strtotime( $item['dateadded'] ) ) ) . '</span>';
								endif;
								?>

								<!-- Add to cart button -->
								<?php if( $show_add_to_cart && isset( $stock_status ) && $stock_status != 'Out' ): ?>
									<?php
										thegem_button(array(
											'tag' => 'a',
											'href' => esc_url( $product->add_to_cart_url() ),
											'text' => esc_html( $product->add_to_cart_text() ),
											'style' => 'flat',
											'size' => 'tiny',
											'attributes' => array(
												'rel' => 'nofollow',
												'data-product_id' => esc_attr( $product->get_id() ),
												'data-product_sku' => esc_attr( $product->get_sku() ),
												'data-quantity' => esc_attr( isset( $quantity ) ? $quantity : 1 ),
											),
											'extra_class' => ($product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button ' : '') . esc_attr( $product->get_type() ),
											'background_color' => thegem_get_option('cart_table_header_background_color') ? thegem_get_option('cart_table_header_background_color') : thegem_get_option('button_background_basic_color'),
											'hover_background_color' => thegem_get_option('button_background_hover_color')
										), true);
									?>
								<?php endif ?>

								<!-- Change wishlist -->
								<?php if( $available_multi_wishlist && is_user_logged_in() && count( $users_wishlists ) > 1 && $move_to_another_wishlist ): ?>
								<select class="change-wishlist selectBox gem-combobox">
									<option value=""><?php esc_html_e( 'Move', 'thegem' ) ?></option>
									<?php
									foreach( $users_wishlists as $wl ):
										if( $wl['wishlist_token'] == $wishlist_token ){
											continue;
										}

									?>
										<option value="<?php echo esc_attr( $wl['wishlist_token'] ) ?>">
											<?php
											$wl_title = ! empty( $wl['wishlist_name'] ) ? esc_html( $wl['wishlist_name'] ) : esc_html( $default_wishlsit_title );
											if( $wl['wishlist_privacy'] == 1 ){
												$wl_privacy = esc_html__( 'Shared', 'thegem' );
											}
											elseif( $wl['wishlist_privacy'] == 2 ){
												$wl_privacy = esc_html__( 'Private', 'thegem' );
											}
											else{
												$wl_privacy = esc_html__( 'Public', 'thegem' );
											}

											echo sprintf( '%s - %s', $wl_title, $wl_privacy );
											?>
										</option>
									<?php
									endforeach;
									?>
								</select>
								<?php endif; ?>
							</td>
						<?php endif; ?>
						</tr>
					<?php
					endif;
				endforeach;
			else: ?>
				<tr>
					<td colspan="<?php echo esc_attr( $column_count ) ?>" class="wishlist-empty"><?php esc_html_e( 'No products were added to the wishlist', 'thegem' ) ?></td>
				</tr>
			<?php
			endif;

			if( ! empty( $page_links ) ) : ?>
				<tr class="pagination-row">
					<td colspan="<?php echo esc_attr( $column_count ) ?>">
						<div class="gem-pagination woocommerce-pagination centered-box">
							<?php echo $page_links; ?>
						</div>
					</td>
				</tr>
			<?php endif ?>
			</tbody>

			<tfoot>
			<tr>
				<td colspan="<?php echo esc_attr( $column_count ) ?>">
					<?php if( $show_cb ) : ?>
						<div class="custom-add-to-cart-button-cotaniner">
							<a href="<?php echo esc_url( add_query_arg( array( 'wishlist_products_to_add_to_cart' => '', 'wishlist_token' => $wishlist_token ) ) ) ?>" class="button alt" id="custom_add_to_cart"><?php echo apply_filters( 'yith_wcwl_custom_add_to_cart_text', esc_html__( 'Add the selected products to the cart', 'thegem' ) ) ?></a>
						</div>
					<?php endif; ?>

					<?php if ( is_user_logged_in() && $is_user_owner && $show_ask_estimate_button && $count > 0 ): ?>
						<div class="ask-an-estimate-button-container">
							<a href="<?php echo ( $additional_info ) ? '#ask_an_estimate_popup' : esc_url($ask_estimate_url) ?>" class="btn button ask-an-estimate-button" <?php echo ( $additional_info ) ? 'data-rel="prettyPhoto[ask_an_estimate]"' : '' ?> >
							<?php echo apply_filters( 'yith_wcwl_ask_an_estimate_icon', '<i class="fa fa-shopping-cart"></i>' )?>
							<?php echo apply_filters( 'yith_wcwl_ask_an_estimate_text', esc_html__( 'Ask for an estimate', 'thegem' ) ) ?>
						</a>
						</div>
					<?php endif; ?>
				</td>
			</tr>
			</tfoot>

		</table>
	</div>

	<div class="wishlist-content responsive">
		<div class="wishlist_table cart" data-pagination="<?php echo esc_attr( $pagination )?>" data-per-page="<?php echo esc_attr( $per_page )?>" data-page="<?php echo esc_attr( $current_page )?>" data-id="<?php echo ( is_user_logged_in() ) ? esc_attr( $wishlist_id ) : '' ?>" data-token="<?php echo esc_attr( $wishlist_token ); ?>">
			<?php
			if( count( $wishlist_items ) > 0 ) :
				foreach( $wishlist_items as $item ) :
					global $product;
					if( function_exists( 'wc_get_product' ) ) {
						$product = wc_get_product( $item['prod_id'] );
					}
					else{
						$product = get_product( $item['prod_id'] );
					}

					if( $product !== false && $product->exists() ) :
						$availability = $product->get_availability();
						$stock_status = $availability['class'];
						?>

						<?php
							$product_columns_count = 0;
							if ($show_price) {
								$product_columns_count++;
							}
							if ($show_stock_status) {
								$product_columns_count++;
							}
							if ($show_last_column) {
								$product_columns_count++;
							}
						?>

						<div class="cart-item cart_item rounded-corners shadow-box">
							<table class="shop_table wishlist_table cart"><tbody><tr id="yith-wcwl-row-<?php echo $item['prod_id'] ?>" data-row-id="<?php echo $item['prod_id'] ?>">
								<?php if( $show_cb ) : ?>
									<td class="product-checkbox">
										<input type="checkbox" class="gem-checkbox" value="<?php echo esc_attr( $item['prod_id'] ) ?>" name="add_to_cart[]" <?php echo ( $product->get_type() != 'simple' ) ? 'disabled="disabled"' : '' ?>/>
									</td>
								<?php endif ?>

								<td class="product-thumbnail">
									<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item['prod_id'] ) ) ) ?>">
										<?php echo $product->get_image() ?>
									</a>
								</td>

								<td class="product-name">
									<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item['prod_id'] ) ) ) ?>"><?php echo apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ) ?></a>
									<?php do_action( 'yith_wccl_table_after_product_name', $item ); ?>
								</td>

								<?php if( $is_user_owner ): ?>
								<td class="product-remove <?php echo $show_cb ? 'with-cb' : ''; ?>">
									<div>
										<a href="<?php echo esc_url( add_query_arg( 'remove_from_wishlist', $item['prod_id'] ) ) ?>" class="remove remove_from_wishlist" title="<?php esc_attr_e( 'Remove this product', 'thegem' ) ?>">&times;</a>
									</div>
								</td>
								<?php endif; ?>
							</tr></tbody></table>

							<?php if ($product_columns_count > 0): ?>
								<div class="product-info">
									<div class="product-info-header clearfix">
										<?php if( $show_price ) : ?>

											<div class="product-price" style="width: <?php echo 100.0/$product_columns_count; ?>%;">
												<span class="nobr">
													<?php echo apply_filters( 'yith_wcwl_wishlist_view_price_heading', esc_html__( 'Price', 'thegem' ) ) ?>
												</span>
											</div>

										<?php endif; ?>

										<?php if( $show_stock_status ) : ?>

											<div class="product-stock-stauts" style="width: <?php echo 100.0/$product_columns_count; ?>%;">
												<span class="nobr">
													<?php echo apply_filters( 'yith_wcwl_wishlist_view_stock_heading', esc_html__( 'Status', 'thegem' ) ) ?>
												</span>
											</div>

										<?php endif; ?>

										<?php if( $show_last_column ) : ?>

											<div class="product-add-to-cart" style="width: <?php echo 100.0/$product_columns_count; ?>%;">&nbsp;</div>

										<?php endif; ?>
									</div>

									<div class="product-info-content clearfix">
										<?php if( $show_price ) : ?>
											<div class="product-price" style="width: <?php echo 100.0/$product_columns_count; ?>%;">
												<?php
												if( is_a( $product, 'WC_Product_Bundle' ) ){
													if( $product->min_price != $product->max_price ){
														echo sprintf( '%s - %s', wc_price( $product->min_price ), wc_price( $product->max_price ) );
													}
													else{
														echo wc_price( $product->min_price );
													}
												}
												elseif( $product->get_price() != '0' ) {
													echo $product->get_price_html();
												}
												else {
													echo apply_filters( 'yith_free_text', esc_html__( 'Free!', 'thegem' ) );
												}
												?>
											</div>
										<?php endif ?>

										<?php if( $show_stock_status ) : ?>
											<div class="product-stock-status" style="width: <?php echo 100.0/$product_columns_count; ?>%;">
												<?php
												if( $stock_status == 'out-of-stock' ) {
													$stock_status = "Out";
													echo '<span class="wishlist-out-of-stock">' . esc_html__( 'Out of Stock', 'thegem' ) . '</span>';
												} else {
													$stock_status = "In";
													echo '<span class="wishlist-in-stock">' . esc_html__( 'In Stock', 'thegem' ) . '</span>';
												}
												?>
											</div>
										<?php endif ?>

										<?php if( $show_last_column ): ?>
										<div class="product-add-to-cart" style="width: <?php echo 100.0/$product_columns_count; ?>%;">
											<!-- Date added -->
											<?php
											if( $show_dateadded && isset( $item['dateadded'] ) ):
												echo '<span class="dateadded">' . sprintf( esc_html__( 'Added on : %s', 'thegem' ), date_i18n( get_option( 'date_format' ), strtotime( $item['dateadded'] ) ) ) . '</span>';
											endif;
											?>

											<!-- Add to cart button -->
											<?php if( $show_add_to_cart && isset( $stock_status ) && $stock_status != 'Out' ): ?>
												<?php
													thegem_button(array(
														'tag' => 'a',
														'href' => esc_url( $product->add_to_cart_url() ),
														'text' => esc_html( $product->add_to_cart_text() ),
														'style' => 'flat',
														'size' => 'tiny',
														'attributes' => array(
															'rel' => 'nofollow',
															'data-product_id' => esc_attr( $product->get_id() ),
															'data-product_sku' => esc_attr( $product->get_sku() ),
															'data-quantity' => esc_attr( isset( $quantity ) ? $quantity : 1 ),
														),
														'extra_class' => ($product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button ' : '') . esc_attr( $product->get_type() ),
											'background_color' => thegem_get_option('cart_table_header_background_color') ? thegem_get_option('cart_table_header_background_color') : thegem_get_option('button_background_basic_color'),
														'hover_background_color' => thegem_get_option('button_background_hover_color')
													), true);
												?>
											<?php endif ?>

											<!-- Change wishlist -->
											<?php if( $available_multi_wishlist && is_user_logged_in() && count( $users_wishlists ) > 1 && $move_to_another_wishlist ): ?>
											<select class="change-wishlist selectBox gem-combobox">
												<option value=""><?php esc_html_e( 'Move', 'thegem' ) ?></option>
												<?php
												foreach( $users_wishlists as $wl ):
													if( $wl['wishlist_token'] == $wishlist_token ){
														continue;
													}

												?>
													<option value="<?php echo esc_attr( $wl['wishlist_token'] ) ?>">
														<?php
														$wl_title = ! empty( $wl['wishlist_name'] ) ? esc_html( $wl['wishlist_name'] ) : esc_html( $default_wishlsit_title );
														if( $wl['wishlist_privacy'] == 1 ){
															$wl_privacy = esc_html__( 'Shared', 'thegem' );
														}
														elseif( $wl['wishlist_privacy'] == 2 ){
															$wl_privacy = esc_html__( 'Private', 'thegem' );
														}
														else{
															$wl_privacy = esc_html__( 'Public', 'thegem' );
														}

														echo sprintf( '%s - %s', $wl_title, $wl_privacy );
														?>
													</option>
												<?php
												endforeach;
												?>
											</select>
											<?php endif; ?>
										</div>
										<?php endif; ?>

									</div>

								</div>
							<?php endif; ?>
						</div>
					<?php
					endif;
				endforeach;
			else: ?>
				<table class="shop_table wishlist_table cart"><tr>
					<td colspan="<?php echo esc_attr( $column_count ) ?>" class="wishlist-empty"><?php esc_html_e( 'No products were added to the wishlist', 'thegem' ) ?></td>
				</tr></table>
			<?php
			endif;
			?>

			<?php if( ! empty( $page_links ) ) : ?>
				<div class="pagination-row">
					<div class="gem-pagination woocommerce-pagination centered-box">
						<?php echo $page_links ?>
					</div>
				</div>
			<?php endif ?>

			<div>
				<?php if( $show_cb ) : ?>
					<div class="custom-add-to-cart-button-cotaniner">
						<a href="<?php echo esc_url( add_query_arg( array( 'wishlist_products_to_add_to_cart' => '', 'wishlist_token' => $wishlist_token ) ) ) ?>" class="button alt" id="custom_add_to_cart"><?php echo apply_filters( 'yith_wcwl_custom_add_to_cart_text', esc_html__( 'Add the selected products to the cart', 'thegem' ) ) ?></a>
					</div>
				<?php endif; ?>

				<?php if ( is_user_logged_in() && $is_user_owner && $show_ask_estimate_button && $count > 0 ): ?>
					<div class="ask-an-estimate-button-container">
						<a href="<?php echo ( $additional_info ) ? '#ask_an_estimate_popup' : esc_url($ask_estimate_url) ?>" class="btn button ask-an-estimate-button" <?php echo ( $additional_info ) ? 'data-rel="prettyPhoto[ask_an_estimate]"' : '' ?> >
						<?php echo apply_filters( 'yith_wcwl_ask_an_estimate_icon', '<i class="fa fa-shopping-cart"></i>' )?>
						<?php echo apply_filters( 'yith_wcwl_ask_an_estimate_text', esc_html__( 'Ask for an estimate', 'thegem' ) ) ?>
					</a>
					</div>
				<?php endif; ?>

				<?php
				do_action( 'yith_wcwl_before_wishlist_share' );

				if ( is_user_logged_in() && $is_user_owner && $share_enabled ){
					yith_wcwl_get_template( 'share.php', $share_atts );
				}

				do_action( 'yith_wcwl_after_wishlist_share' );
				?>
			</div>
		</div>
	</div>

	<?php wp_nonce_field( 'yith_wcwl_edit_wishlist_action', 'yith_wcwl_edit_wishlist' ); ?>

	<input type="hidden" value="<?php echo $wishlist_token ?>" name="wishlist_id" id="wishlist_id">

	<?php do_action( 'yith_wcwl_after_wishlist' ); ?>

</form>

<?php do_action( 'yith_wcwl_after_wishlist_form', $wishlist ); ?>

<?php if( $additional_info ): ?>
	<div id="ask_an_estimate_popup">
		<form action="<?php echo $ask_estimate_url ?>" method="post" class="wishlist-ask-an-estimate-popup">
			<?php if( ! empty( $additional_info_label ) ):?>
				<label for="additional_notes"><?php echo esc_html( $additional_info_label ) ?></label>
			<?php endif; ?>
			<textarea id="additional_notes" name="additional_notes"></textarea>

			<button class="btn button ask-an-estimate-button ask-an-estimate-button-popup" >
				<?php echo apply_filters( 'yith_wcwl_ask_an_estimate_icon', '<i class="fa fa-shopping-cart"></i>' )?>
				<?php esc_html_e( 'Ask for an estimate', 'thegem' ) ?>
			</button>
		</form>
	</div>
<?php endif; ?>
