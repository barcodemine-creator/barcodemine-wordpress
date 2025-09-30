<?php

namespace ElementPack\Modules\AcfTabs\Widgets;

use Elementor\Repeater;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use ElementPack\Element_Pack_Loader;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;
use ElementPack\Utils;
use ElementPack\Includes\ACF_Global;

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

class Acf_Tabs extends Module_Base
{

    public function get_name()
    {
        return 'bdt-acf-tabs';
    }

    public function get_title()
    {
        return BDTEP . esc_html__('ACF Tabs', 'bdthemes-element-pack');
    }

    public function get_icon()
    {
        return 'bdt-wi-acf-tabs';
    }

    public function get_categories()
    {
        return ['element-pack'];
    }

    public function get_keywords()
    {
        return ['acf', 'acf-tabs', 'tabs', 'toggle', 'accordion'];
    }

    public function is_reload_preview_required()
    {
        return false;
    }

    public function get_style_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-tabs'];
        }
    }
    public function get_script_depends()
    {
        if ($this->ep_is_edit_mode()) {
            return ['ep-scripts'];
        } else {
            return ['ep-tabs'];
        }
    }

    public function get_custom_help_url()
    {
        return '';
    }

    protected function register_controls()
    {
        $this->start_controls_section(
            'section_title',
            [
                'label' => esc_html__('Tabs', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'tab_layout',
            [
                'label'   => esc_html__('Layout', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'default' => 'default',
                'options' => [
                    'default' => esc_html__('Top (Default)', 'bdthemes-element-pack'),
                    'bottom'  => esc_html__('Bottom', 'bdthemes-element-pack'),
                    'left'    => esc_html__('Left', 'bdthemes-element-pack'),
                    'right'   => esc_html__('Right', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->add_control(
            'field',
            [
                'label' => __('Repeater Field', 'bdthemes-element-pack'),
                'dynamic' => ['active' => false],
                'type'    => Dynamic_Select::TYPE,
                'label_block' => true,
                'placeholder' => __('Type and select the repeater field...', 'bdthemes-element-pack'),
                'description' => sprintf(__('Supported field type: <b>%1s</b>', 'bdthemes-element-pack'), 'Repeater'),
                'query_args'  => [
                    'query'   => 'acf',
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
                'placeholder' => __('Type repeater sub field for accordion title', 'bdthemes-element-pack'),
                'description' => sprintf(__('Supported field type: <b>%1s</b>, <b>%2s</b>, <b>%3s</b>', 'bdthemes-element-pack'), 'Text','Textarea','WYSIWYG'),
                'query_args'  => [
                    'query'        => 'acf',
                    'field_type'   => ['text', 'textarea', 'wysiwyg'],
                ],
            ]
        );
        $this->add_control(
            'sub_title',
            [
                'label' => __('Sub Title', 'bdthemes-element-pack'),
                'dynamic' => ['active' => false],
                'type'        => Dynamic_Select::TYPE,
                'label_block' => true,
                'placeholder' => __('Type repeater sub field for accordion sub title', 'bdthemes-element-pack'),
                'description' => sprintf(__('Supported field type: <b>%1s</b>, <b>%2s</b>, <b>%3s</b>', 'bdthemes-element-pack'), 'Text','Textarea','WYSIWYG'),
                'query_args'  => [
                    'query'        => 'acf',
                    'field_type'   => ['text', 'textarea', 'wysiwyg'],
                ],
            ]
        );


        $this->add_control(
            'content',
            [
                'label' => __('Content', 'bdthemes-element-pack'),
                'dynamic' => ['active' => false],
                'type'        => Dynamic_Select::TYPE,
                'label_block' => true,
                'placeholder' => __('Type repeater sub field for accordion content', 'bdthemes-element-pack'),
                'description' => sprintf(__('Supported field type: <b>%1s</b>, <b>%2s</b>, <b>%3s</b>', 'bdthemes-element-pack'), 'Text','Textarea','WYSIWYG'),
                'query_args'  => [
                    'query'        => 'acf',
                    'field_type'   => ['text', 'textarea', 'wysiwyg'],
                ],
            ]
        );

        $this->add_control(
            'align',
            [
                'label'     => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::CHOOSE,
                'options'   => [
                    'left'        => [
                        'title' => esc_html__('Left', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-left',
                    ],
                    'center'  => [
                        'title' => esc_html__('Center', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-center',
                    ],
                    'right'   => [
                        'title' => esc_html__('Right', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-right',
                    ],
                    'justify' => [
                        'title' => esc_html__('Justified', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-text-align-justify',
                    ],
                ],
                'condition' => [
                    'tab_layout' => ['default', 'bottom']
                ],
            ]
        );

        $this->add_responsive_control(
            'item_spacing',
            [
                'label'     => esc_html__('Nav Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item'                                                                 => 'padding-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tab'                                                                                => 'margin-left: -{{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tab.bdt-tab-left .bdt-tabs-item, {{WRAPPER}} .bdt-tab.bdt-tab-right .bdt-tabs-item' => 'padding-top: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tab.bdt-tab-left, {{WRAPPER}} .bdt-tab.bdt-tab-right'                               => 'margin-top: -{{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_responsive_control(
            'nav_spacing',
            [
                'label'     => esc_html__('Nav Width', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-grid:not(.bdt-grid-stack) .bdt-tab-wrapper' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'tab_layout' => ['left', 'right']
                ],
            ]
        );

        $this->add_responsive_control(
            'content_spacing',
            [
                'label'     => esc_html__('Content Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'   => [
                    'size' => 20,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-tabs-default .bdt-switcher-wrapper'                              => 'margin-top: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tabs-bottom .bdt-switcher-wrapper'                               => 'margin-bottom: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tabs-left .bdt-grid:not(.bdt-grid-stack) .bdt-switcher-wrapper'  => 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tabs-right .bdt-grid:not(.bdt-grid-stack) .bdt-switcher-wrapper' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tabs-left .bdt-grid-stack .bdt-switcher-wrapper,
                    {{WRAPPER}} .bdt-tabs-right .bdt-grid-stack .bdt-switcher-wrapper'      => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_content_additional',
            [
                'label' => esc_html__('Additional', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'active_item',
            [
                'label' => esc_html__('Active Item No', 'bdthemes-element-pack'),
                'type'  => Controls_Manager::NUMBER,
                'min'   => 1,
                'max'   => 20,
            ]
        );

        $this->add_control(
            'tab_transition',
            [
                'label'   => esc_html__('Transition', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SELECT,
                'options' => element_pack_transition_options(),
                'default' => '',
            ]
        );

        $this->add_control(
            'duration',
            [
                'label'     => esc_html__('Animation Duration', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min'  => 1,
                        'max'  => 501,
                        'step' => 50,
                    ],
                ],
                'default'   => [
                    'size' => 200,
                ],
                'condition' => [
                    'tab_transition!' => ''
                ],
            ]
        );

        $this->add_control(
            'media',
            [
                'label'       => esc_html__('Turn On Horizontal mode', 'bdthemes-element-pack'),
                'description' => esc_html__('It means that tabs nav will switch vertical to horizontal on mobile mode', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::CHOOSE,
                'options'     => [
                    960 => [
                        'title' => esc_html__('On Tablet', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-device-tablet',
                    ],
                    768 => [
                        'title' => esc_html__('On Mobile', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-device-mobile',
                    ],
                ],
                'default'     => 960,
                'condition'   => [
                    'tab_layout' => ['left', 'right']
                ],
            ]
        );

        $this->add_control(
            'nav_sticky_mode',
            [
                'label'     => esc_html__('Tabs Nav Sticky', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SWITCHER,
                'condition' => [
                    'tab_layout!' => 'bottom',
                ],
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'nav_sticky_offset',
            [
                'label'     => esc_html__('Offset', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'default'   => [
                    'size' => 1,
                ],
                'condition' => [
                    'nav_sticky_mode' => 'yes',
                    'tab_layout!'     => 'bottom',
                ],
            ]
        );

        $this->add_control(
            'nav_sticky_on_scroll_up',
            [
                'label'       => esc_html__('Sticky on Scroll Up', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
                'description' => esc_html__('Set sticky options when you scroll up your mouse.', 'bdthemes-element-pack'),
                'condition'   => [
                    'nav_sticky_mode' => 'yes',
                    'tab_layout!'     => 'bottom',
                ],
            ]
        );

        $this->add_control(
            'fullwidth_on_mobile',
            [
                'label'       => esc_html__('Fullwidth Nav on Mobile', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
                'description' => esc_html__('If you have long test tab so this can help design issue.', 'bdthemes-element-pack'),
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'swiping_on_mobile',
            [
                'label'       => esc_html__('Swiping Tab on Mobile', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
                'description' => esc_html__('If you set yes so tab will swiping on mobile device by touch.', 'bdthemes-element-pack'),
                'default'     => 'yes',
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'active_hash',
            [
                'label'   => esc_html__('Hash Location', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'no',
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'hash_top_offset',
            [
                'label'      => esc_html__('Top Offset ', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', ''],
                'range'      => [
                    'px' => [
                        'min'  => 1,
                        'max'  => 1000,
                        'step' => 5,
                    ],

                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 70,
                ],
                'condition'  => [
                    'active_hash'      => 'yes',
                    'nav_sticky_mode!' => 'yes',
                ],
            ]
        );


        $this->add_control(
            'hash_scrollspy_time',
            [
                'label'      => esc_html__('Scrollspy Time', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['ms', ''],
                'range'      => [
                    'px' => [
                        'min'  => 500,
                        'max'  => 5000,
                        'step' => 1000,
                    ],
                ],
                'default'    => [
                    'unit' => 'px',
                    'size' => 1500,
                ],
                'condition'  => [
                    'active_hash'      => 'yes',
                    'nav_sticky_mode!' => 'yes',
                ],
            ]
        );


        $this->add_control(
            'tabs_match_height',
            [
                'label'       => esc_html__('Equal Tab Height', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
                'description' => esc_html__('You can on/off tab item equal height feature.', 'bdthemes-element-pack'),
                'default'     => 'yes',
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'enable_section_bg',
            [
                'label'       => esc_html__('Connect Section Background', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::SWITCHER,
                'description' => esc_html__('You will able to set Section Background as per Tab Items.', 'bdthemes-element-pack'),
                'separator'   => 'before',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_background',
            [
                'label' => esc_html__('Section Background', 'bdthemes-element-pack'),
                'condition' => [
                    'enable_section_bg' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'section_bg_selector',
            [
                'label'       => esc_html__('Section ID / Class', 'bdthemes-element-pack'),
                'type'        => Controls_Manager::TEXT,
                'dynamic'     => [
                    'active'  => true,
                ],
                'description' => esc_html__('Enter your Section ID/Class. Example #sec-1, .sec-1', 'bdthemes-element-pack'),
            ]
        );

        $repeater = new Repeater();

        $repeater->add_control(
            'section_bg',
            [
                'label' => esc_html__('Select Background', 'bdthemes-element-pack'),
                'type' => Controls_Manager::MEDIA,
                'render_type' => 'template',
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $this->add_control(
            'section_bg_list',
            [
                'label'         => esc_html__('Background List', 'bdthemes-element-pack'),
                'type'          => Controls_Manager::REPEATER,
                'fields'        => $repeater->get_controls(),
                'prevent_empty' => false,
                'title_field'   => '<img src="{{{ section_bg.url }}}" style="height: 40px; width: 40px; object-fit: cover;">',
            ]
        );

        $this->add_control(
            'section_bg_anim',
            [
                'label' => esc_html__('Select Animation', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none'         => esc_html__('None', 'bdthemes-element-pack'),
                    'fade'         => esc_html__('Fade', 'bdthemes-element-pack'),
                    'scale-up'     => esc_html__('Scale Up', 'bdthemes-element-pack'),
                    'scale-down'   => esc_html__('Scale Down', 'bdthemes-element-pack'),
                    'slide-top'    => esc_html__('Slide Top', 'bdthemes-element-pack'),
                    'slide-bottom' => esc_html__('Slide Bottom', 'bdthemes-element-pack'),
                    'kenburns'     => esc_html__('Kenburns', 'bdthemes-element-pack'),
                    'shake'        => esc_html__('Shake', 'bdthemes-element-pack'),
                ],
            ]
        );

        $this->end_controls_section();

        //Style
        $this->start_controls_section(
            'section_tab_wrapper_style',
            [
                'label' => esc_html__('Tab Wrapper', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'tab_wrapper_background',
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .bdt-tab-wrapper > div',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'tab_wrapper_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-tab-wrapper > div',
            ]
        );

        $this->add_control(
            'tab_wrapper_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tab-wrapper > div' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_responsive_control(
            'tab_wrapper_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tab-wrapper > div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_toggle_style_title',
            [
                'label' => esc_html__('Tab', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_title_style');

        $this->start_controls_tab(
            'tab_title_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );
        $this->add_control(
            'title_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item-title'     => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'title_background',
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .bdt-tab .bdt-tabs-item-title',
                // 'separator' => 'after',
            ]
        );

       
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'title_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-tab .bdt-tabs-item .bdt-tabs-item-title',
                'separator'   => 'before',
            ]
        );

        $this->add_control(
            'title_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item .bdt-tabs-item-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        //tab item row gap slider
        $this->add_responsive_control(
            'title_row_gap',
            [
                'label'      => esc_html__('Row Gap', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 100,
                        'step' => 1,
                    ],

                ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tabs-area .bdt-tab' => 'row-gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        

        $this->add_responsive_control(
            'title_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'title_typography',
                'selector' => '{{WRAPPER}} .bdt-tab .bdt-tabs-item-title',
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'title_shadow',
                'selector' => '{{WRAPPER}} .bdt-tab .bdt-tabs-item .bdt-tabs-item-title',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_title_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'hover_title_background',
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .bdt-tab .bdt-tabs-item:hover .bdt-tabs-item-title',
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'hover_title_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item:hover .bdt-tabs-item-title'     => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'title_hover_border',
            [
                'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'condition' => [
                    'title_border_border!' => ''
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item:hover .bdt-tabs-item-title' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_title_active',
            [
                'label' => esc_html__('Active', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'active_style_color',
            [
                'label'     => esc_html__('Style Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-title:after' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'active_title_background',
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-title',
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'active_title_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-title'     => 'color: {{VALUE}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'active_title_shadow',
                'selector' => '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-title',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'active_title_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-title',
            ]
        );

        $this->add_control(
            'active_title_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tabs-item-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_sub_title',
            [
                'label' => esc_html__('Sub Title', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_sub_title_style');

        $this->start_controls_tab(
            'tab_sub_title_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );


        $this->add_control(
            'sub_title_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tab-sub-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'sub_title_spacing',
            [
                'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tab-sub-title' => 'margin-top: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'sub_title_typography',
                'selector' => '{{WRAPPER}} .bdt-tab .bdt-tab-sub-title',
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_sub_title_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'hover_sub_title_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item:hover .bdt-tab-sub-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_sub_title_active',
            [
                'label' => esc_html__('Active', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'active_sub_title_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item.bdt-active .bdt-tab-sub-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_toggle_style_content',
            [
                'label' => esc_html__('Content', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'content_background_color',
                'types'     => ['classic', 'gradient'],
                'selector'  => '{{WRAPPER}} .bdt-tabs .bdt-switcher-item-content',
                'separator' => 'after',
            ]
        );

        $this->add_control(
            'content_color',
            [
                'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .bdt-tabs .bdt-switcher-item-content' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'content_border',
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .bdt-tabs .bdt-switcher-item-content',
            ]
        );

        $this->add_control(
            'content_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tabs .bdt-switcher-item-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_responsive_control(
            'content_padding',
            [
                'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tabs .bdt-switcher-item-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'content_typography',
                'selector' => '{{WRAPPER}} .bdt-tabs .bdt-switcher-item-content',
                //'scheme'   => Schemes\Typography::TYPOGRAPHY_3,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style_icon',
            [
                'label' => esc_html__('Icon', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->start_controls_tabs('tabs_icon_style');

        $this->start_controls_tab(
            'tab_icon_normal',
            [
                'label' => esc_html__('Normal', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'icon_align',
            [
                'label'   => esc_html__('Alignment', 'bdthemes-element-pack'),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left'  => [
                        'title' => esc_html__('Start', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-left',
                    ],
                    'right' => [
                        'title' => esc_html__('End', 'bdthemes-element-pack'),
                        'icon'  => 'eicon-h-align-right',
                    ],
                ],
                'default' => is_rtl() ? 'right' : 'left',
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item-title i'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-tab .bdt-tabs-item-title svg'   => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'icon_space',
            [
                'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::SLIDER,
                'range'     => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default'   => [
                    'size' => 8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-tabs .bdt-tabs-item-title .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .bdt-tabs .bdt-tabs-item-title .bdt-button-icon-align-left' => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_icon_hover',
            [
                'label' => esc_html__('Hover', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'icon_hover_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tabs .bdt-tabs-item:hover .bdt-tabs-item-title i'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-tabs .bdt-tabs-item:hover .bdt-tabs-item-title svg'   => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'tab_icon_active',
            [
                'label' => esc_html__('Active', 'bdthemes-element-pack'),
            ]
        );

        $this->add_control(
            'icon_active_color',
            [
                'label'     => esc_html__('Color', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tabs .bdt-tabs-item.bdt-active .bdt-tabs-item-title i'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .bdt-tabs .bdt-tabs-item.bdt-active .bdt-tabs-item-title svg'   => 'fill: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();


        $this->start_controls_section(
            'section_tabs_sticky_style',
            [
                'label' => esc_html__('Sticky', 'bdthemes-element-pack'),
                'tab'   => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'nav_sticky_mode' => 'yes',
                    'tab_layout!'     => 'bottom',
                ],
            ]
        );

        $this->add_control(
            'sticky_background',
            [
                'label'     => esc_html__('Background', 'bdthemes-element-pack'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-tabs > div > .bdt-sticky.bdt-active' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'sticky_shadow',
                'selector' => '{{WRAPPER}} .bdt-tabs > div > .bdt-sticky.bdt-active',
            ]
        );

        $this->add_control(
            'sticky_border_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-tabs > div > .bdt-sticky.bdt-active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function section_background()
    {
        $settings    = $this->get_settings_for_display();
        if ($settings['enable_section_bg'] !== 'yes') {
            return false;
        }

        if (!$settings['section_bg_list']) {
            return false;
        }
        $bg_list = [];
        foreach ($settings['section_bg_list'] as $index => $item) {
            $bg_list[$index] = $item['section_bg']['url'];
        }

        return $bg_list;
    }

    protected function render()
    {
        $settings    = $this->get_settings_for_display();
        $id          = $this->get_id() .'-'. rand(1000, 9999);
        $stickyClass = '';
        if (isset($settings['nav_sticky_mode']) && $settings['nav_sticky_mode'] == 'yes') {
            $stickyClass = 'bdt-sticky-custom';
        }

        $this->add_render_attribute(
            [
                'tabs_sticky_data' => [
                    'data-settings' => [
                        wp_json_encode(
                            array_filter([
                                "id"                  => 'bdt-tabs-' . $id,
                                "status"              => $stickyClass,
                                "activeHash"          => $settings['active_hash'],
                                "hashTopOffset"       => (isset($settings['hash_top_offset']['size']) && !empty($settings['hash_top_offset']['size'])) ? $settings['hash_top_offset']['size'] : 70,
                                "hashScrollspyTime"   => (isset($settings['hash_scrollspy_time']['size']) ? $settings['hash_scrollspy_time']['size'] : 1500),
                                "navStickyOffset"     => (isset($settings['nav_sticky_offset']['size']) ? $settings['nav_sticky_offset']['size'] : 1),
                                "activeItem"          => (!empty($settings['active_item'])) ? $settings['active_item'] : NULL,
                                "linkWidgetId"        => $id,
                                "sectionBgSelector"   => $settings['enable_section_bg'] == 'yes' ? $settings['section_bg_selector'] : false,
                                "sectionBg"           => $settings['enable_section_bg'] == 'yes' ? $this->section_background() : false,
                                "sectionBgAnim"       => $settings['enable_section_bg'] == 'yes' ? $settings['section_bg_anim'] : false,
                            ])
                        ),
                    ],
                ],
            ]
        );

        $this->add_render_attribute('tabs', 'id', 'bdt-tabs-' . esc_attr($id));
        $this->add_render_attribute('tabs', 'class', 'bdt-tabs ');
        $this->add_render_attribute('tabs', 'class', 'bdt-tabs-' . $settings['tab_layout']);

        if ($settings['fullwidth_on_mobile']) {
            $this->add_render_attribute('tabs', 'class', 'fullwidth-on-mobile');
        }

        ?>

        <div class="bdt-tabs-area">

            <div <?php $this->print_render_attribute_string('tabs'); ?> <?php $this->print_render_attribute_string('tabs_sticky_data'); ?>>
                <?php
                if ('left' == $settings['tab_layout'] or 'right' == $settings['tab_layout']) {
                    echo '<div class="bdt-grid-collapse"  bdt-grid>';
                }
                ?>

                <?php if ('bottom' == $settings['tab_layout']) : ?>
                    <?php $this->tabs_content($id); ?>
                <?php endif; ?>

                <?php $this->desktop_tab_items($id); ?>


                <?php if ('bottom' != $settings['tab_layout']) : ?>
                    <?php $this->tabs_content($id); ?>
                <?php endif; ?>

                <?php
                if ('left' == $settings['tab_layout'] or 'right' == $settings['tab_layout']) {
                    echo "</div>";
                }
                ?>
                <a href="#" id="bottom-anchor-<?php echo esc_attr($id); ?>" data-bdt-hidden></a>
            </div>
        </div>
    <?php
    }

    public function tabs_content($id)
    {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('switcher-width', 'class', 'bdt-switcher-wrapper');

        if ('left' == $settings['tab_layout'] or 'right' == $settings['tab_layout']) {

            if (768 == $settings['media']) {
                $this->add_render_attribute('switcher-width', 'class', 'bdt-width-expand@s');
            } else {
                $this->add_render_attribute('switcher-width', 'class', 'bdt-width-expand@m');
            }
        }

    ?>

        <div <?php $this->print_render_attribute_string('switcher-width'); ?>>
            <div id="bdt-tab-content-<?php echo esc_attr($id); ?>" class="bdt-switcher bdt-switcher-item-content">
                <?php

                $repeater_field = get_field_object( $settings['field'] );

                if (empty($settings['field'] && $repeater_field)) {
                    return;
                }

                $acf_helper = new ACF_Global();
                $field_values = $acf_helper->get_acf_field_value( $settings['field'], $repeater_field['parent'] );

                if (empty($field_values)) {
                    return;
                }

                $content = $settings['content'];
                $title = $settings['title'];
                
                foreach ($field_values as $index => $item) : ?>
                    <?php

                    $tab_count = $index + 1;
                    $tab_count_active = '';
                    if ($tab_count === $settings['active_item']) {
                        $tab_count_active = 'bdt-active';
                    }

                    $field_content = isset($item[$content]) ? $item[$content] : '';
                    $field_title = isset($item[$title]) ? $item[$title] : '';

                    ?>
                    <div class="bdt-tab-content-item <?php echo esc_attr($tab_count_active); ?>" data-content-id="<?php echo esc_attr(strtolower(preg_replace('#[ -]+#', '-', trim(preg_replace("![^a-z0-9]+!i", " ", esc_html($field_title)))))); ?>">
                        <div><?php echo wp_kses_post($field_content); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php
    }

    public function desktop_tab_items($id)
    {
        $settings = $this->get_settings_for_display();

        $this->add_render_attribute('tabs-width', 'class', 'bdt-tab-wrapper');

        if ('left' == $settings['tab_layout'] or 'right' == $settings['tab_layout']) {

            if ('right' == $settings['tab_layout']) {
                $this->add_render_attribute('tabs-width', 'class', 'bdt-flex-last@m');
            }

            if (768 == $settings['media']) {
                $this->add_render_attribute('tabs-width', 'class', 'bdt-width-auto@s');
                if ('right' == $settings['tab_layout']) {
                    $this->add_render_attribute('tabs-width', 'class', 'bdt-flex-last');
                }
            } else {
                $this->add_render_attribute('tabs-width', 'class', 'bdt-width-auto@m');
            }
        }

        $this->add_render_attribute(
            [
                'tabs-width' => [
                    'class' => [
                        ('left' == $settings['align'] or 'right' == $settings['align'] or 'center' == $settings['align']) ? 'bdt-flex bdt-flex-' . $settings['align'] : '',
                    ]
                ]
            ]
        );

        $this->add_render_attribute(
            [
                'tab-settings' => [
                    'class' => [
                        'bdt-tab',
                        ('' !== $settings['tab_layout']) ? 'bdt-tab-' . $settings['tab_layout'] : '',
                        ('justify' == $settings['align']) ? 'bdt-child-width-expand' : '',
                        ('left' == $settings['align'] or 'right' == $settings['align'] or 'center' == $settings['align']) ? 'bdt-flex bdt-flex-' . $settings['align'] : '',
                    ]
                ]
            ]
        );

        $this->add_render_attribute('tab-settings', 'data-bdt-tab', 'connect: #bdt-tab-content-' . esc_attr($id) . ';');

        if (isset($settings['tab_transition']) and $settings['tab_transition']) {
            $this->add_render_attribute('tab-settings', 'data-bdt-tab', 'animation: bdt-animation-' . $settings['tab_transition'] . ';');
        }
        if (isset($settings['duration']['size']) and $settings['duration']['size']) {
            $this->add_render_attribute('tab-settings', 'data-bdt-tab', 'duration: ' . $settings['duration']['size'] . ';');
        }
        if (isset($settings['media']) and $settings['media']) {
            $this->add_render_attribute('tab-settings', 'data-bdt-tab', 'media: ' . intval($settings['media']) . ';');
        }
        if ('yes' != $settings['swiping_on_mobile']) {
            $this->add_render_attribute('tab-settings', 'data-bdt-tab', 'swiping: false;');
        }

        if ($settings['tabs_match_height']) {
            $this->add_render_attribute('tab-settings', 'data-bdt-height-match', 'target: > .bdt-tabs-item > .bdt-tabs-item-title; row: false;');
        }

        if (isset($settings['nav_sticky_mode']) && 'yes' == $settings['nav_sticky_mode']) {
            $this->add_render_attribute('tabs-sticky', 'data-bdt-sticky', 'bottom: #bottom-anchor-' . $id . ';');

            if ($settings['nav_sticky_offset']['size']) {
                $this->add_render_attribute('tabs-sticky', 'data-bdt-sticky', 'offset: ' . $settings['nav_sticky_offset']['size'] . ';');
            }
            if ($settings['nav_sticky_on_scroll_up']) {
                $this->add_render_attribute('tabs-sticky', 'data-bdt-sticky', 'show-on-up: true; animation: bdt-animation-slide-top');
            }
        }

        $repeater_field = get_field_object( $settings['field'] );
        if (empty($settings['field'] && $repeater_field)) {
            return;
        }
        $acf_helper = new ACF_Global();
        $field_values = $acf_helper->get_acf_field_value( $settings['field'], $repeater_field['parent'] );
        if (empty($field_values)) {
            return;
        }
        $title = $settings['title']; 
        $sub_title = $settings['sub_title']; 
        
        ?>
        <div <?php $this->print_render_attribute_string('tabs-width'); ?>>
            <div <?php $this->print_render_attribute_string('tabs-sticky'); ?>>
                <div <?php $this->print_render_attribute_string('tab-settings'); ?>>
                    <?php foreach ($field_values as $index => $item) :

                        $field_title = isset($item[$title]) ? $item[$title] : '';
                        $field_sub_title = isset($item[$sub_title]) ? $item[$sub_title] : '';

                        $tab_count = $index + 1;
                        $tab_id = ($field_title) ? $field_title : $id . $tab_count;

                        $hash_text = sanitize_text_field(trim(str_replace(" ", "-", $tab_id)));

                        $tab_id =   'bdt-tab-' . $hash_text;
                        

                        $this->add_render_attribute('tabs-item', 'class', 'bdt-tabs-item', true);
                        if (empty($field_title)) {
                            $this->add_render_attribute('tabs-item', 'class', 'bdt-has-no-title');
                        }
                        if ($tab_count === $settings['active_item']) {
                            $this->add_render_attribute('tabs-item', 'class', 'bdt-active');
                        }

                        /* if (!isset($item['tab_icon']) && !Icons_Manager::is_migration_allowed()) {
                            // add old default
                            $item['tab_icon'] = 'fas fa-book';
                        }

                        $migrated = isset($item['__fa4_migrated']['tab_select_icon']);
                        $is_new   = empty($item['tab_icon']) && Icons_Manager::is_migration_allowed(); */

                        $this->add_render_attribute('tab-link', 'data-title', $hash_text, true);

                        if (empty($field_title)) {
                            $this->add_render_attribute('tab-link', 'data-title', $this->get_id() . '-' . $tab_count, true);
                        }

                        $this->add_render_attribute('tab-link', 'class', 'bdt-tabs-item-title', true);
                        $this->add_render_attribute('tab-link', 'id', esc_attr($tab_id), true);
                        $this->add_render_attribute('tab-link', 'data-tab-index', esc_attr($index), true);

                        // New Added
                        $this->remove_render_attribute('tab-link', 'onclick');
                        $this->add_render_attribute('tab-link', 'href', '#', true);

                    ?>
                        <div <?php $this->print_render_attribute_string('tabs-item'); ?>>
                            <a <?php $this->print_render_attribute_string('tab-link'); ?>>
                                <div class="bdt-tab-text-wrapper bdt-flex-column">

                                    <div class="bdt-tab-title-icon-wrapper">                              
                                        <?php if ($field_title) : ?>
                                            <span class="bdt-tab-text">
                                                <?php echo wp_kses($field_title, element_pack_allow_tags('title')); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($field_sub_title and $field_title) : ?>
                                        <span class="bdt-tab-sub-title bdt-text-small">
                                            <?php echo wp_kses($field_sub_title, element_pack_allow_tags('title')); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
<?php
    }
}
