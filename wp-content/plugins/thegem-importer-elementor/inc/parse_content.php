<?php

function thegem_replace_ids_attachments_content($data, $attachment_ids) {
	preg_match("/[0-9,]+/", $data, $match);
	if(is_numeric($match[0])) {
		$match_id = $match[0];
		$str=str_replace($match_id,isset($attachment_ids[$match_id]) ? $attachment_ids[$match_id] : $match_id,$data);
	} else {
		$match_ids = explode(',',$match[0]);
		$copy_item = $data;
		foreach ($match_ids as $match_id) {
			if($attachment_ids[$match_id]) {
				$copy_item=str_replace($match_id,isset($attachment_ids[$match_id]) ? $attachment_ids[$match_id] : $match_id,$copy_item);
			} else {
				$copy_item=str_replace($match_id.',','',$copy_item);
			}
		}
		$str=$copy_item;
	}
	$result = preg_replace('/[^0-9,]+/','',$str);
	return $result;
}

function thegem_get_ids_attachment ($arr) {
	$query = new WP_Query( array('order' => 'ASC', 'post_status' => 'any', 'post_type' => 'attachment', 'posts_per_page'=>-1 ) );
	$upload_dir = wp_upload_dir();
	$data=array();
	while ( $query->have_posts() ) {
		$query->the_post();
		$data[get_the_ID()] = str_replace($upload_dir['baseurl'],'', wp_get_attachment_url());
	}
	$result = array();
	foreach ($arr as $id=>$src) {
		if($search_id = array_search($src, $data)) {
			$result[$id]=$search_id;
		}
	}
	return $result;
}

function thegem_get_ids_forms($arr) {
	$query = new WP_Query( array('order' => 'ASC', 'post_status' => 'any', 'post_type' => 'wpcf7_contact_form', 'posts_per_page'=>-1 ) );
	$data=array();
	while ( $query->have_posts() ) {
		$query->the_post();
		$data[get_the_ID()] = get_the_title();
	}
	$result = array();
	foreach ($arr as $id=>$title) {
		if($search_id = array_search($title.' (Demo)', $data)) {
			$result[$id]=$search_id;
		}
	}
	return $result;
}

function thegem_replace_attachments_content($content, $attachment_json_data) {
	preg_match_all("/{{SRC_ID_[0-9,]+}}/s", $content, $matches);
	if(!empty($matches[0])) {
		foreach ($matches[0] as $item) {
			$replace=thegem_replace_ids_attachments_content($item, $attachment_json_data);
			if(!empty($replace)) {
				$content = str_replace($item, $replace, $content);
			}
		}
	}
	return $content;
}


function thegem_get_product_categories_ids($arr) {
	$product_categories = get_terms('product_cat');
	$data = array();
	foreach ($product_categories as $category) {
		$data[$category->term_id]=$category->slug;
	}
	$result = array();
	foreach ($arr as $id=>$src) {
		if($search_id = array_search($src, $data)) {
			$result[$id]=$search_id;
		}
	}
	return $result;
}

function thegem_replace_ids_product_categories($data, $categories_ids) {
	$data = str_replace(' ','', $data);
	preg_match("/[0-9,]+/", $data, $match);
	if(is_numeric($match[0])) {
		$match_id = $match[0];
		$str=str_replace($match_id,$categories_ids[$match_id],$data);
	} else {
		$match_ids = explode(',',$match[0]);
		$copy_item = $data;
		foreach ($match_ids as $match_id) {
			if($categories_ids[$match_id]) {
				$copy_item=str_replace($match_id,$categories_ids[$match_id],$copy_item);
			} else {
				$copy_item=str_replace($match_id.',','',$copy_item);
			}
		}
		$str=$copy_item;
	}
	$result = preg_replace('/[^0-9,]+/','',$str);
	$result = 'ids="'.str_replace(',',', ', $result).'"';
	return $result;
}

function thegem_replace_product_categories_content($content, $product_categories_data) {
	preg_match_all("/CAT_IDS=\"[0-9, ]+\"/s", $content, $matches);
	if(!empty($matches[0])) {
		foreach ($matches[0] as $item) {
			$replace=thegem_replace_ids_product_categories($item, $product_categories_data);
			if(!empty($replace)) {
				$content = str_replace($item, $replace, $content);
			}
		}
	}
	return $content;
}

function thegem_replace_ids_forms($data, $forms_ids) {
	preg_match("/[0-9,]+/", $data, $match);
	if(is_numeric($match[0])) {
		$match_id = $match[0];
		$str=str_replace($match_id,$forms_ids[$match_id],$data);
	}
	$result = preg_replace('/[^0-9,]+/','',$str);
	$result = 'id="'.str_replace(',',', ', $result).'"';
	return $result;
}

function thegem_replace_forms_content($content, $forms_data) {
	preg_match_all("/FORM_ID=\"[0-9]+\"/s", $content, $matches);
	if(!empty($matches[0])) {
		foreach ($matches[0] as $item) {
			$replace=thegem_replace_ids_forms($item, $forms_data);
			if(!empty($replace)) {
				$content = str_replace($item, $replace, $content);
			}
		}
	}
	return $content;
}

function thegem_get_content_sliders($content) {
	preg_match_all("/alias=\"[a-zA-Z0-9\-\s]+\"/s", $content, $matches);
	$sliders = array();
	if(!empty($matches[0])) {
		foreach ($matches[0] as $item) {
			$sliders[] = str_replace(array('alias="', '"'), '', $item);
		}
	}
	return $sliders;
}

function sort_array_for_single_item($array) {
	uasort($array, 'thegem_single_item_sort_function');
	return $array;
}

function thegem_single_item_sort_function($a,$b){
		if(!empty($a['parent']) && !empty($b['parent']))  {
			return strcmp($a['parent'], $b['parent']);
		} elseif(!empty($a['title']) && !empty($b['title'])) {
		return strcmp($a['title'], $b['title']);
				}
			}

function thegem_parse_ids_request($ids) {
	parse_str($ids, $ids_source);
	$ids_array = array();
	foreach ($ids_source as $key => $value) {
		foreach ($value as $single_ids) {
			$single_id_row = explode(',', $single_ids);
			foreach ($single_id_row as $single_id) {
				$ids_array[] = $single_id;
			}
		}
	}
	return array_unique($ids_array);
}