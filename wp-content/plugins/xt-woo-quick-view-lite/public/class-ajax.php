<?php
if(function_exists('wc')) {

    class XT_Woo_Quick_View_AJAX extends WC_AJAX {

        /**
         * Hook in ajax handlers.
         */
        public static function init() {

            add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
            add_action( 'template_redirect', array( __CLASS__, 'do_wc_ajax' ), 0 );

            self::add_ajax_events();
        }

        /**
         * Add custom ajax events here
         */
        public static function add_ajax_events() {
            // woocommerce_EVENT => nopriv
            $ajax_events = array(
                'xt_wooqv_quick_view' => true,
            );
            foreach ( $ajax_events as $ajax_event => $nopriv ) {
                add_action( 'wp_ajax_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
                if ( $nopriv ) {
                    add_action( 'wp_ajax_nopriv_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
                    // WC AJAX can be used for frontend ajax requests
                    add_action( 'wc_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
                }
            }
        }

        /**
         * Render Quick View Content
         */
        public static function xt_wooqv_quick_view() {

            $product_id = intval($_REQUEST['id']);
            $variation_id = !empty($_REQUEST['variation_id']) ? intval($_REQUEST['variation_id']) : null;
            $slider_only = !empty($_REQUEST['slider_only']) ? (bool)$_REQUEST['slider_only'] : false;

            $quickview = xt_woo_quick_view()->frontend()->get_product_quick_view($product_id, $variation_id, $slider_only);

            wp_send_json(array('quickview' => $quickview));

        }
    }
}