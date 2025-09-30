<?php
add_action('widgets_init', 'ts_twitter_load_widgets');

function ts_twitter_load_widgets()
{
	register_widget('TS_Twitter_Widget');
}

if(!class_exists('TS_Twitter_Widget')){
	class TS_Twitter_Widget extends WP_Widget {

		function __construct() {
			$widgetOps = array('classname' => 'ts-twitter-widget', 'description' => esc_html__('Display latest tweets', 'themesky'));
			parent::__construct('ts_twitter', esc_html__('TS - Twitter', 'themesky'), $widgetOps);
		}

		function widget( $args, $instance ) {
			
			extract($args);
			
			$defaults = $this->get_default_values();
							
			$instance = wp_parse_args( $instance, $defaults );
			
			extract($instance);
			
			$title = apply_filters( 'widget_title', $title );
			
			if( !$username ){
				return;
			}
			
			$exclude_replies 	= $exclude_replies ? 'true' : 'false';
			
			if( $cache_time == 0 ){
				$cache_time = 12;
			}
			
			if( $consumer_key == '' || $consumer_secret == '' || $access_token == '' || $access_token_secret == '' ){
				$consumer_key 			= "ZLlLWJ6CXHDMcdWtanbJDqpUL";
				$consumer_secret 		= "1PIVXWtA3bjw32cNQSbrV7Q6bkl4SKDg6LsALDEzkYx8q1u87U";
				$access_token 			= "908339957399351296-UmemaSSE33FO2ZOwkQNmlxm5grBe95T";
				$access_token_secret	= "gVPSftM7oNEiET9q5IVyjehTYO1VZvKtd1HoKimopzQ7P";
			}
			
			echo $before_widget;
			
			if( $title ){
				echo $before_title . $title . $after_title; 
			}
			
			$enable_cache = !wp_doing_ajax();
			
			if( $enable_cache ){
				unset($instance['title']);
				
				$cache_key = 'twitter_' . md5( implode('', $instance) );
			
				$cache = get_transient($cache_key);
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
					echo '<div class="twitter-wrapper">';
					foreach( $tweets as $tweet ){
						$tweet_link = 'https://twitter.com/'.$tweet->user->screen_name.'/statuses/'.$tweet->id_str;
						$user_link = 'https://twitter.com/'.$tweet->user->screen_name;
						?>
						<div class="item">
							<div class="avatar-name">
								<img src="<?php echo esc_url($tweet->user->profile_image_url_https); ?>" title="<?php echo esc_attr($tweet->user->name); ?>" alt="<?php echo esc_attr($tweet->user->name); ?>">
								<a class="name" href="<?php echo esc_url($tweet_link); ?>" target="_blank"><?php echo esc_html($tweet->user->name); ?></a>
							</div>
							<div class="content">
								<?php echo esc_html($tweet->text); ?>
							</div>
							<div class="date-time">
							<?php 
								if( $relative_time ){
									echo $this->relative_time($tweet->created_at); 
								}
								else{
									echo $this->date_format($tweet->created_at);
								}
							?>
							</div>
						</div>
						<?php
					}
					echo '</div>';
					
					$output = ob_get_clean();
					echo $output;
					
					if( $enable_cache ){
						set_transient($cache_key, $output, $cache_time * HOUR_IN_SECONDS);
					}
				}
			}
			?>
			<?php
			echo $after_widget;
		}
		
		function relative_time($time){
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
		
		function date_format($time){
			return mysql2date(get_option('time_format'), $time) . ' - ' . mysql2date(get_option('date_format'), $time);
		}

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;	
			$instance['title'] 					=  strip_tags($new_instance['title']);
			$instance['username'] 				=  $new_instance['username'];
			$instance['limit'] 					=  absint($new_instance['limit']);
			$instance['exclude_replies'] 		=  empty($new_instance['exclude_replies']) ? 0 : 1;
			$instance['relative_time'] 			=  empty($new_instance['relative_time']) ? 0 : 1;
			$instance['cache_time'] 			=  absint($new_instance['cache_time']);															
			$instance['consumer_key'] 			=  $new_instance['consumer_key'];														
			$instance['consumer_secret'] 		=  $new_instance['consumer_secret'];															
			$instance['access_token'] 			=  $new_instance['access_token'];															
			$instance['access_token_secret'] 	=  $new_instance['access_token_secret'];															
			return $instance;
		}
		
		function get_default_values(){
			return array(
						'title'					=> 'Latest Tweets'
						,'username'				=> ''
						,'limit'				=> 2
						,'exclude_replies'		=> 1
						,'relative_time'		=> 1
						,'cache_time'			=> 12
						,'consumer_key'			=> ''
						,'consumer_secret'		=> ''
						,'access_token'			=> ''
						,'access_token_secret'	=> ''
					);
		}

		function form( $instance ) {
			$defaults = $this->get_default_values();
							
			$instance = wp_parse_args( (array) $instance, $defaults );
		?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Enter your title', 'themesky'); ?> </label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('username'); ?>"><?php esc_html_e('Username', 'themesky'); ?> </label>
				<input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" name="<?php echo $this->get_field_name('username'); ?>" type="text" value="<?php echo esc_attr($instance['username']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('limit'); ?>"><?php esc_html_e('Limit', 'themesky'); ?> </label>
				<input class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" type="number" min="1" value="<?php echo esc_attr($instance['limit']); ?>" />
			</p>
			<p>
				<input value="1" class="" type="checkbox" id="<?php echo $this->get_field_id('exclude_replies'); ?>" name="<?php echo $this->get_field_name('exclude_replies'); ?>" <?php checked(1, $instance['exclude_replies']); ?> />
				<label for="<?php echo $this->get_field_id('exclude_replies'); ?>"><?php esc_html_e('Exclude replies', 'themesky'); ?></label>
			</p>
			<p>
				<input value="1" class="" type="checkbox" id="<?php echo $this->get_field_id('relative_time'); ?>" name="<?php echo $this->get_field_name('relative_time'); ?>" <?php checked(1, $instance['relative_time']); ?> />
				<label for="<?php echo $this->get_field_id('relative_time'); ?>"><?php esc_html_e('Relative time', 'themesky'); ?></label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('cache_time'); ?>"><?php esc_html_e('Cache time (hours)', 'themesky'); ?> </label>
				<input class="widefat" id="<?php echo $this->get_field_id('cache_time'); ?>" name="<?php echo $this->get_field_name('cache_time'); ?>" type="number" min="1" value="<?php echo esc_attr($instance['cache_time']); ?>" />
			</p>
			<hr>
			<p>
				<strong><?php esc_html_e('API Keys:', 'themesky'); ?></strong> <?php esc_html_e('if you dont input your API Keys, it will use our API Keys.', 'themesky'); ?>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('consumer_key'); ?>"><?php esc_html_e('Consumer key', 'themesky'); ?> </label>
				<input class="widefat" id="<?php echo $this->get_field_id('consumer_key'); ?>" name="<?php echo $this->get_field_name('consumer_key'); ?>" type="text" value="<?php echo esc_attr($instance['consumer_key']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('consumer_secret'); ?>"><?php esc_html_e('Consumer secret', 'themesky'); ?> </label>
				<input class="widefat" id="<?php echo $this->get_field_id('consumer_secret'); ?>" name="<?php echo $this->get_field_name('consumer_secret'); ?>" type="text" value="<?php echo esc_attr($instance['consumer_secret']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('access_token'); ?>"><?php esc_html_e('Access token', 'themesky'); ?> </label>
				<input class="widefat" id="<?php echo $this->get_field_id('access_token'); ?>" name="<?php echo $this->get_field_name('access_token'); ?>" type="text" value="<?php echo esc_attr($instance['access_token']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('access_token_secret'); ?>"><?php esc_html_e('Access token secret', 'themesky'); ?> </label>
				<input class="widefat" id="<?php echo $this->get_field_id('access_token_secret'); ?>" name="<?php echo $this->get_field_name('access_token_secret'); ?>" type="text" value="<?php echo esc_attr($instance['access_token_secret']); ?>" />
			</p>
			<?php }
	}
}