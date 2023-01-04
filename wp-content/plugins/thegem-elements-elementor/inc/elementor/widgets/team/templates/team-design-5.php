<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="<?php echo esc_attr( $columns_classes ); ?><?php echo esc_attr( $hover_class ); ?> thegem-wrap inline-column">

	<div id="post-<?php the_ID(); ?>" <?php post_class( array( 'team-person', 'centered-box', 'default-background') ); ?> >

		<div class="team-person-hover">

			<?php if ( has_post_thumbnail() && in_array( 'img', $els, true ) ) : ?>
				<div class="team-person-image">
					<?php
					$thegem_sources = array(
						array( 'srcset' => array( '2x' => 'thegem-person' ) )
					);
					if ( $settings['columns'] === 4 ) {
						$thegem_sources = array(
							array(
								'media' => '(max-width: 1000px)',
								'srcset' => array(
									'1x' => 'thegem-person-240',
									'2x' => 'thegem-person'
								)
							),
							array(
								'media' => '(max-width: 1920px)',
								'srcset' => array(
									'1x' => 'thegem-person-160',
									'2x' => 'thegem-person'
								)
							)
						);
					}
					echo $thegem_image_start;
					echo '<span class="image-hover">';
					thegem_post_picture( 'thegem-person', $thegem_sources, array( 'class' => 'img-responsive' ), false );
					echo '</span>';
					echo $thegem_image_end; ?>
				</div>
			<?php endif; ?>

			<div class="team-person-info clearfix">

				<?php if ( ! empty( $thegem_item_data['name'] ) && in_array( 'name', $els, true ) ) : ?>
					<?php echo thegem_get_data( $thegem_item_data, 'name', '', '<div class="team-person-name styled-subtitle '.($settings['columns'] === 1 || $settings['columns']===2 ? 'title-h4' : 'title-h5').'" ' . '>', '</div>' ); ?>
				<?php endif; ?>

				<?php if ( ! empty( $thegem_item_data['position'] ) && in_array( 'position', $els, true ) ) : ?>
					<?php echo thegem_get_data( $thegem_item_data, 'position', '', '<div class="team-person-position date-color" ' . '>', '</div>' ); ?>
				<?php endif; ?>

				<?php if ( get_the_content() && in_array( 'descript', $els, true ) ) : ?>
					<div class="team-person-description"><?php the_content(); ?></div>
				<?php endif; ?>

				<?php if ( ! empty( $thegem_item_data['phone'] ) && in_array( 'phone', $els, true ) ) : ?>
					<div class="team-person-phone styled-subtitle">
						<a href="<?php echo esc_url( 'tel:' . $thegem_item_data['phone'] ); ?>"><?php echo esc_html( $thegem_item_data['phone'] ); ?></a>
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

		<?php if ( ! empty( $thegem_item_data['link'] ) ) : ?>
			<?php echo $thegem_link_start . $thegem_link_end; ?>
		<?php endif; ?>

	</div>

</div>