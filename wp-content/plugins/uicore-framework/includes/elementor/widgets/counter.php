<?php
namespace UiCore\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use UiCore\Helper;


defined('ABSPATH') || exit();

/**
 * Counter
 *
 * @author Lucas Marini Falbo <lucas@uicore.co>
 * @since 5.0.6
 */

class Counter extends Widget_Base
{

	public function get_name()
	{
		return 'uicore-counter';
	}
	public function get_title()
	{
		return __( 'Counter', 'uicore-framework' );
	}
	public function get_icon()
	{
		return 'eicon-counter ui-e-widget';
	}
    public function get_keywords()
    {
        return ['counter', 'count', 'number'];
    }
	public function get_categories()
	{
		return [ 'uicore' ];
	}
	public function get_style_depends()
	{
        Helper::register_widget_style('counter');
		return ['ui-e-counter'];
	}
	public function get_script_depends()
	{
		Helper::register_widget_script('counter');
    	return ['ui-e-counter', 'ui-e-odometer'];
    }
	protected function register_controls()
	{

		/**
		 * Content TAB
		 */

			// Counter SECTION
			$this->start_controls_section(
				'content_section',
				[
					'label' => __( 'Counter', 'uicore-framework' ),
					'tab' => Controls_Manager::TAB_CONTENT,
				]
			);

				$this->add_control(
					'show_icon',
					[
						'label' => esc_html__( 'Show Icon', 'uicore-framework' ),
						'type' => Controls_Manager::SWITCHER,
						'label_on' => esc_html__( 'Show', 'uicore-framework' ),
						'label_off' => esc_html__( 'Hide', 'uicore-framework' ),
						'return_value' => 'true',
						'prefix_class' => 'ui-e-counter-media-',
						'render_type'  => 'template',
					]
				);

				$this->add_control(
					'icon_type',
					[
						'label' => esc_html__( 'Icon Type', 'uicore-framework' ),
						'type' => Controls_Manager::CHOOSE,
						'options' => [
							'icon' => [
								'title' => esc_html__( 'Icon', 'uicore-framework' ),
								'icon' => 'eicon-star',
							],
							'image' => [
								'title' => esc_html__( 'Image', 'uicore-framework' ),
								'icon' => 'eicon-image-bold',
							],

						],
						'default' => 'icon',
						'toggle' => false,
						'condition' => [
							'show_icon' => 'true',
						],
					]
				);

				$this->add_control(
					'selected_icon',
					[
						'label' => esc_html__( 'Choose icon', 'uicore-framework' ),
						'type' => Controls_Manager::ICONS,
						'condition' => [
							'show_icon' => 'true',
							'icon_type'	=> 'icon',
						],
					]
				);

				$this->add_control(
					'image',
					[
						'label' => esc_html__( 'Choose Image', 'uicore-framework' ),
						'type' => Controls_Manager::MEDIA,
						'condition' => [
							'show_icon' => 'true',
							'icon_type'	=> 'image',
						],
						'dynamic' => [
							'active' => true,
						],
					]
				);

				$this->add_control(
					'count_start',
					[
						'label' => esc_html__( 'Starting number', 'uicore-framework' ),
						'type' => Controls_Manager::NUMBER,
						'step' => 1,
						'default' => 10,
						'frontend_available' => true,
						'dynamic' => [
							'active' => true,
						],
						'condition'	=>[
							'counter_animation!' => ['motion'],
						],
					]
				);

				$this->add_control(
					'count_end',
					[
						'label' => esc_html__( 'Ending number', 'uicore-framework' ),
						'type' => Controls_Manager::NUMBER,
						'step' => 1,
						'default' => 120,
						'frontend_available'	=> true,
						'dynamic' => [
							'active' => true,
						],
						'condition'	=>[
							'counter_animation!' => ['motion'],
						],
					]
				);

				$this->add_control(
					'count_number',
					[
						'label' => esc_html__( 'Number', 'uicore-framework' ),
						'type' => Controls_Manager::TEXT,
						'default' => '120,00',
						'frontend_available' => true,
						'label_block' => true,
						'dynamic' => [
							'active' => true,
						],
						'condition'	=>[
							'counter_animation' => ['motion'],
						],
					]
				);

				$this->add_control(
					'counter_html_tag',
					[
						'label' => esc_html__( 'Number HTML class', 'uicore-framework' ),
						'type' => Controls_Manager::SELECT,
						'default'	=> 'h1',
						'options' => [
							'h1' => esc_html__( 'H1', 'uicore-framework' ),
							'h2' => esc_html__( 'H2', 'uicore-framework' ),
							'h3' => esc_html__( 'H3', 'uicore-framework' ),
							'h4' => esc_html__( 'H4', 'uicore-framework' ),
							'h5' => esc_html__( 'H5', 'uicore-framework' ),
							'h6' => esc_html__( 'H6', 'uicore-framework' ),
							'p' => esc_html__( 'p', 'uicore-framework' ),
							'span' => esc_html__( 'span', 'uicore-framework' ),
						],
					]
				);

				$this->add_control(
					'counter_animation',
					[
						'label' => esc_html__( 'Number animation', 'uicore-framework' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'simple',
						'options' => [
							'simple' 	=> esc_html__( 'Simple', 'uicore-framework' ),
							'odometer'  => esc_html__( 'Odometer', 'uicore-framework' ),
							'motion' 	=> esc_html__( 'Motion Blur', 'uicore-framework' ),
						],
						'frontend_available' => true,
						'prefix_class'		=> 'ui-e-counter-',
						'render_type' => 'template',
					]
				);

				$this->add_control(
					'content_text',
					[
						'label' => esc_html__( 'Title', 'uicore-framework' ),
						'type' => Controls_Manager::TEXT,
						'default' => esc_html__( 'Awesome number', 'uicore-framework' ),
						'placeholder' => esc_html__( 'Type your title here', 'uicore-framework' ),
						'separator'	=> 'before',
						'label_block' => true,
						'dynamic' => [
							'active' => true,
						],
					]
				);

				$this->add_control(
					'content_html_tag',
					[
						'label' => esc_html__( 'Title HTML tag', 'uicore-framework' ),
						'type' => Controls_Manager::SELECT,
						'default'	=> 'p',
						'options' => [
							'H1' => esc_html__( 'H1', 'uicore-framework' ),
							'H2' => esc_html__( 'H2', 'uicore-framework' ),
							'H3' => esc_html__( 'H3', 'uicore-framework' ),
							'H4' => esc_html__( 'H4', 'uicore-framework' ),
							'H5' => esc_html__( 'H5', 'uicore-framework' ),
							'H6' => esc_html__( 'H6', 'uicore-framework' ),
							'p' => esc_html__( 'p', 'uicore-framework' ),
							'span' => esc_html__( 'span', 'uicore-framework' ),
						],
					]
				);

				$this->add_control(
					'counter_text_inline',
					[
						'label' => esc_html__( 'Text inline', 'uicore-framework' ),
						'type' => Controls_Manager::SWITCHER,
						'label_on' => esc_html__( 'True', 'uicore-framework' ),
						'label_off' => esc_html__( 'False', 'uicore-framework' ),
						'return_value' => 'true',
						'prefix_class'	=> 'ui-e-inline-',
						'render_type' => 'template',
						'default' => false,
					]
				);

				$this->add_control(
					'icon_position',
					[
						'label' => esc_html__( 'Icon position', 'uicore-framework' ),
						'type' => Controls_Manager::CHOOSE,
						'options' => [
							'left' => [
								'title' => esc_html__( 'Left', 'uicore-framework' ),
								'icon' => 'eicon-h-align-left',
							],
							'top' => [
								'title' => esc_html__( 'Top', 'uicore-framework' ),
								'icon' => 'eicon-v-align-top',
							],
							'right' => [
								'title' => esc_html__( 'Right', 'uicore-framework' ),
								'icon' => 'eicon-h-align-right',
							],
						],
						'default' 	=> 'top',
						'toggle'	=> false,
						'condition'	=>[
							'show_icon' => 'true',
							'counter_text_inline!' => 'true',
						],
						'prefix_class'	=> 'ui-e-',
						'render_type' => 'template',
					]
				);

				$this->add_control(
					'icon_position_inline',
					[
						'label' => esc_html__( 'Icon position', 'uicore-framework' ),
						'type' => Controls_Manager::CHOOSE,
						'options' => [
							'left' => [
								'title' => esc_html__( 'Left', 'uicore-framework' ),
								'icon' => 'eicon-h-align-left',
							],
							'right' => [
								'title' => esc_html__( 'Right', 'uicore-framework' ),
								'icon' => 'eicon-h-align-right',
							],
						],
						'default' 	=> 'left',
						'toggle'	=> false,
						'condition'	=>[
							'show_icon' => 'true',
							'counter_text_inline' => 'true',
						],
						'prefix_class'	=> 'ui-e-',
						'render_type' => 'template',
					]
				);

				$this->add_control(
					'vertical_align',
					[
						'label' => esc_html__( 'Vertical position', 'uicore-framework' ),
						'type' => Controls_Manager::CHOOSE,
						'options' => [
							'start' => [
								'title' => esc_html__( 'Top', 'uicore-framework' ),
								'icon' => 'eicon-v-align-top',
							],
							'center' => [
								'title' => esc_html__( 'Center', 'uicore-framework' ),
								'icon' => 'eicon-v-align-middle',
							],
							'end' => [
								'title' => esc_html__( 'Bottom', 'uicore-framework' ),
								'icon' => 'eicon-v-align-bottom',
							],
						],
						'default' 			=> 'center',
						'toggle'	=> false,
						'selectors' => [
							'{{WRAPPER}} > div' => 'align-items: {{VALUE}};',
						],
						'condition' => [
							'icon_position!'	=> ['top'],
							'counter_text_inline!' => ['true'],
						],
					]
				);

				$this->add_control(
					'vertical_align_inline',
					[
						'label' => esc_html__( 'Vertical position', 'uicore-framework' ),
						'type' => Controls_Manager::CHOOSE,
						'options' => [
							'start' => [
								'title' => esc_html__( 'Top', 'uicore-framework' ),
								'icon' => 'eicon-v-align-top',
							],
							'center' => [
								'title' => esc_html__( 'Center', 'uicore-framework' ),
								'icon' => 'eicon-v-align-middle',
							],
							'end' => [
								'title' => esc_html__( 'Bottom', 'uicore-framework' ),
								'icon' => 'eicon-v-align-bottom',
							],
						],
						'default' 	=> 'center',
						'toggle'	=> false,
						'selectors' => [
							'{{WRAPPER}} > div' => 'align-items: {{VALUE}};',
						],
						'condition' => [
							'counter_text_inline' => ['true'],
						],
					]
				);

				$this->add_control(
					'text_align',
					[
						'label' => esc_html__( 'Text Align', 'uicore-framework' ),
						'type' => Controls_Manager::CHOOSE,
						'options' => [
							'left' => [
								'title' => esc_html__( 'Left', 'uicore-framework' ),
								'icon' => 'eicon-text-align-left',
							],
							'center' => [
								'title' => esc_html__( 'Center', 'uicore-framework' ),
								'icon' => 'eicon-text-align-center',
							],
							'right' => [
								'title' => esc_html__( 'Right', 'uicore-framework' ),
								'icon' => 'eicon-text-align-right',
							],
						],
						'toggle' => false,
						'default' => 'center',
						'prefix_class'	=> 'ui-e-align-',
						'selectors' => [
							'{{WRAPPER}} .ui-e-title' => 'text-align: {{VALUE}};',
							'{{WRAPPER}} .ui-e-ico' => 'text-align: {{VALUE}};',
						],

					]
				);

				$this->add_control(
					'icon_offset_toggle',
					[
						'label' => esc_html__( 'Icon Offset', 'uicore-framework' ),
						'type' => Controls_Manager::POPOVER_TOGGLE,
						'label_off' => esc_html__( 'Default', 'uicore-framework' ),
						'label_on' => esc_html__( 'Custom', 'uicore-framework' ),
						'return_value' => 'yes',
						'condition' => [
							'show_icon'			  	=> 'true',
							'counter_text_inline!' 	=> 'true',
						],
					]
				);

				$this->start_popover();

					$this->add_responsive_control(
						'icon_vertical_offset',
						[
							'label' => esc_html__( 'Vertical offset', 'uicore-framework' ),
							'type' => Controls_Manager::SLIDER,
							'size_units' => ['px'],
							'range' => [
								'px' => [
									'min' => -100,
									'max' => 100,
									'step' => 1,
								],
							],
							'default' => [
								'unit' => 'px',
								'size' => 0,
							],
							'condition' => [
								'counter_text_inline!' 		=> 'true',
								'icon_offset_toggle'	=> 'yes',
							],
							'selectors' => [
								'{{WRAPPER}}' => '--ui-e-ico-vertical-off: {{SIZE}}{{UNIT}};',
							],
						]
					);

					$this->add_responsive_control(
						'icon_horizontal_offset',
						[
							'label' => esc_html__( 'Horizontal offset', 'uicore-framework' ),
							'type' => Controls_Manager::SLIDER,
							'size_units' => ['px'],
							'range' => [
								'px' => [
									'min' => -100,
									'max' => 100,
									'step' => 1,
								],
							],
							'default' => [
								'unit' => 'px',
								'size' => 0,
							],
							'condition' => [
								'counter_text_inline!' 		=> 'true',
								'icon_offset_toggle'	=> 'yes',
							],
							'selectors' => [
								'{{WRAPPER}}' => '--ui-e-ico-horizontal-off: {{SIZE}}{{UNIT}};',
							],
						]
					);

				$this->end_popover();

			$this->end_controls_section();

			$this->start_controls_section(
				'add_op_section',
				[
					'label' => __( 'Additional Options', 'uicore-framework' ),
					'tab' => Controls_Manager::TAB_CONTENT,
				]
			);

				$this->add_control(
					'use_grouping',
					[
						'label' => esc_html__( 'Use Grouping', 'uicore-framework' ),
						'type' => Controls_Manager::SWITCHER,
						'label_on' => esc_html__( 'True', 'uicore-framework' ),
						'label_off' => esc_html__( 'False', 'uicore-framework' ),
						'return_value' => 'true',
						'default' => 'true',
						'frontend_available'	=> true,
						'condition'	=>[
							'counter_animation!' => ['motion'],
						],
					]
				);

				$this->add_control(
					'counter_separator',
					[
						'label' => esc_html__( 'Thousand symbol', 'uicore-framework' ),
						'type' => Controls_Manager::TEXT,
						'default' => esc_html__( ',', 'uicore-framework' ),
						'condition'	=>[
							'use_grouping'	=> 'true',
							'counter_animation!' => ['motion'],
						],
						'frontend_available'	=> true,
					]
				);

				$this->add_control(
					'decimal_places',
					[
						'label' => esc_html__( 'Decimal Places', 'uicore-framework' ),
						'type' => Controls_Manager::NUMBER,
						'min' => 0,
						'max' => 4,
						'step' => 1,
						'default' => 0,
						'frontend_available'	=> true,
						'condition'		=> [
							'counter_animation!' => ['motion'],
						],
					]
				);

				$this->add_control(
					'decimal_symbol',
					[
						'label' => esc_html__( 'Decimal Symbol', 'uicore-framework' ),
						'type' => Controls_Manager::TEXT,
						'default' => esc_html__( '.', 'uicore-framework' ),
						'condition'	=>[
							'counter_animation!' => ['motion'],
						],
						'frontend_available'	=> true,
					]
				);

				$this->add_control(
					'duration',
					[
						'label' => esc_html__( 'Duration (in seconds)', 'uicore-framework' ),
						'type' => Controls_Manager::NUMBER,
						'min' => 0.1,
						'max' => 8.0,
						'step' => 0.1,
						'default' => 2.5,
						'condition' => [
							'counter_animation!' => 'motion',
						],
						'frontend_available'	=> true,
					]
				);

				$this->add_control(
					'counter_prefix',
					[
						'label' => esc_html__( 'Counter Prefix', 'uicore-framework' ),
						'type' => Controls_Manager::TEXT,
						'default' => esc_html__( '', 'uicore-framework' ),
						'placeholder' => esc_html__( '$', 'uicore-framework' ),
						'frontend_available'	=> true,
					]
				);

				$this->add_control(
					'counter_suffix',
					[
						'label' => esc_html__( 'Counter Suffix', 'uicore-framework' ),
						'type' => Controls_Manager::TEXT,
						'default' => esc_html__( '', 'uicore-framework' ),
						'placeholder' => esc_html__( '+', 'uicore-framework' ),
						'frontend_available'	=> true,
					]
				);

			$this->end_controls_section();


		/**
		 * Style TAB
		 */

			$this->start_controls_section(
				'icon_image_section',
				[
					'label' => __( 'Icon/Image', 'uicore-framework' ),
					'tab' => Controls_Manager::TAB_STYLE,
					'condition' => [
						'show_icon'	=> 'true',
					],
				]
			);

				$this->start_controls_tabs(
					'style_tabs'
				);

					$this->start_controls_tab(
						'icon_colors',
						[
							'label' => esc_html__( 'Normal', 'uicore-framework' ),
						]
					);

						$this->add_control(
							'icon_color',
							[
								'label' => esc_html__( 'Icon color', 'uicore-framework' ),
								'type' => Controls_Manager::COLOR,
								'condition'	=> [
									'icon_type!' => 'image',
								],
								'selectors' => [
									'{{WRAPPER}} .ui-e-offset > svg' => 'fill:{VALUE}};',
									'{{WRAPPER}} .ui-e-offset > i' => 'color: {{VALUE}};',
								],

							]
						);

						$this->add_group_control(
							Group_Control_Background::get_type(),
							[
								'name' => 'icon_background',
								'types' => [ 'classic', 'gradient', 'video' ],
								'selector'	=> '{{WRAPPER}} .ui-e-ico .ui-e-offset',
							]
						);

					$this->end_controls_tab();

					$this->start_controls_tab(
						'icon_colors_hover',
						[
							'label' => esc_html__( 'Hover', 'uicore-framework' ),
						]
					);

						$this->add_control(
							'icon_color_hover',
							[
								'label' => esc_html__( 'Icon color', 'uicore-framework' ),
								'type' => Controls_Manager::COLOR,
								'condition'	=> [
									'icon_type!' => 'image',
								],
								'selectors' => [
									'{{WRAPPER}} > div:hover .ui-e-offset > svg' => 'fill:{VALUE}};',
									'{{WRAPPER}} > div:hover .ui-e-offset > i' => 'color: {{VALUE}};',
								],

							]
						);

						$this->add_group_control(
							Group_Control_Background::get_type(),
							[
								'name' => 'icon_background_hover',
								'types' => [ 'classic', 'gradient', 'video' ],
								'selector' => '{{WRAPPER}} > div:hover .ui-e-offset',

							]
						);

						$this->add_control(
							'border_color_hover',
							[
								'label' => esc_html__( 'Border Color', 'uicore-framework' ),
								'type' => Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} > div:hover .ui-e-offset' => 'border-color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_control(
					'hr',
					[
						'type' => Controls_Manager::DIVIDER,
					]
				);

				$this->add_responsive_control(
					'icon_padding',
					[
						'label' => esc_html__( 'Padding', 'uicore-framework' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 10,
								'step' => 1,
							],
							'%' => [
								'min' => 0,
								'max' => 20,
							],
						],
						'default' => [
							'unit' => 'px',
							'size' => 25,
						],
						'selectors' => [
							'{{WRAPPER}} .ui-e-offset' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],

					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'icon_border',
						'selector' => '{{WRAPPER}} .ui-e-offset',

					]
				);

				$this->add_responsive_control(
					'icon_radius',
					[
						'label' => esc_html__( 'Border Radius', 'uicore-framework' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'default' => [
							'unit' => 'px',
							'size' => 100,
						],
						'selectors' => [
							'{{WRAPPER}} .ui-e-offset' => 'border-top-left-radius: {{TOP}}{{UNIT}}; border-top-right-radius:{{RIGHT}}{{UNIT}}; border-bottom-right-radius:{{BOTTOM}}{{UNIT}}; border-bottom-left-radius:{{LEFT}}{{UNIT}};',
						],

					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name' => 'icon_shadow',
						'selector' => '{{WRAPPER}} .ui-e-offset',

					]
				);

				$this->add_responsive_control(
					'icon_space',
					[
						'label' => esc_html__( 'Icon Space', 'uicore-framework' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 60,
								'step' => 1,
							],
							'%' => [
								'min' => 0,
								'max' => 20,
							],
						],
						'default' => [
							'unit' => 'px',
							'size' => 8,
						],
						'selectors' => [
							'{{WRAPPER}}' => '--ui-e-ico-spacing: {{SIZE}}{{UNIT}}',
						],

					]
				);

				$this->add_control(
					'image_fullwidth',
					[
						'label' => esc_html__( 'Image fullwidth', 'uicore-framework' ),
						'type' => Controls_Manager::SWITCHER,
						'label_on' => esc_html__( 'True', 'uicore-framework' ),
						'label_off' => esc_html__( 'False', 'uicore-framework' ),
						'return_value' => 'true',
						'default' => 'false',
						'condition'	=> [
							'icon_type' => 'image',
						],
						'selectors' => [
							'{{WRAPPER}} .ui-e-offset > *' => 'width: 100%;',

						],
					]
				);

				$this->add_responsive_control(
					'image_size',
					[
						'label' => esc_html__( 'Image Size', 'uicore-framework' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 991,
								'step' => 1,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'default' => [
							'unit' => 'px',
							'size' => 120,
						],
						'condition'	=> [
							'icon_type' => 'image',
							'image_fullwidth!' => 'true',
						],
						'selectors' => [
							'{{WRAPPER}} .ui-e-offset > *' => 'width: {{SIZE}}{{UNIT}};',

						],
					]
				);

				$this->add_responsive_control(
					'icon_size',
					[
						'label' => esc_html__( 'Icon Size', 'uicore-framework' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' => [
							'px' => [
								'min' => 8,
								'max' => 160,
								'step' => 1,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'default' => [
							'unit' => 'px',
							'size' => 40,
						],
						'condition'	=> [
							'icon_type' => 'icon',
						],
						'selectors' => [
							'{{WRAPPER}}' => '--ui-e-icon-size:{{SIZE}}{{UNIT}}',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'counter_number_section',
				[
					'label' => __( 'Counter Number', 'uicore-framework' ),
					'tab' => Controls_Manager::TAB_STYLE,
				]
			);

				$this->add_responsive_control(
					'counter_number_space',
					[
						'label' => esc_html__( 'Number space', 'uicore-framework' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100,
								'step' => 1,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'default' => [
							'unit' => 'px',
							'size' => 0,
						],
						'selectors' => [
							'{{WRAPPER}}' => '--ui-e-num-spacing: {{SIZE}}{{UNIT}};',
						],

					]
				);

				$this->start_controls_tabs(
					'counter_tabs'
				);

					$this->start_controls_tab(
						'counter_color_normal',
						[
							'label' => esc_html__( 'Normal', 'uicore-framework' ),
						]
					);

						$this->add_control(
							'counter_number_color',
							[
								'label' => esc_html__( 'Number Color', 'uicore-framework' ),
								'type' => Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ui-e-num' => 'color: {{VALUE}}',
									'{{WRAPPER}}' => '--ui-e-num-color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();

					$this->start_controls_tab(
						'counter_color_hover',
						[
							'label' => esc_html__( 'Hover', 'uicore-framework' ),
						]
					);

						$this->add_control(
							'counter_number_color_hover',
							[
								'label' => esc_html__( 'Number Color', 'uicore-framework' ),
								'type' => Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} > div:hover .ui-e-num' => 'color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' => 'counter_number_typography',
						'selector' => '{{WRAPPER}} .ui-e-num',
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'content_text_section',
				[
					'label' => __( 'Content Text', 'uicore-framework' ),
					'tab' => Controls_Manager::TAB_STYLE,
				]
			);

				$this->start_controls_tabs(
					'content_text_tabs'
				);

					$this->start_controls_tab(
						'content_text_normal',
						[
							'label' => esc_html__( 'Normal', 'uicore-framework' ),
						]
					);

						$this->add_control(
							'content_text_color',
							[
								'label' => esc_html__( 'Text Color', 'uicore-framework' ),
								'type' => Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ui-e-title' => 'color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();

					$this->start_controls_tab(
						'content_text_hover',
						[
							'label' => esc_html__( 'Hover', 'uicore-framework' ),
						]
					);

						$this->add_control(
							'content_text_color_hover',
							[
								'label' => esc_html__( 'Text Color', 'uicore-framework' ),
								'type' => Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} > div:hover .ui-e-title' => 'color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_group_control(
					Group_Control_Typography::get_type(),
					[
						'name' => 'content_text_typography',
						'selector' => '{{WRAPPER}} .ui-e-title',
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'additional_section',
				[
					'label' => __( 'Addittional', 'uicore-framework' ),
					'tab' => Controls_Manager::TAB_STYLE,
				]
			);

				$this->add_responsive_control(
					'content_padding',
					[
						'label' => esc_html__( 'Content Padding', 'uicore-framework' ),
						'type' => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 80,
								'step' => 5,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'default' => [
							'unit' => 'px',
							'size' => 15,
						],
						'selectors' => [
							'{{WRAPPER}} > div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

	}
	protected function render()
	{

		$settings = $this->get_settings_for_display();

		$icon_type	= $settings['icon_type'];
		$num_tag	= $settings['counter_html_tag'];
		$txt_tag	= $settings['content_html_tag'];
		$media 		= $settings['show_icon'] ? true : false; // Check if media is enabled,


		if ($media) { // If there's media,
			switch($icon_type){ // get the media source
				case 'icon'  : $icon = $settings['selected_icon']; break;
				case 'image' : $icon = $settings['image']; break;
			}
			$icon_width = ($icon_type == 'image' && $settings['image_fullwidth'] == 'true') ? 'large' : 'thumbnail'; // and sets the proper media size
		}

		$stack 	= $settings['counter_text_inline'] ? false : true; // If text inline, no stack wrapper between elements is needed
		$title 	= !empty($settings['content_text'])  ? "<$txt_tag class='ui-e-title'> {$settings['content_text']} </$txt_tag>" : "<!-- no title -->"; // Check if there is content for title before rendering it
		$number = "<$num_tag class='ui-e-num'>"; // Creates $number and adds the tag opening

		if ($settings['counter_animation'] == 'motion'){ // If animation type equals motion, we need to manually insert the numbers, prefixes and suffixes
			$value 		= $settings['count_number'];
			$prefix		= $settings['counter_prefix'];
			$suffix		= $settings['counter_suffix'];
			$numbers 	= str_split($value);
			if (!empty($prefix)) {
				array_unshift($numbers, $prefix);
			}
			if(!empty($suffix)){
				$numbers[]	= $suffix;
			}
			foreach ($numbers as $n => $value) {
				$number .= "<span style='--index:$n;'>$value</span>";
			}
		}

		$number .= "</$num_tag>"; // Closes the number tag

		// Build the widget HTML
		if($media) : ?>

			<div class="ui-e-ico">
				<div class="ui-e-offset">
					<? if($icon_type == 'icon') {
						Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
					} else {
						echo wp_get_attachment_image( $icon['id'], $icon_width );
					}  ?>
				</div>
			</div>
		<?php endif;

		if($stack) { ?> <div class="ui-e-wrp"> <?php }
			echo $number . $title;
		if($stack) { ?> </div> <?php }
	}

}
\Elementor\Plugin::instance()->widgets_manager->register(new Counter());