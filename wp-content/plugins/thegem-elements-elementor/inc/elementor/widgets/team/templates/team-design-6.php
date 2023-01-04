<?php
if ( ! defined( 'ABSPATH' ) ) exit;

?><div class="<?php echo esc_attr( $columns_classes ); ?><?php echo esc_attr( $hover_class ); ?>  thegem-wrap inline-column">

	<div id="post-<?php the_ID(); ?>" <?php post_class( array( 'team-person', 'default-background' ) ); ?> >

		<div class="team-person-hover">

			<div class="team-person-box clearfix">

				<?php if ( has_post_thumbnail() && in_array( 'img', $els, true ) ) : ?>
					<div class="team-person-box-left">
						<div class="team-person-image">
						<?php echo $thegem_image_start;
									thegem_post_thumbnail( 'thegem-person-80', false, 'img-responsive', array( 'srcset' => array( '2x' => 'thegem-person' ) ) );
							  echo $thegem_image_end; ?>
						</div>
					</div>
				<?php endif; ?>

				<div class="team-person-box-right">

					<div class="team-person-info">

						<?php if ( ! empty( $thegem_item_data['name'] ) && in_array( 'name', $els, true ) ) : ?>
							<?php echo thegem_get_data( $thegem_item_data, 'name', '', '<div class="team-person-name styled-subtitle">', '</div>' ); ?>
						<?php endif; ?>

						<?php if ( ! empty( $thegem_item_data['position'] ) && in_array( 'position', $els, true ) ) : ?>
							<?php echo thegem_get_data( $thegem_item_data, 'position', '', '<div class="team-person-position date-color">', '</div>' ); ?>
						<?php endif; ?>

						<?php if ( get_the_content() && in_array( 'descript', $els, true ) ) : ?>
							<div class="team-person-description"><?php the_content(); ?></div>
						<?php endif; ?>

						<?php if ( ! empty( $thegem_item_data['phone'] ) && in_array( 'phone', $els, true ) ) : ?>
							<div class="gem-styled-color-1">
								<div class="team-person-phone title-h6">
									<a href="<?php echo esc_url( 'tel:' . $thegem_item_data['phone'] ); ?>"><?php echo esc_html( $thegem_item_data['phone'] ); ?></a>
								</div>
							</div>
						<?php endif; ?>

						<?php if ( ! empty( $thegem_item_data['email'] ) && in_array( 'email', $els, true ) ) : ?>
							<div class="team-person-email">
								<a href="mailto:<?php echo esc_attr( $thegem_item_data['email'] ); ?>"><?php echo esc_attr( $thegem_item_data['email'] ); ?></a>
							</div>
						<?php endif; ?>

					</div>

					<?php if ( $thegem_socials_block && in_array( 'social', $els, true ) ) : ?>
						<div class="socials team-person-socials socials-colored-hover"><?php echo $thegem_socials_block; ?></div>
					<?php endif; ?>

				</div>
			</div>
		</div>

			<?php if ( ! empty( $thegem_item_data['link'] ) ) : ?>
				<?php echo $thegem_link_start . $thegem_link_end; ?>
			<?php endif; ?>


	</div>

</div>