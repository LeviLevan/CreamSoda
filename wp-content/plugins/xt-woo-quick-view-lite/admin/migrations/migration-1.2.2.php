<?php

$customizer = xt_woo_quick_view()->customizer();

$icons = array();
$icons['modal_close_icon'] = $customizer->get_option('modal_close_icon');
$icons['modal_nav_icon'] = $customizer->get_option('modal_nav_icon');
$icons['modal_slider_arrow'] = $customizer->get_option('modal_slider_arrow');
$icons['trigger_icon_font'] = $customizer->get_option('trigger_icon_font');

$options = $customizer->get_options();

foreach($icons as $key => $icon) {

    if(!empty($icon) && strpos($icon, 'xt_') === false) {
        $options[$key] = 'xt_'.$icon;
    }
}

$customizer->update_options($options);