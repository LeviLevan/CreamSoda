<?php

class XT_Woo_Quick_View_Woocommerce{

    function __construct() {

        /*
         * To make Quick View Trigger work over images
         *
         * This snippet removes the action that inserts thumbnails to products in the loop
         * and re-adds the function customized with our wrapper in it.
         * It applies to all archives with products.
         */

        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
        remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);

        add_action('woocommerce_before_shop_loop_item_title', array($this, 'template_loop_before_product_thumbnail'), 10);
        add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
        add_action('woocommerce_before_shop_loop_item_title', array($this, 'template_loop_after_product_thumbnail'), 10);

        add_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);

        add_filter('woocommerce_short_description', array($this, 'woocommerce_short_description'), 10, 1);

    }


    function template_loop_before_product_thumbnail() {

        echo '<div class="xt_wooqv-image-wrapper">';
        do_action('xt_wooqv_before_product_image');
    }


    function template_loop_after_product_thumbnail() {

        do_action('xt_wooqv_after_product_image');
        echo '</div>';
    }


    // define the woocommerce_short_description callback
    function woocommerce_short_description( $post_post_excerpt ) {

        global $post;

        $auto_generate_description = xt_wooqv_option_bool('auto_generate_description', false);

        if($auto_generate_description && is_single() && ($post->post_type === 'product') && empty($post_post_excerpt)) {

            $post_post_excerpt = wp_trim_words( strip_tags($post->post_content), 55, null );
        }

        return $post_post_excerpt;
    }

}

new XT_Woo_Quick_View_Woocommerce;