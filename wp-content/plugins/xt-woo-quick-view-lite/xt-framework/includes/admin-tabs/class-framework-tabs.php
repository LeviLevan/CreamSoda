<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(!class_exists('XT_Framework_Framework_Tabs')) {

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the admin-specific stylesheet and JavaScript.
	 *
	 * @package    XT_Framework_Framework_Tabs
	 * @author     XplodedThemes
	 */
	class XT_Framework_Framework_Tabs extends XT_Framework_Admin_Tabs {

		public static $_instance;

		protected function init() {

			parent::init();

			add_filter( 'custom_menu_order', '__return_true' );

			// Move framework menu just above the Plugins menu
			$this->core->framework_loader()->add_filter( 'menu_order', $this, 'menu_order', 10, 1 );
		}

		protected function apply_filters() {

			$this->tabs = apply_filters( $this->core->framework_prefix( 'admin_tabs' ), $this->tabs, $this );

		}

		public function footer_version() {
			return '<span class="alignright"><strong>' . $this->core->framework_menu_name() . '</strong> - v' . $this->core->framework_version() . '</strong></span>';
		}

		public function set_active_tab() {

			if ( ! empty( $_GET['page'] ) && $_GET['page'] !== $this->core->framework_slug() ) {
				$page   = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
				$tab_id = str_replace( $this->core->framework_slug() . '-', '', $page );
				if ( $this->tab_exists( $tab_id ) ) {
					$this->active_tab = $tab_id;
				}
			}

			if ( ! empty( $_GET['page'] ) && $_GET['page'] === $this->core->framework_slug() ) {

				if ( ! empty( $_GET['tab'] ) ) {
					$tab_id = filter_input( INPUT_GET, 'tab', FILTER_SANITIZE_STRING );
					if ( $this->tab_exists( $tab_id ) ) {
						$this->active_tab = $tab_id;

					}
				} else {
					$this->active_tab = $this->default_tab;
				}
			}
		}

		public function add_default_tabs() {

			$this->tabs[] = array(
				'id'        => 'plugins',
				'title'     => esc_html__( 'Browse Plugins', 'xt-framework' ),
				'show_menu' => true,
				'order'     => 10,
				'content'   => array(
					'type'     => 'function',
					'function' => array( $this, 'browse_plugins' )
				),
				'callback'  => array( $this, 'browse_plugins_assets' )
			);

			$this->tabs[] = array(
				'id'         => 'home',
				'title'      => esc_html__( 'XplodedThemes.com', 'xt-framework' ),
				'hide_title' => true,
				'icon'       => 'dashicons-admin-home',
				'show_menu'  => false,
				'order'      => 20,
				'external'   => 'https://xplodedthemes.com',
				'secondary'  => true
			);

			$this->tabs[] = array(
				'id'         => 'docs',
				'title'      => esc_html__( 'Docs', 'xt-framework' ),
				'menu_title' => esc_html__( 'Documentation', 'xt-framework' ),
				'hide_title' => true,
				'icon'       => 'dashicons-info',
				'show_menu'  => true,
				'order'      => 30,
				'external'   => 'https://docs.xplodedthemes.com',
				'secondary'  => true
			);

			$this->tabs[] = array(
				'id'         => 'support',
				'title'      => esc_html__( 'Support', 'xt-framework' ),
				'hide_title' => true,
				'icon'       => 'dashicons-sos',
				'show_menu'  => true,
				'order'      => 40,
				'external'   => 'https://xplodedthemes.com/support',
				'secondary'  => true
			);

		}

		public function browse_plugins() {

			$wp_list_table = _get_list_table( 'WP_Plugin_Install_List_Table' );
			$wp_list_table->prepare_items();

			echo '<form id="plugin-filter" method="post">';
			$wp_list_table->display();
			echo '</div>';
		}

		public function browse_plugins_assets() {

			add_filter( 'plugins_api_result', array( $this, 'plugin_results' ), 1, 3 );
			wp_enqueue_script( 'plugin-install' );
			wp_enqueue_script( 'updates' );
			add_thickbox();
		}

		function plugin_results( $res, $action, $args ) {

			if ( $action !== 'query_plugins' ) {
				return $res;
			}

			$args = (array) $args;

			unset( $args['browse'] );

			if ( ! empty( $args['xt_plugin_query'] ) || ! empty( $args['search'] ) ) {
				return $res;
			}

			$args['author']          = 'XplodedThemes';
			$args['xt_plugin_query'] = true;

			$api = plugins_api( 'query_plugins', $args );

			if ( is_wp_error( $api ) ) {
				return $res;
			}

			$res->plugins = $api->plugins;

			return $res;
		}

		public function is_admin_tabs_page() {
			return ! empty( $_GET['page'] ) && ( ( $_GET['page'] === $this->core->framework_slug() ) || $_GET['page'] === $this->core->framework_slug( $this->active_tab ) );
		}

		public function tabs_admin_menu() {

			// Add global menu
			if ( empty ( $GLOBALS['admin_page_hooks'][ $this->core->framework_slug() ] ) ) {
				add_menu_page( $this->core->framework_menu_name(), $this->core->framework_menu_name(), 'manage_options', $this->core->framework_slug(), array(
					$this,
					'tabs_admin_page'
				), $this->core->framework_icon() );
			}

			// Add menu divider
			add_submenu_page( $this->core->framework_slug(), '', '<span class="xtfw-admin-menu-divider"></span>', 'read', '#', null, 0 );

			foreach ( $this->tabs as $tab ) {

				$id    = $tab['id'];
				$title = ! empty( $tab['menu_title'] ) ? $tab['menu_title'] : $tab['title'];
				$title = apply_filters( $this->core->framework_prefix( 'admin_tabs_tab_title' ), $title, $tab );

				$order     = ! empty( $tab['order'] ) ? $tab['order'] : 1;
				$redirect  = ! empty( $tab['external'] ) ? $tab['external'] : '';
				$redirect  = ! empty( $tab['redirect'] ) ? $tab['redirect'] : $redirect;
				$show_menu = ! empty( $tab['show_menu'] );

				$parent_menu = $show_menu ? $this->core->framework_slug() : null;

				$this->page_hooks[ $id ] = add_submenu_page( $parent_menu, $title, $title, 'manage_options', $this->core->framework_slug( $id ), function () use ( $id, $redirect ) {
					if ( ! $redirect ) {
						$this->tabs_admin_page();
					} else {
						wp_redirect( $redirect );
						exit;
					}
				}, $order );

				remove_submenu_page( $this->core->framework_slug(), $this->core->framework_slug() );

				if ( ! empty( $tab['callback'] ) && $this->is_tab( $id ) ) {
					$tab['callback']();
				}
			}
		}

		public function tabs_admin_page() {

			$classes = array( 'wrap', 'xtfw-admin-tabs-wrap', $this->core->plugin_slug( "tabs-wrap" ) );

			?>
            <div class="<?php echo implode( " ", $classes ); ?>">

                <div class="xtfw-admin-tabs-header">

                    <span class="xtfw-badges">
                        <span class="xtfw-badge xtfw-badge-version"><strong>V.<?php echo $this->core->framework_version(); ?></strong></span>
                    </span>

                    <h1><img class="xtfw-logo" src="<?php echo esc_url( $this->core->framework_logo() ); ?>" class="image-50"/><?php echo $this->core->framework_name(); ?></h1>

                </div>

				<?php $this->show_nav(); ?>

                <div class="xtfw-admin-tabs-panel xtfw-<?php echo $this->get_tab_id(); ?>-tab">

					<?php $this->show_tab(); ?>

                </div>

                <script type="text/javascript">
                    XT_FOLLOW.init();
                </script>

            </div>

			<?php
		}

		public function get_tab_url( $tab = '', $params = array() ) {

			return esc_url( $this->core->framework_admin_url( $tab, $params ) );
		}

		public function menu_order( $menu_order ) {

			$plugins_menu_index       = array_search( 'plugins.php', $menu_order );
			$framework_menu_index     = array_search( $this->core->framework_slug(), $menu_order );
			$framework_menu_new_index = $plugins_menu_index;

			// helper function to move an element inside an array
			function move_element( &$array, $a, $b ) {
				$out = array_splice( $array, $a, 1 );
				array_splice( $array, $b, 0, $out );
			}

			if ( $framework_menu_index ) {
				move_element( $menu_order, $framework_menu_index, $framework_menu_new_index );
			}

			return $menu_order;
		}

		/**
		 * Main XT_Framework_Framework_Tabs Instance
		 *
		 * Ensures only one instance of XT_Framework_Framework_Tabs is loaded or can be loaded.
		 *
		 * @return XT_Framework_Framework_Tabs instance
		 * @see XT_Framework_Framework_Tabs()
		 * @since 1.0.0
		 * @static
		 */
		public static function instance( $core ) {
			if ( empty( self::$_instance ) ) {
				self::$_instance = new self( $core );
			}

			return self::$_instance;
		} // End instance()

	}
}