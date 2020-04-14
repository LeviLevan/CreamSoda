<?php
/**
 * Gallery template
 */

$product_id     = $_product->get_id();
$attachment_ids = $_product->get_gallery_image_ids();
$images_size    = $settings['image_size'];
$enable_gallery = filter_var( $settings['enable_gallery'], FILTER_VALIDATE_BOOLEAN );
$zoom           = filter_var( $settings['enable_zoom'], FILTER_VALIDATE_BOOLEAN ) ? 'jet-woo-product-gallery__image--with-zoom' : '';
$gallery        = '[jet-woo-product-gallery]';

$video_type          = jet_woo_gallery_video_integration()->get_video_type();
$video_thumbnail_url = $this->__get_video_thumbnail_url();
$video               = $this->__get_video_html();

$thumbnail_column_classes = array(
	jet_woo_product_gallery_tools()->col_classes( array(
		'desk' => $settings['pagination_thumbnails_columns'],
		'tab'  => $settings['pagination_thumbnails_columns_tablet'],
		'mob'  => $settings['pagination_thumbnails_columns_mobile'],
	) )
);

$this->set_render_attribute(
	'slick_slider_wrapper',
	'class',
	array(
		'jet-woo-slick__wrapper',
		'jet-woo-slick-type-' . $settings['slider_pagination_type'],
		'jet-woo-slick-direction-' . $settings['slider_pagination_direction'],
		'jet-woo-slick-v-pos-' . $settings['slider_pagination_v_position'],
		'jet-woo-slick-h-pos-' . $settings['slider_pagination_h_position']
	)
);

$this->set_render_attribute(
	'slick_slider',
	'class',
	array(
		'jet-woo-product-gallery-slider',
		'jet-woo-slick',
	)
);

?>
	<div <?php $this->print_render_attribute_string( 'slick_slider_wrapper' ); ?>>
		<div <?php $this->print_render_attribute_string( 'slick_slider' ); ?> <?php echo $this->get_slider_data_settings(); ?>>
		<?php
		if ( has_post_thumbnail( $product_id ) ) {
			include $this->__get_global_template( 'image' );
		} else {
			printf(
				'<div class="jet-woo-product-gallery__image-item featured no-image"><div class="jet-woo-product-gallery__image image-with-placeholder"><img src="%s" alt="%s" /></div></div>',
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
	  if ( 'thumbnails' === $settings['slider_pagination_type'] && 'yes' === $settings['slider_show_pagination'] ) {
		  include $this->__get_global_template( 'thumbnails-pagination' );
	  }
	  ?>
	</div>
<?php
if ( 'popup' === $settings['video_display_in'] ) {
	include $this->__get_global_template( 'popup-video' );
}
?>