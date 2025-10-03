<?php
namespace UiCore\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Includes\Widgets\Traits\Button_Trait;

use UiCore\Pagination;
use UiCore\Helper;
use UiCore\Elementor\Generic\Post_Filter;
use UiCore\Elementor\Generic\Query;
use UiCore\Elementor\Generic\Meta_Trait;

defined('ABSPATH') || exit();

/**
 * Scripts and Styles Class
 */
class AdvancedPostGrid extends Widget_Base
{
    use Button_Trait;
    use Meta_Trait;

    private $_query;

    public function get_name()
    {
        return 'uicore-advanced-post-grid';
    }
    public function get_categories()
    {
        return [ 'uicore', 'uicore-theme-builder' ];
    }

    public function get_title()
    {
        return __('Advanced Post Grid', 'uicore-framework');
    }

    public function get_icon()
    {
        return 'eicon-gallery-grid ui-e-widget';
    }

    public function get_keywords()
    {
        return ['post', 'grid', 'blog', 'recent', 'news'];
    }
    public function get_style_depends() {
        Helper::register_widget_style('advanced-post-grid');
		return [ 'ui-e-advanced-post-grid' ];
	}
    public function get_script_depends() {
        Helper::register_widget_script('advanced-post-grid',['uicore-manifest']);
		return [ 'ui-e-advanced-post-grid' ];
	}


    public function get_query()
    {
        return $this->_query;
    }
    public function query_posts($posts_per_page, $type = null)
    {
        if( !\Elementor\Plugin::$instance->editor->is_edit_mode() && $this->get_settings('posts-filter_post_type') === 'current' && !isset($_GET['uicore-tb'])) {
            global $wp_query;
            $this->_query = $wp_query;

        }elseif( $this->get_settings('posts-filter_post_type') === 'related' ){
            $this->_query = Helper::get_related('random',$posts_per_page);
        } else{
            $query_args = Query::get_query_args('posts-filter', $this->get_settings(), get_the_ID());

            if($type === 'portfolio') {
                $query_args['orderby'] = 'menu_order date';
            }
            
            if($type === 'current'){
                unset($query_args['posts_per_page']);
                // $conditions = \UiCore\Elementor\ThemeBuilder\Admin::get_pretty_condition('include', get_the_ID());
                // if(strpos($conditions, __( 'Portfolio Page', 'uicore-framework' ) ) !== false){ 
                //     $query_args['post_type'] = 'portfolio';
                // } elseif (strpos($conditions, __( 'WooCommerce Shop Page', 'uicore-framework' ) ) !== false){
                //     $query_args['post_type'] = 'product';
                // }
            }

            
            $this->_query = new \WP_Query($query_args);
        }
        
    }

