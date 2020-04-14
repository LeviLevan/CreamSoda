<?php
/**
 * Plugin Name: JetProductGallery For Elementor
 * Plugin URI:  http://jetwoobuilder.zemez.io/
 * Description: Your perfect tool for creating WooCommerce Single Product Gallery
 * Version:     0.9.0-beta.1
 * Author:      Zemez
 * Author URI:  https://zemez.io/wordpress/
 * Text Domain: jet-woo-product-gallery
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 * WC tested up to: 3.4
 * WC requires at least: 3.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// If class `Jet_Woo_Product_Gallery` doesn't exists yet.
if ( ! class_exists( 'Jet_Woo_Product_Gallery' ) ) {

	/**
	 * Sets up and initializes the plugin.
	 */
	class Jet_Woo_Product_Gallery {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * Holder for base plugin URL
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_url = null;

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		private $version = '0.9.0-beta.1';

		/**
		 * Framework loader instance
		 *
		 * @var object
		 */
		public $framework;

		/**
		 * Holder for base plugin path
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_path = null;

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct() {

			// Load the core functions/classes required by the rest of the plugin.
			add_action( 'after_setup_theme', array( $this, 'load_framework' ), - 20 );

			// Internationalize the text strings used.
			add_action( 'init', array( $this, 'lang' ), - 999 );
			// Load files.
			add_action( 'init', array( $this, 'init' ), - 999 );

			// Plugin row meta
			add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );
		}

		/**
		 * Load plugin framework
		 */
		public function load_framework() {

			require $this->plugin_path( 'framework/loader.php' );

			$this->framework = new Jet_Woo_Product_Gallery_CX_Loader(
				array(
					$this->plugin_path( 'framework/interface-builder/cherry-x-interface-builder.php' ),
					$this->plugin_path( 'framework/post-meta/cherry-x-post-meta.php' ),
				)
			);

		}

		/**
		 * Returns plugin version
		 *
		 * @return string
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * Manually init required modules.
		 *
		 * @return void
		 */
		public function init() {

			if ( class_exists( 'WooCommerce' ) ) {

				$this->load_files();

				jet_woo_product_gallery_assets()->init();
				jet_woo_product_gallery_integration()->init();
				jet_woo_gallery_video_integration()->init();

				jet_woo_product_gallery_settings()->init();

				add_action( 'wp_footer', array( $this, 'photoswipe_template' ) );
			}

			if ( is_admin() ) {
				if ( ! $this->has_elementor() ) {
					$this->required_plugins_notice();
				}
			}

		}


		/**
		 * Show recommended plugins notice.
		 *
		 * @return void
		 */
		public function photoswipe_template() {
			require $this->plugin_path( 'templates/photoswipe.php' );
		}

		/**
		 * Show recommended plugins notice.
		 *
		 * @return void
		 */
		public function required_plugins_notice() {
			require $this->plugin_path( 'includes/lib/class-tgm-plugin-activation.php' );
			add_action( 'tgmpa_register', array( $this, 'register_required_plugins' ) );
		}

		/**
		 * Register required plugins
		 *
		 * @return void
		 */
		public function register_required_plugins() {

			$plugins = array(
				array(
					'name'     => 'Elementor',
					'slug'     => 'elementor',
					'required' => true,
				),
				array(
					'name'     => 'WooCommerce',
					'slug'     => 'woocommerce',
					'required' => true,
				),
			);

			$config = array(
				'id'           => 'jet-woo-product-gallery',
				'default_path' => '',
				'menu'         => 'jet-woo-product-gallery-install-plugins',
				'parent_slug'  => 'plugins.php',
				'capability'   => 'manage_options',
				'has_notices'  => true,
				'dismissable'  => true,
				'dismiss_msg'  => '',
				'is_automatic' => false,
				'strings'      => array(
					'notice_can_install_required'    => _n_noop(
						'Jet Product Gallery for Elementor requires the following plugin: %1$s.',
						'Jet Product Gallery for Elementor requires the following plugins: %1$s.',
						'jet-woo-product-gallery'
					),
					'notice_can_install_recommended' => _n_noop(
						'Jet Product Gallery for Elementor recommends the following plugin: %1$s.',
						'Jet Product Gallery for Elementor recommends the following plugins: %1$s.',
						'jet-woo-product-gallery'
					),
				),
			);

			tgmpa( $plugins, $config );

		}

		/**
		 * Check if theme has elementor
		 *
		 * @return boolean
		 */
		public function has_elementor() {
			return defined( 'ELEMENTOR_VERSION' );
		}

		/**
		 * Returns utility instance
		 *
		 * @return object
		 */
		public function utility() {
			$utility = $this->get_core()->modules['cherry-utility'];

			return $utility->utility;
		}

		/**
		 * Load required files.
		 *
		 * @return void
		 */
		public function load_files() {
			require $this->plugin_path( 'includes/class-jet-woo-product-gallery-assets.php' );
			require $this->plugin_path( 'includes/class-jet-woo-product-gallery-tools.php' );
			require $this->plugin_path( 'includes/class-jet-woo-product-gallery-functions.php' );

			require $this->plugin_path( 'includes/integrations/class-jet-woo-product-gallery-integration.php' );
			require $this->plugin_path( 'includes/integrations/class-jet-woo-gallery-video-integration.php' );

			require $this->plugin_path( 'includes/settings/class-jet-woo-product-gallery-settings.php' );
		}

		/**
		 * Returns path to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 *
		 * @return string
		 */
		public function plugin_path( $path = null ) {

			if ( ! $this->plugin_path ) {
				$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
			}

			return $this->plugin_path . $path;
		}

		/**
		 * Returns url to file or dir inside plugin folder
		 *
		 * @param  string $path Path inside plugin dir.
		 *
		 * @return string
		 */
		public function plugin_url( $path = null ) {

			if ( ! $this->plugin_url ) {
				$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
			}

			return $this->plugin_url . $path;
		}

		/**
		 * Loads the translation files.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function lang() {
			load_plugin_textdomain( 'jet-woo-product-gallery', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'jet-woo-product-gallery/template-path', 'jet-woo-product-gallery/' );
		}

		/**
		 * Returns path to template file.
		 *
		 * @return string|bool
		 */
		public function get_template( $name = null ) {

			$template = locate_template( $this->template_path() . $name );

			if ( ! $template ) {
				$template = $this->plugin_path( 'templates/' . $name );
			}

			if ( file_exists( $template ) ) {
				return $template;
			} else {
				return false;
			}
		}

		/**
		 * Add plugin changelog link.
		 *
		 * @param array  $plugin_meta
		 * @param string $plugin_file
		 *
		 * @return array
		 */
		public function plugin_row_meta( $plugin_meta, $plugin_file ) {
			if ( plugin_basename( __FILE__ ) === $plugin_file ) {
				$plugin_meta['changelog'] = sprintf(
					'<a href="http://documentation.zemez.io/wordpress/index.php?project=jetproductgallery&lang=en&section=jetproductgallery-changelog" target="_blank">%s</a>',
					esc_html__( 'Changelog', 'jet-woo-product-gallery' )
				);
			}
			return $plugin_meta;
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function activation() {
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @since  1.0.0
		 * @return void
		 */
		public function deactivation() {
		}

		/**
		 * Returns the instance.
		 *
		 * @since  1.0.0
		 * @access public
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

if ( ! function_exists( 'jet_woo_product_gallery' ) ) {

	/**
	 * Returns instanse of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function jet_woo_product_gallery() {
		return Jet_Woo_Product_Gallery::get_instance();
	}
}

jet_woo_product_gallery();
