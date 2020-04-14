<?php
/**
 * Product Gallery thumbnails template
 */

$image_src = wp_get_attachment_image_src( $attachment_id, 'full' );
$image     = wp_get_attachment_image( $attachment_id, $images_size, false, array(
	'title'                   => get_post_field( 'post_title', $attachment_id ),
	'data-caption'            => get_post_field( 'post_excerpt', $attachment_id ),
	'data-src'                => $image_src[0],
	'data-large_image'        => $image_src[0],
	'data-large_image_width'  => $image_src[1],
	'data-large_image_height' => $image_src[2],
) );

$this->set_render_attribute( 'image_link', 'class', 'jet-woo-product-gallery__image-link' );
$this->set_render_attribute( 'image_link', 'href', esc_url( $image_src[0] ) );
$this->set_render_attribute( 'image_link', 'itemprop', 'image' );
$this->set_render_attribute( 'image_link', 'title', get_post_field( 'post_title', $attachment_id ) );
$this->set_render_attribute( 'image_link', 'rel', 'prettyPhoto' . $gallery );

?>
<div class="jet-woo-product-gallery__image-item">
	<div class="jet-woo-product-gallery__image <?php echo $zoom ?>">
	  <?php
	  if ( $enable_gallery ) {
		  jet_woo_product_gallery_functions()->get_gallery_trigger_button( $settings['gallery_button_icon'] );
	  }
	  ?>
		<a <?php $this->print_render_attribute_string( 'image_link' ); ?>>
		<?php echo $image; ?>
		</a>
	</div>
</div>