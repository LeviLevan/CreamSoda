<?php

$customizer = xt_woo_quick_view()->customizer();

$value = $customizer->get_option('trigger_center_text');
$customizer->delete_option('trigger_center_text');
$customizer->update_option('product_buttons_alignment', $value);
