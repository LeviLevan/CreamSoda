<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://xplodedthemes.com
 * @since      1.0.0
 *
 * @package    XT_Woo_Quick_View
 * @subpackage XT_Woo_Quick_View/public
 */
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    XT_Woo_Quick_View
 * @subpackage XT_Woo_Quick_View/public
 * @author     XplodedThemes 
 */
class XT_Woo_Quick_View_Public
{
    /**
     * Core class reference.
     *
     * @since    1.0.0
     * @access   private
     * @var      XT_Woo_Quick_View    $core    Core Class
     */
    private  $core ;
    public  $woovs_exists = false ;
    public  $woovs_archives_enabled = false ;
    public  $woovs_single_enabled = false ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @var      XT_Woo_Quick_View    $core    Core Class
     */
    public function __construct( &$core )
    {
        $this->core = $core;
        $this->core->plugin_loader()->add_action( 'wp_enqueue_scripts', $this, 'enqueue_vendors' );
        $this->core->plugin_loader()->add_action( 'wp_enqueue_scripts', $this, 'enqueue_styles' );
        $this->core->plugin_loader()->add_action( 'wp_enqueue_scripts', $this, 'enqueue_scripts' );
        $this->core->plugin_loader()->add_action( 'wp_enqueue_scripts', $this, 'enqueue_theme_fixes' );
        // Check if XT Woo variation Swatches exists
        $this->core->plugin_loader()->add_action( 'init', $this, 'check_woovs_exists' );
        // Init Ajax Instance
        $this->core->plugin_loader()->add_action( 'init', $this, 'init_ajax' );
        // Add body classes
        $this->core->plugin_loader()->add_filter( 'body_class', $this, 'body_class' );
        // Add post classes
        $this->core->plugin_loader()->add_filter( 'post_class', $this, 'product_post_class' );
        // Add Quick View Button
        $this->core->plugin_loader()->add_filter(
            'woocommerce_loop_add_to_cart_link',
            $this,
            'add_quick_view_trigger',
            99,
            2
        );
        // Add Quick View Button Before or Above xt_woovs add to cart
        $this->core->plugin_loader()->add_action(
            'xt_woovs_before_add_to_cart_button',
            $this,
            'add_quick_view_trigger_before',
            1
        );
        // Add Quick View Button After or Below xt_woovs add to cart
        $this->core->plugin_loader()->add_action(
            'xt_woovs_after_add_to_cart_button',
            $this,
            'add_quick_view_trigger_after',
            1
        );
        // Add Quick View Button Over Product container
        $this->core->plugin_loader()->add_action(
            'woocommerce_after_shop_loop_item',
            $this,
            'add_quick_view_trigger_over_product',
            1
        );
        // Add Quick View Button Over Image
        $this->core->plugin_loader()->add_action(
            'xt_wooqv_after_product_image',
            $this,
            'add_quick_view_trigger_over_image',
            1
        );
        // Add More Info Button
        $this->core->plugin_loader()->add_action(
            'woocommerce_after_add_to_cart_button',
            $this,
            'add_more_info_button',
            15
        );
        // Register Shortcode
        $this->core->plugin_loader()->add_action( 'init', $this, 'register_shortcode' );
        $this->core->plugin_loader()->add_action( 'wp_footer', $this, 'render' );
        $this->core->plugin_loader()->add_action( 'wp', $this, 'action_template' );
    }
    
    public function check_woovs_exists()
    {
        $this->woovs_exists = function_exists( 'xt_woo_variation_swatches' );
        $this->woovs_archives_enabled = $this->woovs_exists && xt_woo_variation_swatches()->frontend()->enabled( 'archives' );
        $this->woovs_single_enabled = $this->woovs_exists && xt_woo_variation_swatches()->frontend()->enabled( 'single' );
    }
    
    // Init Ajax Instance
    public function init_ajax()
    {
        XT_Woo_Quick_View_AJAX::init();
    }
    
