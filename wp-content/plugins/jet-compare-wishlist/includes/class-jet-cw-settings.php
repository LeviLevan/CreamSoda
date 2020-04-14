<?php
/**
 * Class Compare Wishlist Settings
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_CW_Settings' ) ) {

	/**
	 * Define Jet_CW_Settings class
	 */
	class Jet_CW_Settings {

		/**
		 * [$key description]
		 * @var string
		 */
		public $key = 'jet-cw-settings';

		/**
		 * [$builder description]
		 * @var null
		 */
		public $builder = null;

		/**
		 * [$settings description]
		 * @var null
		 */
		public $settings = null;

		/**
		 * Avaliable Widgets array
		 *
		 * @var array
		 */
		public $avaliable_widgets = array();

		/**
		 * Init page
		 */
		public function __construct() {

			$this->init_builder();

			add_action( 'admin_menu', array( $this, 'register_page' ), 99 );
			add_action( 'init', array( $this, 'save' ), 40 );
			add_action( 'admin_notices', array( $this, 'saved_notice' ) );

			foreach ( glob( jet_cw()->plugin_path( 'includes/widgets/compare/' ) . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class' => 'Class', 'name' => 'Name', 'slug' => 'Slug' ) );

				$slug                             = basename( $file, '.php' );
				$this->avaliable_widgets[ $slug ] = $data['name'];
			}

			foreach ( glob( jet_cw()->plugin_path( 'includes/widgets/wishlist/' ) . '*.php' ) as $file ) {
				$data = get_file_data( $file, array( 'class' => 'Class', 'name' => 'Name', 'slug' => 'Slug' ) );

				$slug                             = basename( $file, '.php' );
				$this->avaliable_widgets[ $slug ] = $data['name'];
			}
		}

		/**
		 * Initialize page builder module if reqired
		 *
		 * @return [type] [description]
		 */
		public function init_builder() {

			if ( ! isset( $_REQUEST['page'] ) || $this->key !== $_REQUEST['page'] ) {
				return;
			}

			$builder_data = jet_cw()->framework->get_included_module_data( 'cherry-x-interface-builder.php' );

			$this->builder = new CX_Interface_Builder(
				array(
					'path' => $builder_data['path'],
					'url'  => $builder_data['url'],
				)
			);

		}

		/**
		 * Show saved notice
		 *
		 * @return bool
		 */
		public function saved_notice() {

			if ( ! isset( $_GET['settings-saved'] ) ) {
				return false;
			}

			$message = esc_html__( 'Settings saved', 'jet-cw' );

			printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', $message );

			return true;

		}

		/**
		 * Save settings
		 *
		 * @return void
		 */
		public function save() {

			if ( ! isset( $_REQUEST['page'] ) || $this->key !== $_REQUEST['page'] ) {
				return;
			}

			if ( ! isset( $_REQUEST['action'] ) || 'save-settings' !== $_REQUEST['action'] ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			$current = get_option( $this->key, array() );
			$data    = $_REQUEST;

			unset( $data['action'] );

			foreach ( $data as $key => $value ) {
				$current[ $key ] = is_array( $value ) ? $value : esc_attr( $value );
			}

			update_option( $this->key, $current );

			$redirect = add_query_arg(
				array( 'dialog-saved' => true ),
				$this->get_settings_page_link()
			);

			wp_redirect( $redirect );
			die();

		}

		/**
		 * Return settings page URL
		 *
		 * @return string
		 */
		public function get_settings_page_link() {

			return add_query_arg(
				array(
					'page' => $this->key,
				),
				esc_url( admin_url( 'admin.php' ) )
			);

		}

		public function get( $setting, $default = false ) {

			if ( null === $this->settings ) {
				$this->settings = get_option( $this->key, array() );
			}

			return isset( $this->settings[ $setting ] ) ? $this->settings[ $setting ] : $default;

		}

		/**
		 * Register add/edit page
		 *
		 * @return void
		 */
		public function register_page() {

			add_submenu_page(
				'elementor',
				esc_html__( 'Jet Compare Wishlist Settings', 'jet-cw' ),
				esc_html__( 'Jet Compare Wishlist Settings', 'jet-cw' ),
				'manage_options',
				$this->key,
				array( $this, 'render_page' )
			);

		}

		/**
		 * Render settings page
		 *
		 * @return void
		 */
		public function render_page() {

			foreach ( $this->avaliable_widgets as $key => $value ) {
				$default_avaliable_widgets[ $key ] = 'true';
			}

			$pages     = array();
			$get_pages = get_pages( 'hide_empty=0' );

			foreach ( $get_pages as $page ) {
				$pages[ $page->ID ] = esc_attr( $page->post_title );
			}

			$this->builder->register_section(
				array(
					'jet_cw_settings' => array(
						'type'   => 'section',
						'scroll' => false,
						'title'  => esc_html__( 'Jet Compare Wishlist Settings', 'jet-cw' ),
					),
				)
			);

			$this->builder->register_form(
				array(
					'jet_cw_settings_form' => array(
						'type'   => 'form',
						'parent' => 'jet_cw_settings',
						'action' => add_query_arg(
							array( 'page' => $this->key, 'action' => 'save-settings' ),
							esc_url( admin_url( 'admin.php' ) )
						),
					),
				)
			);

			$this->builder->register_settings(
				array(
					'settings_top'    => array(
						'type'   => 'settings',
						'parent' => 'jet_cw_settings_form',
					),
					'settings_bottom' => array(
						'type'   => 'settings',
						'parent' => 'jet_cw_settings_form',
					),
				)
			);

			$this->builder->register_component(
				array(
					'jet_cw_tab_vertical' => array(
						'type'   => 'component-tab-vertical',
						'parent' => 'settings_top',
					),
				)
			);

			$this->builder->register_settings(
				array(
					'compare_options' => array(
						'parent' => 'jet_cw_tab_vertical',
						'title'  => esc_html__( 'Compare', 'jet-cw' ),
					),
				)
			);

			$this->builder->register_control(
				array(
					'enable_compare' => array(
						'type'        => 'switcher',
						'id'          => 'enable_compare',
						'name'        => 'enable_compare',
						'parent'      => 'compare_options',
						'value'       => $this->get( 'enable_compare', true ),
						'title'       => esc_html__( 'Enable Compare', 'jet-cw' ),
						'description' => esc_html__( 'Enable Compare', 'jet-cw' ),
					),
				)
			);

			$this->builder->register_control(
				array(
					'save_user_compare_list' => array(
						'type'        => 'switcher',
						'id'          => 'save_user_compare_list',
						'name'        => 'save_user_compare_list',
						'parent'      => 'compare_options',
						'value'       => $this->get( 'save_user_compare_list' ),
						'title'       => esc_html__( 'Save for logged users', 'jet-cw' ),
						'description' => esc_html__( 'Enable this option if you want save compare list for logged users', 'jet-cw' ),
					),
				)
			);

			$this->builder->register_control(
				array(
					'compare_page' => array(
						'type'        => 'select',
						'id'          => 'compare_page',
						'name'        => 'compare_page',
						'parent'      => 'compare_options',
						'value'       => $this->get( 'compare_page' ),
						'options'     => $pages,
						'title'       => esc_html__( 'Compare Page :', 'jet-cw' ),
						'description' => esc_html__( 'Choose Compare Page', 'jet-cw' ),
					),
				)
			);

			$this->builder->register_control(
				array(
					'compare_page_max_items' => array(
						'type'        => 'select',
						'id'          => 'compare_page_max_items',
						'name'        => 'compare_page_max_items',
						'parent'      => 'compare_options',
						'value'       => $this->get( 'compare_page_max_items' ),
						'default'     => 2,
						'options'     => array(
							2 => __( '2 item', 'jet-cw' ),
							3 => __( '3 item', 'jet-cw' ),
							4 => __( '4 item', 'jet-cw' ),
						),
						'title'       => esc_html__( 'Count products to compare :', 'jet-cw' ),
						'description' => esc_html__( 'Count products to show in compare widget', 'jet-cw' ),
					),
				)
			);

			$this->builder->register_control(
				array(
					'add_default_compare_button' => array(
						'type'        => 'switcher',
						'id'          => 'add_default_compare_button',
						'name'        => 'add_default_compare_button',
						'parent'      => 'compare_options',
						'value'       => $this->get( 'add_default_compare_button' ),
						'title'       => esc_html__( 'Add default Compare Button', 'jet-cw' ),
						'description' => esc_html__( 'Add compare button to default WooCommerce templates', 'jet-cw' ),
					),
				)
			);

			$this->builder->register_settings(
				array(
					'wishlist_options' => array(
						'parent' => 'jet_cw_tab_vertical',
						'title'  => esc_html__( 'Wishlist', 'jet-cw' ),
					),
				)
			);

			$this->builder->register_control(
				array(
					'enable_wishlist' => array(
						'type'        => 'switcher',
						'id'          => 'enable_wishlist',
						'name'        => 'enable_wishlist',
						'parent'      => 'wishlist_options',
						'value'       => $this->get( 'enable_wishlist', true ),
						'title'       => esc_html__( 'Enable Wishlist', 'jet-cw' ),
						'description' => esc_html__( 'Enable Wishlist', 'jet-cw' ),
					),
				)
			);

			$this->builder->register_control(
				array(
					'save_user_wish_list' => array(
						'type'        => 'switcher',
						'id'          => 'save_user_wish_list',
						'name'        => 'save_user_wish_list',
						'parent'      => 'wishlist_options',
						'value'       => $this->get( 'save_user_wish_list' ),
						'title'       => esc_html__( 'Save for logged users', 'jet-cw' ),
						'description' => esc_html__( 'Enable this option if you want save wish list for logged users', 'jet-cw' ),
					),
				)
			);

			$this->builder->register_control(
				array(
					'wishlist_page' => array(
						'type'        => 'select',
						'id'          => 'wishlist_page',
						'name'        => 'wishlist_page',
						'parent'      => 'wishlist_options',
						'value'       => $this->get( 'wishlist_page' ),
						'options'     => $pages,
						'title'       => esc_html__( 'Wishlist Page :', 'jet-cw' ),
						'description' => esc_html__( 'Choose Wishlist Page', 'jet-cw' ),
					),
				)
			);

			$this->builder->register_control(
				array(
					'add_default_wishlist_button' => array(
						'type'        => 'switcher',
						'id'          => 'add_default_wishlist_button',
						'name'        => 'add_default_wishlist_button',
						'parent'      => 'wishlist_options',
						'value'       => $this->get( 'add_default_wishlist_button' ),
						'title'       => esc_html__( 'Add default Wishlist Button', 'jet-cw' ),
						'description' => esc_html__( 'Add wishlist button to default WooCommerce templates', 'jet-cw' ),
					),
				)
			);

			$this->builder->register_settings(
				array(
					'available_widgets_options' => array(
						'parent' => 'jet_cw_tab_vertical',
						'title'  => esc_html__( 'Available Widgets', 'jet-cw' ),
					),
				)
			);

			$this->builder->register_control(
				array(
					'avaliable_widgets' => array(
						'type'        => 'checkbox',
						'id'          => 'avaliable_widgets',
						'name'        => 'avaliable_widgets',
						'parent'      => 'available_widgets_options',
						'value'       => $this->get( 'avaliable_widgets', $default_avaliable_widgets ),
						'options'     => $this->avaliable_widgets,
						'title'       => esc_html__( 'Global Available Widgets', 'jet-cw' ),
						'description' => esc_html__( 'List of widgets that will be available when editing the page', 'jet-cw' ),
						'class'       => 'jet_cw_settings_form__checkbox-group'
					),
				)
			);

			$this->builder->register_html(
				array(
					'save_button' => array(
						'type'   => 'html',
						'parent' => 'settings_bottom',
						'class'  => 'cx-component dialog-save',
						'html'   => '<button type="submit" class="button button-primary">' . esc_html__( 'Save', 'jet-cw' ) . '</button>',
					),
				)
			);

			echo '<div class="jet-cw-settings-page">';
			$this->builder->render();
			echo '</div>';
		}

	}
}

