<?php

$fields[] = array(
    'id'       => 'api_description',
    'section'  => 'api',
    'type'     => 'custom',
    'label'    => esc_html__( 'JS API', 'woo-quick-view' ),
    'default'  => '<div>' . esc_html__( 'These JS functions can be used to programmatically control the quick view modal. They can be tested within your browser console.', 'woo-quick-view' ) . '</div>',
    'priority' => 10,
);
$fields[] = array(
    'id'       => 'api_open_modal',
    'section'  => 'api',
    'type'     => 'custom',
    'label'    => esc_html__( 'Open Modal', 'woo-quick-view' ),
    'default'  => '<input readonly="readonly" class="xirki-code-input" value="xt_wooqv_open(product_id)" />',
    'priority' => 10,
);
$fields[] = array(
    'id'       => 'api_close_modal',
    'section'  => 'api',
    'type'     => 'custom',
    'label'    => esc_html__( 'Close Modal', 'woo-quick-view' ),
    'default'  => '<input readonly="readonly" class="xirki-code-input" value="xt_wooqv_close()" />',
    'priority' => 10,
);
$fields[] = array(
    'id'       => 'api_is_modal_open',
    'section'  => 'api',
    'type'     => 'custom',
    'label'    => esc_html__( 'Is Modal Open ?', 'woo-quick-view' ),
    'default'  => '<input readonly="readonly" class="xirki-code-input" value="xt_wooqv_is_modal_open()" />',
    'priority' => 10,
);
$fields[] = array(
    'id'      => 'api_features',
    'section' => 'api',
    'type'    => 'xt-premium',
    'default' => array(
    'type'  => 'image',
    'value' => $this->core->plugin_url() . 'admin/customizer/assets/images/api.png',
    'link'  => $this->core->plugin_upgrade_url(),
),
);