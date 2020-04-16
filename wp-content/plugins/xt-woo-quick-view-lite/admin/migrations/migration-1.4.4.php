<?php

$customizer = xt_woo_quick_view()->customizer();

$convert_padding_to_dimensions = array(
    'trigger_padding',
    'add_to_cart_padding'
);

$options = $customizer->get_options();

foreach ( $convert_padding_to_dimensions as $key ) {

    if(isset($options[$key])) {

        if ( isset($options[$key]) && !is_array(isset($options[$key])) ) {

            $dimensions = explode(" ", $options[$key]);

            if(count($dimensions) === 4) {

                $padding_top = $dimensions[0];
                $padding_right = $dimensions[1];
                $padding_bottom = $dimensions[2];
                $padding_left = $dimensions[3];

            }else if(count($dimensions) === 3) {

                $padding_top = $dimensions[0];
                $padding_right = $dimensions[1];
                $padding_bottom = $dimensions[2];
                $padding_left = $dimensions[1];

            }else if(count($dimensions) === 2) {

                $padding_top = $dimensions[0];
                $padding_right = $dimensions[1];
                $padding_bottom = $dimensions[0];
                $padding_left = $dimensions[1];

            }else if(count($dimensions) === 1) {

                $padding_top = $dimensions[0];
                $padding_right = $dimensions[0];
                $padding_bottom = $dimensions[0];
                $padding_left = $dimensions[0];
            }

            $options[$key] = array(
                'padding-top'    => str_replace("px", "", $padding_top)."px",
                'padding-right'  => str_replace("px", "", $padding_right)."px",
                'padding-bottom' => str_replace("px", "", $padding_bottom)."px",
                'padding-left'   => str_replace("px", "", $padding_left)."px",
            );

        }
    }
}

$customizer->update_options( $options );