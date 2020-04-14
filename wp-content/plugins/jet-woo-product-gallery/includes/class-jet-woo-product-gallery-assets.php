<?php

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_Woo_Product_Gallery_Assets' ) ) {

	/**
	 * Define Jet_Woo_Product_Gallery_Assets class
	 */
	class Jet_Woo_Product_Gallery_Assets {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since 1.0.0
		 * @var   object
		 */
		private static $instance = null;

		/**
		 * Constructor for the class
		 */
		public function init() {

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'elementor/frontend/after_enqueue_scripts', array(
				'WC_Frontend_Scripts',
				'localize_printed_scripts'
			), 5 );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		}

		public function enqueue_admin_assets() {

			wp_enqueue_style(
				'jet-woo-product-gallery-admin',
				jet_woo_product_gallery()->plugin_url( 'assets/css/admin.css' ),
				false,
				jet_woo_product_gallery()->get_version()
			);

		}

		/**
		 * Enqueue public-facing stylesheets.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function enqueue_styles() {

			if ( is_rtl() ) {
				wp_enqueue_style(
					'jet-woo-product-gallery',
					jet_woo_product_gallery()->plugin_url( 'assets/css/jet-woo-product-gallery-rtl.css' ),
					false,
					jet_woo_product_gallery()->get_version()
				);
			} else {
				wp_enqueue_style(
					'jet-woo-product-gallery',
					jet_woo_product_gallery()->plugin_url( 'assets/css/jet-woo-product-gallery.css' ),
					false,
					jet_woo_product_gallery()->get_version()
				);
			}

		}

		/**
		 * Enqueue plugin scripts only with elementor scripts
		 *
		 * @return void
		 */
		public function enqueue_scripts() {

			wp_enqueue_script(
				'jet-woo-product-gallery',
				jet_woo_product_gallery()->plugin_url( 'assets/js/jet-woo-product-gallery.js' ),
				array( 'jquery', 'elementor-frontend' ),
				jet_woo_product_gallery()->get_version(),
				true
			);

			wp_localize_script(
				'jet-woo-product-gallery',
				'jetWooProductGalleryData',
				apply_filters( 'jet-woo-product-gallery/frontend/localize-data', array() )
			);

		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @return object
		 */
		public static function get_instance() {

			// If the single instance hasn't been set, set it now.
			if ( null == self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}

}

/**
 * Returns instance of Jet_Woo_Product_Gallery_Assets
 *
 * @return object
 */
function jet_woo_product_gallery_assets() {
	return Jet_Woo_Product_Gallery_Assets::get_instance();
}
