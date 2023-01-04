<?php
if ( $item['qf_item_link']['url'] ) :
	$link_key = 'link_' . $index;
	$this->add_link_attributes( $link_key, $item['qf_item_link'] );
	$this->add_render_attribute( $link_key, 'class', 'quickfinder-item-link' ); ?>
	<a <?php echo $this->get_render_attribute_string( $link_key ); ?>></a>
<?php endif; ?>