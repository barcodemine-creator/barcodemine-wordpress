<?php
use Elementor\Controls_Manager;

class TS_Elementor_Widget_Team_Members extends TS_Elementor_Widget_Base{
	public function get_name(){
        return 'ts-team-members';
    }
	
	public function get_title(){
        return esc_html__( 'TS Team Members', 'themesky' );
    }
	
	public function get_categories(){
        return array( 'ts-elements', 'general' );
    }
	
	public function get_icon(){
		return 'eicon-person';
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
            'limit'
            ,array(
                'label'     => esc_html__( 'Number of members', 'themesky' )
                ,'type'     => Controls_Manager::NUMBER
				,'default'  => 6
				,'min'      => 1
            )
        );
		
		$this->add_control(
            'ids'
            ,array(
                'label'     	=> esc_html__( 'Include these members', 'themesky' )
                ,'type'      	=> 'ts_autocomplete'
                ,'default'   	=> ''
                ,'options'   	=> array()
				,'autocomplete'	=> array(
					'type'		=> 'post'
					,'name'		=> 'ts_team'
				)
				,'multiple' 	=> true
				,'label_block' 	=> true
            )
        );
		
		$this->add_control(
            'columns'
            ,array(
                'label' => esc_html__( 'Columns', 'themesky' )
                ,'type' => Controls_Manager::SELECT
                ,'default' => '3'
				,'options'	=>array(
							'1'		=> '1'
							,'2'	=> '2'
							,'3'	=> '3'
							,'4'	=> '4'
							,'5'	=> '5'
							,'6'	=> '6'
							)			
                ,'description' => esc_html__( 'Number of Columns. 5 columns is not available on the Grid layout', 'themesky' )
            )
        );
		
		$this->add_control(
            'target'
            ,array(
                'label' 		=> esc_html__( 'Target', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '_blank'
				,'options'		=> array(
									'_blank'	=> esc_html__( 'New Window Tab', 'themesky' )
									,'_self'	=> esc_html__( 'Self', 'themesky' )
								)			
                ,'description' => ''
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
                ,'description' => ''
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
                ,'description' => ''
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
                ,'description' => ''
            )
        );
		
		$this->end_controls_section();
	}
	