    public function body_class( $classes )
    {
        $prefix = 'xt_wooqv-';
        $modal_mode = xt_wooqv_modal_type();
        $classes[] = $prefix . $modal_mode;
        $bg_color = xt_wooqv_option( 'modal_box_bg_color', '#ffffff' );
        $box_bg_class = xtfw_light_or_dark( $bg_color, 'is-light-bg', 'is-dark-bg' );
        $classes[] = $prefix . $box_bg_class;
        
        if ( $modal_mode === 'default' ) {
            $overlay_color = xt_wooqv_option( 'modal_overlay_color', 'rgba(71,55,78,0.8)' );
            $modal_overlay_class = xtfw_light_or_dark( $overlay_color, 'is-light-overlay', 'is-dark-overlay' );
            $classes[] = $prefix . $modal_overlay_class;
        }
        
        $nav_mobile_position = xt_wooqv_option( 'modal_nav_mobile_position', 'left' );
        $nav_desktop_fullscreen_position = xt_wooqv_option( 'modal_nav_desktop_fullscreen_position', 'middle' );
        $classes[] = $prefix . 'mobile-nav-pos-' . $nav_mobile_position;
        $classes[] = $prefix . 'desktop-fullscreen-nav-pos-' . $nav_desktop_fullscreen_position;
        $trigger_position = xt_wooqv_option( 'trigger_position', 'before' );
        
        if ( in_array( $trigger_position, array( 'above', 'below' ) ) ) {
            $shop_buttons_display = xt_wooqv_option( 'product_buttons_fullwidth', 'block' );
            $classes[] = $prefix . 'button-' . $shop_buttons_display;
        }
        
        return $classes;
    }
    
    public function product_post_class( $classes )
    {
        if ( get_post_type() !== 'product' ) {
            return $classes;
        }
        $position = $this->core->customizer()->get_option( 'trigger_position', 'before' );
        if ( $position !== 'over-product' ) {
            return $classes;
        }
        $classes[] = 'xt_wooqv-relative';
        return $classes;
    }
    
