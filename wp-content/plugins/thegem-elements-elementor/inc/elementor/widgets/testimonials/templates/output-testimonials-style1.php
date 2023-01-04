<?php
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div id="post-<?php the_ID(); ?>" <?php post_class('gem-testimonial-item'); ?>>
	<?php if( $thegem_item_data['link'] ) : ?><a href="<?php echo esc_url( $thegem_item_data['link'] ); ?>" target="<?php echo esc_attr( $thegem_item_data['link_target'] ); ?>"><?php endif; ?>
		<div class="gem-testimonial-wrapper  <?php if( !in_array( 'quote', $els, false ) ) : ?> quote-color-added <?php endif; ?>">

			<?php if ( has_post_thumbnail() && in_array( 'img', $els, true ) ) : ?>
				<div class="gem-testimonial-image">
					<span>
						<?php thegem_post_thumbnail( NULL, false, 'img-responsive img-circle', array( 'srcset' => array( '2x' => 'thegem-testimonial' ) ) ); ?>
					</span>
				</div>
			<?php endif; ?>

			<div class="gem-testimonial-content">

			<?php if ( ! empty( $thegem_item_data['name'] ) && in_array( 'name', $els, true ) ) : ?>
				<?php echo thegem_get_data( $thegem_item_data, 'name', '', '<div class="gem-testimonial-name title-h6">', '</div>' ); ?>
			<?php endif; ?>

			<?php if ( ! empty( $thegem_item_data['company'] ) && in_array( 'company', $els, true ) ) : ?>
				<?php echo thegem_get_data( $thegem_item_data, 'company', '', '<div class="gem-testimonial-company">', '</div>' ); ?>
			<?php endif; ?>

			<?php if ( ! empty( $thegem_item_data['position'] ) && in_array( 'position', $els, true ) ) : ?>
				<?php echo thegem_get_data( $thegem_item_data, 'position', '', '<div class="gem-testimonial-position">', '</div>' ); ?>
			<?php endif; ?>

				<div class="gem-testimonial-text">
					<?php the_content(); ?>
				</div>
			</div>
	</div>

	<?php if($thegem_item_data['link']) : ?></a><?php endif; ?>
</div>