<?php
use Elementor\Controls_Manager;

class TS_Elementor_Widget_Twitter_Slider extends TS_Elementor_Widget_Base{
	public function get_name(){
        return 'ts-twitter-slider';
    }
	
	public function get_title(){
        return esc_html__( 'TS Twitter Slider', 'themesky' );
    }
	
	public function get_categories(){
        return array( 'ts-elements', 'general' );
    }
	
	public function get_icon(){
		return 'eicon-twitter';
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
            'title'
            ,array(
                'label' 		=> esc_html__( 'Title', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> ''		
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'username'
            ,array(
                'label' 		=> esc_html__( 'Username', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> ''		
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'limit'
            ,array(
                'label'     => esc_html__( 'Limit tweets', 'themesky' )
                ,'type'     => Controls_Manager::NUMBER
				,'default'  => 4
				,'min'      => 1
            )
        );
		
		$this->add_control(
            'exclude_replies'
            ,array(
                'label' 		=> esc_html__( 'Exclude replies', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> 'false'
				,'options'		=> array(
									'false'	=> esc_html__( 'No', 'themesky' )
									,'true'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'show_nav'
            ,array(
                'label' 		=> esc_html__( 'Show navigation button', 'themesky' )
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
            'show_dots'
            ,array(
                'label' 		=> esc_html__( 'Show dot navigation', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '0'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> esc_html__( 'Show dot navigation at the bottom. If it is enabled, the navigation buttons will be removed', 'themesky' )
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
		
		$this->add_control(
            'cache_time'
            ,array(
                'label'     => esc_html__( 'Cache time (hours)', 'themesky' )
                ,'type'     => Controls_Manager::NUMBER
				,'default'  => 12
				,'min'      => 1
            )
        );
		
		$this->end_controls_section();
		
		$this->start_controls_section(
            'section_api'
            ,array(
                'label' 	=> esc_html__( 'API Keys', 'themesky' )
                ,'tab'   	=> Controls_Manager::TAB_CONTENT
            )
        );
		
		$this->add_control(
            'consumer_key'
            ,array(
                'label' 		=> esc_html__( 'Consumer key', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> ''		
                ,'description' 	=> ''
				,'label_block'	=> true
            )
        );
		
		$this->add_control(
            'consumer_secret'
            ,array(
                'label' 		=> esc_html__( 'Consumer secret', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> ''		
                ,'description' 	=> ''
				,'label_block'	=> true
            )
        );
		
		$this->add_control(
            'access_token'
            ,array(
                'label' 		=> esc_html__( 'Access token', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> ''		
                ,'description' 	=> ''
				,'label_block'	=> true
            )
        );
		
		$this->add_control(
            'access_token_secret'
            ,array(
                'label' 		=> esc_html__( 'Access token secret', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> ''		
                ,'description' 	=> ''
				,'label_block'	=> true
            )
        );
		
		$this->end_controls_section();
	}
	
	protected function render(){
		$settings = $this->get_settings_for_display();
		
		$default = array(
			'title'					=> ''
			,'username'				=> ''
			,'limit'				=> 4
			,'exclude_replies'		=> 'false'
			,'show_nav'				=> 0
			,'nav_position'			=> 'nav-middle'
			,'show_dots'			=> 0
			,'auto_play'			=> 0
			,'cache_time'			=> 12
			,'consumer_key'			=> ''
			,'consumer_secret'		=> ''
			,'access_token'			=> ''
			,'access_token_secret'	=> ''
		);
		
		$settings = wp_parse_args( $settings, $default );
		
		extract( $settings );
		
		if( $username == '' ){
			return;
		}
		
		$enable_cache = !wp_doing_ajax();
		
		if( $show_dots ){
			$show_nav = 0;
		}
		
		if( $consumer_key == '' || $consumer_secret == '' || $access_token == '' || $access_token_secret == '' ){
			$consumer_key 			= "ZLlLWJ6CXHDMcdWtanbJDqpUL";
			$consumer_secret 		= "1PIVXWtA3bjw32cNQSbrV7Q6bkl4SKDg6LsALDEzkYx8q1u87U";
			$access_token 			= "908339957399351296-UmemaSSE33FO2ZOwkQNmlxm5grBe95T";
			$access_token_secret	= "gVPSftM7oNEiET9q5IVyjehTYO1VZvKtd1HoKimopzQ7P";
		}
		
		if( $enable_cache ){
			$atts = array();
			foreach( $default as $k => $v ){
				$atts[] = $settings[$k];
			}
			
			$cache_key = 'twitter_' . md5( implode( '', $atts ) );
			$cache = get_transient( $cache_key );
		}
		else{
			$cache = false;
		}
		
		if( $cache !== false ){
			echo $cache;
		}
		else{
			require_once THEMESKY_DIR . 'includes/twitteroauth.php';
			
			$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
			$tweets = $connection->get('https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name='.$username.'&count='.$limit.'&exclude_replies='.$exclude_replies);
			if( !isset($tweets->errors) && is_array($tweets) ){
				ob_start();
				
				$classes = array();
				$classes[] = 'ts-twitter-slider ts-shortcode ts-slider';
				if( $show_nav ){
					$classes[] = 'show-nav';
					$classes[] = $nav_position;
				}
				if( $show_dots ){
					$classes[] = 'show-dots';
				}
				
				$data_attr = array();
				$data_attr[] = 'data-nav="'.esc_attr($show_nav).'"';
				$data_attr[] = 'data-dots="'.esc_attr($show_dots).'"';
				$data_attr[] = 'data-autoplay="'.esc_attr($auto_play).'"';
				?>
				<div class="ts-shortcode <?php echo esc_attr(implode(' ', $classes)) ?>" <?php echo implode(' ', $data_attr); ?>>
					<?php if( $title ): ?>
					<header class="shortcode-heading-wrapper">
						<h2 class="shortcode-title">
							<?php echo esc_html($title); ?>
						</h2>
					</header>
					<?php endif; ?>
				
					<div class="items loading">
				
					<?php 
					foreach( $tweets as $tweet ){
						$tweet_link = 'http://twitter.com/'.$tweet->user->screen_name.'/statuses/'.$tweet->id;
						$user_link = 'http://twitter.com/'.$tweet->user->screen_name;
						?>
						<div class="item">
							<div class="twitter-content">
								<div class="icon">
									<i class="fab fa-twitter"></i>
								</div>
								<div class="content">
									<?php echo esc_html($tweet->text); ?>
								</div>
								<h4 class="name">
									<a href="<?php echo esc_url($user_link); ?>" target="_blank"><?php echo '@'.esc_html($tweet->user->name); ?></a>
								</h4>
								<div class="date-time">
								<?php 
									echo $this->relative_time($tweet->created_at); 
									esc_html_e(' on ', 'themesky');
								?>
									<a href="<?php echo esc_url($tweet_link); ?>" target="_blank"><?php esc_html_e('Twitter.com', 'themesky') ?></a>
								</div>
							</div>
						</div>
					<?php 
					}
					?>
					</div>
				</div>
				<?php
				
				$output = ob_get_clean();
				echo $output;
				
				if( $enable_cache ){
					set_transient($cache_key, $output, $cache_time * HOUR_IN_SECONDS);
				}
			}
		}
	}
	
	function relative_time( $time = '' ){
		if( empty($time) ){
			return '';
		}
		
		$second = 1;
		$minute = 60 * $second;
		$hour = 60 * $minute;
		$day = 24 * $hour;
		$month = 30 * $day;

		$delta = strtotime('+0 hours') - strtotime($time);
		if ($delta < 2 * $minute) {
			return esc_html__('1 min ago', 'themesky');
		}
		if ($delta < 45 * $minute) {
			return floor($delta / $minute) . esc_html__(' min ago', 'themesky');
		}
		if ($delta < 90 * $minute) {
			return esc_html__('1 hour ago', 'themesky');
		}
		if ($delta < 24 * $hour) {
			return floor($delta / $hour) . esc_html__(' hours ago', 'themesky');
		}
		if ($delta < 48 * $hour) {
			return esc_html__('yesterday', 'themesky');
		}
		if ($delta < 30 * $day) {
			return floor($delta / $day) . esc_html__(' days ago', 'themesky');
		}
		if ($delta < 12 * $month) {
			$months = floor($delta / $day / 30);
			return $months <= 1 ? esc_html__('1 month ago', 'themesky') : $months . esc_html__(' months ago', 'themesky');
		} else {
			$years = floor($delta / $day / 365);
			return $years <= 1 ? esc_html__('1 year ago', 'themesky') : $years . esc_html__(' years ago', 'themesky');
		}
	}
}

$widgets_manager->register_widget_type( new TS_Elementor_Widget_Twitter_Slider() );