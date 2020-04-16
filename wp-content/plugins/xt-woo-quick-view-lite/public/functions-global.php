<?php

function xt_wooqv_class()
{
    $classes = array(
        'xt-woo-quick-view',
        'woocommerce',
        'single-product',
        'quick-view'
    );
    $lightbox_enabled = xt_wooqv_option_bool( 'modal_slider_lightbox_enabled', false );
    $gallery_enabled = xt_wooqv_option_bool( 'modal_slider_thumb_gallery_enabled', false );
    $gallery_visible_onhover = xt_wooqv_option_bool( 'modal_slider_thumb_gallery_visible_hover', false );
    $add_to_cart_button_position = xt_wooqv_option( 'add_to_cart_button_position', 'inline' );
    if ( $lightbox_enabled ) {
        $classes[] = 'xt_wooqv-lightbox-enabled';
    }
    if ( !empty($gallery_enabled) && !empty($gallery_visible_onhover) ) {
        $classes[] = 'xt_wooqv-thumbs-visible-onhover';
    }
    $slider_grayscale_transition = xt_wooqv_option( 'wqv_', '0' );
    if ( !empty($slider_grayscale_transition) ) {
        $classes[] = 'xt_wooqv-grayscale-transition';
    }
    $classes[] = 'xt_wooqv-modal-buttons-pos-' . $add_to_cart_button_position;
    $classes = apply_filters( 'xt_wooqv_modal_class', $classes );
    echo  implode( ' ', $classes ) ;
}

function xt_wooqv_attributes()
{
    $attributes = array(
        'xt_wooqv-mobile-slider-width'             => xt_wooqv_option( 'modal_slider_width_mobile', 350 ),
        'xt_wooqv-mobile-slider-height'            => xt_wooqv_option( 'modal_slider_height_mobile', 350 ),
        'xt_wooqv-desktop-slider-width'            => xt_wooqv_option( 'modal_slider_width_desktop', 400 ),
        'xt_wooqv-desktop-slider-height'           => xt_wooqv_option( 'modal_slider_height_desktop', 400 ),
        'xt_wooqv-desktop-slider-width-fullscreen' => xt_wooqv_option( 'modal_slider_width_desktop_fullscreen', 40 ),
        'xt_wooqv-mobile-slider-height-fullscreen' => xt_wooqv_option( 'modal_slider_height_mobile_fullscreen', 55 ),
    );
    $attributes = apply_filters( 'xt_wooqv_modal_attributes', $attributes );
    $data_string = '';
    foreach ( $attributes as $key => $value ) {
        $data_string .= ' ' . $key . '="' . esc_attr( $value ) . '"';
    }
    echo  $data_string ;
}

function xt_wooqv_trigger_cart_icon_class()
{
    $classes = array( 'xt_wooqv-trigger-icon' );
    $icon_type = xt_wooqv_option( 'trigger_icon_type', 'font' );
    if ( empty($icon_type) ) {
        return '';
    }
    
    if ( $icon_type == 'font' ) {
        $icon = xt_wooqv_option( 'trigger_icon_font' );
        if ( !empty($icon) ) {
            $classes[] = $icon;
        }
    }
    
    $classes = apply_filters( 'xt_wooqv_trigger_cart_icon_class', $classes );
    return implode( ' ', $classes );
}

function xt_wooqv_modal_close_icon_class( $mobile = false )
{
    $classes = array( 'xt_wooqv-close-icon' );
    $close_button_enabled = xt_wooqv_option( 'modal_close_enabled', '1' );
    
    if ( !empty($close_button_enabled) ) {
        $key = 'modal_close_icon';
        
        if ( $mobile ) {
            $key .= '_mobile';
            $classes[] = 'xt_wooqv-close-icon-mobile';
        } else {
            $classes[] = 'xt_wooqv-close-icon-desktop';
        }
        
        $icon = xt_wooqv_option( $key );
        if ( !empty($icon) ) {
            $classes[] = $icon;
        }
    }
    
    $classes = apply_filters( 'xt_wooqv_modal_close_icon_class', $classes, $mobile );
    return implode( ' ', $classes );
}

function xt_wooqv_nav_icon_class()
{
    $classes = array( 'xt_wooqv-nav-icon' );
    
    if ( xt_wooqv_modal_type_is( 'fullscreen' ) ) {
        $icon = xt_wooqv_option( 'modal_nav_icon' );
    } else {
        $icon = xt_wooqv_option( 'modal_nav_icon' );
    }
    
    if ( !empty($icon) ) {
        $classes[] = $icon;
    }
    $classes = apply_filters( 'xt_wooqv_nav_icon_class', $classes );
    return implode( ' ', $classes );
}

function xt_woo_quick_view_template(
    $slug,
    $vars = array(),
    $return = false,
    $locateOnly = false
)
{
    return xt_woo_quick_view()->get_template(
        $slug,
        $vars,
        $return,
        $locateOnly
    );
}

function xt_wooqv_option( $id, $default = null )
{
    return xt_woo_quick_view()->customizer()->get_option( $id, $default );
}

function xt_wooqv_option_bool( $id, $default = null )
{
    return xt_woo_quick_view()->customizer()->get_option_bool( $id, $default );
}

function xt_wooqv_update_option( $id, $value )
{
    xt_woo_quick_view()->customizer()->update_option( $id, $value );
}

function xt_wooqv_delete_option( $id )
{
    xt_woo_quick_view()->customizer()->delete_option( $id );
}

function xt_wooqv_modal_type()
{
    return 'default';
}

function xt_wooqv_modal_type_is( $type )
{
    $mode = xt_wooqv_modal_type();
    return $mode === $type;
}

function xt_wooqv_slider_gallery_size( $device = 'desktop', $default = '12' )
{
    $value = xt_wooqv_option( 'modal_slider_thumb_gallery_visible_' . $device, $default );
    return 2 - intval( $value ) + 20;
}
