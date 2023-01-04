<?php
/**
 * The template for displaying the footer
 */

?>

		</div><!-- #main -->
		<div id="lazy-loading-point"></div>

		<footer class="custom-footer">
			<div class="<?php echo (get_page_template_slug() !== 'single-thegem_footer-fullwidth.php' ? 'container' : 'fullwidth-content' ); ?>">
				<?php
					while ( have_posts() ) : the_post();
						the_content();
					endwhile;
				?>
			</div>
		</footer>

	</div><!-- #page -->

	<?php if(thegem_get_option('header_layout') == 'perspective') : ?>
		</div><!-- #perspective -->
	<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
