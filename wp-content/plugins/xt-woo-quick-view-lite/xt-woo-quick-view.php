<?php
/**
 * XT WooCommerce Quick View
 *
 * @package     XT_Woo_Quick_View
 * @author      XplodedThemes
 * @copyright   2018 XplodedThemes
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: XT WooCommerce Quick View
 * Plugin URI:  https://xplodedthemes.com/products/woo-quick-view/
 * Description: An interactive product quick view modal for WooCommerce that provides the user a quick access to the main product information with smooth animation. Fully customizable right from WordPress Customizer with Live Preview.
 * Version:     1.5.5
 * WC requires at least: 3.0.0
 * WC tested up to: 4.0
 * Author:      XplodedThemes
 * Author URI:  https://xplodedthemes.com
 * Text Domain: woo-quick-view
 * Domain Path: /languages/
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
  */
 
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $xt_wooqv_plugin;

$market = '##XT_MARKET##';
$market = (strpos($market, 'XT_MARKET') !== false) ? 'freemius' : $market;
$market = (defined('XT_MARKET')) ? XT_MARKET : $market;

$xt_wooqv_plugin = array(
    'version'       => '1.5.5',
    'name'          => 'XT WooCommerce Quick View',
    'menu_name'     => 'Woo Quick View',
    'url'           => 'https://xplodedthemes.com/products/woo-quick-view/',
    'icon'          => 'dashicons-welcome-view-site',
    'slug'          => 'xt-woo-quick-view',
    'prefix'        => 'xt_woo_quick_view',
    'short_prefix'  => 'xt_wooqv',
    'market'        => $market,
    'markets'       => array(
        'freemius' => array(
            'id' => 2905,
            'key' => 'pk_3226f1f4df976b3ad44b987fd9c76',
            'url' => 'https://xplodedthemes.com/products/woo-quick-view/',
            'premium_slug'  => 'xt-woo-quick-view',
            'freemium_slug' => 'xt-woo-quick-view-lite',
        ),
        'envato' => array(
            'id' => 19801709,
            'url' => 'https://codecanyon.net/item/woocommerce-interactive-quick-view/19801709',
            'premium_slug'  => 'xt-woo-quick-view',
        )
    ),
    'dependencies' => array(
        array(
            'name'  => 'WooCommerce',
            'class' => 'WooCommerce',
            'url'   => 'https://en-ca.wordpress.org/plugins/woocommerce/'
        )
    ),
    'file'          => __FILE__
);

if ( function_exists( 'xt_woo_quick_view' ) ) {

    xt_woo_quick_view()->access_manager()->set_basename( false, __FILE__ );

} else {

    /**
     * Require XT Framework
     *
     * @since    1.0.0
     */
    require_once plugin_dir_path(__FILE__) . 'xt-framework/start.php';

    /**
     * Require main plugin file
     *
     * @since    1.0.0
     */
    require_once plugin_dir_path(__FILE__) . 'class-core.php';

    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function xt_woo_quick_view() {

        global $xt_wooqv_plugin;

        return XT_Woo_Quick_View::instance( $xt_wooqv_plugin );
    }

    // Run Plugin.
    xt_woo_quick_view();

}
