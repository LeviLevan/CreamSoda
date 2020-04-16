<?php

$fields[] = array(
    'id'      => 'product_slider_features',
    'section' => 'modal-product-slider',
    'type'    => 'xt-premium',
    'default' => array(
    'type'  => 'image',
    'value' => $this->core->plugin_url() . 'admin/customizer/assets/images/product-slider.png',
    'link'  => $this->core->plugin_upgrade_url(),
),
);