<?php
/**
 * Posts navigation template
 */
?>
<# var icons = <?php echo json_encode( jet_blog_tools()->get_svg_arrows() ); ?>#>
<nav class="navigation posts-navigation" role="navigation">
	<div class="nav-links">
		<div class="nav-previous">
			<a href="#">
				<i class="jet-arrow-prev jet-blog-arrow">{{ icons['prev'] }}</i>
				<?php $this->_edit_html( 'prev_text' ); ?>
			</a>
		</div>
		<div class="nav-next">
			<a href="#">
				<?php $this->_edit_html( 'next_text' ); ?>
				<i class="jet-arrow-next jet-blog-arrow">{{ icons['next'] }}</i>
			</a>
		</div>
	</div>
</nav>
