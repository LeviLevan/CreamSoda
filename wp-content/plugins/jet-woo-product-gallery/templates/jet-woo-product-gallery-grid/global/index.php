<?php
/**
 * Gallery template
 */

$product_id     = $_product->get_id();
$attachment_ids = $_product->get_gallery_image_ids();
$images_size    = $settings['image_size'];
$enable_gallery = filter_var( $settings['enable_gallery'], FILTER_VALIDATE_BOOLEAN );
$zoom           = 'yes' === $settings['enable_zoom'] ? 'jet-woo-product-gallery__image--with-zoom' : '';
$gallery        = '[jet-woo-product-gallery]';

$video_thumbnail_url = $this->__get_video_thumbnail_url();
$video_type          = jet_woo_gallery_video_integration()->get_video_type();
$video               = $this->__get_video_html();

$column_classes = array(
	jet_woo_product_gallery_tools()->col_classes( array(
		'desk' => $settings['columns'],
		'tab'  => $settings['columns_tablet'],
		'mob'  => $settings['columns_mobile'],
	) )
);

?>
<div class="jet-woo-product-gallery__content">
	<div class="jet-woo-product-gallery-grid col-row">
	  <?php
	  if ( has_post_thumbnail( $product_id ) ) {
		  include $this->__get_global_template( 'image' );
	  } else {
		  printf(
			  '<div class="jet-woo-product-gallery__image-item featured no-image %s"><div class="jet-woo-product-gallery__image image-with-placeholder"><img src="%s" alt="%s" /></div></div>',
			  implode( ' ', $column_classes ),
			  wc_placeholder_img_src(),
			  __( 'Placeholder', 'jet-woo-product-gallery' )
		  );
	  }

	  if ( $attachment_ids ) {
		  foreach ( $attachment_ids as $attachment_id ) {
			  include $this->__get_global_template( 'thumbnails' );
		  }
	  }

	  if ( 'content' === $settings['video_display_in'] ) {
		  include $this->__get_global_template( 'video' );
	  }
	  ?>
	</div>
	<?php
	if ( 'popup' === $settings['video_display_in'] ) {
		include $this->__get_global_template( 'popup-video' );
	}
	?>
</div>