    protected function register_controls()
    {
        // Query(curent/custom/related/manual)

        // /Pagination (filtering?)
        // /Aditional (no posts)

        $this->start_controls_section('section_layout', [
            'label' => esc_html__('Grid', 'uicore-framework'),
        ]);
        $this->add_control('masonry',[
				'label' => __( 'Masonry', 'uicore-framework' ),
				'type' => Controls_Manager::SWITCHER,
                'frontend_available' => true,
                'default'  => 'no',
                'return_value' => 'ui-e-maso',
                // 'prefix_class' => ' '
			]
		);
        $this->add_responsive_control('columns',[
                'label' => __( 'Columns', 'uicore-framework' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 8,
                'step' => 1,
                'default' => 3,
                'selectors' => [
					'{{WRAPPER}} .ui-e-adv-grid' => 'grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr))',
				],
            ]
        );
        $this->add_responsive_control('gap',[
                'label' => __( 'Items Gap', 'uicore-framework' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 30,
				],
                'selectors' => [
					'{{WRAPPER}} .ui-e-adv-grid' => 'grid-gap: {{SIZE}}{{UNIT}}',
				],
            ]
        );
        $this->end_controls_section();


        $this->start_controls_section('section_item_content', [
            'label' => esc_html__('Item', 'uicore-framework'),
        ]);
        $this->add_control('box_style',[
				'label' => __( 'Item Style', 'uicore-framework' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'classic',
				'options' => [
					'classic'  => __( 'Classic', 'uicore-framework' ),
					'split' => __( 'Split', 'uicore-framework' ),
					'overlay' => __( 'Overlay', 'uicore-framework' ),
                ],
                'prefix_class' => 'ui-e-apg-',
			]
		);
        $this->add_control('image',[
				'label' => __( 'Image', 'uicore-framework' ),
				'type' => Controls_Manager::SWITCHER,
                'separator'=> 'before',
                'default'=> 'yes'
			]
		);
        $this->add_control(
			'image_size_select',
            [
                'label' => __( 'Image Size', 'uicore-framework' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'uicore-medium',
                'options' => self::get_images_sizes(),
                'condition' => [
                    'image' => 'yes',
                ]
            ]
        );
        // $this->add_control('cat_type',[
        //         'label' => __( 'Cat Type', 'uicore-framework' ),
        //         'type' => Controls_Manager::SELECT,
        //         'default' => 'category',
        //         'options' => [
        //             'category'  => __( 'Category', 'uicore-framework' ),
        //             'custom' => __( 'Custom Taxonomy', 'uicore-framework' ),
        //         ],
        //         'condition' => array(
        //             'category' => 'yes',
        //         ),
        //     ]
        // );
        //     $this->add_control('cat_type_name',[
        //             'label' => __( 'Taxonomy', 'uicore-framework' ),
        //             'type' => Controls_Manager::TEXT,
        //             'placeholder' => __( 'my_custom_tax', 'uicore-framework' ),
        //             'condition' => array(
        //                 'cat_type' => 'custom',
        //             ),
        //         ]
        //     );
        $this->add_control('title',[
                'label' => __( 'Title', 'uicore-framework' ),
                'type' => Controls_Manager::SWITCHER,
                'separator'=> 'before',
                'default'=> 'yes'
            ]
        );
        $this->add_control('excerpt',[
                'label' => __( 'Excerpt', 'uicore-framework' ),
                'type' => Controls_Manager::SWITCHER,
                'separator'=> 'before',
                'default'=> 'yes'
            ]
        );
        $this->add_control('excerpt_trim',[
                'label' => __( 'Excerpt Length (words)', 'uicore-framework' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 5,
				'max' => 100,
				'step' => 1,
                'default'=> 55,
                'condition' => array(
                    'excerpt' => 'yes',
                ),
            ]
        );
        $this->add_control('show_button',[
            'label' => __( 'Read More Button', 'uicore-framework' ),
            'type' => Controls_Manager::SWITCHER,
            'separator'=> 'before',
            'default'=> 'no'
            ]
        );
        $this->end_controls_section();


        $this->start_controls_section('section_button_content', [
            'label' => esc_html__('Button', 'uicore-framework'),
            'condition' => array(
                'show_button' => 'yes',
            ),
        ]);
        $this->register_button_content_controls(['section_condition'=>['show_button' => 'yes'],'button_default_text'=>'Read More' ]);
        $this->remove_control('button_type');
        $this->remove_control('link');
        $this->remove_control('button_css_id');
        $this->remove_control('size');
        $this->remove_control('align');
        $this->end_controls_section();


        $this->start_controls_section('section_extra_item_content', [
            'label' => esc_html__('Meta', 'uicore-framework'),
        ]);
        $this->add_control('top_meta',[
				'label' => esc_html__( 'Top Meta', 'uicore-framework' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $this->get_meta_content_controls(),
				'default' => [
					[
						'type' => 'author'
					],
				],
				'title_field' => '<span style="text-transform: capitalize">{{{ type }}}</span>',
                'prevent_empty' => false,
                'separator'=> 'before'
			]
		);
        $this->add_control('before_title_meta',[
				'label' => esc_html__( 'Before Title Meta', 'uicore-framework' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $this->get_meta_content_controls(),
				'default' => [
					[
						'type' => 'author'
					],
				],
				'title_field' => '<span style="text-transform: capitalize">{{{ type }}}</span>',
                'prevent_empty' => false,
                'separator'=> 'before',
			]
		);
        $this->add_control('after_title_meta',[
				'label' => esc_html__( 'After Title Meta', 'uicore-framework' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $this->get_meta_content_controls(),
				'default' => [
					[
						'type' => 'author'
					],
				],
				'title_field' => '<span style="text-transform: capitalize">{{{ type }}}</span>',
                'prevent_empty' => false,
                'separator'=> 'before',
			]
		);
        $this->add_control('bottom_meta',[
				'label' => esc_html__( 'Bottom Meta', 'uicore-framework' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $this->get_meta_content_controls(),
				'default' => [
					[
						'type' => 'author'
					],
				],
				'title_field' => '<span style="text-transform: capitalize">{{{ type }}}</span>',
                'prevent_empty' => false,
                'separator'=> 'before',
			]
		);
        $this->end_controls_section();

        $this->start_controls_section('section_style_item',
			[
				'label' => __( 'Item Style', 'uicore-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        $this->add_control('item_radius',[
				'label'       => esc_html__('Border Radius', 'uicore-framework'),
				'type'        => Controls_Manager::DIMENSIONS,
				'selectors'   => [
					'{{WRAPPER}} ' => '--ui-e-border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;'
				],
			]
		);

        $this->start_controls_tabs(
			'item_border_shadow'
		);

		$this->start_controls_tab(
			'item_bs_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'plugin-name' ),
			]
		);
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'selector' => '{{WRAPPER}} .ui-e-post-item article',
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_shadow',
				'selector' => '{{WRAPPER}} .ui-e-post-item article',
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'item_bs_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'plugin-name' ),
			]
		);
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'item_border_hover',
                'selector' => '{{WRAPPER}} .ui-e-post-item:hover article',
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'item_shadow_hover',
				'selector' => '{{WRAPPER}} .ui-e-post-item:hover article',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
        $this->end_controls_section();


        $this->start_controls_section('section_post_grid_def', [
            'label' => esc_html__('Query', 'uicore-framework'),
        ]);

        $this->add_group_control(Post_Filter::get_type(), [
            'name' => 'posts-filter',
            'label' => esc_html__('Posts', 'uicore-framework'),
            'description' => esc_html__('Current Query Settings > Reading', 'uicore-framework')
        ]);

        $this->add_control('item_limit', [
            'label' => esc_html__('Item Limit', 'uicore-framework'),
            'type' => Controls_Manager::SLIDER,
            'reder_type' => 'template',
            'range' => [
                'px' => [
                    'min' => -1,
                    'max' => 100,
                ],
            ],
            'default' => [
                'size' => 3,
            ],
            'condition' => array(
                'posts-filter_post_type!' => 'current',
            ),
        ]);

        $this->end_controls_section();


        $this->start_controls_section('section_pagination', [
            'label' => esc_html__('Pagination', 'uicore-framework'),
        ]);
        $this->add_control('pagination',[
                'label' => __( 'Pagination', 'uicore-framework' ),
                'type' => Controls_Manager::SWITCHER,
                // 'separator'=> 'before',
                'default'=> 'no',
                'reder_type' => 'template',
            ]
        );
        $this->end_controls_section();
        

        //STYLE
        $this->start_controls_section('section_style_content',[
				'label' => __( 'Content Style', 'uicore-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        $this->add_control('image_size',[
                'label' => __( 'Image Height (%)', 'uicore-framework' ),
                'type' => Controls_Manager::NUMBER,
                'min' => 1,
                'max' => 500,
                'step' => 1,
                'default'=> 57,
                'condition' => array(
                    'masonry!' => 'ui-e-maso',
                ),
                'selectors' => [
                    '{{WRAPPER}} .ui-e-post-top' => '--ui-e-img-size: {{VALUE}}',
                ],
            ]
        );
        $this->add_control('image_overflow',[
                'label' => __( 'Independent Border Radius and Shadow', 'uicore-framework' ),
                'type' => Controls_Manager::SWITCHER,
                'default'=> 'no',
                'prefix_class' => 'ui-e-post-ovf-',
                'condition' => array(
                    'box_style!' => 'overlay',
                ),
            ]
        );
        $this->add_control('image_radius',[
				'label'       => esc_html__('Border Radius', 'uicore-framework'),
				'type'        => Controls_Manager::DIMENSIONS,
				'selectors'   => [
					'{{WRAPPER}} .ui-e-post-top' => 'border-radius: {{TOP}}px {{RIGHT}}px {{BOTTOM}}px {{LEFT}}px;'
				],
                'condition' => array(
                    'image_overflow' => 'yes',
                ),
			]
		);
        $this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'image_shadow',
				'label' => esc_html__( 'Box Shadow', 'uicore-framework' ),
				'selector' => '{{WRAPPER}} .ui-e-post-top',
                'condition' => array(
                    'image_overflow' => 'yes',
                ),
			]
		);
        $this->add_responsive_control('content_align',
			[
				'label' => esc_html__( 'Content Alignment', 'uicore-framework' ),
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
					'stretch' => [
						'title' => esc_html__( 'Justified', 'uicore-framework' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ui-e-post-content' => 'align-items: {{VALUE}}; text-align: {{VALUE}};',
				],
			]
		);
        $this->add_control('title_color', [
				'label' => esc_html__( 'Title Color', 'uicore-framework' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
                'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .ui-e-post-title' => 'color: {{VALUE}};',
				],
			]
		);
        $this->add_control('title_hcolor', [
				'label' => esc_html__( 'Title Hover Color', 'uicore-framework' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ui-e-post-title:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
                'label' => esc_html__( 'Title Typography', 'uicore-framework' ),
				'selector' => '{{WRAPPER}} .ui-e-post-title',
			]
		);
        $this->add_control('title_gap',[
                'label' => __( 'Title Top Space', 'uicore-framework' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'em' ],
                'separator' => 'after',
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 1.2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ui-e-post-title' => 'margin-top: {{SIZE}}em;',
                ],
            ]
        );
        $this->add_control('text_color', [
				'label' => esc_html__( 'Excerpt Color', 'uicore-framework' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .ui-e-post-text' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'text_typography',
                'label' => esc_html__( 'Excerpt Typography', 'uicore-framework' ),
				'selector' => '{{WRAPPER}} .ui-e-post-text',
			]
		);
        $this->add_control('text_gap',[
                'label' => __( 'Excerpt Top Space', 'uicore-framework' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'em' ],
				'separator' => 'after',
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 0.8,
                ],
                'selectors' => [
                    '{{WRAPPER}} .ui-e-post-text' => 'margin-top: {{SIZE}}em;',
                ],
            ]
        );
        $this->add_responsive_control('content_padding',
			[
				'label'      => esc_html__('Content Padding', 'uicore-framework'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ui-e-post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);
        $this->add_control('content_gap',[
            'label' => __( 'Content Space', 'uicore-framework' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'em' ],
            'separator' => 'after',
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 3,
                    'step' => 0.1,
                ],
            ],
            'default' => [
                'unit' => 'em',
                'size' => 0,
            ],
            'selectors' => [
                '{{WRAPPER}} ' => '--ui-e-content-space: {{SIZE}}em;',
            ],
        ]
    );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'content_bg',
                'selector' => '{{WRAPPER}} .ui-e-post-content',
            ]
        );
        $this->end_controls_section();


        $this->start_controls_section('section_button_style', [
            'label' => esc_html__('Button Style', 'uicore-framework'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => array(
                'show_button' => 'yes',
            ),
        ]);
        $this->register_button_style_controls(['section_condition'=>['show_button' => 'yes']]);
        $this->add_control('button_gap',[
            'label' => __( 'Button Top Space', 'uicore-framework' ),
            'type' => Controls_Manager::SLIDER,
            'size_units' => [ 'em' ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 3,
                    'step' => 0.1,
                ],
            ],
            'default' => [
                'unit' => 'em',
                'size' => 0.8,
            ],
            'selectors' => [
                '{{WRAPPER}} .elementor-button' => 'margin-top: {{SIZE}}em;',
            ],
            ]
        );
        $this->end_controls_section();


        $this->start_controls_section('section_style_extra_content',
			[
				'label' => __( 'Meta Style', 'uicore-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        $this->start_controls_tabs('style_extra_tabs');
        $this->start_controls_tab('style_top_tab',[
                'label' => esc_html__( 'Top', 'uicore-framework' ),
            ]
        );
        $this->get_meta_style_controls('top');
        $this->end_controls_tab();
        $this->start_controls_tab('style_before_title_tab',[
                'label' => esc_html__( 'Before Title', 'uicore-framework' ),
            ]
        );
        $this->get_meta_style_controls('before_title');
        $this->end_controls_tab();
        $this->start_controls_tab('style_after_title_tab',[
                'label' => esc_html__( 'After Title', 'uicore-framework' ),
            ]
        );
        $this->get_meta_style_controls('after_title');
        $this->end_controls_tab();
        $this->start_controls_tab('style_bottom_tab',[
                'label' => esc_html__( 'Bottom', 'uicore-framework' ),
            ]
        );
        $this->get_meta_style_controls('bottom');
        $this->end_controls_tab();

        $this->end_controls_tabs();
        $this->end_controls_section();


        $this->start_controls_section('section_style_pagination',
			[
				'label' => __( 'Pagination', 'uicore-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pagination_typography',
                'label' => esc_html__( 'Typography', 'uicore-framework' ),
				'selector' => '{{WRAPPER}} .uicore-page-item',
                'condition' => array(
                    'pagination' => 'yes',
                ),
			]
		);
        $this->add_responsive_control('pagination_top',[
                'label' => __( 'Pagination Top Space', 'uicore-framework' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'px' ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 500,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 50,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uicore-pagination' => 'margin-top: {{SIZE}}px;',
                ],
                'condition' => array(
                    'pagination' => 'yes',
                ),
            ]
        );
        $this->add_control('pagination_align',
			[
				'label' => esc_html__( 'Pagination Alignment', 'uicore-framework' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => esc_html__( 'Left', 'uicore-framework' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'uicore-framework' ),
						'icon' => 'eicon-text-align-center',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .uicore-pagination ul' => 'justify-content: {{VALUE}};',
				],
                'condition' => array(
                    'pagination' => 'yes',
                ),
			]
		);
        $this->add_control('pagination_padding',[
                'label' => __( 'Items Padding', 'uicore-framework' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'em' ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 0.4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uicore-pagination ul li > *' => '    width: calc(1em + {{SIZE}}em);line-height: calc(1em + {{SIZE}}em);',
                ],
                'condition' => array(
                    'pagination' => 'yes',
                ),
            ]
        );
        $this->add_control('pagination_gap',[
                'label' => __( 'Items Gap', 'uicore-framework' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'em' ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 0.4,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uicore-pagination ul' => 'gap: {{SIZE}}em;',
                ],
                'condition' => array(
                    'pagination' => 'yes',
                ),
            ]
        );
        $this->add_control('pagination_radius',[
                'label' => __( 'Items Border Radius', 'uicore-framework' ),
                'type' => Controls_Manager::SLIDER,
                'size_units' => [ 'em' ],
                'range' => [
                    'em' => [
                        'min' => 0,
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'default' => [
                    'unit' => 'em',
                    'size' => 0.2,
                ],
                'selectors' => [
                    '{{WRAPPER}} .uicore-pagination ul li' => 'border-radius: {{SIZE}}em;',
                ],
                'condition' => array(
                    'pagination' => 'yes',
                ),
            ]
        );
        
        $this->start_controls_tabs(
			'pagination_item',[
            'condition' => array(
                'pagination' => 'yes',
            ),]
		);

		$this->start_controls_tab(
			'pagination_normal_tab',
			[
				'label' => esc_html__( 'Normal', 'plugin-name' ),
			]
		);
        $this->add_control('pagination_bg', [
                'label' => esc_html__( 'bg', 'uicore-framework' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .uicore-pagination li' => 'background: {{VALUE}};',
                ],
            ]
        );
        $this->add_control('pagination_color', [
                'label' => esc_html__( 'Color', 'uicore-framework' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .uicore-pagination li a' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'pagination_border',
                'selector' => '{{WRAPPER}} .uicore-pagination ul li',
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pagination_shadow',
				'selector' => '{{WRAPPER}} .uicore-pagination ul li',
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab(
			'pagination_hover_tab',
			[
				'label' => esc_html__( 'Hover', 'plugin-name' ),
			]
		);
        $this->add_control('pagination_hover_bg', [
                'label' => esc_html__( 'Bg', 'uicore-framework' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .uicore-pagination li:hover' => 'background: {{VALUE}};',
                ],
            ]
        );
        $this->add_control('pagination_hover_color', [
                'label' => esc_html__( 'Color', 'uicore-framework' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'separator' => 'before',
                'selectors' => [
                    '{{WRAPPER}} .uicore-pagination li:hover' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'pagination_border_hover',
                'selector' => '{{WRAPPER}} .uicore-pagination ul li:hover',
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pagination_shadow_hover',
				'selector' => '{{WRAPPER}} .uicore-pagination ul li:hover',
			]
		);

		$this->end_controls_tab();
        $this->start_controls_tab(
			'pagination_active_tab',
			[
				'label' => esc_html__( 'Active', 'plugin-name' ),
			]
		);
        $this->add_control('pagination_active_bg', [
                'label' => esc_html__( 'Bg', 'uicore-framework' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .uicore-page-link.current' => 'background: {{VALUE}};',
                ],
            ]
        );
        $this->add_control('pagination_active_color', [
                'label' => esc_html__( 'Color', 'uicore-framework' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .uicore-page-link.current' => 'color: {{VALUE}};',
                ],
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'pagination_border_active',
                'selector' => '{{WRAPPER}} .uicore-pagination li.uicore-active',
                'separator' => 'before',
            ]
        );
        $this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pagination_shadow_active',
				'selector' => '{{WRAPPER}} .uicore-pagination li.uicore-active',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();
        $this->end_controls_section();

         
        $this->start_controls_section('section_style_animations',
			[
				'label' => __( 'Animations', 'uicore-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
        $this->add_control('animate_items',[
                'label'              => esc_html__('Animate each Item', 'uicore-framework'),
                'type'               => Controls_Manager::SWITCHER,
                'default'            => '',
                'return_value'       => 'ui-e-grid-animate',
                'frontend_available' => true,
                'prefix_class'       => '',
				'render_type'		 => 'none'
            ]
        );
        $this->add_control(
			'animate_item_type',
			[
				'label' => __( 'Animation', 'uicore-framework' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fadeInUp',
				'options' => [
					'fadeInUp' => __( 'Fade In Up', 'uicore-framework' ),
					'fadeInDown' => __( 'Fade In Down', 'uicore-framework' ),
					'fadeInLeft' => __( 'Fade In Left', 'uicore-framework' ),
					'fadeInRight' => __( 'Fade In Right', 'uicore-framework' ),
					'fadeIn' => __( 'Fade In', 'uicore-framework' ),
					'zoomIn' => __( 'Zoom In', 'uicore-framework' ),
				],
				'frontend_available' => true,
				'condition' => array(
                    'animate_items' => 'ui-e-grid-animate',
				),
				'render_type'		=> 'none'
			]
        );


		$this->add_control('animate_item_speed',[
				'label' => __( 'Speed', 'uicore-framework' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => array(
                    'animate_items' => 'ui-e-grid-animate',
				),
				'default'=> [
                    'unit' => 'px',
                    'size' => 1500,
                ],
				'range' => [
					'px' => [
						'min'  => 10,
						'max'  => 3000,
						'step' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} ' => '---ui-speed: {{SIZE}}ms',
				],
			]
        );
		$this->add_control('animate_item_delay',[
				'label' => __( 'Animation Delay', 'uicore-framework' ),
				'type' => Controls_Manager::SLIDER,
				'condition' => array(
                    'animate_items' => 'ui-e-grid-animate',
				),
				'default'=> [
                    'unit' => 'px',
                    'size' => 200,
                ],
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 1500,
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} ' => '---ui-delay: {{SIZE}}ms',
				],
			]
        );
		$this->add_control( 'animate_item_stagger', [
                'label' => __( 'Stagger', 'uicore-framework' ),
                'type' => Controls_Manager::SLIDER,
                'condition' => array(
                    'animate_items' => 'ui-e-grid-animate',
                ),
                'default'=> [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'range' => [
                    'px' => [
                        'min'  => 4,
                        'max'  => 500,
                        'step' => 2,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ' => '---ui-stagger: {{SIZE}}ms',
                ],
            ]
        );
        $this->add_control(
			'anim_image',
			[
				'label' => __( 'Image Hover Animation', 'uicore-framework' ),
				'type' => Controls_Manager::SELECT,
                'label_block' => true,
				'default' => 'ui-e-img-anim-zoom',
                'separator'=> 'before',
				'options' => [
					'' => __( 'None', 'uicore-framework' ),
					'ui-e-img-anim-zoom' => __( 'Zoom', 'uicore-framework' ),
				],
				'prefix_class'       => '',
			]
        );
        $this->add_control(
			'anim_meta',
			[
				'label' => __( 'Meta Hover Animation', 'uicore-framework' ),
				'type' => Controls_Manager::SELECT,
                'label_block' => true,
				'default' => '',
				'options' => [
					'' => __( 'None', 'uicore-framework' ),
					'ui-e-meta-anim-show' => __( 'Show', 'uicore-framework' ),
				],
				'prefix_class'       => '',
			]
        );
        $this->add_control(
			'important_note',
			[
				'label' => esc_html__( 'Important Note', 'uicore-framework' ),
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => '<div class="elementor-control-field-description" style="margin-top: -7px;">'.esc_html__( 'This works only for Below Title Meta.', 'uicore-framework' ).'</div>',
                'show_label'    => false,
                'condition' => array(
                    'box_style' => 'overlay',
                ),
			]
		);
        $this->add_control(
			'anim_title',
			[
				'label' => __( 'Title Hover Animation', 'uicore-framework' ),
				'type' => Controls_Manager::SELECT,
                'label_block' => true,
				'default' => '',
				'options' => [
					'' => __( 'None', 'uicore-framework' ),
					'ui-e-title-anim-underline' => __( 'Underline', 'uicore-framework' ),
				],
				'prefix_class'       => '',
			]
        );
        $this->add_control(
			'anim_content',
			[
				'label' => __( 'Content Hover Animation', 'uicore-framework' ),
				'type' => Controls_Manager::SELECT,
                'label_block' => true,
				'default' => '',
				'options' => [
					'' => __( 'None', 'uicore-framework' ),
					'ui-e-content-anim-show' => __( 'Show', 'uicore-framework' ),
				],
				'prefix_class'       => '',
                'condition' => array(
                    'box_style' => 'overlay',
                ),
			]
        );
        $this->add_control(
			'anim_btn',
			[
				'label' => __( 'Button Hover Animation', 'uicore-framework' ),
				'type' => Controls_Manager::SELECT,
                'label_block' => true,
				'default' => '',
				'options' => [
					'' => __( 'None', 'uicore-framework' ),
					'ui-e-btn-anim-show' => __( 'Show', 'uicore-framework' ),
				],
				'prefix_class'       => '',
                'condition' => [
                    'show_button' => 'yes',
                ],
			]
        );
        $this->add_control(
			'anim_item',
			[
				'label' => __( 'Item Hover Animation', 'uicore-framework' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
                'label_block' => true,
				'options' => [
					'' => __( 'None', 'uicore-framework' ),
					'ui-e-item-anim-translate' => __( 'Translate', 'uicore-framework' ),
				],
				'prefix_class'       => '',
			]
        );
        $this->end_controls_section();


    }

    static function get_images_sizes(){
        $sizes = [];
        foreach(get_intermediate_image_sizes() as $size){
            $sizes[$size] = $size;
        }
        return $sizes;
    }
    protected function get_post_image()
    {
        if($this->get_settings_for_display( 'image' ) === 'yes'){
        $pic_id = get_post_thumbnail_id();
        if(!$pic_id)
            return;

        $size = $this->get_settings_for_display( 'image_size_select' ) ?? 'uicore-medium';
        ?>
        <a class="ui-e-post-img-wrapp" href="<?php echo esc_url( get_permalink() );?>" title="<?php echo __('View Post:','uicore-framework') . the_title_attribute(['echo'=>false]); ?>">
            <?php if($this->get_settings_for_display( 'masonry' ) === 'ui-e-maso') {?>
            <?php the_post_thumbnail($size, ['class'=>'ui-e-post-img']);?>
            <?php } else { ?>
            <div class="ui-e-post-img" style="background-image:url(<?php echo wp_get_attachment_image_url($pic_id, $size)?>)" ></div>
            <?php } ?>
        </a>
        <?php
        }
    }
    
    protected function get_post_title()
    {
        if($this->get_settings_for_display( 'title' ) === 'yes')
        ?>
        <a href="<?php echo esc_url( get_permalink() );?>" title="<?php echo __('View Post:','uicore-framework') . the_title_attribute(['echo'=>false]); ?>">
            <h4 class="ui-e-post-title"><span><?php echo get_the_title(); ?></span></h4>
        </a>
      <?php
    }
    

    protected function get_post_meta($position)
    {
        $meta_list = $this->get_settings_for_display( $position.'_meta' );
        if(!isset($meta_list[0]) || $meta_list[0]['type'] == ''){
            return;
        }   
        echo ($position == 'top') ? '<div class="ui-e-post-top-meta">' :  '';
        echo ($position == 'after_title') ? '<div class="ui-e-meta-wrapp">' :  '';
            echo '<div class="ui-e-post-meta ui-e-'.$position.'">';
            foreach ($meta_list as $meta) {
                if($meta['type'] != 'none'){
                    echo '<div class="ui-e-meta-item">';
                    $this->display_meta($meta);
                    echo '</div>';

                    if( next( $meta_list ) && $this->get_settings_for_display( $position.'_meta_separator' ) ) {
                        echo '<span class="ui-e-separator">'.$this->get_settings_for_display( $position.'_meta_separator' ).'</span>';
                    }
                }
            }
            echo '</div>';
        echo ($position == 'top' || $position == 'after_title') ? '</div>' : '';
    }
    function get_button() {
        $settings = $this->get_settings_for_display();
    
        $this->add_render_attribute( 'button', 'class', 'elementor-button-link' );
        $this->add_render_attribute( 'button', 'class', 'elementor-button' );
        $this->add_render_attribute( 'button', 'role', 'button' );
        $this->add_render_attribute( 'content-wrapper', 'class', 'elementor-button-content-wrapper' );
    
        if ( ! empty( $settings['button_css_id'] ) ) {
            $this->add_render_attribute( 'button', 'id', $settings['button_css_id'] );
        }
    
        if ( ! empty( $settings['size'] ) ) {
            $this->add_render_attribute( 'button', 'class', 'elementor-size-' . $settings['size'] );
        }
    
        if ( ! empty( $settings['hover_animation'] ) ) {
            $this->add_render_attribute( 'button', 'class', 'elementor-animation-' . $settings['hover_animation'] );
        }
        $this->add_render_attribute( [
            'icon-align' => [
                'class' => [
                    'elementor-button-icon',
                    'elementor-align-icon-' . $settings['icon_align'],
                ],
            ],
            'text' => [
                'class' => 'elementor-button-text',
            ],
        ] );

        $tbn_content = '<span ' . $this->get_render_attribute_string('content-wrapper') . '>';

        if (!empty($settings['icon']) || !empty($settings['selected_icon']['value'])) {
            $tbn_content .= '<span ' . $this->get_render_attribute_string('icon-align') . '>';
            \ob_start();
                \Elementor\Icons_Manager::render_icon($settings['selected_icon'], ['aria-hidden' => 'true']);
            $tbn_content .= \ob_get_clean();
            $tbn_content .= '</span>';
        }
        
        $tbn_content .= '<span ' . $this->get_render_attribute_string('text') . '>' . $this->get_settings_for_display('text') . '</span>';
        $tbn_content .= '</span>';




        if ( 'product' === get_post_type() ) {
            // WooCommerce product
            global $product;
          
            if ( $product ) {
                // Display WooCommerce add to cart button
                echo apply_filters(
                    'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
                    sprintf(
                        '<a href="%s" data-quantity="%s" %s>%s</a>',
                        esc_url( $product->add_to_cart_url() ),
                        esc_attr( 1 ),
                        $this->get_render_attribute_string( 'button' ),
                        $tbn_content
                    ),
                    $product
                );
            }

            
        }else{
    
        // Default button
        ?>
        <a href="<?php echo esc_url( get_permalink() ); ?>" <?php $this->print_render_attribute_string( 'button' ); ?>>
            <?php echo $tbn_content; ?>
        </a>
        <?php
        }
}
    
    protected function render_item()
    {
        $excerpt_length = $this->get_settings_for_display( 'excerpt_trim' );
        $extra_post_classes = ['ui-e-post-item'];
        if($this->get_settings_for_display( 'animate_items' ) === 'ui-e-grid-animate'){
            $extra_post_classes[] = 'elementor-invisible';
        }
        ?>
        <div class="<?php  echo esc_attr( implode( ' ',$extra_post_classes) ); ?>">
            <article <?php post_class(); ?> >
                <div class="ui-e-post-top">
                    <?php $this->get_post_image(); ?>
                    <?php $this->get_post_meta('top'); ?>
                </div>
                <div class="ui-e-post-content">
                    <?php $this->get_post_meta('before_title'); ?>
                    <?php
                    if($this->get_settings_for_display( 'title' ) === 'yes')
                        $this->get_post_title();
                    ?>
                    <?php $this->get_post_meta('after_title'); ?>
                    <?php
                    if($this->get_settings_for_display( 'excerpt' ))
                    echo '<div class="ui-e-post-text">'.wp_trim_words(get_the_excerpt(), $excerpt_length). '</div>';
                    ?>
                    <?php $this->get_post_meta('bottom');  // button ?>
                    <?php
                    if($this->get_settings_for_display('show_button') === 'yes'){
                        $this->get_button();
                    }
                    ?>
                </div>
            </article>
        </div>
        <?php
    }
    function content_template(){

    }

    protected function render()
    {
        global $wp_query;
        $default_query = $wp_query;
        $settings = $this->get_settings();
        $this->query_posts($settings['item_limit']['size'], $settings['posts-filter_post_type']);
        $wp_query = $this->get_query();

        //Start the loop
        ?>
        <div class="ui-e-adv-grid">
        <?php
        while ($wp_query->have_posts()) {

            $wp_query->the_post();

            $this->render_item();
        }
        
        ?>
        </div>
        <?php
        if($settings['pagination'] === 'yes'){
            if (!class_exists('Uicore\Pagination')) {
                require UICORE_INCLUDES . '/templates/pagination.php';
            }
            new Pagination();
        }

        wp_reset_query();
        $wp_query = $default_query;
    }
}
\Elementor\Plugin::instance()->widgets_manager->register(new AdvancedPostGrid());