    /**
     * Register vendors assets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_vendors()
    {
        // WooCommerce Variations Script
        wp_enqueue_script( 'wc-add-to-cart-variation' );
        // WooCommerce Bundles Assets
        
        if ( !is_single() && class_exists( 'WC_PB_Display' ) ) {
            WC_PB_Display::instance()->frontend_scripts();
            wp_enqueue_script( 'wc-add-to-cart-bundle' );
            wp_enqueue_style( 'wc-bundle-css' );
            wp_enqueue_style( 'wc-pb-bs-single' );
        }
        
        // WooCommerce Composites Assets
        
        if ( !is_single() && class_exists( 'WC_CP_Display' ) ) {
            WC_CP_Display::instance()->frontend_scripts();
            wp_enqueue_script( 'wc-add-to-cart-composite' );
            wp_enqueue_style( 'wc-composite-single-css' );
            wp_enqueue_style( 'wc-composite-css' );
        }
        
        // Load Plugin Vendors
        wp_enqueue_script( 'jquery-effects-core' );
        wp_enqueue_script(
            'xt-jquery-touch',
            $this->core->plugin_url( 'public/assets/vendors', 'jquery.touch' . XTFW_SCRIPT_SUFFIX . '.js' ),
            array( 'jquery' ),
            $this->core->plugin_version(),
            false
        );
        wp_enqueue_script(
            'xt-velocity',
            $this->core->plugin_url( 'public/assets/vendors', 'velocity' . XTFW_SCRIPT_SUFFIX . '.js' ),
            array( 'jquery' ),
            $this->core->plugin_version(),
            false
        );
        wp_enqueue_style(
            'xt-icons',
            xtfw_dir_url( XTFW_DIR_CUSTOMIZER ) . '/controls/xt_icons/css/xt-icons.css',
            array(),
            $this->core->framework_version(),
            'all'
        );
        wp_enqueue_script(
            'xt-jquery-serializejson',
            $this->core->plugin_url( 'public/assets/vendors', 'jquery.serializejson' . XTFW_SCRIPT_SUFFIX . '.js' ),
            array( 'jquery' ),
            $this->core->plugin_version(),
            false
        );
        if ( is_customize_preview() ) {
            wp_enqueue_script(
                'xt-jquery-attrchange',
                $this->core->plugin_url( 'public/assets/vendors', 'jquery.attrchange' . XTFW_SCRIPT_SUFFIX . '.js' ),
                array( 'jquery' ),
                $this->core->plugin_version(),
                false
            );
        }
    }
    
    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in XT_Woo_Quick_View_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The XT_Woo_Quick_View_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style(
            $this->core->plugin_slug(),
            $this->core->plugin_url( 'public/assets/css', 'frontend.css' ),
            array(),
            filemtime( $this->core->plugin_path( 'public/assets/css', 'frontend.css' ) ),
            'all'
        );
        if ( is_rtl() ) {
            wp_enqueue_style(
                $this->core->plugin_slug( 'rtl' ),
                $this->core->plugin_url( 'public/assets/css', 'rtl.css' ),
                array( $this->core->plugin_slug() ),
                filemtime( $this->core->plugin_path( 'public/assets/css', 'rtl.css' ) ),
                'all'
            );
        }
    }
    
    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in XT_Woo_Quick_View_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The XT_Woo_Quick_View_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        $wc_ajax_url = add_query_arg( 'wc-ajax', '%%endpoint%%', home_url( '/' ) );
        $vars = array(
            'wc_ajax_url'           => urldecode( $wc_ajax_url ),
            'layouts'               => $this->core->customizer()->breakpointsJson(),
            'is_fullscreen'         => xt_wooqv_modal_type_is( 'fullscreen' ),
            'is_inline'             => xt_wooqv_modal_type_is( 'inline' ),
            'fullscreen_animation'  => xt_wooqv_option( 'animation_type', 'none' ),
            'close_on_added'        => xt_wooqv_option_bool( 'close_modal_on_added', false ),
            'modal_nav_enabled'     => xt_wooqv_option( 'modal_nav_enabled', false ),
            'slider_lightbox'       => xt_wooqv_option_bool( 'modal_slider_lightbox_enabled', false ),
            'slider_items_desktop'  => xt_wooqv_option( 'modal_slider_items_visible_desktop', 1 ),
            'slider_vertical'       => xt_wooqv_option_bool( 'modal_slider_vertical', false ),
            'slider_animation'      => xt_wooqv_option( 'modal_slider_animation', 'slide' ),
            'slider_autoplay'       => xt_wooqv_option_bool( 'modal_slider_autoplay', false ),
            'slider_gallery'        => xt_wooqv_option_bool( 'modal_slider_thumb_gallery_enabled', false ),
            'slider_arrows_enabled' => xt_wooqv_option_bool( 'modal_slider_arrows_enabled', false ),
            'slider_arrow'          => xt_wooqv_option( 'modal_slider_arrow', '' ),
            'can_use_premium_code'  => $this->core->access_manager()->can_use_premium_code__premium_only(),
        );
        wp_register_script(
            $this->core->plugin_slug(),
            $this->core->plugin_url( 'public/assets/js', 'frontend' . XTFW_SCRIPT_SUFFIX . '.js' ),
            array( 'jquery' ),
            filemtime( $this->core->plugin_path( 'public/assets/js', 'frontend' . XTFW_SCRIPT_SUFFIX . '.js' ) ),
            false
        );
        wp_localize_script( $this->core->plugin_slug(), 'XT_WOOQV', $vars );
        wp_enqueue_script( $this->core->plugin_slug() );
    }
    
    /**
     * Load frontend Theme Fixes.
     * @access  public
     * @since   1.0.0
     * @return void
     */
    public function enqueue_theme_fixes()
    {
        $theme_name = get_template();
        $theme_fixes = array();
        if ( !empty($theme_fixes[$theme_name]) ) {
            foreach ( $theme_fixes[$theme_name] as $type ) {
                
                if ( $type == 'css' ) {
                    wp_register_style(
                        $this->core->plugin_slug( $theme_name ),
                        $this->core->plugin_url( 'public' ) . 'assets/theme-fix/css/' . $theme_name . '.css',
                        array( $this->core->plugin_slug() ),
                        $this->core->plugin_version()
                    );
                    wp_enqueue_style( $this->core->plugin_slug( $theme_name ) );
                } else {
                    wp_register_script(
                        $this->core->plugin_slug( $theme_name ),
                        $this->core->plugin_url( 'public' ) . 'assets/theme-fix/js/' . $theme_name . '.js',
                        array( $this->core->plugin_slug() ),
                        $this->core->plugin_version(),
                        true
                    );
                    wp_enqueue_script( $this->core->plugin_slug( $theme_name ) );
                }
            
            }
        }
    }
    
    // End enqueue_theme_fixes ()
    /**
     * Add quick view button in wc product loop
     *
     * @access public
     * @return $link
     * @since  1.0.0
     */
    public function add_quick_view_trigger( $link, $product )
    {
        if ( $this->woovs_archives_enabled && $product->is_type( 'variable' ) ) {
            return $link;
        }
        $product_id = $product->get_id();
        $position = $this->core->customizer()->get_option( 'trigger_position', 'before' );
        if ( $position == 'shortcode' ) {
            $position = 'before';
        }
        if ( $position == 'over-image' || $position == 'over-product' ) {
            return $link;
        }
        $quickViewButton = $this->trigger_button( $product_id );
        
        if ( $position == 'before' || $position == 'above' ) {
            
            if ( strpos( $link, '<a' ) !== false ) {
                $link = str_replace( '<a', $quickViewButton . '<a', $link );
            } else {
                $link = $quickViewButton . $link;
            }
        
        } else {
            
            if ( strpos( $link, '</a>' ) !== false ) {
                $link = str_replace( '</a>', '</a>' . $quickViewButton, $link );
            } else {
                $link = $link . $quickViewButton;
            }
        
        }
        
        return $link;
    }
    
