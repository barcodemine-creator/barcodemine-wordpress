<?php
use Elementor\Controls_Manager;

class TS_Elementor_Widget_Testimonial extends TS_Elementor_Widget_Base{
	public function get_name(){
        return 'ts-testimonial';
    }
	
	public function get_title(){
        return esc_html__( 'TS Testimonial', 'themesky' );
    }
	
	public function get_categories(){
        return array( 'ts-elements', 'general' );
    }
	
	public function get_icon(){
		return 'eicon-testimonial';
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
            'categories'
            ,array(
                'label' 		=> esc_html__( 'Categories', 'themesky' )
                ,'type' 		=> 'ts_autocomplete'
                ,'default' 		=> array()
				,'options'		=> array()
				,'autocomplete'	=> array(
					'type'		=> 'taxonomy'
					,'name'		=> 'ts_testimonial_cat'
				)
				,'multiple' 	=> true
				,'sortable' 	=> false
				,'label_block' 	=> true
            )
        );
		
		$this->add_control(
            'ids'
            ,array(
                'label' 		=> esc_html__( 'Specific testimonials', 'themesky' )
                ,'type' 		=> 'ts_autocomplete'
                ,'default' 		=> array()
				,'options'		=> array()
				,'autocomplete'	=> array(
					'type'		=> 'post'
					,'name'		=> 'ts_testimonial'
				)
				,'multiple' 	=> true
				,'label_block' 	=> true
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
                'label'     => esc_html__( 'Limit', 'themesky' )
                ,'type'     => Controls_Manager::NUMBER
				,'default'  => 6
				,'min'      => 1
            )
        );
		
		$this->add_control(
            'show_avatar'
            ,array(
                'label' => esc_html__( 'Show avatar', 'themesky' )
                ,'type' => Controls_Manager::SELECT
                ,'default' => '0'
				,'options'	=>array(
							'0'		=> esc_html__( 'No', 'themesky' )
							,'1'	=> esc_html__( 'Yes', 'themesky' )
							)			
                ,'description' => ''
            )
        );
		
		$this->add_control(
            'show_name'
            ,array(
                'label' => esc_html__( 'Show name', 'themesky' )
                ,'type' => Controls_Manager::SELECT
                ,'default' => '1'
				,'options'	=>array(
							'0'		=> esc_html__( 'No', 'themesky' )
							,'1'	=> esc_html__( 'Yes', 'themesky' )
							)			
                ,'description' => ''
            )
        );
		
		$this->add_control(
            'show_byline'
            ,array(
                'label' => esc_html__( 'Show byline', 'themesky' )
                ,'type' => Controls_Manager::SELECT
                ,'default' => '1'
				,'options'	=>array(
							'0'		=> esc_html__( 'No', 'themesky' )
							,'1'	=> esc_html__( 'Yes', 'themesky' )
							)			
                ,'description' => ''
            )
        );
		
		$this->add_control(
            'show_rating'
            ,array(
                'label' => esc_html__( 'Show rating', 'themesky' )
                ,'type' => Controls_Manager::SELECT
                ,'default' => '1'
				,'options'	=>array(
							'0'		=> esc_html__( 'No', 'themesky' )
							,'1'	=> esc_html__( 'Yes', 'themesky' )
							)			
                ,'description' => ''
            )
        );
		
		$this->add_control(
            'excerpt_words'
            ,array(
                'label'     => esc_html__( 'Number of words in excerpt', 'themesky' )
                ,'type'     => Controls_Manager::NUMBER
				,'default'  => 40
				,'min'      => 1
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
                'label' => esc_html__( 'Show in a carousel slider', 'themesky' )
                ,'type' => Controls_Manager::SELECT
                ,'default' => '1'
				,'options'	=>array(
							'0'		=> esc_html__( 'No', 'themesky' )
							,'1'	=> esc_html__( 'Yes', 'themesky' )
							)			
                ,'description' => ''
            )
        );
		
		$this->add_control(
            'show_dots'
            ,array(
                'label' => esc_html__( 'Show dot navigation', 'themesky' )
                ,'type' => Controls_Manager::SELECT
                ,'default' => '1'
				,'options'	=>array(
							'0'		=> esc_html__( 'No', 'themesky' )
							,'1'	=> esc_html__( 'Yes', 'themesky' )
							)			
                ,'description' => ''
            )
        );
		$this->add_control(
            'dots_position'
            ,array(
                'label' => esc_html__( 'Dots Position', 'themesky' )
                ,'type' => Controls_Manager::SELECT
                ,'default' => 'dots-bottom'
				,'options'	=>array(
							'dots-bottom'		=> esc_html__( 'Bottom', 'themesky' )
							,'dots-top'		=> esc_html__( 'Top', 'themesky' )
							)			
                ,'description' => ''
				,'condition'	=> array( 'show_dots' => '1' )
            )
        );
		
		$this->add_control(
            'auto_play'
            ,array(
                'label' => esc_html__( 'Auto play', 'themesky' )
                ,'type' => Controls_Manager::SELECT
                ,'default' => '0'
				,'options'	=>array(
							'0'		=> esc_html__( 'No', 'themesky' )
							,'1'	=> esc_html__( 'Yes', 'themesky' )
							)			
                ,'description' => ''
            )
        );
		
