<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the slider part of the quick view modal.
 *
 * This template can be overridden by copying it to yourtheme/woo-quick-view/parts/product-slider.php.
 *
 * HOWEVER, on occasion we will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @link       http://xplodedthemes.com
 * @since      1.4.3
 *
 * @package    XT_Woo_Quick_View
 * @subpackage XT_Woo_Quick_View/public/templates/parts
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 

global $product, $attachment_ids;

$fullscreen = xt_wooqv_modal_type_is('fullscreen');

$attachment_ids = array();

if(!empty($variation_id)) {
		
	$variation = new WC_Product_Variation( $variation_id );

	$image_id = $variation->get_image_id();
	
	if(!empty($image_id)) {
		$attachment_ids[] = $image_id;
	}
		
	if(class_exists('WC_Additional_Variation_Images')) {
		$gallery_attachment_ids = get_post_meta( $variation_id, '_wc_additional_variation_images', true );
		$gallery_attachment_ids = explode( ',', $gallery_attachment_ids );
		$attachment_ids = array_merge($attachment_ids, $gallery_attachment_ids);
	}	
	
}else{

	$image_id = $product->get_image_id();
	if(!empty($image_id)) {
		$attachment_ids[] = $image_id;
	}
	
	$gallery_attachment_ids = $product->get_gallery_image_ids();
	$attachment_ids = array_merge($attachment_ids, $gallery_attachment_ids);
}

$attachment_ids = array_filter($attachment_ids);
?>

<div class="xt_wooqv-slider-wrapper" data-attachments="<?php echo count($attachment_ids);?>">

	<ul class="xt_wooqv-slider">
		<?php
		if ( !empty($attachment_ids) ) {

            foreach ( $attachment_ids as $attachment_id ) {

                $thumb_image_size = apply_filters('xt_wooqv_modal_slider_thumb_size', 'woocommerce_gallery_thumbnail');
                $single_image_size = apply_filters('xt_wooqv_modal_slider_image_size', 'large');

                $props            = wc_get_product_attachment_props( $attachment_id, $product );
                $thumb_image_src  = wp_get_attachment_image_src( $attachment_id, $thumb_image_size, 0);
                $single_image_src  = wp_get_attachment_image_src( $attachment_id, $single_image_size, 0);

                echo apply_filters(
                    'woocommerce_single_product_image_html',
                    sprintf(
                        '<li data-thumb="%s" data-src="%s" itemprop="image" title="%s" style="background-image:url(%s)"><img src="%s" width="%s" height="%s" /></li>',
                        $thumb_image_src[0],
                        $single_image_src[0],
                        esc_attr( $props['caption'] ),
	                    $single_image_src[0],
	                    $single_image_src[0],
	                    $single_image_src[1],
	                    $single_image_src[2]
                    ),
                    get_the_ID()
                );


            }
				
		}else{
			
			echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<li><img src="%s" alt="%s" /></li>', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), get_the_ID() );
		}
		?>
	</ul>
	
</div> 
