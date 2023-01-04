<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>


<?php if ('yes' === $settings['counter_show_person_block']) : ?>
<div class="team-person-info team-person centered-box default-background">

	<?php if( empty($settings['content_team_all_persons']) || $settings['content_team_all_persons'] === '0' ):?>

		<div class="team-person-image">
				<img width="80" height="80" src="<?php echo THEGEM_ELEMENTOR_WIDGET_COUNTER_URL.'/assets/img/team-thegem-person.jpg'; ?>">
		</div>
		<div class="team-person-name title-h2"><span class="light">STEVEN BEALS</span></div>
		<div class="team-person-position date-color styled-subtitle">Senior Sales Manager</div>
		<div class="team-person-phone title-h4"><a href="tel:+1%20(987)%201625346">+1 (987) 1625346</a></div>
		<div class="team-person-email"><a href="mailto:info@domain.tld">info@domain.tld</a></div>
		<div class="socials team-person-socials socials-colored-hover"><a href="#">
				<i class="socials-item-icon social-item-rounded facebook"></i></a>
		</div>
		<div class="socials team-person-socials socials-colored-hover"><a href="#">
				<i class="socials-item-icon social-item-rounded googleplus"></i></a>
		</div>
		<div class="socials team-person-socials socials-colored-hover"><a href="#">
				<i class="socials-item-icon social-item-rounded twitter"></i></a>
		</div>
		<div class="socials team-person-socials socials-colored-hover"><a href="#">
				<i class="socials-item-icon social-item-rounded linkedin"></i></a>
		</div>
		<div class="socials team-person-socials socials-colored-hover"><a href="#">
				<i class="socials-item-icon social-item-rounded instagram"></i></a>
		</div>
		<div class="socials team-person-socials socials-colored-hover"><a href="#">
				<i class="socials-item-icon social-item-rounded skype"></i></a>
		</div>

	<?php endif; ?>


	<?php if($settings["counter-preset3_content_elems_img"] === 'yes'): ?>
		<?php
			$thumbnail = get_the_post_thumbnail_url( $settings['content_team_all_persons'], 'thumbnail');
			echo (!empty($thumbnail)) ? '<div class="team-person-image"><img src="'.$thumbnail.'"/></div>' : ''; ?>
	<?php endif; ?>

	<?php if($settings["counter-preset3_content_elems_name"] === 'yes'): ?>
		<?php
			$name = get_post_meta($settings['content_team_all_persons'], 'thegem_team_person_data')[0]['name'];
			echo (!empty($name)) ? '<div class="team-person-name title-h2"><span class="light">'.$name.'</span></div>' : '';
		?>
	<?php endif; ?>

	<?php if($settings["counter-preset3_content_elems_position"] === 'yes'): ?>
		<?php
			$position = get_post_meta($settings['content_team_all_persons'], 'thegem_team_person_data')[0]['position'];
			echo (!empty($position)) ? '<div class="team-person-position date-color styled-subtitle">'.$position.'</div>' : '';
		?>
	<?php endif; ?>

	<?php if($settings["counter-preset3_content_elems_phone"] === 'yes'): ?>
		<?php
			$phone = get_post_meta($settings['content_team_all_persons'], 'thegem_team_person_data')[0]['phone'];
			echo (!empty($phone)) ? '<div class="team-person-phone title-h4"><a href="tel:'.$phone.'">'.$phone.'</a></div>' : '';
		?>
	<?php endif; ?>

	<?php if($settings["counter-preset3_content_elems_email"] === 'yes'): ?>

		<?php
			$email = get_post_meta($settings['content_team_all_persons'], 'thegem_team_person_data')[0]['email'];
			echo (!empty($email)) ? '<div class="team-person-email"><a href="mailto:'.$email.'">'.$email.'</a></div>' : '';
		?>
	<?php endif; ?>

	<?php if($settings["counter-preset3_content_elems_social"] === 'yes'): ?>

		<?php

			$facebook = get_post_meta($settings['content_team_all_persons'], 'thegem_team_person_data')[0]['social_link_facebook'];
			echo (!empty($facebook)) ? '<div class="socials team-person-socials socials-colored-hover"><a href="'.$facebook.'"><i class="socials-item-icon social-item-rounded facebook"></i></a></div>' : '';

			$googleplus = get_post_meta($settings['content_team_all_persons'], 'thegem_team_person_data')[0]['social_link_googleplus'];
			echo (!empty($facebook)) ? '<div class="socials team-person-socials socials-colored-hover"><a href="'.$googleplus.'"><i class="socials-item-icon social-item-rounded googleplus"></i></a></div>' : '';

			$twitter = get_post_meta($settings['content_team_all_persons'], 'thegem_team_person_data')[0]['social_link_twitter'];
			echo (!empty($twitter)) ? '<div class="socials team-person-socials socials-colored-hover"><a href="'.$twitter.'"><i class="socials-item-icon social-item-rounded twitter"></i></a></div>' : '';

			$linkedin = get_post_meta($settings['content_team_all_persons'], 'thegem_team_person_data')[0]['social_link_linkedin'];
			echo (!empty($linkedin)) ? '<div class="socials team-person-socials socials-colored-hover"><a href="'.$linkedin.'"><i class="socials-item-icon social-item-rounded linkedin"></i></a></div>' : '';

			$instagram = get_post_meta($settings['content_team_all_persons'], 'thegem_team_person_data')[0]['social_link_instagram'];
			echo (!empty($instagram)) ? '<div class="socials team-person-socials socials-colored-hover"><a href="'.$instagram.'"><i class="socials-item-icon social-item-rounded instagram"></i></a></div>' : '';

			$skype = get_post_meta($settings['content_team_all_persons'], 'thegem_team_person_data')[0]['social_link_skype'];
			echo (!empty($skype)) ? '<div class="socials team-person-socials socials-colored-hover"><a href="'.$skype.'"><i class="socials-item-icon social-item-rounded skype"></i></a></div>' : '';

			?>

	<?php endif; ?>

