<?php

$version = get_option('woo-quick-view-version');
$options = get_option('wooqv');

if(update_option('xt-woo-quick-view-version', $version)) {

    delete_option('woo-quick-view-version');
}

if(update_option('xt_wooqv', $options)) {

    delete_option('wooqv');
}