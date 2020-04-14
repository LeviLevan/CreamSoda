<?php

/**

 * Plugin name: WooCommerce Easy Duplicate Product
 * Author: WPGem.com
 * Plugin URI: http://wpgem.com/woo-easy-duplicate
 * Description: An easy and convenient way for you to duplicate a product.
 * Version: 0.1.2
 * Author: WPGem.com
 * Author URI: http://WPGem.com
 * 
**/


function wedp_show_the_duplicate_link ($post){
	
	$url = '<a target="_blank" href="' . wp_nonce_url( admin_url( 'edit.php?post_type=product&action=duplicate_product&amp;post=' . $post->ID ), 'woocommerce-duplicate-product_' . $post->ID ) . '" aria-label="' . esc_attr__( 'Make a duplicate from this product', 'woocommerce' )
			. '" rel="permalink">' . __( 'Duplicate', 'woocommerce' ) . '</a>';

	$url .= '<p><small><em><a style="color: #444;" href="https://bit.ly/wp-help-form" target="_blank">(Need help?)</a></em></<small></p>';

	echo $url;
}

function wedp_add_the_metabox($_post){
	
	global $post;	

	$post_type = $post->post_type;

	if('product' != $post_type){
		return;
	}

	add_meta_box( 'woocommerce-easy-product-duplicate', __( 'Duplicate this product', 'woocommerce' ), 'wedp_show_the_duplicate_link', 'product', 'side', 'high' );
}

add_action( 'add_meta_boxes', 'wedp_add_the_metabox', 30 );