</div>
<?php endif; ?>

<?php if ('yes' === $settings['counter_show_connector'] || 'yes' === $settings['counter_show_connector_no']) : ?>
	<div class="divider-counter">
		<span></span>
	</div>
<?php endif; ?>

<div data-number-format="<?php echo esc_attr($settings['numbers_format' ]); ?>" class="gem-counter-box default-background">
	<div class="gem-counter">
		<div class="gem-counter-inner">
			<?php if(!empty($settings['counter_icon']['value'])) : ?>
				<div class="gem-counter-icon">
					<div class="gem-icon gem-icon-pack-material gem-icon-size-medium gem-icon-shape-circle gem-simple-icon <?php echo ('yes' === $settings['counter_animation_enabled'] ? ' lazy-loading-item' : ''); ?> <?php echo ('yes' === $settings['counter_animation_enabled'] ? ' lazy-loading-item-fading' : ''); ?>">
						<div class="gem-icon-inner default-background-border">
							<div class="icon-hover-bg"></div>
							<?php \Elementor\Icons_Manager::render_icon($settings['counter_icon'], ['aria-hidden' => 'true']); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<div class="gem-counter-number">
				<div class="gem-counter-odometer odometer odometer-auto-theme" data-to="<?php echo esc_attr($settings['counter_ending_number' ]); ?>">
					<?php if ( 'yes' !== $settings['counter_animation_enabled'] ) : ?>
						<?php echo $settings['counter_ending_number' ]; ?>
					<?php else: ?>
						<?php echo $settings['counter_starting_number' ]; ?>
					<?php endif; ?>
				</div>

				<?php if( ! empty($settings['counter_number_suffix' ]) ):?>
					<div class="gem-counter-suffix <?php echo ('yes' === $settings['counter_spacing_suffix'])?'gem-counter-suffix-spacing':''?>">
						<?php echo esc_html($settings['counter_number_suffix' ]); ?>
					</div>
				<?php endif; ?>
			</div>
			<div class="gem-counter-text styled-subtitle">
				<?php echo ( ! empty($settings['counter_description' ]) ) ? esc_html($settings['counter_description' ]) : 'Counters'; ?>
			</div>
		</div>
	</div>
	<?php
		$link = $this->get_link_url( $settings );
		if ( $link ) :
			$this->add_link_attributes( 'link', $link );
			$this->add_render_attribute( 'link', 'class', 'gem-counter-link' );
		?>

			<a <?php echo ($this->get_render_attribute_string( 'link' )) ?>></a>

	<?php endif; ?>
</div>