    /**
     * Add quick view before add to cart
     *
     * @access public
     * @return $link
     * @since  1.0.0
     */
    public function add_quick_view_trigger_before()
    {
        $product_id = get_the_ID();
        if ( empty($product_id) ) {
            return false;
        }
        $position = $this->core->customizer()->get_option( 'trigger_position', 'before' );
        if ( !in_array( $position, array( 'before', 'above', 'shortcode' ) ) ) {
            return false;
        }
        echo  $this->trigger_button( $product_id ) ;
    }
    
    /**
     * Add quick view before add to cart
     *
     * @access public
     * @return $link
     * @since  1.0.0
     */
    public function add_quick_view_trigger_after()
    {
        $product_id = get_the_ID();
        if ( empty($product_id) ) {
            return false;
        }
        $position = $this->core->customizer()->get_option( 'trigger_position', 'before' );
        if ( !in_array( $position, array( 'after', 'below' ) ) ) {
            return false;
        }
        echo  $this->trigger_button( $product_id ) ;
    }
    
    public function add_quick_view_trigger_over_product()
    {
        $product_id = get_the_ID();
        if ( empty($product_id) ) {
            return false;
        }
        $position = $this->core->customizer()->get_option( 'trigger_position', 'before' );
        if ( $position != 'over-product' ) {
            return false;
        }
        $trigger_overlay = $this->core->customizer()->get_option( 'trigger_overlay', false );
        if ( !empty($trigger_overlay) ) {
            echo  '<span class="xt_wooqv-product-overlay"></span>' ;
        }
        echo  $this->trigger_button( $product_id, 'span' ) ;
    }
    
    public function add_quick_view_trigger_over_image()
    {
        $product_id = get_the_ID();
        if ( empty($product_id) ) {
            return false;
        }
        $position = $this->core->customizer()->get_option( 'trigger_position', 'before' );
        if ( $position != 'over-image' ) {
            return false;
        }
        $trigger_overlay = $this->core->customizer()->get_option( 'trigger_overlay', false );
        if ( !empty($trigger_overlay) ) {
            echo  '<span class="xt_wooqv-product-overlay"></span>' ;
        }
        echo  $this->trigger_button( $product_id, 'span' ) ;
    }
    
    public function register_shortcode()
    {
        add_shortcode( 'xt_wooqv_trigger', array( $this, 'trigger_button_shortcode' ) );
    }
    
    public function trigger_button_shortcode( $atts )
    {
        extract( shortcode_atts( array(
            'id'  => '',
            'tag' => 'a',
        ), $atts ) );
        $uniqid = 'xt_wooqv-shortcode-trigger-' . uniqid();
        $output = $this->trigger_button( $id, $tag, $uniqid );
        $output .= '<div id="' . $uniqid . '" class="xt_wooqv-shortcode-product">' . do_shortcode( '[products ids="' . $id . '"]' ) . '</div>';
        return $output;
    }
    
    public function trigger_button( $product_id, $tag = 'a', $uniqid = null )
    {
        $quickview = $this->get_encoded_product_quick_view( $product_id );
        $classes = array( 'xt_wooqv-trigger', 'button', 'alt' );
        $extra_classes = explode( ' ', $this->core->customizer()->get_option( 'trigger_classes', '' ) );
        $classes = array_merge( $classes, $extra_classes );
        $position = $this->core->customizer()->get_option( 'trigger_position', 'before' );
        $icon_type = $this->core->customizer()->get_option( 'trigger_icon_type', 'font' );
        $icon_only = $this->core->customizer()->get_option( 'trigger_icon_only', '0' );
        if ( !empty($icon_only) && empty($icon_type) ) {
            $icon_only = false;
        }
        $classes[] = 'xt_wooqv-' . esc_attr( $position );
        if ( $position == 'over-product' ) {
            $classes[] = 'xt_wooqv-over-image';
        }
        if ( !empty($icon_type) ) {
            $classes[] = 'xt_wooqv-icontype-' . $icon_type;
        }
        if ( !empty($icon_only) ) {
            $classes[] = 'xt_wooqv-icon-only';
        }
        if ( !empty($uniqid) ) {
            $classes[] = 'xt_wooqv-shortcode-trigger';
        }
        $classes = apply_filters( 'xt_wooqv_trigger_classes', $classes, $product_id );
        $classes = implode( ' ', $classes );
        $button = '<' . $tag . ' href="#" class="' . esc_attr( $classes ) . '" data-id="' . esc_attr( $product_id ) . '" data-quickview="' . esc_attr( $quickview ) . '"';
        if ( !empty($uniqid) ) {
            $button .= ' target="' . $uniqid . '"';
        }
        $button .= '>';
        if ( !empty($icon_type) ) {
            $button .= '<span class="' . xt_wooqv_trigger_cart_icon_class() . '"></span>';
        }
        if ( empty($icon_only) ) {
            $button .= '<span>' . esc_html__( 'Quick View', 'woo-quick-view' ) . '</span>';
        }
        $button .= '</' . $tag . '>';
        $button = apply_filters(
            'xt_wooqv_trigger_button',
            $button,
            $product_id,
            $tag,
            $uniqid
        );
        return $button;
    }
    