	protected function render(){
		$settings = $this->get_settings_for_display();
		
		$default = array(
			'limit'			=> 6
			,'ids'			=> ''
			,'columns'		=> 3
			,'target'		=> '_blank'
			,'is_slider'	=> 0			
			,'show_nav'		=> 1				
			,'auto_play'	=> 0
		);
		
		$settings = wp_parse_args( $settings, $default );
		
		extract( $settings );
		
		$columns = absint($columns);
		if( !in_array( $columns, array(1, 2, 3, 4, 5, 6) ) || ( $is_slider == 0 && $columns == 5 ) ){
			$columns = 3;
		}
		
		global $post, $ts_team_members;
		$thumb_size_name = isset($ts_team_members->thumb_size_name)?$ts_team_members->thumb_size_name:'ts_team_thumb';
		
		$args = array(
					'post_type'				=> 'ts_team'
					,'post_status'			=> 'publish'
					,'posts_per_page'		=> $limit
				);

		if( $ids ){
			$args['post__in'] = $ids;
			$args['orderby'] = 'post__in';
		}
		
		$team = new WP_Query($args);
		
		if( $team->have_posts() ){
			$classes = array();
			$classes[] = 'ts-team-members ts-shortcode';
			$item_class = '';
			$item_extra_class = '';
			if( $is_slider ){
				$classes[] = 'ts-slider';
				if( $show_nav ){
					$classes[] = 'show-nav';
					$classes[] = 'nav-middle';
					$classes[] = 'middle-thumbnail';
				}
			}
			else{
				$item_class = 'ts-col-' . (24/$columns);
			}
			
			$data_attr = array();
			if( $is_slider ){
				$data_attr[] = 'data-nav="'.$show_nav.'"';
				$data_attr[] = 'data-autoplay="'.$auto_play.'"';
				$data_attr[] = 'data-columns="'.$columns.'"';
			}
			$key = -1;
			?>
			<div class="<?php echo esc_attr( implode(' ', $classes) ) ?>" <?php echo implode(' ', $data_attr); ?>>
				<div class="items <?php echo $is_slider?'loading':''; ?>">
			<?php
			while( $team->have_posts() ){
				$key++;
				if( $key == 0 ){
					$item_extra_class = 'first';
				}
				else{
					$item_extra_class = ($key % $columns == 0)?'first':(($key % $columns == $columns - 1)?'last':'');
				}
				$team->the_post();
				$profile_link = get_post_meta($post->ID, 'ts_profile_link', true);
				if( $profile_link == '' ){
					$profile_link = '#';
				}
				$name = get_the_title($post->ID);
				$role = get_post_meta($post->ID, 'ts_role', true);
				
				$facebook_link = get_post_meta($post->ID, 'ts_facebook_link', true);
				$twitter_link = get_post_meta($post->ID, 'ts_twitter_link', true);
				$linkedin_link = get_post_meta($post->ID, 'ts_linkedin_link', true);
				$rss_link = get_post_meta($post->ID, 'ts_rss_link', true);
				$dribbble_link = get_post_meta($post->ID, 'ts_dribbble_link', true);
				$pinterest_link = get_post_meta($post->ID, 'ts_pinterest_link', true);
				$instagram_link = get_post_meta($post->ID, 'ts_instagram_link', true);
				$custom_link = get_post_meta($post->ID, 'ts_custom_link', true);
				$custom_link_icon_class = get_post_meta($post->ID, 'ts_custom_link_icon_class', true);
				
				$social_content = '';
				
				if( $facebook_link ){
					$social_content .= '<a class="facebook" href="'.esc_url($facebook_link).'" target="'.$target.'"><i class="fab fa-facebook-f"></i></a>';
				}
				if( $twitter_link ){
					$social_content .= '<a class="twitter" href="'.esc_url($twitter_link).'" target="'.$target.'"><i class="fab fa-twitter"></i></a>';
				}
				if( $linkedin_link ){
					$social_content .= '<a class="linked" href="'.esc_url($linkedin_link).'" target="'.$target.'"><i class="fab fa-linkedin-in"></i></a>';
				}
				if( $rss_link ){
					$social_content .= '<a class="rss" href="'.esc_url($rss_link).'" target="'.$target.'"><i class="fas fa-rss"></i></a>';
				}
				if( $dribbble_link ){
					$social_content .= '<a class="dribbble" href="'.esc_url($dribbble_link).'" target="'.$target.'"><i class="fab fa-dribbble"></i></a>';
				}
				if( $pinterest_link ){
					$social_content .= '<a class="pinterest" href="'.esc_url($pinterest_link).'" target="'.$target.'"><i class="fab fa-pinterest-p"></i></a>';
				}
				if( $instagram_link ){
					$social_content .= '<a class="instagram" href="'.esc_url($instagram_link).'" target="'.$target.'"><i class="fab fa-instagram"></i></a>';
				}
				if( $custom_link ){
					$social_content .= '<a class="custom" href="'.esc_url($custom_link).'" target="'.$target.'"><i class="fab '.esc_attr($custom_link_icon_class).'"></i></a>';
				}
				
				?>
				<div class="item <?php echo $item_class ?> <?php echo (has_post_thumbnail())?'has-thumbnail':'' ?> <?php echo $item_extra_class ?>">
					<div class="team-content">
						<?php if( has_post_thumbnail() ): ?>
						<div class="image-thumbnail">
							<figure>
								<a href="<?php echo esc_url($profile_link); ?>" target="<?php echo esc_attr($target) ?>">
								<?php the_post_thumbnail($thumb_size_name); ?>
								</a>
							</figure>
						</div>
						<?php endif; ?>
						
						<header class="team-info">
							<h6 class="member-role"><?php echo esc_html($role); ?></h6>
							<div class="name"><a href="<?php echo esc_url($profile_link); ?>" target="<?php echo esc_attr($target) ?>"><?php echo esc_html($name); ?></a></div>
							<?php if( $social_content ): ?>
							<div class="member-social"><?php echo $social_content; ?></div>
							<?php endif; ?>
						</header>
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

$widgets_manager->register_widget_type( new TS_Elementor_Widget_Team_Members() );