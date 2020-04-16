<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 * @package    XT_Woo_Quick_View
 * @author     XplodedThemes
*/
// If this file is called directly, abort.
if ( !defined( 'WPINC' ) ) {
    die;
}
class XT_Woo_Quick_View extends XT_Framework
{
    /**
     * The single instance of XT_Woo_Quick_View.
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static  $_instance = null ;
    /**
     * Bootstrap plugin
     *
     * This hack is needed. Overriding parent for Freemius to work properly.
     * Freemius needs to be called from each plugin and not from the XT Framework instance.
     * This way, when Freemius calls the function "get_caller_main_file_and_type", it will return the correct plugin path
     * Otherwise, the main path will be seen for all plugins and will cause issues
     *
     * Waiting for a fix from Freemius
     *
     * @since    1.0.0
     * @access   public
     */
    public function bootstrap()
    {
        parent::bootstrap();
    }
    
    /**
     * Load Freemius License Manager
     *
     * This hack is needed. Implementing this abstract XT Framework method for Freemius to work properly.
     * Freemius fs_dynamic_init needs to be called from each plugin and not from the XT Framework instance,
     * This way the "is_premium" param will correctly be generated for both free and premium versions
     *
     * Waiting for a fix from Freemius
     *
     * @return mixed
     * @since    1.0.0
     */
    protected function freemius_access_manager()
    {
        // Activate multisite network integration.
        if ( !defined( 'WP_FS__PRODUCT_' . $this->market_product()->id . '_MULTISITE' ) ) {
            define( 'WP_FS__PRODUCT_' . $this->market_product()->id . '_MULTISITE', true );
        }
        // Include Freemius SDK.
        require_once $this->plugin_framework_path( 'includes/freemius', 'start.php' );
        $menu = array(
            'slug'    => $this->plugin_slug(),
            'contact' => false,
            'support' => false,
        );
        if ( !$this->plugin()->top_menu() ) {
            $menu['parent'] = array(
                'slug' => $this->framework_slug(),
            );
        }
        return fs_dynamic_init( array(
            'id'              => $this->market_product()->id,
            'slug'            => $this->market_product()->freemium_slug,
            'premium_slug'    => $this->market_product()->premium_slug,
            'type'            => 'plugin',
            'public_key'      => $this->market_product()->key,
            'is_premium'      => false,
            'premium_suffix'  => 'Pro',
            'has_addons'      => false,
            'has_paid_plans'  => true,
            'has_affiliation' => 'all',
            'trial'           => array(
            'days'               => 14,
            'is_require_payment' => true,
        ),
            'menu'            => $menu,
            'navigation'      => 'menu',
            'is_live'         => true,
        ) );
    }
    
    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    protected function init_backend()
    {
        $this->plugin_backend = new XT_Woo_Quick_View_Admin( $this );
    }
    
    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    protected function init_frontend()
    {
        $this->plugin_frontend = new XT_Woo_Quick_View_Public( $this );
    }
    
    /**
     * The plugin theme templates path
     *
     * @return    string    The plugin theme templates path.
     * @since     1.0.0
     */
    public function template_path()
    {
        return apply_filters( $this->plugin_prefix( 'template_path' ), 'woo-quick-view/' );
    }
    
    /**
     * Main XT_Woo_Quick_View Instance
     *
     * Ensures only one instance of XT_Woo_Quick_View is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see XT_Woo_Quick_View()
     * @return XT_Woo_Quick_View instance
     */
    public static function instance( $params )
    {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self( $params );
        }
        return self::$_instance;
    }

}