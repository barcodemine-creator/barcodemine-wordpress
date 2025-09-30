<?php
add_action('widgets_init', 'ts_instagram_load_widgets');

function ts_instagram_load_widgets()
{
	register_widget('TS_Instagram_Widget');
}

if(!class_exists('TS_Instagram_Widget')){
	class TS_Instagram_Widget extends WP_Widget {

		public $access_token, $base_access_token, $number, $option_key = 'ts_instagram_tokens';

		function __construct(){
			$widgetOps = array('classname' => 'ts-instagram-widget', 'description' => esc_html__('Display your photos from Instagram', 'themesky'));
			parent::__construct('ts_instagram', esc_html__('TS - Instagram', 'themesky'), $widgetOps);
		}

		function widget( $args, $instance ) {
			
			extract($args);
			
			$defaults = $this->get_default_values();
							
			$instance = wp_parse_args( $instance, $defaults );
			
			extract($instance);
			
			$title = apply_filters( 'widget_title', $title );
			
			if( !$access_token ){
				return;
			}
			
			$this->base_access_token = $access_token;
			$this->number = $number;
			
			if( $cache_time == 0 ){
				$cache_time = 12;
			}
			if( $is_slider && $show_nav ){
				$before_widget = str_replace('widget-container', 'widget-container has-nav', $before_widget);
			}
			echo $before_widget;
			
			if( $title ){
				echo $before_title . $title . $after_title; 
			}
			
			$enable_cache = !wp_doing_ajax();
			
			if( $enable_cache ){
				unset($instance['title']);
			
				$cache_key = 'instagram_' . md5( implode('', $instance) );
				
				$cache = get_transient($cache_key);
			}
			else{
				$cache = false;
			}
			
			if( $cache !== false ){
				echo $cache;
			}
			else{
				$media_array = array();
				
				if( $this->base_access_token ){ // always refresh if added
					$this->maybe_clean_token();
					$refresh_result = $this->maybe_refresh_token();
					if( is_wp_error($refresh_result) ){
						echo esc_html( $refresh_result->get_error_message() );
						$this->base_access_token = ''; // dont get data
					}
				}
				
				if( $this->base_access_token ){
					$media_array = $this->get_data_with_token();
					if( is_wp_error( $media_array ) ){
						echo esc_html( $media_array->get_error_message() );
					}
				}
				
				if( !is_wp_error( $media_array ) && !empty( $media_array ) ){
					ob_start();
					$classes = array();
					$classes[] = 'ts-instagram-wrapper';
					$classes[] = 'columns-' . $column;
					
					$data_attr = array();
					if( $is_slider ){
						$data_attr[] = 'data-nav="'.esc_attr($show_nav).'"';
						$data_attr[] = 'data-autoplay="'.esc_attr($auto_play).'"';
						$data_attr[] = 'data-columns="'.absint($column).'"';
						
						$classes[] = 'ts-slider loading';
					}
					?>
					<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" <?php echo implode(' ', $data_attr); ?>>
						<div class="items">
							<?php foreach( $media_array as $index => $item ){?>
							<div class="item">
								<a href="<?php echo esc_url( $item['permalink'] ) ?>" target="<?php echo esc_attr( $target ) ?>">
									<?php if( $enable_cache ){ ?>
										<img class="ts-lazy-load" src="data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20viewBox='0%200%201%201'%3E%3C/svg%3E" data-src="<?php echo esc_url( $item['media_url'] ) ?>" alt="<?php echo esc_attr( $item['caption'] ) ?>" title="<?php echo esc_attr( $item['caption'] ) ?>" />
									<?php } else { ?>
										<img src="<?php echo esc_url( $item['media_url'] ) ?>" alt="<?php echo esc_attr( $item['caption'] ) ?>" title="<?php echo esc_attr( $item['caption'] ) ?>" />
									<?php } ?>
								</a>
							</div>
							<?php } ?>
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
			echo $after_widget;
		}

		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;	
			$instance['title'] 				=  strip_tags($new_instance['title']);
			$instance['access_token'] 		=  $new_instance['access_token'];
			$instance['number'] 			=  $new_instance['number'];
			$instance['column'] 			=  $new_instance['column'];									
			$instance['target'] 			=  $new_instance['target'];									
			$instance['cache_time'] 		=  absint($new_instance['cache_time']);
			$instance['is_slider'] 			= empty($new_instance['is_slider']) ? 0 : 1;
			$instance['show_nav'] 			= empty($new_instance['show_nav']) ? 0 : 1;
			$instance['auto_play'] 			= empty($new_instance['auto_play']) ? 0 : 1;
			return $instance;
		}
		
		function get_default_values(){
			return array(
						'title'			=> 'Instagram'
						,'access_token' => ''
						,'number' 		=> 9
						,'column' 		=> 3
						,'target' 		=> '_self'
						,'cache_time'	=> 12
						,'is_slider'	=> 0
						,'show_nav' 	=> 1
						,'auto_play' 	=> 1
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
				<label for="<?php echo $this->get_field_id('access_token'); ?>"><?php esc_html_e('Access Token', 'themesky'); ?> </label>
				<input class="widefat" id="<?php echo $this->get_field_id('access_token'); ?>" name="<?php echo $this->get_field_name('access_token'); ?>" type="text" value="<?php echo esc_attr($instance['access_token']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php esc_html_e('Number of photos', 'themesky'); ?> </label>
				<input class="widefat" type="number" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" value="<?php echo esc_attr($instance['number']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('column'); ?>"><?php esc_html_e('Column', 'themesky'); ?> </label>
				<select class="widefat" id="<?php echo $this->get_field_id('column'); ?>" name="<?php echo $this->get_field_name('column'); ?>" >
					<?php for( $i = 2; $i <= 6; $i++ ): if( $i == 5 ){ continue; } ?>
					<option value="<?php echo $i; ?>" <?php selected($instance['column'], $i); ?> ><?php echo $i; ?></option>
					<?php endfor; ?>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('target'); ?>"><?php esc_html_e('Target', 'themesky'); ?> </label>
				<select class="widefat" id="<?php echo $this->get_field_id('target'); ?>" name="<?php echo $this->get_field_name('target'); ?>" >
					<option value="_self" <?php selected($instance['target'], '_self'); ?> ><?php esc_html_e('Self', 'themesky') ?></option>
					<option value="_blank" <?php selected($instance['target'], '_blank'); ?> ><?php esc_html_e('New window tab', 'themesky') ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('cache_time'); ?>"><?php esc_html_e('Cache time (hours)', 'themesky'); ?> </label>
				<input class="widefat" type="number" min="1" id="<?php echo $this->get_field_id('cache_time'); ?>" name="<?php echo $this->get_field_name('cache_time'); ?>" value="<?php echo esc_attr($instance['cache_time']); ?>" />
			</p>
			
			<hr/>
			
			<p>
				<input type="checkbox" id="<?php echo $this->get_field_id('is_slider'); ?>" name="<?php echo $this->get_field_name('is_slider'); ?>" value="1" <?php echo ($instance['is_slider'])?'checked':''; ?> />
				<label for="<?php echo $this->get_field_id('is_slider'); ?>"><?php esc_html_e('Show in a carousel slider', 'themesky'); ?></label>
			</p>
			
			<p>
				<input type="checkbox" id="<?php echo $this->get_field_id('show_nav'); ?>" name="<?php echo $this->get_field_name('show_nav'); ?>" value="1" <?php echo ($instance['show_nav'])?'checked':''; ?> />
				<label for="<?php echo $this->get_field_id('show_nav'); ?>"><?php esc_html_e('Show navigation button', 'themesky'); ?></label>
			</p>
			
			<p>
				<input type="checkbox" id="<?php echo $this->get_field_id('auto_play'); ?>" name="<?php echo $this->get_field_name('auto_play'); ?>" value="1" <?php echo ($instance['auto_play'])?'checked':''; ?> />
				<label for="<?php echo $this->get_field_id('auto_play'); ?>"><?php esc_html_e('Auto play', 'themesky'); ?></label>
			</p>
			
			<?php 
		}
		
		function connect( $url ){
			$args = array(
				'timeout' => 60
				,'sslverify' => false
			);
			$response = wp_remote_get( $url, $args );

			if( ! is_wp_error( $response ) ){
				$response = json_decode( str_replace( '%22', '&rdquo;', $response['body'] ), true );
			}

			if( isset($response['data']) ){
				return $response['data'];
			}
			else{
				return $response;
			}
		}
		
		function maybe_clean_token(){
			$split_token = explode( ' ', trim( $this->base_access_token ) );
			$this->base_access_token = preg_replace("/[^A-Za-z0-9 ]/", '', $split_token[0] );
			
			if( substr_count ( $this->base_access_token , '.' ) < 3 ){
				$this->access_token = $this->base_access_token;
				return;
			}

			$parts = explode( '.', trim( $this->base_access_token ) );
			$last_part = $parts[2] . $parts[3];
			$this->access_token = $parts[0] . '.' . base64_decode( $parts[1] ) . '.' . base64_decode( $last_part );
		}
		
		// Token need to be refreshed every 60 days
		function maybe_refresh_token(){	
			$need_refresh = true;
			$value = get_option($this->option_key, array());
			if( isset($value[$this->base_access_token]['timestamp']) ){
				$current_token = $value[$this->base_access_token]['refreshed_token'];
				$timestamp = $value[$this->base_access_token]['timestamp'];
				if( $timestamp > time() ){
					$need_refresh = false;
				}
				$this->access_token = $current_token;
			}
			else if( !is_array($value) ){
				$value = array();
			}
			
			if( $need_refresh ){
				$url = 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . $this->access_token;
				$data = $this->connect( $url );
				
				// show error here if failed
				if( isset($data['access_token']) ){
					if( !isset($value[$this->base_access_token]) ){
						$value[$this->base_access_token] = array();
					}
					$value[$this->base_access_token]['refreshed_token'] = $data['access_token'];
					$value[$this->base_access_token]['timestamp'] = time() + MONTH_IN_SECONDS; // refresh after a month
					
					// delete unuse token before saving
					foreach( $value as $t => $v ){
						if( $t != $this->base_access_token && isset($v['timestamp']) && ( $v['timestamp'] + YEAR_IN_SECONDS ) < time() ){
							unset($value[$t]);
						}
					}
					
					update_option($this->option_key, $value); // use refreshed token for future
					$this->access_token = $data['access_token'];
					return true;
				}
				else{
					return new WP_Error( 'cant_refresh_token', esc_html__( 'Can not refresh Instagram token. It may be incorrect.', 'themesky' ) );
				}
			}
			return true;
		}
		
		function get_user_id(){
			$value = get_option($this->option_key, array());
			
			if( isset($value[$this->base_access_token]['user_id']) ){
				return $value[$this->base_access_token]['user_id'];
			}
			
			$url = 'https://graph.instagram.com/me?fields=id,username&access_token=' . $this->access_token;
			$response = $this->connect( $url );
			
			if( isset($response['id']) ){
				if( !isset($value[$this->base_access_token]) ){
					$value[$this->base_access_token] = array();
				}
				$value[$this->base_access_token]['user_id'] = $response['id'];
				update_option($this->option_key, $value);
				return $response['id'];
			}
			else{
				return new WP_Error( 'invalid_response', esc_html__( 'Unable to communicate with Instagram.', 'themesky' ) );
			}
		}
		
		function get_data_with_token(){
			$user_id = $this->get_user_id();
			
			if( is_wp_error($user_id) ){
				return $user_id;
			}
			
			$number = $this->number * 2; // prevent have video/album
			
			$url = 'https://graph.instagram.com/'.$user_id.'/media?fields=media_url,caption,id,media_type,permalink&limit='.$number.'&access_token=' . $this->access_token;
			
			$response = $this->connect( $url );
			
			if( !is_array($response) ){
				return new WP_Error( 'invalid_response', esc_html__( 'Instagram has returned invalid data.', 'themesky' ) );
			}
			
			if( isset($response['error']['message']) ){
				return new WP_Error( 'error_response', $response['error']['message'] );
			}
			
			$items = array();
			foreach( $response as $node ){
				if( !isset($node['media_type']) || $node['media_type'] != 'IMAGE' ){
					continue;
				}
				$item = array();
				$item['permalink'] =  $node['permalink'];
				$item['media_url'] =  $node['media_url'];
				$item['caption'] = isset($node['caption'])?$node['caption']:__('Instagram Image', 'themesky');
				$items[] = $item;
			}
			
			return array_slice( $items, 0, $this->number );
		}
	}
}