		$this->end_controls_section();
	}
	
	protected function render(){
		$settings = $this->get_settings_for_display();
		
		$default = array(
			'categories'			=> array()
			,'columns'				=> 1
			,'limit'				=> 5
			,'ids'					=> array()
			,'show_avatar'			=> 0
			,'show_name'			=> 1
			,'show_byline'			=> 0
			,'show_rating'			=> 1
			,'show_date'			=> 0
			,'excerpt_words'		=> 40
			,'is_slider'			=> 1
			,'show_nav'				=> 0
			,'show_dots'			=> 1
			,'dots_position'		=> 'dots-bottom'
			,'auto_play'			=> 1
		);
		
		$settings = wp_parse_args( $settings, $default );
		
		extract( $settings );
		
		$classes = array();
		$classes[] = 'columns-'.$columns;
		if( $is_slider ){
			$classes[] = 'ts-slider';
			if( $show_dots ){
				$classes[] = 'show-dots';
			}
		}
		if( $dots_position ){
			$classes[] = $dots_position;
		}
		
		$data_attr = array();
		if( $is_slider ){
			$data_attr[] = 'data-columns="'.$columns.'"';
			$data_attr[] = 'data-nav="'.esc_attr($show_nav).'"';
			$data_attr[] = 'data-dots="'.esc_attr($show_dots).'"';
			$data_attr[] = 'data-autoplay="'.esc_attr($auto_play).'"';
		}

		global $post, $ts_testimonials;
		
		$args = array(
				'post_type'				=> 'ts_testimonial'
				,'post_status'			=> 'publish'
				,'posts_per_page' 		=> $limit
				,'orderby' 				=> 'date'
				,'order' 				=> 'desc'
			);
		
		if( is_array($categories) && count($categories) > 0 ){
			$args['tax_query'] = array(
									array(
										'taxonomy' 			=> 'ts_testimonial_cat'
										,'terms' 			=> $categories
										,'field' 			=> 'term_id'
										,'include_children' => false
									)
								);
		}
		
		if( is_array($ids) && count($ids) > 0 ){
			$args['post__in'] = $ids;
			$args['orderby'] = 'post__in';
		}
		
		$testimonials = new WP_Query($args);
		
		if( $testimonials->have_posts() ){
			if( isset($testimonials->post_count) && $testimonials->post_count <= 1 ){
				$is_slider = false;
			}
			?>
			<div class="ts-testimonial-wrapper ts-shortcode <?php echo esc_attr(implode(' ', $classes)); ?>" <?php echo implode(' ', $data_attr); ?>>
		
				<div class="items <?php echo ($is_slider)?'loading':'' ?>">
				<?php
				while( $testimonials->have_posts() ){
					$testimonials->the_post();
					if( function_exists('mydecor_the_excerpt_max_words') ){
						$content = mydecor_the_excerpt_max_words($excerpt_words, $post, true, '', false);
					}
					else{
						$content = wp_trim_words( $post->post_content, $excerpt_words );
					}
					$byline = get_post_meta($post->ID, 'ts_byline', true);
					$url = get_post_meta($post->ID, 'ts_url', true);
					if( $url == '' ){
						$url = '#';
					}
					$rating = get_post_meta($post->ID, 'ts_rating', true);
					$rating_percent = '0';
					if( $rating != '-1' && $rating != '' ){
						$rating_percent = $rating * 100 / 5;
					}
					
					?>
					<div class="item">
						<div class="testimonial-content">
							<?php if( $show_avatar ): ?>
							<div class="image">
								<?php echo $ts_testimonials->get_image($post->ID); ?>
							</div>
							<?php endif; ?>
							
							<div class="content">
								<?php echo esc_html($content); ?>
							</div>
							
							<?php if( $show_name || $show_byline || ($show_rating && $rating != '-1' && $rating != '') ): ?>
							<div class="entry-meta-top">
								<?php if( $show_name ): ?>
								<span class="author">
									<span><?php esc_html_e('By', 'themesky'); ?></span>
									<a href="<?php echo esc_url($url); ?>" target="_blank"><?php echo get_the_title($post->ID); ?></a>
								</span>
								<?php endif; ?>
								
								<?php if( $show_byline ): ?>
								<span class="role"><?php echo esc_html($byline); ?></span>
								<?php endif; ?>
								
								<?php if( $show_rating && $rating != '-1' && $rating != ''): ?>
								<div class="rating" title="<?php printf( esc_html__('Rated %s out of 5', 'themesky'), $rating ); ?>">
									<span style="width: <?php echo $rating_percent . '%'; ?>"><?php printf( esc_html__('Rated %s out of 5', 'themesky'), $rating ); ?></span>
								</div>
								<?php endif; ?>
							</div>
							<?php endif; ?>
						</div>
					</div>
					<?php
				}
				?>
				</div>
			</div>
			<?php
		}
		
		wp_reset_postdata();
	}
}

$widgets_manager->register_widget_type( new TS_Elementor_Widget_Testimonial() );