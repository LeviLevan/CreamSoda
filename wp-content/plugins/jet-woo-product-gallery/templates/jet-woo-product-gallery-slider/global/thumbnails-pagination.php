<?php
/**
 * Product Gallery thumbnails template
 */


$this->set_render_attribute( 'thumbs', 'class', array(
	'jet-woo-slick-control-nav',
	'jet-woo-slick-control-thumbs',
) );

$this->set_render_attribute( 'thumbs_item', 'class', array( 'jet-woo-slick-control-thumbs__item' ) );


if ( has_post_thumbnail( $product_id ) ) {
	array_unshift( $attachment_ids, intval( get_post_thumbnail_id( $product_id ) ) );
}

$thumbs_video_placeholder_html = '';
$thumbs_html                   = '';

if ( $this->product_has_video() && 'content' === $settings['video_display_in'] ) {
	if ( $this->__video_has_custom_placeholder()  ) {
		$video_thumbnail_id = jet_woo_gallery_video_integration()->get_video_custom_placeholder();
		array_push( $attachment_ids, $video_thumbnail_id );
	} else {
		$video_placeholder_url         = jet_woo_product_gallery()->plugin_url( 'assets/images/video-thumbnails-placeholder.png' );
		$thumbs_video_placeholder_html = '<li data-thumb="' . esc_url( $video_placeholder_url ) . '" ' . $this->get_render_attribute_string( 'thumbs_item' ) . '><div class="jet-woo-slick-control-thumbs__item-image"><img width="300" height="300" src="' . esc_url( $video_placeholder_url ) . '" ></div></li>';
	}
}

if ( $attachment_ids ) {
	foreach ( $attachment_ids as $attachment_id ) {
		$image_src   = wp_get_attachment_image_src( $attachment_id, 'full' );
		$image       = wp_get_attachment_image( $attachment_id, $settings['thumbs_image_size'], false );
		$thumbs_html .= '<li data-thumb="' . esc_url( $image_src[0] ) . '" ' . $this->get_render_attribute_string( 'thumbs_item' ) . '><div class="jet-woo-slick-control-thumbs__item-image">' . $image . '</div></li>';
	}
}

if ( 'content' === $settings['video_display_in'] ) {
	$thumbs_html .= $thumbs_video_placeholder_html;
}

?>
<ol <?php $this->print_render_attribute_string( 'thumbs' ); ?>>
	<?php echo $thumbs_html; ?>
</ol>