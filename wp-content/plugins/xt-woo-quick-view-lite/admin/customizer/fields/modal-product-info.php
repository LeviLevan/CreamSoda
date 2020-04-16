<?php

$default_font = 'Open Sans';
$fields[] = array(
    'id'        => 'product_info_padding',
    'section'   => 'modal-product-info',
    'label'     => esc_attr__( 'Product Info Container Padding ', 'woo-quick-view' ),
    'type'      => 'slider',
    'choices'   => array(
    'min'    => '10',
    'max'    => '40',
    'step'   => '1',
    'suffix' => 'px',
),
    'default'   => '30',
    'priority'  => 10,
    'transport' => 'auto',
    'output'    => array(
    array(
    'element'       => '#xt_wooqv .xt_wooqv-item-info .xt_wooqv-item-info-inner',
    'property'      => 'padding',
    'value_pattern' => '$px',
),
    array(
    'element'       => '#xt_wooqv .xt_wooqv-item-info .xt_wooqv-item-info-inner',
    'media_query'   => $customizer->media_query( 'desktop', 'min' ),
    'property'      => 'padding',
    'value_pattern' => 'calc($px * 1.25)',
),
    array(
    'element'       => '#xt_wooqv .xt_wooqv-item-info .xt_wooqv-item-info-inner',
    'media_query'   => $customizer->media_query( 'tablet', 'max' ),
    'property'      => 'padding-bottom',
    'value_pattern' => 'calc($px + 75px)',
),
    array(
    'element'       => '.xt_wooqv-mobile-bar-visible #xt_wooqv .xt_wooqv-item-info .xt_wooqv-item-info-inner',
    'media_query'   => $customizer->media_query( 'tablet', 'max' ),
    'property'      => 'padding-bottom',
    'value_pattern' => 'calc($px + 75px + 114px)',
)
),
);
$fields[] = array(
    'id'        => 'typo_product_title',
    'section'   => 'modal-product-info',
    'label'     => esc_attr__( 'Product Title Typography', 'woo-quick-view' ),
    'type'      => 'typography',
    'default'   => array(
    'font-family'    => $default_font,
    'variant'        => '700',
    'font-size'      => '26px',
    'letter-spacing' => '0',
    'subsets'        => array( 'latin-ext' ),
    'text-transform' => 'capitalize',
    'color'          => '',
),
    'priority'  => 10,
    'transport' => 'auto',
    'output'    => array( array(
    'element' => '#xt_wooqv .xt_wooqv-item-info .product_title',
), array(
    'element'       => '#xt_wooqv .xt_wooqv-item-info .product_title',
    'media_query'   => $customizer->media_query( 'mobile', 'max' ),
    'property'      => 'font-size',
    'choice'        => 'font-size',
    'value_pattern' => 'calc($ * 0.75)',
) ),
);
$fields[] = array(
    'id'        => 'product_title_margin_bottom',
    'section'   => 'modal-product-info',
    'label'     => esc_attr__( 'Product Title Bottom Margin ', 'woo-quick-view' ),
    'type'      => 'slider',
    'choices'   => array(
    'min'    => '0',
    'max'    => '50',
    'step'   => '1',
    'suffix' => 'px',
),
    'default'   => '10',
    'priority'  => 10,
    'transport' => 'auto',
    'output'    => array( array(
    'element'       => '#xt_wooqv .xt_wooqv-item-info .product_title',
    'property'      => 'margin-bottom',
    'value_pattern' => '$px!important',
) ),
);
$fields[] = array(
    'id'      => 'product_info_features',
    'section' => 'modal-product-info',
    'type'    => 'xt-premium',
    'default' => array(
    'type'  => 'image',
    'value' => $this->core->plugin_url() . 'admin/customizer/assets/images/product-info.png',
    'link'  => $this->core->access_manager()->get_upgrade_url(),
),
);