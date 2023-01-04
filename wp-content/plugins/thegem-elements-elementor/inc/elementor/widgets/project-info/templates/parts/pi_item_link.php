<?php

$link_key = 'link_' . $index;
$this->add_link_attributes( $link_key, $item['pi_item_link'] );
$link = $this->get_render_attribute_string( $link_key );
echo '<a class="project-info-item-link"'. $link .'></a>';