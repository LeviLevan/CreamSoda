<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'XT_Framework_System_Status' ) ) {

	/**
	 * XT_Framework System Status Panel
	 *
	 * Setting Page to Manage Plugins
	 *
	 * @class      XT_Framework_System_Status
	 * @package    XT_Framework
	 * @since      1.0
	 * @author     XplodedThemes
	 */
	class XT_Framework_System_Status {

		/**
		 * Core class reference.
		 *
		 * @since    1.0.0
		 * @access   private
		 * @var      XT_Framework $core
		 */
		private $core;

		/**
		 * @var string the page slug
		 */
		protected $slug = 'system-status';

		/**
		 * @var array The settings require to add the submenu page "System Status"
		 */
		protected $_settings = array();

		/**
		 * @var array plugins requirements list
		 */
		protected $_plugins_requirements = array();

		/**
		 * @var array requirements labels
		 */
		protected $_requirement_labels = array();

		/**
		 * Single instance of the class
		 *
		 * @var \XT_Framework_System_Status
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * Constructor
		 *
		 * @return void
		 * @since  1.0.0
		 * @author XplodedThemes
		 */
		public function __construct( &$core ) {

			$this->core = $core;

			$this->_requirement_labels = array(
				'min_wp_version'    => esc_html__( 'WordPress Version', 'xt-framework' ),
				'min_wc_version'    => esc_html__( 'WooCommerce Version', 'xt-framework' ),
				'wp_memory_limit'   => esc_html__( 'Available Memory', 'xt-framework' ),
				'min_php_version'   => esc_html__( 'PHP Version', 'xt-framework' ),
				'min_tls_version'   => esc_html__( 'TLS Version', 'xt-framework' ),
				'wp_cron_enabled'   => esc_html__( 'WordPress Cron', 'xt-framework' ),
				'simplexml_enabled' => esc_html__( 'SimpleXML', 'xt-framework' ),
				'mbstring_enabled'  => esc_html__( 'MultiByte String', 'xt-framework' ),
				'imagick_version'   => esc_html__( 'ImageMagick Version', 'xt-framework' ),
				'gd_enabled'        => esc_html__( 'GD Library', 'xt-framework' ),
				'iconv_enabled'     => esc_html__( 'Iconv Module', 'xt-framework' ),
				'opcache_enabled'   => esc_html__( 'OPCache Save Comments', 'xt-framework' ),
				'url_fopen_enabled' => esc_html__( 'URL FOpen', 'xt-framework' ),
			);

			add_filter( $this->core->framework_prefix( 'admin_tabs' ), array( $this, 'add_system_status_tab' ), 1, 1 );
			$this->core->framework_loader()->add_action( 'admin_init', $this, 'check_system_status' );
			$this->render_notices();

		}


		public function add_system_status_tab( $tabs ) {

			$system_info  = get_option( 'xt_framework_system_info' );
			$error_notice = ( $system_info['errors'] === true ? ' <span class="xt-framework-system-info-menu update-plugins">!</span>' : '' );

			$tabs[] = array(
				'id'        => $this->slug,
				'title'     => esc_html__( 'System Status', 'xt-framework' ) . $error_notice,
				'show_menu' => true,
				'content'   => array(
					'type'     => 'function',
					'function' => array( $this, 'show_information_panel' )
				)
			);

			return $tabs;
		}

		/**
		 * Add "System Information" page template under the framework menu
		 *
		 * @return void
		 * @since  1.0.0
		 * @author XplodedThemes
		 */
		public function show_information_panel() {

			$labels = $this->_requirement_labels;

			$system_info        = get_option( 'xt_framework_system_info' );
			$recommended_memory = 134217728;
			$output_ip          = 'n/a';

			if ( function_exists( 'curl_init' ) && apply_filters( 'xt_framework_system_status_check_ip', true ) ) {
				//Get Output IP Address
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, 'https://ifconfig.co/ip' );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				$data = curl_exec( $ch );
				curl_close( $ch );
				$output_ip = $data != '' ? $data : 'n/a';
			}

			?>
            <div id="xt-framework-sysinfo" class="wrap xt-framework-system-info">

				<?php if ( ! isset( $_GET['xt-framework-phpinfo'] ) || $_GET['xt-framework-phpinfo'] != 'true' ): ?>

                    <h3><?php echo esc_html__( 'General Info', 'xt-framework' ); ?></h3>
                    <table class="widefat striped general-info-table">
                        <tr>
                            <th>
								<?php esc_html_e( 'Site URL', 'xt-framework' ); ?>
                            </th>
                            <td class="requirement-value">
                                <a target="_blank"
                                   href="<?php echo esc_url( get_site_url() ); ?>"><?php echo esc_url( get_site_url() ); ?></a>
                            </td>
                        </tr>
                        <tr>
                            <th>
								<?php esc_html_e( 'Output IP Address', 'xt-framework' ); ?>
                            </th>
                            <td class="requirement-value">
								<?php echo $output_ip ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
								<?php esc_html_e( 'XT Framework PATH', 'xt-framework' ); ?>
                            </th>
                            <td class="requirement-value">
								<?php echo XTFW_DIR; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
								<?php esc_html_e( 'XT Framework Version', 'xt-framework' ); ?>
                            </th>
                            <td class="requirement-value">
                                <strong><?php echo XTFW_VERSION; ?></strong>
                            </td>
                        </tr>
                    </table>

                    <h3><?php echo esc_html__( 'Active XT Plugins', 'xt-framework' ); ?></h3>
                    <table class="widefat striped xt-plugins-table">
						<?php foreach ( $this->core->instances() as $instance ) : ?>

                            <tr>
                                <th class="requirement-name">
                                    <a href="<?php echo $instance->plugin_admin_url(); ?>">
										<?php echo $instance->plugin_name(); ?>
                                    </a>
                                </th>
                                <td class="requirement-value">
                                    <strong>v.<?php echo $instance->plugin_version(); ?></strong>
                                </td>
                                <td class="requirement-value">
                                    <img src="<?php echo esc_url( xtfw_dir_url( XTFW_DIR_ADMIN_TABS_ASSETS ) ); ?>/images/markets/<?php echo esc_attr( $instance->market() ); ?>.svg"
                                         class="xtfw-market-logo xtfw-market-<?php echo esc_attr( $instance->market() ) ?>"/>
                                </td>
                                <td class="requirement-value hide-on-mobile">
									<?php echo '/' . basename( WP_PLUGIN_DIR ) . '/' . basename( $instance->plugin_path() ); ?>
                                </td>
                                <td class="requirement-value align-right">
									<?php echo $instance->plugin_tabs()->render_header_badges( false ); ?>
                                </td>
                            </tr>

						<?php endforeach; ?>
                    </table>

                    <h3><?php echo esc_html__( 'System Info', 'xt-framework' ); ?></h3>
                    <table class="widefat striped system-info-table">
						<?php foreach ( $system_info['system_info'] as $key => $item ): ?>
							<?php
							$to_be_enabled = strpos( $key, '_enabled' ) !== false;
							$has_errors    = isset( $item['errors'] );
							$has_warnings  = false;

							if ( $key == 'wp_memory_limit' && ! $has_errors ) {
								$has_warnings = $item['value'] < $recommended_memory;
							} elseif ( ( $key == 'min_tls_version' || $key == 'imagick_version' ) && ! $has_errors ) {
								$has_warnings = $item['value'] == 'n/a';
							}

							?>
                            <tr>
                                <th class="requirement-name">
									<?php echo $labels[ $key ]; ?>
                                </th>
                                <td class="requirement-value <?php echo( $has_errors ? 'has-errors' : '' ) ?> <?php echo( $has_warnings ? 'has-warnings' : '' ) ?>">
                                    <span class="dashicons dashicons-<?php echo( $has_errors || $has_warnings ? 'warning' : 'yes' ) ?>"></span>

									<?php if ( $to_be_enabled ) {
										echo $item['value'] ? esc_html__( 'Enabled', 'xt-framework' ) : esc_html__( 'Disabled', 'xt-framework' );
									} elseif ( $key == 'wp_memory_limit' ) {
										echo esc_html( size_format( $item['value'] ) );
									} else {

										if ( $item['value'] == 'n/a' ) {
											echo esc_html__( 'N/A', 'xt-framework' );
										} else {
											echo $item['value'];
										}

									} ?>

                                </td>
                                <td class="requirement-messages">
									<?php if ( $has_errors ) : ?>
                                        <ul>
											<?php foreach ( $item['errors'] as $plugin => $requirement ) : ?>
                                                <li>
													<?php
													if ( $to_be_enabled ) {
														echo sprintf( esc_html__( '%s needs %s enabled', 'xt-framework' ), '<b>' . $plugin . '</b>', '<b>' . $labels[ $key ] . '</b>' );
													} elseif ( $key == 'wp_memory_limit' ) {
														echo sprintf( esc_html__( '%s needs at least %s of available memory', 'xt-framework' ), '<b>' . $plugin . '</b>', '<span class="error">' . esc_html( size_format( $this->memory_size_to_num( $requirement ) ) ) . '</span>' );
														if ( $this->memory_size_to_num( $requirement ) < $recommended_memory ) {
															echo '<br/>';
															echo sprintf( esc_html__( 'For optimal functioning of our plugins, we suggest setting at least %s of available memory', 'xt-framework' ), '<span class="error">' . esc_html( size_format( $recommended_memory ) ) . '</span>' );
														}
													} else {
														echo sprintf( esc_html__( '%s needs at least %s version', 'xt-framework' ), '<b>' . $plugin . '</b>', '<span class="error">' . $requirement . '</span>' );
													}
													?>
                                                </li>
											<?php endforeach; ?>
                                        </ul>
										<?php switch ( $key ) {

											case 'min_wp_version':
											case 'min_wc_version':
												echo esc_html__( 'Update it to the latest version in order to benefit of all new features and security updates.', 'xt-framework' );
												break;
											case 'min_php_version':
											case 'min_tls_version':
											case 'imagick_version':
												if ( $item['value'] != 'n/a' ) {
													echo esc_html__( 'Contact your hosting company in order to update it.', 'xt-framework' );
												}
												break;
											case 'wp_cron_enabled':
												echo sprintf( esc_html__( 'Remove %s from %s file', 'xt-framework' ), '<code>define( "DISABLE_WP_CRON", true );</code>', '<b>wp-config.php</b>' );
												break;
											case 'mbstring_enabled':
											case 'simplexml_enabled':
											case 'gd_enabled':
											case 'iconv_enabled':
											case 'opcache_enabled':
											case 'url_fopen_enabled':
												echo esc_html__( 'Contact your hosting company in order to enable it.', 'xt-framework' );
												break;
											case 'wp_memory_limit':
												echo sprintf( esc_html__( 'Read more %s here%s or contact your hosting company in order to increase it.', 'xt-framework' ), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">', '</a>' );
												break;
											default:
												echo apply_filters( 'xt_framework_system_generic_message', '', $item );

										} ?>
									<?php endif; ?>

									<?php if ( $has_warnings ) {

										if ( $item['value'] != 'n/a' ) {

											echo sprintf( esc_html__( 'For optimal functioning of our plugins, we suggest setting at least %s of available memory', 'xt-framework' ), '<span class="error">' . esc_html( size_format( $recommended_memory ) ) . '</span>' );
											echo '<br/>';
											echo sprintf( esc_html__( 'Read more %s here%s or contact your hosting company in order to increase it.', 'xt-framework' ), '<a href="https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">', '</a>' );

										} else {

											switch ( $key ) {
												case 'min_tls_version':
													echo sprintf( esc_html__( 'We cannot determine which %1$sTLS%2$s version is installed because %1$scURL%2$s module is disabled. Ask your hosting company to enable it.', 'xt-framework' ), '<strong>', '</strong>' );
													break;
												case 'imagick_version':
													echo sprintf( esc_html__( '%1$sImageMagick%2$s module is not installed. Ask your hosting company to install it.', 'xt-framework' ), '<strong>', '</strong>' );
													break;
											}

										}

									} ?>
                                </td>
                            </tr>
						<?php endforeach; ?>
                    </table>
                    <br>
                    <p>
                        <a href="<?php echo add_query_arg( array( 'xt-framework-phpinfo' => 'true' ) ) ?> "><?php esc_html_e( 'Show Full PHP Info', 'xt-framework' ) ?></a>
                    </p>
				<?php else : ?>
                    <p>
                        <a href="<?php echo add_query_arg( array( 'xt-framework-phpinfo' => 'false' ) ) ?> "><?php esc_html_e( 'Back to System Status', 'xt-framework' ) ?></a>
                    </p>
					<?php

					ob_start();
					phpinfo( 61 );
					$pinfo = ob_get_contents();
					ob_end_clean();

					$pinfo = preg_replace( '%^.*<div class="center">(.*)</div>.*$%ms', '$1', $pinfo );
					$pinfo = preg_replace( '%(^.*)<a name=\".*\">(.*)</a>(.*$)%m', '$1$2$3', $pinfo );
					$pinfo = str_replace( '<table>', '<table class="widefat striped xt-framework-phpinfo">', $pinfo );
					$pinfo = str_replace( '<td class="e">', '<th class="e">', $pinfo );
					echo $pinfo;

					?>

                    <a href="#xt-framework-sysinfo"><?php esc_html_e( 'Back to top', 'xt-framework' ) ?></a>

				<?php endif; ?>
            </div>
			<?php
		}

		/**
		 * Perform system status check
		 *
		 * @return void
		 * @since  1.0.0
		 * @author XplodedThemes
		 */
		public function check_system_status() {

			if ( '' == get_option( 'xt_framework_system_info' ) ) {

				$this->add_requirements( $this->core->framework_menu_name(), array(
					'min_wp_version'  => '4.9',
					'min_wc_version'  => '3.4',
					'min_php_version' => '5.6.20'
				) );
				$this->add_requirements( esc_html__( 'WooCommerce', 'xt-framework' ), array(
					'wp_memory_limit' => '64M'
				) );

				$system_info   = $this->get_system_info();
				$check_results = array();
				$errors        = false;

				foreach ( $system_info as $key => $value ) {
					$check_results[ $key ] = array( 'value' => $value );

					if ( isset( $this->_plugins_requirements[ $key ] ) ) {

						foreach ( $this->_plugins_requirements[ $key ] as $plugin_name => $required_value ) {

							switch ( $key ) {
								case 'wp_cron_enabled'  :
								case 'mbstring_enabled' :
								case 'simplexml_enabled':
								case 'gd_enabled':
								case 'iconv_enabled':
								case 'url_fopen_enabled':
								case 'opcache_enabled'  :

									if ( ! $value ) {
										$check_results[ $key ]['errors'][ $plugin_name ] = $required_value;
										$errors                                          = true;
									}
									break;

								case 'wp_memory_limit'  :
									$required_memory = $this->memory_size_to_num( $required_value );

									if ( $required_memory > $value ) {
										$check_results[ $key ]['errors'][ $plugin_name ] = $required_value;
										$errors                                          = true;
									}
									break;

								default:
									if ( ! version_compare( $value, $required_value, '>=' ) && $value != 'n/a' ) {
										$check_results[ $key ]['errors'][ $plugin_name ] = $required_value;
										$errors                                          = true;
									}

							}

						}

					}

				}

				update_option( 'xt_framework_system_info', array(
					'system_info' => $check_results,
					'errors'      => $errors
				) );

			}

		}

		/**
		 * Handle plugin requirements
		 *
		 * @param $plugin_name  string
		 * @param $requirements array
		 *
		 * @return void
		 * @since  1.0.0
		 *
		 * @author XplodedThemes
		 */
		public function add_requirements( $plugin_name, $requirements ) {

			$allowed_requirements = array_keys( $this->_requirement_labels );

			foreach ( $requirements as $requirement => $value ) {

				if ( in_array( $requirement, $allowed_requirements ) ) {
					$this->_plugins_requirements[ $requirement ][ $plugin_name ] = $value;
				}
			}
		}

		/**
		 * Show system notice
		 *
		 * @return  void
		 * @since   1.0.0
		 * @author  XplodedThemes
		 */
		public function render_notices() {

			$system_info = get_option( 'xt_framework_system_info', '' );

			if ( ( $system_info == '' ) || ( $system_info != '' && $system_info['errors'] === false ) ) {
				return;
			}

			if ( ! $this->core->framework_is_admin_url() && ! $this->core->framework_is_admin_url( $this->slug ) ) {
				$message = sprintf( esc_html__( '{title:} The system check has detected some compatibility issues on your installation. %sClick here%s to know more', 'xt-framework' ), '<a href="' . esc_url( $this->core->framework_admin_url( $this->slug ) ) . '"><strong>', '</strong></a>' );
				$this->core->framework_notices()->add_warning_message( $message );
			}

		}

		/**
		 * Get system information
		 *
		 * @return  array
		 * @since   1.0.0
		 * @author  XplodedThemes
		 */
		public function get_system_info() {

			$tls = $imagick_version = 'n/a';

			if ( function_exists( 'curl_init' ) && apply_filters( 'xt_framework_system_status_check_ssl', true ) ) {
				//Get TLS version
				$ch = curl_init();
				curl_setopt( $ch, CURLOPT_URL, 'https://www.howsmyssl.com/a/check' );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
				$data = curl_exec( $ch );
				curl_close( $ch );
				$json = json_decode( $data );
				$tls  = $json != null ? str_replace( 'TLS ', '', $json->tls_version ) : '';
			}

			//Get PHP version
			preg_match( "#^\d+(\.\d+)*#", PHP_VERSION, $match );
			$php_version = $match[0];

			// WP memory limit.
			$wp_memory_limit = $this->memory_size_to_num( WP_MEMORY_LIMIT );
			if ( function_exists( 'memory_get_usage' ) ) {
				$wp_memory_limit = max( $wp_memory_limit, $this->memory_size_to_num( @ini_get( 'memory_limit' ) ) );
			}

			if ( class_exists( 'Imagick' ) && is_callable( array( 'Imagick', 'getVersion' ) ) ) {
				preg_match( "/([0-9]+\.[0-9]+\.[0-9]+)/", Imagick::getVersion()['versionString'], $imatch );
				$imagick_version = $imatch[0];
			}

			return apply_filters( 'xt_framework_system_additional_check', array(
				'min_wp_version'    => get_bloginfo( 'version' ),
				'min_wc_version'    => function_exists( 'WC' ) ? WC()->version : 'n/a',
				'wp_memory_limit'   => $wp_memory_limit,
				'min_php_version'   => $php_version,
				'min_tls_version'   => $tls,
				'imagick_version'   => $imagick_version,
				'wp_cron_enabled'   => ! ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ),
				'mbstring_enabled'  => extension_loaded( 'mbstring' ),
				'simplexml_enabled' => extension_loaded( 'simplexml' ),
				'gd_enabled'        => extension_loaded( 'gd' ) && function_exists( 'gd_info' ),
				'iconv_enabled'     => extension_loaded( 'iconv' ),
				'opcache_enabled'   => ini_get( 'opcache.save_comments' ),
				'url_fopen_enabled' => ini_get( 'allow_url_fopen' ),
			) );

		}

		/**
		 * Convert site into number
		 *
		 * @param   $memory_size string
		 *
		 * @return  integer
		 * @since   1.0.0
		 *
		 * @author  XplodedThemes
		 */
		public function memory_size_to_num( $memory_size ) {
			$unit = strtoupper( substr( $memory_size, - 1 ) );
			$size = substr( $memory_size, 0, - 1 );

			$multiplier = array(
				'P' => 5,
				'T' => 4,
				'G' => 3,
				'M' => 2,
				'K' => 1,
			);

			if ( isset( $multiplier[ $unit ] ) ) {
				for ( $i = 1; $i <= $multiplier[ $unit ]; $i ++ ) {
					$size *= 1024;
				}
			}

			return $size;
		}

		/**
		 * Main plugin Instance
		 *
		 * @return XT_Framework_System_Status
		 * @since  1.0.0
		 * @author XplodedThemes
		 */
		public static function instance( $core ) {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self( $core );
			}

			return self::$_instance;
		}

	}
}