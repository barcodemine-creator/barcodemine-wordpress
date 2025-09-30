<?php
use Elementor\Controls_Manager;

class TS_Elementor_Widget_Portfolios extends TS_Elementor_Widget_Base{
	public function get_name(){
        return 'ts-portfolios';
    }
	
	public function get_title(){
        return esc_html__( 'TS Portfolios', 'themesky' );
    }
	
	public function get_categories(){
        return array( 'ts-elements', 'general' );
    }
	
	public function get_icon(){
		return 'eicon-gallery-masonry';
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
		
		$this->add_control(
            'columns'
            ,array(
                'label' 		=> esc_html__( 'Columns', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '2'
				,'options'		=> array(
									'2'		=> '2'
									,'3'	=> '3'
									,'4'	=> '4'
								)			
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'limit'
            ,array(
                'label'     	=> esc_html__( 'Limit', 'themesky' )
                ,'type'     	=> Controls_Manager::NUMBER
				,'default'  	=> 8
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
					,'name'		=> 'ts_portfolio_cat'
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
                'label' 		=> esc_html__( 'Show portfolio title', 'themesky' )
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
            'show_like_icon'
            ,array(
                'label' 		=> esc_html__( 'Show like icon', 'themesky' )
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
            'original_image'
            ,array(
                'label' 		=> esc_html__( 'Original image', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '0'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> esc_html__( 'Use original image instead of thumbnail', 'themesky' )
            )
        );
		
		$this->add_control(
            'show_filter_bar'
            ,array(
                'label' 		=> esc_html__( 'Show filter bar', 'themesky' )
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
            'show_load_more'
            ,array(
                'label' 		=> esc_html__( 'Show load more button', 'themesky' )
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
            'load_more_text'
            ,array(
                'label' 		=> esc_html__( 'Load more button text', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> 'LOAD MORE'		
                ,'description' 	=> ''
            )
        );
		
		$this->end_controls_section();
		
		$this->start_controls_section(
            'section_slider'
            ,array(
                'label' 	=> esc_html__( 'Slider', 'themesky' )
                ,'tab'   	=> Controls_Manager::TAB_CONTENT
            )
        );
		
		$this->add_control(
            'is_slider'
            ,array(
                'label' 		=> esc_html__( 'Show in a carousel slider', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '0'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> esc_html__( 'If enabled, the filter bar and load more button will be removed', 'themesky' )
            )
        );
		
		$this->add_title_and_style_controls();
		
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
            )
        );

		$this->end_controls_section();
	}
	
	protected function render(){
		$settings = $this->get_settings_for_display();
		
		$default = array(
			'title'				=> ''
			,'title_style'		=> 'title-default'
			,'columns'			=> 2
			,'limit'			=> 8
			,'categories'		=> array()
			,'orderby'			=> 'none'
			,'order'			=> 'DESC'
			,'show_filter_bar'	=> 1
			,'show_load_more'	=> 1
			,'load_more_text'	=> 'LOAD MORE'
			,'show_title'		=> 1
			,'show_categories'	=> 1
			,'show_like_icon'	=> 1
			,'original_image'	=> 0
			,'is_slider'		=> 0
			,'show_nav'			=> 1
			,'auto_play'		=> 0
		);
		
		$settings = wp_parse_args( $settings, $default );
		
		extract( $settings );
		
		if( $is_slider ){
			$show_filter_bar = 0;
			$show_load_more = 0;
		}
		else{
			wp_enqueue_script( 'isotope' );
		}
		
		$args = array(
			'post_type'				=> 'ts_portfolio'
			,'posts_per_page'		=> $limit
			,'post_status'			=> 'publish'
			,'orderby'				=> $orderby
			,'order'				=> $order
		);
		
		if( is_array($categories) && count($categories) > 0 ){
			$args['tax_query']	= array(
						array(
							'taxonomy'	=> 'ts_portfolio_cat'
							,'field'	=> 'term_id'
							,'terms'	=> $categories
						)
					);
		}
		
		global $post, $wp_query, $ts_portfolios;
		
		$classes = array( 'ts-portfolio-wrapper ts-shortcode loading' );
		if( $is_slider ){
			$classes[] = 'ts-slider';
			$classes[] = $title_style;
			if( $show_nav ){
				$classes[] = 'show-nav nav-middle';
			}
		}
		else{
			$classes[] = 'ts-masonry columns-' . $columns;
		}
		
		$data_attr = array();
		if( $is_slider ){
			$data_attr[] = 'data-nav="'.esc_attr($show_nav).'"';
			$data_attr[] = 'data-autoplay="'.esc_attr($auto_play).'"';
			$data_attr[] = 'data-columns="'.absint($columns).'"';
		}
		
		$posts = new WP_Query( $args );
		if( $posts->have_posts() ){
			if( $posts->max_num_pages == 1 ){
				$show_load_more = 0;
			}
			
			if( is_array($categories) ){
				$categories = implode(',', $categories);
			}
			
			$atts = compact('columns', 'limit', 'categories', 'orderby', 'order', 'show_filter_bar', 'show_title','show_categories', 'show_like_icon', 'original_image', 'is_slider', 'show_nav', 'auto_play');
			?>
			<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" data-atts="<?php echo htmlentities(json_encode($atts)); ?>" <?php echo implode(' ', $data_attr); ?>>
			
			<?php if( $is_slider && $title ){ ?>
				<header class="shortcode-heading-wrapper">
					<h2 class="shortcode-title">
						<?php echo esc_html($title); ?>
					</h2>
				</header>
			<?php } ?>
			
			<?php
			/* Get filter bar */
			if( $show_filter_bar ){
				$terms = array();
				foreach( $posts->posts as $p ){
					$post_terms = wp_get_post_terms($p->ID, 'ts_portfolio_cat');
					if( is_array($post_terms) ){
						foreach( $post_terms as $term ){
							$terms[$term->slug] = $term->name;
						}
					}
				}
				
				if( !empty($terms) ){
					?>
					<ul class="filter-bar">
						<li data-filter="*" class="current"><?php esc_html_e('All', 'themesky'); ?></li>
						<?php
						foreach( $terms as $slug => $name ){
						?>
						<li data-filter="<?php echo '.'.$slug; ?>"><?php echo esc_attr($name) ?></li>
						<?php
						}
						?>
					</ul>
					<?php
				}
			}
			?>
				<div class="portfolio-inner items">
					<?php ts_get_portfolio_items_content($atts, $posts); ?>
				</div>
				
				<?php if( $show_load_more ){ ?>
				<div class="load-more-wrapper">
					<a href="#" class="load-more button button-border-2" data-total_pages="<?php echo $posts->max_num_pages; ?>" data-paged="2"><?php echo esc_html($load_more_text) ?></a>
				</div>
				<?php } ?>
			</div>
			
			<?php
		}
		
		wp_reset_postdata();
	}
}

$widgets_manager->register_widget_type( new TS_Elementor_Widget_Portfolios() );