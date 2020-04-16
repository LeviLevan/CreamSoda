<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if(!class_exists('XT_Framework_Plugin_Tabs')) {

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * Defines the plugin name, version, and two examples hooks for how to
	 * enqueue the admin-specific stylesheet and JavaScript.
	 *
	 * @package    XT_Framework_Plugin_Tabs
	 * @author     XplodedThemes
	 */
	class XT_Framework_Plugin_Tabs extends XT_Framework_Admin_Tabs {

		public $logo = '';
		public $description = '';

		protected function init() {

			parent::init();

			if ( ! defined( 'DOING_AJAX' ) ) {
				$this->core->plugin_loader()->add_filter( 'plugin_action_links_' . plugin_basename( $this->core->plugin_file() ), $this, 'action_links', 99 );
			}

			if ( $this->is_admin_tabs_page() ) {

				if ( $this->core->market_is( 'freemius' ) ) {
					$this->core->access_manager()->add_filter( 'templates/account.php', array(
						$this,
						'wrap_freemius_admin_page'
					), 10, 1 );
					$this->core->access_manager()->add_filter( 'templates/pricing.php', array(
						$this,
						'wrap_freemius_minimal_admin_page'
					), 10, 1 );
					$this->core->access_manager()->add_filter( 'templates/checkout.php', array(
						$this,
						'wrap_freemius_minimal_admin_page'
					), 10, 1 );
					$this->core->access_manager()->add_filter( 'templates/add-ons.php', array(
						$this,
						'wrap_freemius_admin_page'
					), 10, 1 );
					$this->core->access_manager()->add_filter( 'templates/contact.php', array(
						$this,
						'wrap_freemius_admin_page'
					), 10, 1 );
					$this->core->access_manager()->add_filter( '/forms/affiliation.php', array(
						$this,
						'wrap_freemius_admin_page'
					), 10, 1 );
				}

			}
		}

		public function is_admin_tabs_page() {

			return ! empty( $_GET['page'] ) && ( strpos( $_GET['page'], $this->core->plugin_slug() ) !== false );
		}

		public function wrap_freemius_admin_page( $template ) {

			ob_start();
			if ( strpos( $template, 'fs-secure-notice' ) !== false ) {
				echo '<p>&nbsp;</p>';
			}
			$this->tabs_admin_page( $template );

			return ob_get_clean();
		}

		public function wrap_freemius_minimal_admin_page( $template ) {

			ob_start();
			if ( strpos( $template, 'fs-secure-notice' ) !== false ) {
				echo '<p>&nbsp;</p>';
			}
			$this->tabs_admin_page( $template, true );

			return ob_get_clean();
		}

		protected function apply_filters() {

			$this->tabs        = apply_filters( $this->core->plugin_prefix( 'admin_tabs' ), $this->tabs, $this );
			$this->logo        = apply_filters( $this->core->plugin_prefix( 'admin_tabs_logo' ), $this->logo, $this );
			$this->description = apply_filters( $this->core->plugin_prefix( 'admin_tabs_description' ), $this->description, $this );
		}

		public function footer_version() {
			return '<span class="alignright"><a href="' . $this->core->plugin()->url . '"><strong>' . $this->core->plugin_menu_name() . '</strong></a> - v' . $this->core->plugin_version() . '</strong></span>';
		}

		public function set_active_tab() {

			if ( ! empty( $_GET['page'] ) && $_GET['page'] !== $this->core->plugin_slug() ) {
				$page             = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
				$tab_id           = str_replace( $this->core->plugin_slug() . '-', '', $page );
				$this->active_tab = $tab_id;
			}

			if ( ! empty( $_GET['page'] ) && $_GET['page'] === $this->core->plugin_slug() ) {

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
				'id'         => 'changelog',
				'title'      => esc_html__( 'Change Log', 'xt-framework' ),
				'menu_title' => esc_html__( 'About', 'xt-framework' ),
				'show_menu'  => false,
				'order'      => 10,
				'content'    => array(
					'type'         => 'changelog',
					'show_refresh' => true
				),
				'secondary'  => true
			);

			if ( ! $this->core->market_is( 'freemius' ) ) {

				$this->tabs[] = array(
					'id'        => 'contact',
					'title'     => esc_html__( 'Support', 'xt-framework' ),
					'show_menu' => false,
					'external'  => 'https://xplodedthemes.com/support',
					'order'     => 20,
					'secondary' => true
				);

			} else {

				$this->tabs[] = array(
					'id'        => '_contact',
					'title'     => esc_html__( 'Support', 'xt-framework' ),
					'show_menu' => false,
					'redirect'  => $this->core->plugin_admin_url( 'contact' ),
					'order'     => 20,
					'secondary' => true
				);

				if ( $this->core->access_manager()->is_premium() ) {
					$this->tabs[] = array(
						'id'        => '_affiliation',
						'title'     => esc_html__( 'Make $$$', 'xt-framework' ),
						'show_menu' => false,
						'redirect'  => $this->core->plugin_admin_url( 'affiliation' ),
						'order'     => 30,
						'secondary' => true
					);
				}

				if ( $this->core->access_manager()->is_registered() && $this->core->access_manager()->is_tracking_allowed() ) {
					$this->tabs[] = array(
						'id'        => '_account',
						'title'     => esc_html__( 'Account', 'xt-framework' ),
						'show_menu' => false,
						'redirect'  => $this->core->plugin_admin_url( 'account' ),
						'order'     => 40,
						'secondary' => true
					);
				}

				if ( ! $this->core->access_manager()->is_paying() ) {

					$this->tabs[] = array(
						'id'        => '_pricing',
						'title'     => esc_html__( 'Upgrade&nbsp;&nbsp;➤', 'xt-framework' ),
						'show_menu' => false,
						'redirect'  => $this->core->access_manager()->get_upgrade_url(),
						'featured'  => true,
						'order'     => 50,
						'secondary' => true
					);
				}

			}

		}

		public function action_links( $links ) {

			foreach ( $this->tabs as $i => $tab ) {

				if ( empty( $tab['action_link'] ) ) {
					continue;
				}

				$id  = $i > 0 ? $tab['id'] : '';
				$url = $this->get_tab_url( $id );

				$action_link = $tab['action_link'];

				if ( is_array( $action_link ) ) {

					$url   = ! empty( $action_link['url'] ) ? $action_link['url'] : $url;
					$title = ! empty( $action_link['title'] ) ? $action_link['title'] : $tab['title'];
					$color = ! empty( $action_link['color'] ) ? $action_link['color'] : '';

				} else {

					$title = $tab['title'];
					$color = '';
				}

				$links[] = '<a style="color: ' . esc_attr( $color ) . '" href="' . esc_url( $url ) . '">' . sanitize_text_field( $title ) . '</a>';

			}

			return $links;
		}

		public function tabs_admin_menu() {

			if ( $this->core->plugin()->top_menu() ) {

				add_menu_page( $this->core->plugin_menu_name(), $this->core->plugin_menu_name(), 'manage_options', $this->core->plugin_slug(), array(
					$this,
					'tabs_admin_page'
				), $this->core->plugin_icon() );

			} else {

				add_submenu_page( $this->core->framework_slug(), $this->core->plugin_menu_name(), $this->core->plugin_menu_name(), 'manage_options', $this->core->plugin_slug(), array(
					$this,
					'tabs_admin_page'
				), 0 );
			}

			foreach ( $this->tabs as $tab ) {

				$id    = $tab['id'];
				$title = ! empty( $tab['menu_title'] ) ? $tab['menu_title'] : ( ! empty( $tab['title'] ) ? $tab['title'] : '' );
				$title = apply_filters( $this->core->plugin_prefix( 'admin_tabs_tab_title' ), $title, $tab );

				$order     = ! empty( $tab['order'] ) ? $tab['order'] : 1;
				$redirect  = ! empty( $tab['external'] ) ? $tab['external'] : '';
				$redirect  = ! empty( $tab['redirect'] ) ? $tab['redirect'] : $redirect;
				$show_menu = ! empty( $tab['show_menu'] );

				$parent_menu = ( $show_menu && $this->core->plugin()->top_menu ) ? $this->core->plugin_slug() : null;

				if ( $this->is_default_tab( $id ) ) {
					$this->page_hooks[ $id ] = add_submenu_page( $parent_menu, $title, $title, 'manage_options', $this->core->plugin_slug( $id ), array(
						$this,
						'tabs_admin_page'
					) );
				} else {
					$this->page_hooks[ $id ] = add_submenu_page( $parent_menu, $title, $title, 'manage_options', $this->core->plugin_slug( $id ), function () use ( $id, $redirect ) {
						if ( ! $redirect ) {
							$this->tabs_admin_page();
						} else {
							wp_redirect( $redirect );
							exit;
						}
					}, $order );
				}

				if ( $this->core->plugin()->top_menu() ) {
					remove_submenu_page( $this->core->plugin_slug(), $this->core->plugin_slug() );
				}

				if ( ! empty( $tab['callback'] ) && $this->is_tab( $id ) ) {
					$tab['callback']();
				}
			}
		}

		public function tabs_admin_page( $page_content = null, $minimal = false ) {

			$classes = array( 'wrap', 'xtfw-admin-tabs-wrap', $this->core->plugin_slug( "tabs-wrap" ) );

			if ( $minimal ) {
				$classes[] = 'xtfw-admin-tabs-minimal';
			}

			?>
            <div class="<?php echo implode( " ", $classes ); ?>">

                <div class="xtfw-admin-tabs-header">

					<?php
					if ( ! $minimal ) {

						echo '<span class="xtfw-badges">';
						echo $this->render_header_badges();
						echo '</span>';
					}
					?>

					<?php if ( ! empty( $this->logo ) ): ?>
                        <div class="xtfw-admin-tabs-logo">
                            <img src="<?php echo esc_url( $this->logo ); ?>" class="image-50"/>
                        </div>
					<?php else: ?>
                        <h1><img class="xtfw-logo" src="<?php echo esc_url( $this->core->framework_logo() ); ?>"
                                 class="image-50"/><?php echo $this->core->plugin_name(); ?></h1>
					<?php endif; ?>

					<?php if ( ! empty( $this->description ) ): ?>
                        <div class="xtfw-admin-tabs-description">
							<?php echo $this->description; ?>
                        </div>
					<?php endif; ?>

                </div>

				<?php
				if ( ! $minimal ) {
					$this->show_nav();
				} else {

					$this->show_tab( $page_content );

					return;
				}
				?>

                <div class="xtfw-admin-tabs-panel xtfw-<?php echo esc_attr( $this->get_tab_id() ); ?>-tab xtfw-panel-<?php echo esc_attr( $this->get_tab_content_type() ); ?>-type">

					<?php $this->show_tab( $page_content ); ?>

                </div>

                <script type="text/javascript">
                    XT_FOLLOW.init();
                </script>

            </div>

			<?php

		}

		public function render_header_badges( $include_version = true ) {

			if ( $this->core->access_manager()->is_registered() && $this->core->access_manager()->is_tracking_allowed() ) {
				$account_url = $this->core->access_manager()->get_account_url();
			} else {
				$account_url = $this->core->plugin_admin_url();
			}

			if ( $this->core->access_manager()->is_premium() ) {

				if ( $this->core->access_manager()->can_use_premium_code() ) {

					echo '<a class="xtfw-badge xtfw-badge-green" href="' . esc_url( $account_url ) . '"><em>' . esc_html( 'Active License', 'xt-framework' ) . '</em></a>';

				} else {

					echo '<a class="xtfw-badge xtfw-badge-grey" href="' . esc_url( $account_url ) . '"><em>' . esc_html( 'Inactive License', 'xt-framework' ) . '</em></a>';
				}

				echo '<a class="xtfw-badge xtfw-badge-blue" href="' . esc_url( $account_url ) . '"><em>' . esc_html( 'Premium Version', 'xt-framework' ) . '</em></a>';

			} else {

				echo '<span class="xtfw-badge xtfw-badge-grey"><em>' . esc_html( 'Free Version', 'xt-framework' ) . '</em></span>';
			}

			if ( $include_version ) {
				$this->render_version_badge();
			}
		}

		public function render_version_badge() {

			echo '<span class="xtfw-badge xtfw-badge-version"><strong>V.' . $this->core->plugin_version() . '</strong></span>';

		}

		public function show_tab( $page_content = null ) {

			$tab = parent::show_tab( $page_content );

			do_action( $this->core->plugin_prefix( 'admin_tabs_show_tab' ), $tab, $this );

			return $tab;
		}

		public function get_active_tab_url( $params = array() ) {

			return $this->get_tab_url( $this->active_tab, $params );
		}

		public function get_tab_url( $tab = '', $params = array() ) {

			$params['tab'] = $tab;

			return esc_url( $this->core->plugin_admin_url( '', $params ) );
		}

		function get_changelog() {

			$readme_file = $this->core->plugin_path( '/', 'readme.txt' );

			require_once XTFW_DIR_INCLUDES . '/class-parsedown.php';

			$parsedown = new XT_Framework_Parsedown();

			$changelog = '';

			$data = file_get_contents( $readme_file );

			if ( ! empty( $data ) ) {
				$data = explode( '== Changelog ==', $data );
				if ( ! empty( $data[1] ) ) {

					$changelog = $data[1];
					$changelog = preg_replace(
						array(
							'/\[\/\/\]\: \# fs_.+?_only_begin/',
							'/\[\/\/\]\: \# fs_.+?_only_end/',
						),
						'',
						$changelog
					);

					$changelog = $parsedown->text( $changelog );

					$changelog = preg_replace(
						array(
							'/\<strong\>(.+?)\<\/strong>\:(.+?)\n/i',
							'/\<p\>/',
							'/\<\/p\>/'
						),
						array(
							'<span class="update-type $1">$1</span><span class="update-txt">$2</span>',
							'',
							''
						),
						$changelog
					);

				}
			}

			return '<div class="xtfw-changelog">' . wp_kses( $changelog, wp_kses_allowed_html( 'post' ) ) . '</div>';
		}

	}
}