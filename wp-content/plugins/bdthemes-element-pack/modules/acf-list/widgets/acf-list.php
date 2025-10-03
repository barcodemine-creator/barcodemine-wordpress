<?php

namespace ElementPack\Modules\AcfList\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Icons_Manager;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use ElementPack\Utils;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;
use ElementPack\Includes\ACF_Global;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

class Acf_List extends Module_Base {

	public function get_name() {
		return 'bdt-acf-list';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'ACF List', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-acf-list';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_style_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'ep-styles' ];
		} else {
			return [ 'ep-fancy-list' ];
		}
	}

	public function get_keywords() {
		return [ 'acf list', 'acf', 'list' ];
	}

	public function get_custom_help_url() {
		return '';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_acf_field',
			[ 
				'label' => esc_html__( 'ACF Field', 'bdthemes-element-pack' ),
			]
		);

        $this->add_control(
            'repeater_field',
            [
                'label' => __('Repeater Field', 'bdthemes-element-pack'),
                'dynamic' => ['active' => false],
                'type'    => Dynamic_Select::TYPE,
                'label_block' => true,
                'placeholder' => __('Type and select the repeater field...', 'bdthemes-element-pack'),
                'description' => sprintf(__('Supported field type: <b>%1s</b>', 'bdthemes-element-pack'), 'Repeater'),
                'query_args'  => [
                    'query'        => 'acf',
                    'field_type' => ['repeater'],
                ],
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => __('Title', 'bdthemes-element-pack'),
                'dynamic' => ['active' => false],
                'type'        => Dynamic_Select::TYPE,
                'label_block' => true,
                'placeholder' => __('Type repeater sub field for list title', 'bdthemes-element-pack'),
                'description' => sprintf(__('Supported field type: <b>%1s</b>, <b>%2s</b>, <b>%3s</b>', 'bdthemes-element-pack'), 'Text','Textarea','WYSIWYG'),
                'query_args'  => [
                    'query'        => 'acf',
                    'field_type'   => ['text', 'textarea', 'wysiwyg'],
                ],
            ]
        );

        $this->add_control(
            'text',
            [
                'label' => __('Text', 'bdthemes-element-pack'),
                'dynamic' => ['active' => false],
                'type'        => Dynamic_Select::TYPE,
                'label_block' => true,
                'placeholder' => __('Type repeater sub field for list text', 'bdthemes-element-pack'),
                'description' => sprintf(__('Supported field type: <b>%1s</b>, <b>%2s</b>, <b>%3s</b>', 'bdthemes-element-pack'), 'Text','Textarea','WYSIWYG'),
                'query_args'  => [
                    'query'        => 'acf',
                    'field_type'   => ['text', 'textarea', 'wysiwyg'],
                ],
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => __('Image', 'bdthemes-element-pack'),
                'dynamic' => ['active' => false],
                'type'        => Dynamic_Select::TYPE,
                'label_block' => true,
                'placeholder' => __('Type repeater sub field for list image', 'bdthemes-element-pack'),
                'description' => sprintf(__('Supported field type: <b>%1s</b>', 'bdthemes-element-pack'), 'Image'),
                'query_args'  => [
                    'query'        => 'acf',
                    'field_type'   => ['image'],
                ],
            ]
        );

        $this->add_control(
            'link',
            [
                'label' => __('Link', 'bdthemes-element-pack'),
                'dynamic' => ['active' => false],
                'type'        => Dynamic_Select::TYPE,
                'label_block' => true,
                'placeholder' => __('Type repeater sub field for list link', 'bdthemes-element-pack'),
                'description' => sprintf(__('Supported field type: <b>%1s</b>', 'bdthemes-element-pack'), 'URL'),
                'query_args'  => [
					'query'        => 'acf',
                    'field_type'   => ['url'],
                ],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_layout',
			[ 
				'label' => esc_html__( 'Layout', 'bdthemes-dark-mode' ),
			]
		);

		$this->add_control(
			'layout_style',
			[ 
				'label'   => esc_html__( 'Layout Style', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [ 
					'style-1' => '01',
					'style-2' => '02',
					'style-3' => '03',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_additional',
			[ 
				'label' => esc_html__( 'Additional', 'bdthemes-element-pack' ),
			]
		);
		
		$this->add_control(
			'list_icon',
			[ 
				'label'       => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ICONS,
				'label_block' => false,
				'skin'        => 'inline',
				
			]
		);

		$this->add_responsive_control(
			'icon_position',
			[ 
				'label'                => esc_html__( 'Icon Position', 'bdthemes-element-pack' ),
				'type'                 => Controls_Manager::CHOOSE,
				'toggle'               => true,
				'options'              => [ 
					'left'   => [ 
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right'  => [ 
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-right',
					],
					'top'    => [ 
						'title' => esc_html__( 'Top', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-v-align-top',
					],
					'bottom' => [ 
						'title' => esc_html__( 'Bottom', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors'            => [ 
					'{{WRAPPER}} .bdt-fancy-list .flex-wrap' => '{{VALUE}};',
				],
				'selectors_dictionary' => [ 
					'left'   => 'flex-direction: row-reverse; -webkit-flex-direction: row-reverse;',
					'top'    => 'flex-direction: column-reverse; -webkit-flex-direction: column-reverse;',
					'bottom' => 'flex-direction: column; -webkit-flex-direction: column;',
				],
				'separator'            => 'after',
				'condition'      => [ 
					'list_icon[value]!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'columns',
			[ 
				'label'          => esc_html__( 'Columns', 'bdthemes-element-pack' ),
				'type'           => Controls_Manager::SELECT,
				'default'        => '1',
				'tablet_default' => '1',
				'mobile_default' => '1',
				'options'        => [ 
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'selectors'      => [ 
					'{{WRAPPER}} .bdt-fancy-list ul.bdt-fancy-list-group' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
				],
				'separator'      => 'before',
				'condition'      => [ 
					'layout_style' => 'style-1',
				],

			]
		);

		$this->add_responsive_control(
			'list_item_space_between',
			[ 
				'label'     => esc_html__( 'Grid Gap', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list ul.bdt-fancy-list-group' => 'grid-gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'show_item_count',
			[ 
				'label' => esc_html__( 'Show Item Count', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'title_tags',
			[ 
				'label'   => esc_html__( 'Title HTML Tag', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => element_pack_title_tags(),
			]
		);

		$this->add_control(
			'content_position',
			[ 
				'label'        => esc_html__( 'Content Position', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::CHOOSE,
				'default'      => 'left',
				'toggle'       => false,
				'options'      => [ 
					'left'  => [ 
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [ 
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'bdt-content-position--',
			]
		);

		$this->add_responsive_control(
			'list_item_align',
			[ 
				'label'        => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::CHOOSE,
				'options'      => [ 
					'left'   => [ 
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [ 
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [ 
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'prefix_class' => 'elementor%s-align-',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_list_items',
			[ 
				'label' => esc_html__( 'List Item', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs(
			'list_item_tabs'
		);

		$this->start_controls_tab(
			'list_item_tabs_normal',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'list_item_bg_color',
				'selector' => '{{WRAPPER}} .bdt-fancy-list .flex-wrap',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'     => 'list_item_border',
				'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-fancy-list .flex-wrap',
			]
		);

		$this->add_responsive_control(
			'list_item_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-fancy-list .flex-wrap' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_responsive_control(
			'list_item_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-fancy-list .flex-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} ',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'list_item_box_shadow',
				'label'    => esc_html__( 'Box Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-fancy-list .flex-wrap',
			]
		);

		$this->add_responsive_control(
			'list_item_border',
			[ 
				'label'     => esc_html__( 'Height', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 1,
						'max' => 200,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list .flex-wrap' => 'min-height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'list_item_tabs_hover',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'list_item_hover_bg_color',
				'selector' => '{{WRAPPER}} .bdt-fancy-list .flex-wrap:hover',
			]
		);

		$this->add_control(
			'list_item_hover_border',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list .flex-wrap:hover' => 'border-color: {{VALUE}} !important',
				],
				'condition' => [ 
					'list_item_border_border!' => ''
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'list_item_box_shadow_hover',
				'label'    => esc_html__( 'Box Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-fancy-list .flex-wrap:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon',
			[ 
				'label'     => esc_html__( 'Number Count', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_item_count' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_number_icon_style' );

		$this->start_controls_tab(
			'tab_number_icon_normal',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'number_icon_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-number-icon span' => 'color: {{VALUE}} ',
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-number-icon' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'     => 'icon_number_border',
				'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-fancy-list-number-icon',
			]
		);

		$this->add_responsive_control(
			'icon_number_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-fancy-list-number-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} ',
				],
			]
		);

		$this->add_responsive_control(
			'icon_number_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-fancy-list-number-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_number_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-fancy-list-number-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'number_icon_typography',
				'selector' => '{{WRAPPER}} .bdt-fancy-list-number-icon span',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_number_icon_hover',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'number_icon_color_hover',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-group .bdt-fancy-list-wrap:hover .bdt-fancy-list-number-icon span' => 'color: {{VALUE}} ',
				],
			]
		);

		$this->add_control(
			'number_icon_bg_color_hover',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-group .bdt-fancy-list-wrap:hover .bdt-fancy-list-number-icon' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'number_icon_border_color_hover',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-group .bdt-fancy-list-wrap:hover .bdt-fancy-list-number-icon' => 'border-color: {{VALUE}}',
				],
				'condition' => [ 
					'icon_number_border_border!' => ''
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_text_style',
			[ 
				'label' => esc_html__( 'Title / Text', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_mode_style' );
		$this->start_controls_tab(
			'tab_normal_mode_normal',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'title_heading',
			[ 
				'label' => esc_html__( 'Title', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'title_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-title ' => 'color: {{VALUE}} ',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .bdt-fancy-list-title',
			]
		);

		$this->add_control(
			'text_heading',
			[ 
				'label'     => esc_html__( 'Text', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'text_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-text' => 'color: {{VALUE}} ',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'text_typography',
				'selector' => '{{WRAPPER}} .bdt-fancy-list-text',
			]
		);

		$this->add_responsive_control(
			'text_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-fancy-list-text' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_hover_mode_normal',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'title_color_hover',
			[ 
				'label'     => esc_html__( 'Title Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-wrap:hover .bdt-fancy-list-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'text_color_hover',
			[ 
				'label'     => esc_html__( 'Sub Title Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-wrap:hover .bdt-fancy-list-text' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_icon_style',
			[ 
				'label' => esc_html__( 'Icon', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'list_icon[value]!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_mode_style1' );
		$this->start_controls_tab(
			'tab_normal_mode_normal1',
			[ 
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'icon_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#242424',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-icon'     => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-fancy-list-icon svg' => 'fill: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'right_icon_bg_color',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-icon ' => 'background: {{VALUE}} ;'
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'     => 'icon_border',
				'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-fancy-list-icon',
			]
		);

		$this->add_responsive_control(
			'icon_border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-fancy-list-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} ;',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[ 
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-fancy-list-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-fancy-list-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'icon_typography',
				'selector' => '{{WRAPPER}} .bdt-fancy-list-icon',
			]
		);
		//shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'icon_shadow',
				'label'    => esc_html__( 'Box Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-fancy-list-icon',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_hover_mode_normal1',
			[ 
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'icon_color_hover',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-wrap:hover .bdt-fancy-list-icon'     => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-fancy-list-wrap:hover .bdt-fancy-list-icon i'   => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-fancy-list-wrap:hover .bdt-fancy-list-icon svg' => 'fill: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'icon_bg_color_hover',
			[ 
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-wrap:hover .bdt-fancy-list-icon' => 'background-color: {{VALUE}} ;',
				],
			]
		);

		$this->add_control(
			'icon_border_color_hover',
			[ 
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [ 
					'icon_border_border!' => '',
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-wrap:hover .bdt-fancy-list-icon' => 'border-color: {{VALUE}} ;',
				],
			]
		);
		//shadow
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[ 
				'name'     => 'icon_shadow_hover',
				'label'    => esc_html__( 'Box Shadow', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-fancy-list-wrap:hover .bdt-fancy-list-icon',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_image_style',
			[ 
				'label' => esc_html__( 'Image', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[ 
				'name'     => 'image_border',
				'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-fancy-list-img img',
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[ 
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-fancy-list-img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} ;',
				],
			]
		);

		$this->add_responsive_control(
			'image_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-fancy-list-img' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_size',
			[ 
				'label'     => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-fancy-list-img img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute( 'icon_list', 'class', 'bdt-fancy-list-icon' );
		$this->add_render_attribute( 'list_item', 'class', 'elementor-icon-list-item' );

        $repeater_field = get_field_object( $settings['repeater_field'] );

        if (empty($settings['repeater_field'] && $repeater_field)) {
            return;
        }

        $acf_helper = new ACF_Global();
        $field_values = $acf_helper->get_acf_field_value( $settings['repeater_field'], $repeater_field['parent'] );

        if (empty($field_values)) {
            return;
        }

        $title = $settings['title'];
        $text = $settings['text'];
        $image = $settings['image'];
        $link = $settings['link'];



		?>
		<div class="bdt-fancy-list bdt-acf bdt-fancy-list-<?php echo esc_attr( $settings['layout_style'] ); ?>">
			<ul class="bdt-list bdt-fancy-list-group" <?php $this->print_render_attribute_string( 'icon_list' ); ?>>
				<?php
				foreach ( $field_values as $index => $item ) :
					$repeater_setting_key = $this->get_repeater_setting_key( 'text', 'icon_list', $index );
					$this->add_render_attribute( $repeater_setting_key, 'class', 'elementor-icon-list-text' );
					$this->add_inline_editing_attributes( $repeater_setting_key );

					$this->add_render_attribute( 'list_title_tags', 'class', 'bdt-fancy-list-title', true );
                    
                    $list_title = isset($item[$title]) ? $item[$title] : '';
                    $list_text = isset($item[$text]) ? $item[$text] : '';
                    $image_src = !empty($item[$image]) ? wp_get_attachment_image_src($item[$image]['id'], 'full') : '';
                    $image_src =  $image_src ? $image_src[0] : '';
                    $list_link = isset($item[$link]) ? $item[$link] : '';
                    

					?>
					<li>
						<?php
						if ( ! empty( $list_link ) ) {
							$link_key = 'link_' . $index;

							$this->add_render_attribute(
								[
									$link_key => [
										'href' => esc_url($list_link),
										'class' => 'bdt-fancy-list-wrap',
									]
								],
								'',
								'',
								true
							);

							echo '<a ' . wp_kses_post($this->get_render_attribute_string( $link_key )) . '>';
						} else {
							echo '<div class="bdt-fancy-list-wrap">';
						}
						?>
						<div class="bdt-flex flex-wrap">
							<?php
							if ( $settings['show_item_count'] == 'yes' ) {
								echo '<div class="bdt-fancy-list-number-icon"><span>'; ?>
								<?php echo esc_html( $index + 1); ?>
								<?php echo '</span></div>';
							}
							?>
							<?php if ( ! empty( $image_src ) ) : ?>
								<div class="bdt-fancy-list-img">
									<?php
									$thumb_url = $image_src;
									if ( $thumb_url ) {
										print( wp_get_attachment_image(
											$item[$image]['id'],
											'medium',
											false,
											[ 
												'alt' => esc_html( $list_title )
											]
										) );
									}
									?>
								</div>
							<?php endif; ?>
							<div class="bdt-fancy-list-content">
								<<?php echo esc_attr( Utils::get_valid_html_tag( $settings['title_tags'] ) ); ?>
									<?php $this->print_render_attribute_string( 'list_title_tags' ); ?>>
									<?php echo wp_kses_post( $list_title ); ?>
								</<?php echo esc_attr( Utils::get_valid_html_tag( $settings['title_tags'] ) ); ?>>
								<p class="bdt-fancy-list-text">
									<?php echo wp_kses_post( $list_text ); ?>
								</p>
							</div>
							<?php if ( ! empty( $settings['list_icon']['value'] ) ) : ?>
								<div class="bdt-fancy-list-icon">
									<?php Icons_Manager::render_icon( $settings['list_icon'], [ 'aria-hidden' => 'true' ] ); ?>
								</div>
							<?php endif; ?>
						</div>
						<?php
						if ( ! empty( $list_link ) ) :
							?>
							</a>
						<?php else : ?>
				</div>
			<?php endif; ?>
			</li>
		<?php endforeach; ?>
		</ul>
		</div>
		<?php
	}
}
