<?php
/**
 * Class: Jet_Woo_Product_Gallery_Slider
 * Name: Gallery Slider
 * Slug: jet-woo-product-gallery-slider
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Jet_Woo_Product_Gallery_Slider extends Jet_Woo_Product_Gallery_Base {

	public function get_name() {
		return 'jet-woo-product-gallery-slider';
	}

	public function get_title() {
		return esc_html__( 'Gallery Slider', 'jet-woo-product-gallery' );
	}

	public function get_script_depends() {
		return array(
			'jquery-slick',
			'zoom',
			'wc-single-product',
			'mediaelement',
			'photoswipe-ui-default',
			'photoswipe'
		);
	}

	public function get_style_depends() {
		return array( 'mediaelement', 'photoswipe', 'photoswipe-default-skin' );
	}

	public function get_icon() {
		return 'jetwoobuilder-icon-5';
	}

	public function get_categories() {
		return array( 'jet-woo-product-gallery' );
	}

	public function register_product_gallery_controls() {
		$this->start_controls_section(
			'section_product_images',
			array(
				'label'      => esc_html__( 'Images', 'jet-woo-product-gallery' ),
				'tab'        => Controls_Manager::TAB_CONTENT,
				'show_label' => false,
			)
		);

		$this->add_control(
			'image_size',
			array(
				'label'   => esc_html__( 'Image Size', 'jet-woo-product-gallery' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => jet_woo_product_gallery_tools()->get_image_sizes(),
			)
		);

		$this->add_control(
			'thumbs_image_size',
			array(
				'label'   => esc_html__( 'Thumbnails Image Size', 'jet-woo-product-gallery' ),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => jet_woo_product_gallery_tools()->get_image_sizes(),
			)
		);

		$this->end_controls_section();

		$css_scheme = apply_filters(
			'jet-woo-product-gallery-slider/css-scheme',
			array(
				'slider'                 => '.jet-woo-product-gallery-slider',
				'slider-item'            => '.jet-woo-product-gallery-slider .jet-woo-product-gallery__image-item',
				'images'                 => '.jet-woo-product-gallery-slider .jet-woo-product-gallery__image',
				'images-arrows'          => '.jet-woo-slick .slick-arrow',
				'pagination'             => '.slick-dots',
				'pagination-items'       => '.slick-dots > li > button',
				'pagination-active-item' => '.slick-dots > li.slick-active > button',
				'thumbnails-wrapper'     => '.jet-woo-slick-control-thumbs',
				'thumbnails-list'        => '.jet-woo-slick-control-thumbs .slick-list',
				'thumbnails-list-slide'  => '.jet-woo-slick-control-thumbs .slick-list .slick-slide',
				'thumbnails'             => '.jet-woo-slick-control-thumbs__item-image',
				'thumbnails-arrows'      => '.jet-woo-slick-control-thumbs .slick-arrow',
			)
		);

		$this->register_controls_slider( $css_scheme );

		$this->register_controls_images_styles( $css_scheme );

		$this->register_controls_thumbnails_styles( $css_scheme );

		$this->register_controls_pagination_styles( $css_scheme );

	}

	public function register_controls_slider( $css_scheme ) {

		$this->start_controls_section(
			'section_slider_style',
			array(
				'label'      => esc_html__( 'Slider', 'jet-woo-product-gallery' ),
				'tab'        => Controls_Manager::TAB_CONTENT,
				'show_label' => false,
			)
		);

		$this->add_control(
			'slider_enable_center_mode',
			array(
				'label'        => esc_html__( 'Enable Center Mode', 'jet-woo-product-gallery' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-woo-product-gallery' ),
				'label_off'    => esc_html__( 'No', 'jet-woo-product-gallery' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_responsive_control(
			'slider_center_mode_slides',
			array(
				'label'     => esc_html__( 'Slides To Show', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 3,
				'min'       => 1,
				'max'       => 8,
				'step'      => 1,
				'condition' => array(
					'slider_enable_center_mode' => 'yes'
				)
			)
		);

		$this->add_control(
			'slider_center_mode_padding',
			array(
				'label'      => esc_html__( 'Center Mode Padding', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array(
					'px'
				),
				'range'      => array(
					'px' => array(
						'min' => 0,
						'max' => 500,
					),
				),
				'default'    => array(
					'size' => 150,
					'unit' => 'px',
				),
				'condition'  => array(
					'slider_enable_center_mode' => 'yes'
				)
			)
		);

		$this->add_responsive_control(
			'slider_center_mode_gutter',
			array(
				'label'              => esc_html__( 'Slides Gutter', 'jet-woo-product-gallery' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => 'horizontal',
				'render_type'        => 'template',
				'placeholder'        => array(
					'top'    => 'auto',
					'right'  => '',
					'bottom' => 'auto',
					'left'   => '',
				),
				'size_units'         => array( 'px', '%' ),
				'selectors'          => array(
					'{{WRAPPER}} ' . $css_scheme['slider-item'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'          => array(
					'slider_enable_center_mode' => 'yes'
				)
			)
		);

		$this->add_control(
			'slider_nav_heading',
			array(
				'label'     => esc_html__( 'Navigation', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'slider_show_nav',
			array(
				'label'        => esc_html__( 'Show Navigation', 'jet-woo-product-gallery' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-woo-product-gallery' ),
				'label_off'    => esc_html__( 'No', 'jet-woo-product-gallery' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'slider_nav_arrow_prev',
			array(
				'label'       => esc_html__( 'Arrow Previous', 'jet-woo-product-gallery' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'fa fa-angle-left',
				'options'     => jet_woo_product_gallery_tools()->get_available_prev_arrows_list(),
				'condition'   => array(
					'slider_show_nav' => 'yes',
				),
			)
		);

		$this->add_control(
			'slider_nav_arrow_next',
			array(
				'label'       => esc_html__( 'Arrow next', 'jet-woo-product-gallery' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'fa fa-angle-right',
				'options'     => jet_woo_product_gallery_tools()->get_available_next_arrows_list(),
				'condition'   => array(
					'slider_show_nav' => 'yes',
				),
			)
		);

		$this->add_control(
			'slider_pagination_heading',
			array(
				'label'     => esc_html__( 'Pagination', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'slider_show_pagination',
			array(
				'label'        => esc_html__( 'Show Pagination', 'jet-woo-product-gallery' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'jet-woo-product-gallery' ),
				'label_off'    => esc_html__( 'No', 'jet-woo-product-gallery' ),
				'return_value' => 'yes',
				'default'      => 'yes',
			)
		);

		$this->add_control(
			'slider_pagination_type',
			array(
				'label'     => esc_html__( 'Type :', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'bullets',
				'options'   => array(
					'bullets'    => array(
						'title' => esc_html__( 'Bullets', 'jet-woo-product-gallery' ),
						'icon'  => 'fa fa-ellipsis-h',
					),
					'thumbnails' => array(
						'title' => esc_html__( 'Thumbnails', 'jet-woo-product-gallery' ),
						'icon'  => 'fa fa-image',
					),
				),
				'condition' => array(
					'slider_show_pagination' => 'yes'
				),
				'toggle'    => true,
			)
		);

		$this->add_control(
			'slider_pagination_direction',
			array(
				'label'     => esc_html__( 'Direction:', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'horizontal',
				'options'   => array(
					'vertical'   => esc_html__( 'Vertical', 'jet-woo-product-gallery' ),
					'horizontal' => esc_html__( 'Horizontal', 'jet-woo-product-gallery' ),
				),
				'condition' => array(
					'slider_show_pagination' => 'yes'
				),
			)
		);

		$this->add_control(
			'slider_pagination_v_position',
			array(
				'label'     => esc_html__( 'Position :', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'left',
				'options'   => array(
					'left'  => array(
						'title' => esc_html__( 'Left', 'jet-woo-product-gallery' ),
						'icon'  => 'fa fa-arrow-left',
					),
					'right' => array(
						'title' => esc_html__( 'Right', 'jet-woo-product-gallery' ),
						'icon'  => 'fa fa-arrow-right',
					),
				),
				'condition' => array(
					'slider_show_pagination'      => 'yes',
					'slider_pagination_direction' => 'vertical'
				),
			)
		);

		$this->add_control(
			'slider_pagination_h_position',
			array(
				'label'     => esc_html__( 'Position :', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'bottom',
				'options'   => array(
					'top'    => array(
						'title' => esc_html__( 'Top', 'jet-woo-product-gallery' ),
						'icon'  => 'fa fa-arrow-up',
					),
					'bottom' => array(
						'title' => esc_html__( 'Bottom', 'jet-woo-product-gallery' ),
						'icon'  => 'fa fa-arrow-down',
					),
				),
				'condition' => array(
					'slider_show_pagination'      => 'yes',
					'slider_pagination_direction' => 'horizontal'
				),
			)
		);

		$this->add_control(
			'slider_pagination_thumbnails_heading',
			array(
				'label'     => esc_html__( 'Thumbnails', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->add_control(
			'pagination_thumbnails_slider_arrow_prev',
			array(
				'label'       => esc_html__( 'Arrow Previous', 'jet-woo-product-gallery' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'fa fa-angle-left',
				'options'     => jet_woo_product_gallery_tools()->get_available_prev_arrows_list(),
				'condition'   => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails',
				),
			)
		);

		$this->add_control(
			'pagination_thumbnails_slider_arrow_next',
			array(
				'label'       => esc_html__( 'Arrow next', 'jet-woo-product-gallery' ),
				'label_block' => true,
				'type'        => Controls_Manager::SELECT,
				'default'     => 'fa fa-angle-right',
				'options'     => jet_woo_product_gallery_tools()->get_available_next_arrows_list(),
				'condition'   => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_thumbnails_columns',
			array(
				'label'     => esc_html__( 'Visible Items Count', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 4,
				'options'   => array(
					2 => 2,
					3 => 3,
					4 => 4,
					5 => 5,
					6 => 6,
				),
				'condition' => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_thumbnails_width',
			array(
				'label'      => esc_html__( 'Width', 'jet-elements' ),
				'type'       => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'size_units' => array(
					'px', '%',
				),
				'range'      => array(
					'px' => array(
						'min' => 10,
						'max' => 1000,
					),
					'%' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'default' => array(
					'size' => 100,
					'unit' => '%',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-wrapper'] => 'max-width: {{SIZE}}{{UNIT}}; align-self:center;',
				),
				'condition' => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_direction' => 'horizontal',
					'slider_pagination_type' => 'thumbnails',
				),
			)
		);

		$this->end_controls_section();

	}

	public function register_controls_images_styles( $css_scheme ) {
		$this->start_controls_section(
			'section_images_style',
			array(
				'label'      => esc_html__( 'Images', 'jet-woo-product-gallery' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'images_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['images'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'images_border',
				'label'       => esc_html__( 'Border', 'jet-woo-product-gallery' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['images'],
			)
		);

		$this->add_responsive_control(
			'images_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['images'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_control(
			'images_arrows_style_heading',
			array(
				'label'     => esc_html__( 'Prev/Next Arrows', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'images_arrows_size',
			array(
				'label'     => esc_html__( 'Size', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 80,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'slider_show_nav' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_images_arrows_style' );

		$this->start_controls_tab(
			'images_arrows_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-woo-product-gallery' ),
			)
		);

		$this->add_control(
			'images_arrows_normal_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'images_arrows_normal_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'images_arrows_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-woo-product-gallery' ),
			)
		);

		$this->add_control(
			'images_arrows_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . ':hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'images_arrows_hover_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . ':hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'images_arrows_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . ':hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'images_arrows_border_border!' => '',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'images_arrows_disabled',
			array(
				'label' => esc_html__( 'Disabled', 'jet-woo-product-gallery' ),
			)
		);

		$this->add_control(
			'images_arrows_disabled_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-disabled' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'images_arrows_disabled_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-disabled' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'images_arrows_disabled_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-disabled' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'images_arrows_border_border!' => '',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'images_arrows_border',
				'label'       => esc_html__( 'Border', 'jet-woo-product-gallery' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['images-arrows'],
				'separator'   => 'before',
				'condition'   => array(
					'slider_show_nav' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'images_arrows_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'slider_show_nav' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'images_arrows_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'slider_show_nav' => 'yes',
				),
			)
		);

		$this->add_control(
			'images_prev_arrow_heading',
			array(
				'label'     => esc_html__( 'Prev Arrow', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'images_prev_arrow_v_position',
			array(
				'label'                => esc_html__( 'Vertical Position by: ', 'jet-woo-product-gallery' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'center',
				'options'              => array(
					'top'    => esc_html__( 'Top', 'jet-woo-product-gallery' ),
					'center' => esc_html__( 'Center', 'jet-woo-product-gallery' ),
					'bottom' => esc_html__( 'Bottom', 'jet-woo-product-gallery' ),
				),
				'selectors_dictionary' => array(
					'top'    => 'top: 0; -webkit-transform: translate(0,0); transform: translate(0,0);',
					'center' => 'top: 50%; -webkit-transform: translate(0,-50%); transform: translate(0,-50%);',
					'bottom' => 'top: auto; bottom: 0; -webkit-transform: translate(0,0); transform: translate(0,0);',
				),
				'selectors'            => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-prev' => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'images_prev_arrow_top_position',
			array(
				'label'      => esc_html__( 'Top Indent', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'images_prev_arrow_v_position' => 'top',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-prev' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'images_prev_arrow_bottom_position',
			array(
				'label'      => esc_html__( 'Bottom Indent', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'images_prev_arrow_v_position' => 'bottom',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-prev' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
				),
			)
		);

		$this->add_control(
			'images_prev_arrow_h_position',
			array(
				'label'                => esc_html__( 'Horizontal Position by: ', 'jet-woo-product-gallery' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'left',
				'options'              => array(
					'left'  => esc_html__( 'Left', 'jet-woo-product-gallery' ),
					'right' => esc_html__( 'Right', 'jet-woo-product-gallery' ),
				),
				'selectors_dictionary' => array(
					'left'  => 'right: auto;',
					'right' => 'left: auto;',
				),
				'selectors'            => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-prev' => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'images_prev_arrow_left_position',
			array(
				'label'      => esc_html__( 'Left Indent', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'images_prev_arrow_h_position' => 'left',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-prev' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'images_prev_arrow_right_position',
			array(
				'label'      => esc_html__( 'Right Indent', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'images_prev_arrow_h_position' => 'right',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-prev' => 'right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'images_next_arrow_heading',
			array(
				'label'     => esc_html__( 'Next Arrow', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'images_next_arrow_v_position',
			array(
				'label'                => esc_html__( 'Vertical Position by: ', 'jet-woo-product-gallery' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'center',
				'options'              => array(
					'top'    => esc_html__( 'Top', 'jet-woo-product-gallery' ),
					'center' => esc_html__( 'Center', 'jet-woo-product-gallery' ),
					'bottom' => esc_html__( 'Bottom', 'jet-woo-product-gallery' ),
				),
				'selectors_dictionary' => array(
					'top'    => 'top: 0; -webkit-transform: translate(0,0); transform: translate(0,0);',
					'center' => 'top: 50%; -webkit-transform: translate(0,-50%); transform: translate(0,-50%);',
					'bottom' => 'top: auto; bottom: 0; -webkit-transform: translate(0,0); transform: translate(0,0);',
				),
				'selectors'            => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-next' => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'images_next_arrow_top_position',
			array(
				'label'      => esc_html__( 'Top Indent', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'images_next_arrow_v_position' => 'top',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-next' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'images_next_arrow_bottom_position',
			array(
				'label'      => esc_html__( 'Bottom Indent', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'images_next_arrow_v_position' => 'bottom',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-next' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
				),
			)
		);

		$this->add_control(
			'images_next_arrow_h_position',
			array(
				'label'                => esc_html__( 'Horizontal Position by: ', 'jet-woo-product-gallery' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'right',
				'options'              => array(
					'left'  => esc_html__( 'Left', 'jet-woo-product-gallery' ),
					'right' => esc_html__( 'Right', 'jet-woo-product-gallery' ),
				),
				'selectors_dictionary' => array(
					'left'  => 'right: auto;',
					'right' => 'left: auto;',
				),
				'selectors'            => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-next' => '{{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'images_next_arrow_left_position',
			array(
				'label'      => esc_html__( 'Left Indent', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'images_next_arrow_h_position' => 'left',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-next' => 'left: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'images_next_arrow_right_position',
			array(
				'label'      => esc_html__( 'Right Indent', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'images_next_arrow_h_position' => 'right',
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['images-arrows'] . '.slick-next' => 'right: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	public function register_controls_pagination_styles( $css_scheme ) {
		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label'      => esc_html__( 'Pagination', 'jet-woo-product-gallery' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition'  => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'bullets'
				),
			)
		);

		$this->add_responsive_control(
			'pagination_items_size',
			array(
				'label'     => esc_html__( 'Size', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 40,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-items'] => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->start_controls_tabs( 'pagination_items_style' );

		$this->start_controls_tab(
			'pagination_items_normal',
			array(
				'label' => esc_html__( 'Normal', 'jet-woo-product-gallery' ),
			)
		);

		$this->add_control(
			'pagination_items_background',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-items'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_items_hover',
			array(
				'label' => esc_html__( 'Hover', 'jet-woo-product-gallery' ),
			)
		);

		$this->add_control(
			'pagination_items_background_hover',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-items'] . ':hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_items_border_color_hover',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-items'] . ':hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'pagination_items_border_border!' => ''
				)
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'pagination_items_active',
			array(
				'label' => esc_html__( 'Active', 'jet-woo-product-gallery' ),
			)
		);

		$this->add_control(
			'pagination_items_background_active',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-active-item'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_items_border_color_active',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-active-item'] => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'pagination_items_border_border!' => ''
				)
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'pagination_items_border',
				'label'       => esc_html__( 'Border', 'jet-woo-product-gallery' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['pagination-items'],
				'separator'   => 'before'
			)
		);

		$this->add_responsive_control(
			'pagination_items_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-items'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_items_margin',
			array(
				'label'      => esc_html__( 'Margin', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['pagination-items'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	public function register_controls_thumbnails_styles( $css_scheme ) {
		$this->start_controls_section(
			'section_thumbnails_style',
			array(
				'label'      => esc_html__( 'Thumbnails', 'jet-woo-product-gallery' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition'  => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->add_responsive_control(
			'thumbnails_vertical_width',
			array(
				'label'       => esc_html__( 'Width', 'jet-woo-product-gallery' ),
				'type'        => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'size_units'  => array(
					'px',
					'%',
				),
				'range'       => array(
					'px' => array(
						'min' => 70,
						'max' => 500,
					),
					'%'  => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'     => array(
					'size' => 150,
					'unit' => 'px',
				),
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-wrapper'] => 'max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['slider']             => 'max-width: calc(100% - {{SIZE}}{{UNIT}});',
				),
				'condition'   => array(
					'slider_show_pagination'      => 'yes',
					'slider_pagination_direction' => 'vertical'
				),
			)
		);

		$this->add_control(
			'thumbnails_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'thumbnails_border',
				'label'       => esc_html__( 'Border', 'jet-woo-product-gallery' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['thumbnails'],
			)
		);

		$this->add_responsive_control(
			'thumbnails_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				),
			)
		);

		$this->add_responsive_control(
			'thumbnails_space_between_h',
			array(
				'label'       => esc_html__( 'Space Between Items', 'jet-woo-product-gallery' ),
				'type'        => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'size_units'  => array(
					'px'
				),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'     => array(
					'size' => 5,
					'unit' => 'px',
				),
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-list']       => 'margin-left: -{{SIZE}}{{UNIT}}; margin-right: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['thumbnails-list-slide'] => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'slider_pagination_direction' => 'horizontal'
				)
			)
		);

		$this->add_responsive_control(
			'thumbnails_gutter_h',
			array(
				'label'              => esc_html__( 'Gutter', 'jet-woo-product-gallery' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', '%' ),
				'allowed_dimensions' => 'vertical',
				'render_type'        => 'template',
				'placeholder'        => array(
					'top'    => '',
					'right'  => 'auto',
					'bottom' => '',
					'left'   => 'auto',
				),
				'selectors'          => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-list-slide'] => 'padding-top: {{TOP}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				),
				'condition'          => array(
					'slider_pagination_direction' => 'horizontal'
				)
			)
		);

		$this->add_responsive_control(
			'thumbnails_space_between_v',
			array(
				'label'       => esc_html__( 'Space Between Items', 'jet-woo-product-gallery' ),
				'type'        => Controls_Manager::SLIDER,
				'render_type' => 'template',
				'size_units'  => array(
					'px'
				),
				'range'       => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'default'     => array(
					'size' => 5,
					'unit' => 'px',
				),
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-list']       => 'margin-top: -{{SIZE}}{{UNIT}}; margin-bottom: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['thumbnails-list-slide'] => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
				),
				'condition'   => array(
					'slider_pagination_direction' => 'vertical'
				)
			)
		);

		$this->add_responsive_control(
			'thumbnails_gutter_v',
			array(
				'label'              => esc_html__( 'Gutter', 'jet-woo-product-gallery' ),
				'type'               => Controls_Manager::DIMENSIONS,
				'size_units'         => array( 'px', '%' ),
				'allowed_dimensions' => 'horizontal',
				'render_type'        => 'template',
				'placeholder'        => array(
					'top'    => 'auto',
					'right'  => '',
					'bottom' => 'auto',
					'left'   => '',
				),
				'selectors'          => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-wrapper'] => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};',
				),
				'condition'          => array(
					'slider_pagination_direction' => 'vertical'
				)
			)
		);

		$this->add_control(
			'thumbnails_arrows_style_heading',
			array(
				'label'     => esc_html__( 'Prev/Next Arrows', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->add_responsive_control(
			'thumbnails_arrows_size',
			array(
				'label'     => esc_html__( 'Size', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => array(
					'px' => array(
						'min' => 6,
						'max' => 80,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-arrows'] => 'font-size: {{SIZE}}{{UNIT}};',
				),
				'condition' => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->start_controls_tabs( 'tabs_thumbnails_arrows_style' );

		$this->start_controls_tab(
			'thumbnails_arrows_normal',
			array(
				'label'     => esc_html__( 'Normal', 'jet-woo-product-gallery' ),
				'condition' => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->add_control(
			'thumbnails_arrows_normal_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-arrows'] => 'color: {{VALUE}}',
				),
				'condition' => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->add_control(
			'thumbnails_arrows_normal_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-arrows'] => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'thumbnails_arrows_hover',
			array(
				'label'     => esc_html__( 'Hover', 'jet-woo-product-gallery' ),
				'condition' => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->add_control(
			'thumbnails_arrows_hover_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-arrows'] . ':hover' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->add_control(
			'thumbnails_arrows_hover_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-arrows'] . ':hover' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->add_control(
			'thumbnails_arrows_hover_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-arrows'] . ':hover' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'thumbnails_arrows_border_border!' => '',
					'slider_show_pagination'           => 'yes',
					'slider_pagination_type'           => 'thumbnails'
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'thumbnails_arrows_disabled',
			array(
				'label'     => esc_html__( 'Disabled', 'jet-woo-product-gallery' ),
				'condition' => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->add_control(
			'thumbnails_arrows_disabled_color',
			array(
				'label'     => esc_html__( 'Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-arrows'] . '.slick-disabled' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->add_control(
			'thumbnails_arrows_disabled_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-arrows'] . '.slick-disabled' => 'background-color: {{VALUE}}',
				),
				'condition' => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->add_control(
			'thumbnails_arrows_disabled_border_color',
			array(
				'label'     => esc_html__( 'Border Color', 'jet-woo-product-gallery' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-arrows'] . '.slick-disabled' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'thumbnails_arrows_border_border!' => '',
					'slider_show_pagination'           => 'yes',
					'slider_pagination_type'           => 'thumbnails'
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'thumbnails_arrows_border',
				'label'       => esc_html__( 'Border', 'jet-woo-product-gallery' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['thumbnails-arrows'],
				'separator'   => 'before',
				'condition'   => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->add_responsive_control(
			'thumbnails_arrows_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-arrows'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->add_responsive_control(
			'thumbnails_arrows_padding',
			array(
				'label'      => esc_html__( 'Padding', 'jet-woo-product-gallery' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['thumbnails-arrows'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'slider_show_pagination' => 'yes',
					'slider_pagination_type' => 'thumbnails'
				),
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		global $post, $product, $_product;

		$settings = $this->get_settings();

		if ( ! empty( $settings['product_id'] ) ) {
			$_product = wc_get_product( $settings['product_id'] );
		} else {
			$_product = wc_get_product();
		}

		if ( ! empty( $_product ) ) {
			$this->__context = 'render';

			$this->__open_wrap();
			include $this->__get_global_template( 'index' );
			$this->__close_wrap();
		} else {
			printf(
				'<div class="jet-woo-product-gallery__content">%s</div>',
				esc_html__( 'Not found product with current id', 'jet-woo-product-gallery' )
			);
		}
	}

	public function get_slider_data_settings() {
		$settings = $this->get_settings();

		$slider_settings = array(
			'slider_enable_center_mode'        => $settings['slider_enable_center_mode'],
			'slider_center_mode_padding'       => $settings['slider_center_mode_padding'],
			'slider_center_mode_slides'        => $settings['slider_center_mode_slides'],
			'slider_center_mode_slides_tablet' => $settings['slider_center_mode_slides_tablet'],
			'slider_center_mode_slides_mobile' => $settings['slider_center_mode_slides_mobile'],
			'show_nav'                         => $settings['slider_show_nav'],
			'nav_prev_icon'                    => $settings['slider_nav_arrow_prev'],
			'nav_next_icon'                    => $settings['slider_nav_arrow_next'],
			'show_pagination'                  => $settings['slider_show_pagination'],
			'pagination_type'                  => $settings['slider_pagination_type'],
			'pagination_direction'             => $settings['slider_pagination_direction'],
			'pagination_v_position'            => $settings['slider_pagination_v_position'],
			'pagination_h_position'            => $settings['slider_pagination_h_position'],
			'thumbnails_columns'               => $settings['pagination_thumbnails_columns'],
			'thumbnails_columns_tablet'        => $settings['pagination_thumbnails_columns_tablet'],
			'thumbnails_columns_mobile'        => $settings['pagination_thumbnails_columns_mobile'],
			'thumbnail_slider_prev_icon'       => $settings['pagination_thumbnails_slider_arrow_prev'],
			'thumbnail_slider_next_icon'       => $settings['pagination_thumbnails_slider_arrow_next'],
		);

		$slider_settings = apply_filters( 'jet-woo-product-gallery/slider/pre-options', $slider_settings, $settings );

		$options = array(
			'enable-center-mode'           => filter_var( $slider_settings['slider_enable_center_mode'], FILTER_VALIDATE_BOOLEAN ),
			'center-mode-padding'          => $slider_settings['slider_center_mode_padding'],
			'center-mode-slides'           => intval( $slider_settings['slider_center_mode_slides'] ),
			'center-mode-slides-tablet'    => intval( $slider_settings['slider_center_mode_slides_tablet'] ),
			'center-mode-slides-mobile'    => intval( $slider_settings['slider_center_mode_slides_mobile'] ),
			'show-navigation'              => filter_var( $slider_settings['show_nav'], FILTER_VALIDATE_BOOLEAN ),
			'show-pagination'              => filter_var( $slider_settings['show_pagination'], FILTER_VALIDATE_BOOLEAN ),
			'pagination-type'              => $slider_settings['pagination_type'],
			'pagination-direction'         => $slider_settings['pagination_direction'],
			'thumbnails-columns'           => intval( $slider_settings['thumbnails_columns'] ),
			'thumbnails-columns-tablet'    => intval( $slider_settings['thumbnails_columns_tablet'] ),
			'thumbnails-columns-mobile'    => intval( $slider_settings['thumbnails_columns_mobile'] ),
			'pagination-v-position'        => $slider_settings['pagination_v_position'],
			'pagination-h-position'        => $slider_settings['pagination_v_position'],
			'slider-prev-arrow'            => jet_woo_product_gallery_functions()->get_slider_arrow(
				array( $slider_settings['nav_prev_icon'], 'slick-prev' )
			),
			'slider-next-arrow'            => jet_woo_product_gallery_functions()->get_slider_arrow(
				array( $slider_settings['nav_next_icon'], 'slick-next' )
			),
			'thumbnails-slider-prev-arrow' => jet_woo_product_gallery_functions()->get_slider_arrow(
				array( $slider_settings['thumbnail_slider_prev_icon'], 'slick-prev' )
			),
			'thumbnails-slider-next-arrow' => jet_woo_product_gallery_functions()->get_slider_arrow(
				array( $slider_settings['thumbnail_slider_next_icon'], 'slick-next' )
			),
		);

		$options = apply_filters( 'jet-woo-product-gallery/slider/options', $options, $settings );
		$options = json_encode( $options );

		return sprintf( 'data-slick-settings=\'%1$s\'', $options );
	}

}