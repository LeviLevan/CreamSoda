<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Charizma_Archive_Document extends Jet_Document_Base {

	public function get_name() {
		return 'charizma_archive';
	}

	public static function get_title() {
		return __( 'Charizma Archive', 'charizma' );
	}

	public function get_preview_as_query_args() {
		return array(
			'post_type'   => 'post',
			'numberposts' => get_option( 'posts_per_page', 10 ),
		);
	}

}