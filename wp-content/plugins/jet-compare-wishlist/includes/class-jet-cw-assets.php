<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_CW_Assets' ) ) {

	/**
	 * Define Jet_CW_Assets class
	 */
	class Jet_CW_Assets {

		/**
		 * Constructor for the class
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
			add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'editor_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Admin styles
		 * @return [type] [description]
		 */
		public function admin_styles() {
		}

		/**
		 * Enqueue public-facing stylesheets.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function enqueue_styles() {

			wp_enqueue_style(
				'jet-cw',
				jet_cw()->plugin_url( 'assets/css/jet-cw.css' ),
				false,
				jet_cw()->get_version()
			);


			wp_enqueue_style(
				'jet-cw-frontend',
				jet_cw()->plugin_url( 'assets/css/lib/jet-cw-frontend-font/css/jet-cw-frontend-font.css' ),
				false,
				jet_cw()->get_version()
			);

		}

		public function editor_styles(){
			wp_enqueue_style(
				'jet-cw-icons-font',
				jet_cw()->plugin_url( 'assets/css/lib/jet-cw-font/css/jet-cw-font.css' ),
				array(),
				jet_cw()->get_version()
			);
		}

		/**
		 * Enqueue filter scripts
		 */
		public function enqueue_scripts() {


		}

	}

}