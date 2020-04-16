<?php

$fields[] = array(
    'id'          => 'modal_zindex_desktop',
    'section'     => 'modal-settings',
    'label'       => esc_html__( 'Modal Z-Index', 'woo-quick-view' ),
    'description' => esc_html__( 'Set the stack order for the cart. An element with greater stack order is always in front of an element with a lower stack order. Use this option to adjust the cart order if for some reason it is not visible on your theme or maybe being overlapped by other elements', 'woo-quick-view' ),
    'type'        => 'slider',
    'choices'     => array(
    'min'  => '999',
    'max'  => '99999',
    'step' => '9',
),
    'priority'    => 10,
    'default'     => '90000',
    'transport'   => 'auto',
    'screen'      => 'desktop',
    'output'      => array(
    array(
    'element'  => '.xt_wooqv-overlay',
    'property' => 'z-index',
),
    array(
    'element'       => '#xt_wooqv.xt_wooqv-is-visible',
    'value_pattern' => 'calc($ + 100)',
    'property'      => 'z-index',
),
    array(
    'element'       => array( '.xt_wooqv-ready .lg-backdrop', '.xt_wooqv-ready .lg-outer' ),
    'value_pattern' => 'calc($ + 9999)',
    'property'      => 'z-index',
),
    array(
    'element'       => '.xt_wooqv-fullscreen .xt_wooqv-nav',
    'value_pattern' => 'calc($ + 110)',
    'property'      => 'z-index',
)
),
);
$fields[] = array(
    'id'          => 'modal_zindex_tablet',
    'section'     => 'modal-settings',
    'label'       => esc_html__( 'Modal Z-Index', 'woo-quick-view' ),
    'description' => esc_html__( 'Set the stack order for the cart. An element with greater stack order is always in front of an element with a lower stack order. Use this option to adjust the cart order if for some reason it is not visible on your theme or maybe being overlapped by other elements', 'woo-quick-view' ),
    'type'        => 'slider',
    'choices'     => array(
    'min'  => '999',
    'max'  => '99999',
    'step' => '9',
),
    'priority'    => 10,
    'default'     => '90000',
    'transport'   => 'auto',
    'screen'      => 'tablet',
    'output'      => array(
    array(
    'element'  => '.xt_wooqv-overlay',
    'property' => 'z-index',
),
    array(
    'element'       => '#xt_wooqv.xt_wooqv-is-visible',
    'value_pattern' => 'calc($ + 100)',
    'property'      => 'z-index',
),
    array(
    'element'       => array( '.xt_wooqv-ready .lg-backdrop', '.xt_wooqv-ready .lg-outer' ),
    'value_pattern' => 'calc($ + 9999)',
    'property'      => 'z-index',
),
    array(
    'element'       => '.xt_wooqv-fullscreen .xt_wooqv-nav',
    'value_pattern' => 'calc($ + 110)',
    'property'      => 'z-index',
)
),
);
$fields[] = array(
    'id'          => 'modal_zindex_mobile',
    'section'     => 'modal-settings',
    'label'       => esc_html__( 'Modal Z-Index', 'woo-quick-view' ),
    'description' => esc_html__( 'Set the stack order for the cart. An element with greater stack order is always in front of an element with a lower stack order. Use this option to adjust the cart order if for some reason it is not visible on your theme or maybe being overlapped by other elements', 'woo-quick-view' ),
    'type'        => 'slider',
    'choices'     => array(
    'min'  => '999',
    'max'  => '99999',
    'step' => '9',
),
    'priority'    => 10,
    'default'     => '90000',
    'transport'   => 'auto',
    'screen'      => 'mobile',
    'output'      => array(
    array(
    'element'  => '.xt_wooqv-overlay',
    'property' => 'z-index',
),
    array(
    'element'       => '#xt_wooqv.xt_wooqv-is-visible',
    'value_pattern' => 'calc($ + 100)',
    'property'      => 'z-index',
),
    array(
    'element'       => array( '.xt_wooqv-ready .lg-backdrop', '.xt_wooqv-ready .lg-outer' ),
    'value_pattern' => 'calc($ + 9999)',
    'property'      => 'z-index',
),
    array(
    'element'       => '.xt_wooqv-fullscreen .xt_wooqv-nav',
    'value_pattern' => 'calc($ + 110)',
    'property'      => 'z-index',
)
),
);
$fields[] = array(
    'id'      => 'box_features',
    'section' => 'modal-settings',
    'type'    => 'xt-premium',
    'default' => array(
    'type'  => 'image',
    'value' => $this->core->plugin_url() . 'admin/customizer/assets/images/settings.png',
    'link'  => $this->core->plugin_upgrade_url(),
),
);