    public function add_more_info_button()
    {
        $classes = array( 'xt_wooqv-button', 'xt_wooqv-more-info', 'button' );
        $classes = apply_filters( 'xt_wooqv_more_info_button_classes', $classes, get_the_ID() );
        $classes = implode( ' ', $classes );
        ?>
		<button type="button" class="<?php 
        echo  esc_attr( $classes ) ;
        ?>" onclick="location.href='<?php 
        the_permalink();
        ?>'"><?php 
        esc_html_e( 'More info', 'woo-quick-view' );
        ?></button>
		<?php 
    }
    
    public function get_encoded_product_quick_view( $product )
    {
        if ( !is_object( $product ) ) {
            $product = wc_get_product( $product );
        }
        $quickview = $this->get_product_quick_view( $product );
        $quickview = htmlspecialchars( json_encode( $quickview ), ENT_QUOTES, 'UTF-8' );
        return $quickview;
    }
    
    /**
     * Render Quick View Content
     */
    public function get_product_quick_view( $product, $variation_id = null, $slider_only = false )
    {
        if ( !is_object( $product ) ) {
            $product = wc_get_product( $product );
        }
        $backup = $GLOBALS['wp_query'];
        $is_variable = $product->is_type( 'variable' );
        query_posts( 'p=' . $product->get_id() . '&post_type=product' );
        the_post();
        // remove product thumbnails gallery
        remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
        
        if ( !empty($variation_id) ) {
            $quickview = xt_woo_quick_view()->get_template( 'parts/product-slider', array(
                'variation_id' => $variation_id,
            ), true );
        } else {
            
            if ( !empty($slider_only) ) {
                $quickview = xt_woo_quick_view()->get_template( 'parts/product-slider', array(), true );
            } else {
                $loop = wc_get_loop_prop( 'loop' );
                wc_set_loop_prop( 'loop', $loop - 1 );
                
                if ( $is_variable && $this->woovs_single_enabled && xt_woovs_is_single_product() ) {
                    remove_action( 'woocommerce_single_variation', array( xt_woo_variation_swatches()->frontend(), 'loop_variation_add_to_cart_button' ), 20 );
                    add_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
                }
                
                $quickview = xt_woo_quick_view()->get_template( 'parts/product', array(), true );
                
                if ( $is_variable && $this->woovs_single_enabled && xt_woovs_is_single_product() ) {
                    add_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
                    remove_action( 'woocommerce_single_variation', array( xt_woo_variation_swatches()->frontend(), 'loop_variation_add_to_cart_button' ), 20 );
                }
            
            }
        
        }
        
        $GLOBALS['wp_query'] = $backup;
        return $quickview;
    }
    
    public function action_template()
    {
        $table_variations_installed = function_exists( 'remove_variable_product_add_to_cart' ) && function_exists( 'woo_variations_table_available_options_btn' );
        $xt_wooqv_action = !empty($_POST['action']) && $_POST['action'] == 'xt_wooqv_quick_view';
        if ( $table_variations_installed && $xt_wooqv_action ) {
            add_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 1 );
        }
        add_action( 'xt_wooqv_product_summary', 'woocommerce_template_single_title', 5 );
        add_action( 'xt_wooqv_product_summary', 'woocommerce_template_single_rating', 10 );
        add_action( 'xt_wooqv_product_summary', 'woocommerce_template_single_price', 15 );
        add_action( 'xt_wooqv_product_summary', 'woocommerce_template_single_excerpt', 20 );
        add_action( 'xt_wooqv_product_summary', 'woocommerce_template_single_meta', 25 );
        add_action( 'xt_wooqv_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    }
    
    public function render()
    {
        $this->core->get_template( 'quickview' );
    }

}