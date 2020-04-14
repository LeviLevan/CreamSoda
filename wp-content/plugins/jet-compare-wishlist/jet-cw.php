<?php
/**
 * Plugin Name: JetCompareWishlist For Elementor
 * Plugin URI:  https://jetcomparewishlist.zemez.io/
 * Description: JetCompareWishlist - Compare and Wishlist functionality for Elementor Page Builder
 * Version:     1.0.0
 * Author:      Zemez
 * Author URI:  https://zemez.io/wordpress/
 * Text Domain: jet-cw
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// If class `Jet_CW` doesn't exists yet.
if ( ! class_exists( 'Jet_CW' ) ) {

	/**
	 * Sets up and initializes the plugin.
	 */
	class Jet_CW {

		/**
		 * A reference to an instance of this class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private static $instance = null;

		/**
		 * A reference to an instance of cherry framework core class.
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    object
		 */
		private $core = null;

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

		private $version = '1.0.0';

		/**
		 * Holder for base plugin path
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_path = null;

		/**
		 * Check if Compare and Wishlist enabled
		 */
		public $compare_enabled;
		/**
		 * @var
		 */
		public $wishlist_enabled;

		/**
		 * Components
		 */
		public $widgets_store;
		public $widgets_templates;
		public $render;
		public $settings;
		public $assets;
		public $integration;
		public $compatibility;
		public $compare_integration;
		public $compare_render;
		public $compare_data;
		public $wishlist_integration;
		public $wishlist_render;
		public $wishlist_data;

		/**
		 * Sets up needed actions/filters for the plugin to initialize.
		 *
		 * @since 1.0.0
		 * @access public
		 * @return void
		 */
		public function __construct() {

			// Check if Elementor installed and activated
			if ( ! did_action( 'elementor/loaded' ) ) {
				add_action( 'admin_notices', array( $this, 'admin_notice_missing_main_plugin' ) );
				return;
			}

			add_action( 'plugins_loaded', array( $this, 'woocommerce_loaded' ) );

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
		 * Check that WooCommerce active
		 */
		function woocommerce_loaded() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				add_action( 'admin_notices', [ $this, 'admin_notice_missing_woocommerce_plugin' ] );
				return;
			}
		}

		/**
		 * [admin_notice_missing_main_plugin description]
		 * @return [type] [description]
		 */
		public function admin_notice_missing_main_plugin() {

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			$elementor_link = sprintf(
				'<a href="%1$s">%2$s</a>',
				admin_url() . 'plugin-install.php?s=elementor&tab=search&type=term',
				'<strong>' . esc_html__( 'Elementor', 'jet-cw' ) . '</strong>'
			);

			$message = sprintf(
				esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'jet-cw' ),
				'<strong>' . esc_html__( 'Jet Compare Wishlist', 'jet-cw' ) . '</strong>',
				$elementor_link
			);

			printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

			if ( ! class_exists( 'WooCommerce' ) ) {
				$woocommerce_link = sprintf(
					'<a href="%1$s">%2$s</a>',
					admin_url() . 'plugin-install.php?s=woocommerce&tab=search&type=term',
					'<strong>' . esc_html__( 'WooCommerce', 'jet-cw' ) . '</strong>'
				);

				$message = sprintf(
					esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'jet-cw' ),
					'<strong>' . esc_html__( 'Jet Compare Wishlist', 'jet-cw' ) . '</strong>',
					$woocommerce_link
				);

				printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
			}

		}

		/**
		 * [admin_notice_missing_main_plugin description]
		 * @return [type] [description]
		 */
		public function admin_notice_missing_woocommerce_plugin() {

			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
			}

			$woocommerce_link = sprintf(
				'<a href="%1$s">%2$s</a>',
				admin_url() . 'plugin-install.php?s=woocommerce&tab=search&type=term',
				'<strong>' . esc_html__( 'WooCommerce', 'jet-cw' ) . '</strong>'
			);

			$message = sprintf(
				esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'jet-cw' ),
				'<strong>' . esc_html__( 'Jet Compare Wishlist', 'jet-cw' ) . '</strong>',
				$woocommerce_link
			);

			printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

		}

		/**
		 * Add plugin changelog link.
		 *
		 * @param array $plugin_meta
		 * @param string $plugin_file
		 *
		 * @return array
		 */
		public function plugin_row_meta( $plugin_meta, $plugin_file ) {
			if ( plugin_basename( __FILE__ ) === $plugin_file ) {
				$plugin_meta['changelog'] = sprintf(
					'<a href="http://documentation.zemez.io/wordpress/index.php?project=jetcomparewishlist&lang=en&section=jetcomparewishlist-changelog" target="_blank">%s</a>',
					esc_html__( 'Changelog', 'jet-cw' )
				);
			}

			return $plugin_meta;
		}


		/**
		 * Load plugin framework
		 */
		public function load_framework() {

			require $this->plugin_path( 'framework/loader.php' );

			$this->framework = new Jet_CW_CX_Loader(
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
			if ( ! $this->has_elementor() ) {
				return;
			}

			$this->load_files();

			$this->settings      = new Jet_CW_Settings();
			$this->assets        = new Jet_CW_Assets();
			$this->integration   = new Jet_CW_Integration();
			$this->widgets_store = new Jet_CW_Widgets_Store();
			$this->compatibility = new Jet_CW_Compatibility();

			$this->compare_enabled = $this->settings->get( 'enable_compare' );
			$this->wishlist_enabled = $this->settings->get( 'enable_wishlist' );

			if ( filter_var( $this->compare_enabled, FILTER_VALIDATE_BOOLEAN ) ) {
				$this->compare_integration = new Jet_CW_Compare_Integration();
				$this->compare_render      = new Jet_CW_Compare_Render();
				$this->compare_data        = new Jet_CW_Compare_Data();
			}

			if ( filter_var( $this->wishlist_enabled, FILTER_VALIDATE_BOOLEAN ) ) {
				$this->wishlist_integration = new Jet_CW_Wishlist_Integration();
				$this->wishlist_render      = new Jet_CW_Wishlist_Render();
				$this->wishlist_data        = new Jet_CW_Wishlist_Data();
			}

			if ( is_admin() ) {

				require $this->plugin_path( 'includes/updater/class-jet-cw-plugin-update.php' );

				jet_cw_updater()->init( array(
					'version' => $this->get_version(),
					'slug'    => 'jet-cw',
				) );

			}

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
		 * Load required files.
		 *
		 * @return void
		 */
		public function load_files() {
			require $this->plugin_path( 'includes/class-jet-cw-settings.php' );
			require $this->plugin_path( 'includes/class-jet-cw-tools.php' );
			require $this->plugin_path( 'includes/class-jet-cw-integration.php' );
			require $this->plugin_path( 'includes/class-jet-cw-functions.php' );
			require $this->plugin_path( 'includes/class-jet-cw-widgets-functions.php' );
			require $this->plugin_path( 'includes/class-jet-cw-widgets-store.php' );
			require $this->plugin_path( 'includes/class-jet-cw-assets.php' );

			require $this->plugin_path( 'includes/compare/class-jet-cw-compare-integration.php' );
			require $this->plugin_path( 'includes/compare/class-jet-cw-compare-render.php' );
			require $this->plugin_path( 'includes/compare/class-jet-cw-compare-data.php' );

			require $this->plugin_path( 'includes/wishlist/class-jet-cw-wishlist-integration.php' );
			require $this->plugin_path( 'includes/wishlist/class-jet-cw-wishlist-render.php' );
			require $this->plugin_path( 'includes/wishlist/class-jet-cw-wishlist-data.php' );

			require $this->plugin_path( 'includes/lib/compatibility/class-jet-cw-compatibility.php' );
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
			load_plugin_textdomain( 'jet-cw', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'jet-cw/template-path', 'jet-compare-wishlist/' );
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

if ( ! function_exists( 'jet_cw' ) ) {

	/**
	 * Returns instanse of the plugin class.
	 *
	 * @since  1.0.0
	 * @return object
	 */
	function jet_cw() {
		return Jet_CW::get_instance();
	}
}

jet_cw();
