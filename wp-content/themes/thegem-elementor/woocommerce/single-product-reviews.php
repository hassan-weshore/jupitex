<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.3.0
 */

defined( 'ABSPATH' ) || exit;

global $product, $thegem_product_data;

if ( ! comments_open() ) {
	return;
}

$isLegacy = $thegem_product_data['product_page_layout'] == 'legacy';
$isTabs = $thegem_product_data['product_page_desc_review_layout'] == 'tabs';
$isTabsLegacy = $thegem_product_data['product_page_desc_review_layout_tabs_style'] == 'legacy';
$lightTitle = 'light';

?>
<div id="reviews" class="woocommerce-Reviews">
	<div id="comments">
		<<?=$isLegacy || (!$isLegacy && $isTabs && $isTabsLegacy) ? 'h3' : 'h4'?> class="woocommerce-Reviews-title <?=$isLegacy || (!$isLegacy && $isTabs && $isTabsLegacy) ? null : $lightTitle?>">
			<?php
			$count = $product->get_review_count();
			if ( $count && wc_review_ratings_enabled() ) {
				/* translators: 1: reviews count 2: product name */
				$reviews_title = sprintf( esc_html( _n( '%1$s review for %2$s', '%1$s reviews for %2$s', $count, 'woocommerce' ) ), esc_html( $count ), '<span class="ligth">' . get_the_title() . '</span>' );
				echo apply_filters( 'woocommerce_reviews_title', $reviews_title, $count, $product ); // WPCS: XSS ok.
			} else {
				esc_html_e( 'Reviews', 'woocommerce' );
			}
			?>
		</<?=$isLegacy || (!$isLegacy && $isTabs && $isTabsLegacy) ? 'h3' : 'h4'?>>
		<?php if ( have_comments() ) : ?>

			<div class="commentlist">
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments', 'style' => 'div' ) ) ); ?>
			</div>

			<?php
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="woocommerce-pagination">';
				paginate_comments_links(
					apply_filters(
						'woocommerce_comment_pagination_args',
						array(
							'prev_text' => is_rtl() ? '&rarr;' : '&larr;',
							'next_text' => is_rtl() ? '&larr;' : '&rarr;',
							'type'      => 'list',
						)
					)
				);
				echo '</nav>';
			endif;
			?>
		<?php else : ?>
			<p class="woocommerce-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'woocommerce' ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>
		<div id="review_form_wrapper">
			<div id="review_form">
				<?php
				$commenter    = wp_get_current_commenter();
				$comment_form = array(
					'title_reply'         => $isLegacy || (!$isLegacy && $isTabs && $isTabsLegacy) ? __( 'Add <span class="light">a review</span>', 'thegem' ) : __( '<span class="light">Add a review</span>', 'thegem' ),
					'title_reply_to'      => esc_html__( 'Leave a Reply to %s', 'woocommerce' ),
					'comment_notes_before' => '',
					'comment_notes_after' => '',
					'label_submit'        => esc_html__( 'Submit Review', 'thegem' ),
					'logged_in_as'        => '',
					'comment_field'       => '',
					'submit_field' => '<div class="form-submit gem-button-position-inline">%1$s</div>%2$s',
					'class_submit' => $isLegacy ? 'gem-button gem-button-size-medium submit' : 'gem-button gem-button-size-small submit',
					'submit_button' => '<button name="%1$s" type="submit" id="%2$s" class="%3$s" value="%4$s" />%4$s</button>',
				);

				$name_email_required = (bool) get_option( 'require_name_email', 1 );
				$fields              = array(
					'author' => array(
						'label'    => __( 'Name', 'woocommerce' ),
						'type'     => 'text',
						'value'    => $commenter['comment_author'],
						'required' => $name_email_required,
					),
					'email'  => array(
						'label'    => __( 'Email', 'woocommerce' ),
						'type'     => 'email',
						'value'    => $commenter['comment_author_email'],
						'required' => $name_email_required,
					),
				);

				$comment_form['fields'] = array();

				foreach ( $fields as $key => $field ) {
				    if ($isLegacy) {
					    $field_html  = '<div class="col-md-6 col-xs-12 comment-' . esc_attr( $key ) . '-input">';
					    $field_html .= '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' placeholder="' . esc_attr( $field['label'] . ( $field['required'] ? ' *' : '' ) ) . '"/>';
					    $field_html .= '</div>';
                    } else {
					    $field_html  = '<div class="comment-' . esc_attr( $key ) . '-input">';
					    $field_html .= '<label for="'.esc_attr( $key ).'">' . esc_attr( $field['label'] . ( $field['required'] ? ' *' : '' ) ) . '</label>';
					    $field_html .= '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . '/>';
					    $field_html .= '</div>';
                    }

					$comment_form['fields'][ $key ] = $field_html;
				}

				$account_page_url = wc_get_page_permalink( 'myaccount' );
				if ( $account_page_url ) {
					/* translators: %s opening and closing link tags respectively */
					$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'woocommerce' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
				}

				if ( wc_review_ratings_enabled() ) {
					$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating">' . esc_html__( 'Your rating', 'woocommerce' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '</label><select name="rating" id="rating" required>
						<option value="">' . esc_html__( 'Rate&hellip;', 'woocommerce' ) . '</option>
						<option value="5">' . esc_html__( 'Perfect', 'woocommerce' ) . '</option>
						<option value="4">' . esc_html__( 'Good', 'woocommerce' ) . '</option>
						<option value="3">' . esc_html__( 'Average', 'woocommerce' ) . '</option>
						<option value="2">' . esc_html__( 'Not that bad', 'woocommerce' ) . '</option>
						<option value="1">' . esc_html__( 'Very poor', 'woocommerce' ) . '</option>
					</select></div>';
				}

				$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__('Your review *', 'thegem') . '</label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

				comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		</div>
	<?php else : ?>
		<p class="woocommerce-verification-required"><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ); ?></p>
	<?php endif; ?>

	<div class="clear"></div>
</div>
