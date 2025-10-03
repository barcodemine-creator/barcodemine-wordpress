<?php
use Elementor\Controls_Manager;

class TS_Elementor_Widget_Blogs extends TS_Elementor_Widget_Base{
	public function get_name(){
        return 'ts-blogs';
    }
	
	public function get_title(){
        return esc_html__( 'TS Blogs', 'themesky' );
    }
	
	public function get_categories(){
        return array( 'ts-elements', 'general' );
    }
	
	public function get_icon(){
		return 'eicon-posts-grid';
	}
	
	public function get_script_depends(){
		if( \Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() ){
			return array('isotope');
		}
		
		return array();
	}
	
	protected function register_controls(){
		$this->start_controls_section(
            'section_general'
            ,array(
                'label' 	=> esc_html__( 'General', 'themesky' )
                ,'tab'   	=> Controls_Manager::TAB_CONTENT
            )
        );
		
		$this->add_title_and_style_controls();
		
		$this->add_control(
            'layout'
            ,array(
                'label' 		=> esc_html__( 'Layout', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> 'grid'
				,'options'		=> array(
									'grid'			=> esc_html__( 'Grid', 'themesky' )
									,'slider'		=> esc_html__( 'Slider', 'themesky' )
									,'masonry'		=> esc_html__( 'Masonry', 'themesky' )
								)			
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'columns'
            ,array(
                'label' 		=> esc_html__( 'Columns', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '3'
				,'options'		=> array(
									'1'			=> '1'
									,'2'		=> '2'
									,'3'		=> '3'
									,'4'		=> '4'
								)			
                ,'description' 	=> esc_html__( 'Number of Columns', 'themesky' )
            )
        );
		
		$this->add_control(
            'limit'
            ,array(
                'label'     	=> esc_html__( 'Limit', 'themesky' )
                ,'type'     	=> Controls_Manager::NUMBER
				,'default'  	=> 5
				,'min'      	=> 1
				,'description' 	=> esc_html__( 'Number of Posts', 'themesky' )
            )
        );
		
		$this->add_control(
            'categories'
            ,array(
                'label' 		=> esc_html__( 'Categories', 'themesky' )
                ,'type' 		=> 'ts_autocomplete'
                ,'default' 		=> array()
				,'options'		=> array()
				,'autocomplete'	=> array(
					'type'		=> 'taxonomy'
					,'name'		=> 'category'
				)
				,'multiple' 	=> true
				,'sortable' 	=> false
				,'label_block' 	=> true
            )
        );
		
		$this->add_control(
            'orderby'
            ,array(
                'label' 		=> esc_html__( 'Order by', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> 'none'
				,'options'		=> array(
									'none'		=> esc_html__( 'None', 'themesky' )
									,'ID'		=> esc_html__( 'ID', 'themesky' )
									,'date'		=> esc_html__( 'Date', 'themesky' )
									,'name'		=> esc_html__( 'Name', 'themesky' )
									,'title'	=> esc_html__( 'Title', 'themesky' )
								)		
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'order'
            ,array(
                'label' 		=> esc_html__( 'Order', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> 'DESC'
				,'options'		=> array(
									'DESC'		=> esc_html__( 'Descending', 'themesky' )
									,'ASC'		=> esc_html__( 'Ascending', 'themesky' )
								)		
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'show_title'
            ,array(
                'label' 		=> esc_html__( 'Show post title', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '1'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'show_thumbnail'
            ,array(
                'label' 		=> esc_html__( 'Show thumbnail', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '1'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'show_categories'
            ,array(
                'label' 		=> esc_html__( 'Show categories', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '0'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'show_author'
            ,array(
                'label' 		=> esc_html__( 'Show author', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '1'
				,'options'		=> array(
								'0'		=> esc_html__( 'No', 'themesky' )
								,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description'	=> ''
            )
        );
		
		$this->add_control(
            'show_comment'
            ,array(
                'label' 		=> esc_html__( 'Show comment', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '1'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'show_date'
            ,array(
                'label' 		=> esc_html__( 'Show date', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '1'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'show_excerpt'
            ,array(
                'label' 		=> esc_html__( 'Show excerpt', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '0'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'excerpt_words'
            ,array(
                'label'     => esc_html__( 'Number of words in excerpt', 'themesky' )
                ,'type'     => Controls_Manager::NUMBER
				,'default'  => 14
				,'min'      => 1
				,'condition'=> array( 'show_excerpt' => '1' )
            )
        );
		
		$this->add_control(
            'show_readmore'
            ,array(
                'label' 		=> esc_html__( 'Show read more button', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '0'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'view_more_text'
            ,array(
                'label' 		=> esc_html__( 'View more button text', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> ''		
                ,'description' 	=> ''
                ,'condition' 	=> array( 'layout' => array('grid', 'slider') )
            )
        );
		
		$this->add_control(
            'view_more_link'
            ,array(
                'label' 		=> esc_html__( 'View more link', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> ''		
                ,'description' 	=> ''
                ,'condition' 	=> array( 'layout' => array('grid', 'slider') )
            )
        );
		
		$this->add_control(
            'show_load_more'
            ,array(
                'label' 		=> esc_html__( 'Show load more button', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '0'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> ''
				,'condition' 	=> array( 'layout' => array('grid', 'masonry') )
            )
        );
		
		$this->add_control(
            'load_more_text'
            ,array(
                'label' 		=> esc_html__( 'Load more button text', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> 'LOAD MORE'		
                ,'description' 	=> ''
                ,'condition' 	=> array( 'layout' => array('grid', 'masonry') )
            )
        );
		
		$this->add_control(
            'show_nav'
            ,array(
                'label' 		=> esc_html__( 'Show navigation button', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '1'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> ''
				,'condition' 	=> array( 'layout' => 'slider' )
            )
        );
		
		$this->add_control(
            'auto_play'
            ,array(
                'label' 		=> esc_html__( 'Auto play', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '0'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> ''
				,'condition' 	=> array( 'layout' => 'slider' )
            )
        );
		
		$this->end_controls_section();
	}
	
	protected function render(){
		$settings = $this->get_settings_for_display();
		
		$default = array(
			'title'				=> ''
			,'title_style'		=> ''
			,'layout'			=> 'grid'
			,'columns'			=> 3
			,'categories'		=> array()
			,'limit'			=> 5
			,'orderby'			=> 'none'
			,'order'			=> 'DESC'
			,'show_title'		=> 1
			,'show_thumbnail'	=> 1
			,'show_categories'	=> 1
			,'show_author'		=> 1
			,'show_date'		=> 1
			,'show_comment'		=> 1
			,'show_excerpt'		=> 0
			,'show_readmore'	=> 0
			,'excerpt_words'	=> 14
			,'view_more_text'	=> ''
			,'view_more_link'	=> ''
			,'show_nav'			=> 1
			,'auto_play'		=> 0
			,'show_load_more'	=> 0
			,'load_more_text'	=> 'LOAD MORE'
		);
		
		$settings = wp_parse_args( $settings, $default );
		
		extract( $settings );
		
		if( !is_numeric($excerpt_words) ){
			$excerpt_words = 20;
		}
		
		$is_slider = 0;
		$is_masonry = 0;
		if( $layout == 'slider' ){
			$is_slider = 1;
		}
		if( $layout == 'masonry' ){
			wp_enqueue_script( 'isotope' );
			$is_masonry = 1;
		}
		
		$columns = absint($columns);
		if( !in_array($columns, array(1, 2, 3, 4, 6)) ){
			$columns = 4;
		}
		
		$args = array(
			'post_type' 			=> 'post'
			,'post_status' 			=> 'publish'
			,'ignore_sticky_posts' 	=> 1
			,'posts_per_page'		=> $limit
			,'orderby'				=> $orderby
			,'order'				=> $order
			,'tax_query'			=> array()
		);
		
		if( is_array($categories) && count($categories) > 0 ){
			$args['tax_query'][] = array(
										'taxonomy' 	=> 'category'
										,'terms' 	=> $categories
										,'field' 	=> 'term_id'
										,'include_children' => false
									);
		}
		
		global $post;
		$posts = new WP_Query($args);
		
		if( $posts->have_posts() ):
			if( $posts->post_count <= 1 ){
				$is_slider = 0;
			}
			if( $is_slider || $posts->max_num_pages == 1 ){
				$show_load_more = 0;
			}
			
			$classes = array();
			$classes[] = 'ts-blogs-wrapper ts-shortcode ts-blogs';
			$classes[] = $title_style;
			if( $is_slider ){
				$classes[] = 'ts-slider loading';
				if( $show_nav ){
					$classes[] = 'show-nav nav-middle middle-thumbnail';
				}
			}
			if( $view_more_text ){
				$classes[] = 'show-view-more';
			}
			if( $is_masonry ){
				$classes[] = 'ts-masonry loading';
			}
			
			$data_attr = array();
			if( $is_slider ){
				$data_attr[] = 'data-nav="'.esc_attr($show_nav).'"';
				$data_attr[] = 'data-autoplay="'.esc_attr($auto_play).'"';
				$data_attr[] = 'data-columns="'.absint($columns).'"';
			}
			
			if( is_array($categories) ){
				$categories = implode(',', $categories);
			}
			
			$atts = compact('layout', 'columns', 'categories', 'limit', 'orderby', 'order'
							,'show_title', 'show_thumbnail', 'show_author', 'show_categories'
							,'show_date', 'show_comment', 'show_excerpt', 'show_readmore', 'excerpt_words'
							,'is_slider', 'show_nav', 'auto_play', 'is_masonry', 'show_load_more');
			?>
			<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-atts="<?php echo htmlentities(json_encode($atts)); ?>" <?php echo implode(' ', $data_attr); ?>>
			
				<?php if( $title ): ?>
				<header class="shortcode-heading-wrapper">
					<h2 class="shortcode-title">
						<?php echo esc_html($title); ?>
					</h2>
				</header>
				<?php endif; ?>
				
				<div class="content-wrapper">
					<div class="blogs items">
						<?php ts_get_blog_items_content($atts, $posts); ?>
					</div>
					<?php if( $show_load_more ): ?>
					<div class="load-more-wrapper">
						<a href="#" class="load-more button button-border-2" data-total_pages="<?php echo $posts->max_num_pages; ?>" data-paged="2"><?php echo esc_html($load_more_text) ?></a>
					</div>
					<?php endif; ?>
				</div>
				
				<?php if( $view_more_text && $view_more_link ): ?>
				<div class="view-more">
					<a class="button button-border-2" href="<?php echo esc_url($view_more_link); ?>"><?php echo esc_html($view_more_text) ?></a>
				</div>
				<?php endif; ?>
				
			</div>
		<?php
		endif;
		wp_reset_postdata();
	}
}

$widgets_manager->register_widget_type( new TS_Elementor_Widget_Blogs() );