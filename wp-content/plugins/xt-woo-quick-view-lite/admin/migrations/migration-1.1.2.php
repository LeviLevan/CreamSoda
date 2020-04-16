<?php

$customizer = xt_woo_quick_view()->customizer();

// update multicolor field to 2 color fields	
$fields = [
	'modal_close_color' => array('modal_close_color', 'modal_close_hover_color'),
	'modal_nav_color' => array('modal_nav_color', 'modal_nav_hover_color'),
	'modal_slider_arrow_color' => array('modal_slider_arrow_color', 'modal_slider_arrow_hover_color'),
];	

foreach($fields as $field) {
	
	$old_key = $field[0];
	
	$link_color_key = $old_key;
	$hover_color_key = $field[1];
	
	$color = $customizer->get_option($old_key);
	
	if(!empty($color)) {
		
		if(isset($color['link'])) {
			$customizer->update_option($link_color_key, $color['link']);
		}
		
		if(isset($color['hover'])) {
			$customizer->update_option($hover_color_key, $color['hover']);
		}
	}
}