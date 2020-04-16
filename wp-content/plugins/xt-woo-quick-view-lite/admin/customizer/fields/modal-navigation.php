<?php

$fields[] = array(
    'id'      => 'navigation_features',
    'section' => 'modal-navigation',
    'type'    => 'xt-premium',
    'default' => array(
    'type'  => 'image',
    'value' => $this->core->plugin_url() . 'admin/customizer/assets/images/navigation.png',
    'link'  => $this->core->plugin_upgrade_url(),
),
);