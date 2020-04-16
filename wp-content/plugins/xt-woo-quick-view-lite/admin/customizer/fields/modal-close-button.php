<?php

$fields[] = array(
    'id' => 'modal_close_size',
    'section' => 'modal-close-button',
    'label' => esc_html__('Icon Size', 'woo-quick-view'),
    'type' => 'slider',
    'choices' => array(
        'min' => '20',
        'max' => '50',
        'step' => '1',
        'suffix' => 'px'
    ),
    'priority' => 10,
    'default' => '25',
    'transport' => 'auto',
    'screen' => 'desktop',
    'hidden_screens' => array('mobile'),
    'output' => array(
        array(
            'element' => array('#xt_wooqv .xt_wooqv-close-icon'),
            'property' => 'font-size',
            'value_pattern' => '$px'
        )

    )
);

$fields[] = array(
    'id' => 'modal_close_size_mobile',
    'section' => 'modal-close-button',
    'label' => esc_html__('Icon Size', 'woo-quick-view'),
    'type' => 'slider',
    'choices' => array(
        'min' => '0.8',
        'max' => '1.3',
        'step' => '0.05',
        'suffix' => 'x'
    ),
    'priority' => 10,
    'default' => '1.1',
    'transport' => 'auto',
    'screen' => 'tablet_mobile',
    'hidden_screens' => array('mobile'),
    'output' => array(
        array(
            'element' => array('#xt_wooqv .xt_wooqv-close-icon'),
            'property' => 'transform',
            'value_pattern' => 'scale($)'
        )

    )
);

$fields[] = array(
    'id' => 'modal_close_icon',
    'section' => 'modal-close-button',
    'label' => esc_html__('Close Icon', 'woo-quick-view'),
    'type' => 'xticons',
    'choices' => array('types' => array('close_2')),
    'priority' => 10,
    'default' => 'xt_wooqvicon-cancel-4',
    'transport' => 'postMessage',
    'screen' => 'desktop',
    'hidden_screens' => array('mobile'),
    'js_vars' => array(
        array(
            'element' => '#xt_wooqv .xt_wooqv-close-icon-desktop',
            'function' => 'class'
        )
    )
);

$fields[] = array(
    'id' => 'modal_close_icon_mobile',
    'section' => 'modal-close-button',
    'label' => esc_html__('Close Icon', 'woo-quick-view'),
    'type' => 'xticons',
    'choices' => array('types' => array('close-no-bg')),
    'priority' => 10,
    'default' => 'xt_wooqvicon-cancel-4',
    'transport' => 'postMessage',
    'screen' => 'tablet_mobile',
    'hidden_screens' => array('mobile'),
    'js_vars' => array(
        array(
            'element' => '#xt_wooqv .xt_wooqv-close-icon-mobile',
            'function' => 'class'
        )
    )
);

$fields[] = array(
    'id' => 'modal_close_bg_color',
    'section' => 'modal-close-button',
    'label' => esc_html__('Icon Background Color', 'woo-quick-view'),
    'type' => 'color-alpha',
    'priority' => 10,
    'default' => '',
    'transport' => 'auto',
    'screen' => 'tablet_mobile',
    'hidden_screens' => array('mobile'),
    'output' => array(
        array(
            'element' => array('#xt_wooqv .xt_wooqv-close-icon'),
            'property' => 'background-color',
        )
    )
);

$fields[] = array(
    'id' => 'modal_close_color',
    'section' => 'modal-close-button',
    'label' => esc_html__('Icon Color', 'woo-quick-view'),
    'type' => 'color-alpha',
    'priority' => 10,
    'default' => '',
    'transport' => 'auto',
    'screen' => 'desktop',
    'hidden_screens' => array('mobile'),
    'output' => array(
        array(
            'element' => array('#xt_wooqv .xt_wooqv-close-icon'),
            'property' => 'color',
        )
    )
);

$fields[] = array(
    'id' => 'modal_close_color_mobile',
    'section' => 'modal-close-button',
    'label' => esc_html__('Icon Color', 'woo-quick-view'),
    'type' => 'color-alpha',
    'priority' => 10,
    'default' => '',
    'transport' => 'auto',
    'screen' => 'tablet_mobile',
    'hidden_screens' => array('mobile'),
    'output' => array(
        array(
            'element' => array('#xt_wooqv .xt_wooqv-close-icon'),
            'property' => 'color',
        )
    )
);

$fields[] = array(
    'id' => 'modal_close_hover_color',
    'section' => 'modal-close-button',
    'label' => esc_html__('Icon Hover Color', 'woo-quick-view'),
    'type' => 'color-alpha',
    'priority' => 10,
    'default' => '',
    'transport' => 'auto',
    'screen' => 'desktop',
    'hidden_screens' => array('mobile'),
    'output' => array(
        array(
            'element' => array('.xt_wooqv-no-touchevents #xt_wooqv .xt_wooqv-close-icon:hover'),
            'property' => 'color',
        )
    )
);

$fields[] = array(
    'id' => 'modal_close_hover_bg_color',
    'section' => 'modal-close-button',
    'label' => esc_html__('Icon Hover Background Color', 'woo-quick-view'),
    'type' => 'color-alpha',
    'priority' => 10,
    'default' => '',
    'transport' => 'auto',
    'screen' => 'tablet_mobile',
    'hidden_screens' => array('mobile'),
    'output' => array(
        array(
            'element' => array('.xt_wooqv-no-touchevents #xt_wooqv .xt_wooqv-close-icon:hover'),
            'property' => 'background-color',
        )
    )
);

$fields[] = array(
    'id' => 'modal_close_hover_color_mobile',
    'section' => 'modal-close-button',
    'label' => esc_html__('Icon Hover Color', 'woo-quick-view'),
    'type' => 'color-alpha',
    'priority' => 10,
    'default' => '',
    'transport' => 'auto',
    'screen' => 'tablet_mobile',
    'hidden_screens' => array('mobile'),
    'output' => array(
        array(
            'element' => array('.xt_wooqv-no-touchevents #xt_wooqv .xt_wooqv-close-icon:hover'),
            'property' => 'color',
        )
    )
);
