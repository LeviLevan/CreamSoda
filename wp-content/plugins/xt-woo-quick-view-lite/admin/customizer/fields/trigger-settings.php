<?php

$fields[] = array(
    'id'        => 'trigger_position',
    'section'   => 'trigger-settings',
    'label'     => esc_html__( 'Quick View Button Position', 'woo-quick-view' ),
    'type'      => 'radio',
    'priority'  => 10,
    'transport' => 'auto',
    'choices'   => array(
    'before' => esc_html__( 'Before Add to cart button', 'woo-quick-view' ),
    'after'  => esc_html__( 'After add to cart button', 'woo-quick-view' ),
),
    'default'   => 'before',
);
$fields[] = array(
    'id'      => 'trigger_features',
    'section' => 'trigger-settings',
    'type'    => 'xt-premium',
    'default' => array(
    'type'  => 'image',
    'value' => $this->core->plugin_url() . 'admin/customizer/assets/images/trigger.png',
    'link'  => $this->core->plugin_upgrade_url(),
),
);