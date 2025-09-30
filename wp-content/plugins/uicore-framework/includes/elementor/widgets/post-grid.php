<?php
namespace UiCore\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

use UiCore\Assets;
use UiCore\Portfolio;
use UiCore\Blog;
use UiCore\Helper;
use UiCore\Elementor\Generic\Post_Filter;
use UiCore\Elementor\Generic\Query;

defined('ABSPATH') || exit();

/**
 * Scripts and Styles Class
 */
class PostGrid extends Widget_Base
{
    private $_query;
    
    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);

    }

    public function get_name()
    {
        return 'uicore-post-grid';
    }
    public function get_categories()
    {
        return ['uicore'];
    }

    public function get_title()
    {
        return __('Post Grid', 'uicore-framework');
    }

    public function get_icon()
    {
        return 'eicon-gallery-grid ui-e-widget';
    }

    public function get_keywords()
    {
        return ['post', 'grid', 'blog', 'recent', 'news'];
    }

    public function on_import($element)
    {
        if (!get_post_type_object($element['settings']['posts-filter_post_type'])) {
            $element['settings']['posts-filter_post_type'] = 'post';
        }

        return $element;
    }

    public function on_export($element)
    {
        $element = Post_Filter::on_export_remove_setting_from_element($element, 'uicore-posts-filter');
        return $element;
    }

    public function get_query()
    {
        return $this->_query;
    }

    protected function register_controls()
    {
        $default_columns = Helper::get_option('blog_col', 3);

        $this->start_controls_section('section_post_grid_def', [
            'label' => esc_html__('Query', 'uicore-framework'),
        ]);

        $this->add_group_control(Post_Filter::get_type(), [
            'name' => 'posts-filter',
            'label' => esc_html__('Posts', 'uicore-framework'),
        ]);

        $this->add_control('item_limit', [
            'label' => esc_html__('Item Limit', 'uicore-framework'),
            'type' => Controls_Manager::SLIDER,
            'reder_type' => 'template',
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 30,
                ],
            ],
            'default' => [
                'size' => 3,
            ],
        ]);
        $this->add_control('col_number', [
            'label' => esc_html__('Columns Number', 'uicore-framework'),
            'type' => Controls_Manager::SLIDER,
            'reder_type' => 'template',
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 4,
                ],
            ],
            'default' => [
                'size' => $default_columns,
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('section_post_grid_layout', [
            'label' => esc_html__('Layout', 'uicore-framework'),
        ]);
        $this->add_control(
			'layout',
			[
				'label' => __( 'Item Style', 'uicore-framework' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => __( 'Default', 'uicore-framework' ),
					'classic' => __( 'classic', 'uicore-framework' ),
					'grid' => __( 'Grid', 'uicore-framework' ),
					'horizontal' => __( 'Horizontal', 'uicore-framework' ),
					'masonry' => __( 'Masonry', 'uicore-framework' ),
				],
                'condition' => array(
                    'posts-filter_post_type!' => 'portfolio',
                ),
			]
		);

        $this->add_control(
			'box_style',
			[
				'label' => __( 'layout', 'uicore-framework' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => __( 'Default', 'uicore-framework' ),
					'boxed' => __( 'Boxed', 'uicore-framework' ),
					'boxed-creative' => __( 'Boxed Creative', 'uicore-framework' ),
					'cover' => __( 'Cover', 'uicore-framework' ),
				],
                'condition' => array(
                    'posts-filter_post_type!' => 'portfolio',
                ),
			]
		);

        $this->add_control(
			'box_ratio',
			[
				'label' => __( 'Image Ratio', 'uicore-framework' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => __( 'Default', 'uicore-framework' ),
					'square' => __( 'Square', 'uicore-framework' ),
					'landscape' => __( 'Landscape', 'uicore-framework' ),
					'portrait' => __( 'Portrait', 'uicore-framework' ),
				],
                'condition' => array(
                    'posts-filter_post_type!' => 'portfolio',
                ),
			]
		);
        $this->add_control(
			'extra_author',
			[
				'label' => __( 'Author', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
                'reder_type' => 'template',
				'condition' => array(
                    'posts-filter_post_type!' => 'portfolio',
                ),
			]
		);
        $this->add_control(
			'extra_date',
			[
				'label' => __( 'Date', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
                'reder_type' => 'template',
				'condition' => array(
                    'posts-filter_post_type!' => 'portfolio',
                ),
			]
		);
        $this->add_control(
			'extra_excerpt',
			[
				'label' => __( 'Excerpt', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
                'reder_type' => 'template',
				'condition' => array(
                    'posts-filter_post_type!' => 'portfolio',
                ),
			]
		);
        $this->add_control(
			'extra_category',
			[
				'label' => __( 'Category', 'hub-elementor-addons' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => '',
                'reder_type' => 'template',
				'condition' => array(
                    'posts-filter_post_type!' => 'portfolio',
                ),
			]
		);

        $this->end_controls_section();


        $this->start_controls_section(
			'section_style_typo',
			[
				'label' => __( 'Content Style', 'uicore-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

        $this->add_control(
			'post_heading_title',
			[
				'label' => esc_html__( 'Post Title', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'post_title_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .uicore-post-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'post_title_typography',
				'selector' => '{{WRAPPER}} .uicore-post-title, {{WRAPPER}} .uicore-post-title',
			]
		);


		$this->add_control(
			'extra_excerpt_heading',
			[
				'label' => esc_html__( 'Excerpt', 'elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
        $this->add_responsive_control(
			'extra_excerpt_bottom_space',
			[
				'label' => esc_html__( 'Spacing', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}  .uicore-post-info-wrapper > p' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'extra_excerpt_color',
			[
				'label' => esc_html__( 'Color', 'elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .uicore-post-info-wrapper > p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'extra_excerpt_typography',
				'selector' => '{{WRAPPER}} .uicore-post-info-wrapper > p',
			]
		);


        $this->add_responsive_control(
			'box_padding',
			[
				'label' => esc_html__( 'Content Padding', 'elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%', 'rem' ],
                'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .uicore-blog-grid .uicore-post .uicore-post-info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}!important;',
				],
			]
		);

        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'box_background',
				'selector' => '{{WRAPPER}} .uicore-blog-grid .uicore-post',
			]
		);

        $this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'box_border',
				'selector' => '{{WRAPPER}} .uicore-blog-grid .uicore-post',
                'separator' => 'before',
			]
		);

		$this->add_control(
			'box_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'elementor' ),
				'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
				'selectors' => [
					'{{WRAPPER}} .uicore-blog-grid .uicore-post' => '--uicore-blog--radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'box_shadow_style',
				'selector' => '{{WRAPPER}} .uicore-blog-grid .uicore-post',
			]
		);


        $this->end_controls_section();
    }

    public function query_posts($posts_per_page, $type = null)
    {
        $query_args = Query::get_query_args('posts-filter', $this->get_settings());

		if($type === 'portfolio') {
			$query_args['orderby'] = 'menu_order date';
		}

        $query_args['posts_per_page'] = $posts_per_page;

        $this->_query = new \WP_Query($query_args);
    }

    protected function render()
    {
        $settings = $this->get_settings();

        $col = $settings['col_number']['size'];
        $type = $settings['posts-filter_post_type'];

        $blog_item_style = $settings['box_style'];
        if($type != 'portfolio'){

            $type = str_replace(' ', '-', $blog_item_style);
            if( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                $content = '';

                $content .= \file_get_contents( Assets::get_global('uicore-blog.css') );
                if($blog_item_style != 'default'){
                    $content .= \file_get_contents(UICORE_ASSETS . '/css/blog/item-style-'.$type.'.css');
                }

                ?>
                <style>
                    <?php echo $content; ?>
                </style>
                <?php
            }
            else{
				if($blog_item_style != 'default'){
                	wp_enqueue_style('uicore_blog_grid_'.$type, UICORE_ASSETS . '/css/blog/item-style-'.$type.'.css', UICORE_VERSION);
				}
            }
        }

        $this->query_posts($settings['item_limit']['size'], $type);
        $wp_query = $this->get_query();

        if (!$wp_query->found_posts) {
            echo 'No Posts Found!';
            return;
        }


        if ($type === 'portfolio') {
            if( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                $content = '';
                $content .= \file_get_contents( Assets::get_global('uicore-portfolio.css') );
                ?>
                <style>
                    <?php  echo $content; ?>
                </style>
                <?php
            }
            if(!class_exists('\UiCore\Portfolio\Frontend')){
                require_once UICORE_INCLUDES . '/portfolio/class-template.php';
                require_once UICORE_INCLUDES . '/portfolio/class-frontend.php';
            }
            Portfolio\Frontend::frontend_css(true);
            $portfolio = new Portfolio\Template('display');
            $portfolio->portfolio_layout($wp_query, null, $col);
        } else {
            $layout = $settings['layout'] === 'default' ? null : $settings['layout'];
            $style = $blog_item_style === 'default' ? null : $blog_item_style;

            if(isset($settings['box_ratio'])){
                $ratio = $settings['box_ratio'] === 'default' ? null : $settings['box_ratio'];
            }else{//Fallback for older versions
                $ratio = null;
            }
            if(isset($settings['extra_author'])){
                $extra = [
                    'author'    => $settings['extra_author'] === 'yes' ? true : false,
                    'date'      => $settings['extra_date'] === 'yes' ? true : false,
                    'excerpt'   => $settings['extra_excerpt'] === 'yes' ? true : false,
                    'category'  => $settings['extra_category'] === 'yes' ? true : false
                ]; 
            }else{ //Fallback for older versions
                $extra = [
                    'author'    => null,
                    'date'      => null,
                    'excerpt'   => null,
                    'category'  => null
                ];
            }
            
            
            if(!class_exists('\UiCore\Blog\Frontend')){
                require_once UICORE_INCLUDES . '/blog/class-template.php';
                require_once UICORE_INCLUDES . '/blog/class-frontend.php';
            }
            Blog\Frontend::frontend_css(true);
            $blog = new Blog\Template('display');
            $blog->blog_layout($wp_query, $layout, $col,null,$ratio,$extra, $style);
        }

    }
}
\Elementor\Plugin::instance()->widgets_manager->register(new PostGrid());
