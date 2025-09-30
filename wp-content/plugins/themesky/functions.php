<?php 
/*** Remove product hooks based on user's selection ***/
if( !function_exists('ts_remove_product_hooks') ){
	function ts_remove_product_hooks( $options = array() ){
		if( isset($options['show_label']) && !$options['show_label'] ){
			remove_action('woocommerce_after_shop_loop_item_title', 'mydecor_template_loop_product_label', 1);
		}
		if( isset($options['show_image']) && !$options['show_image'] ){
			remove_action('woocommerce_before_shop_loop_item_title', 'mydecor_template_loop_product_thumbnail', 10);
		}
		
		if( isset($options['show_categories']) && !$options['show_categories'] ){
			remove_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_categories', 10);
		}
		if( isset($options['show_title']) && !$options['show_title'] ){
			remove_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_product_title', 20);
		}
		if( isset($options['show_rating']) && !$options['show_rating'] ){
			remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 30);
		}
		if( isset($options['show_price']) && !$options['show_price'] ){
			remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 40);
		}
		if( isset($options['show_sku']) && !$options['show_sku'] ){
			remove_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_product_sku', 50);
		}
		if( isset($options['show_short_desc']) && !$options['show_short_desc'] ){
			remove_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_short_description', 60);
		}
		if( isset($options['show_add_to_cart']) && !$options['show_add_to_cart'] ){
			remove_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_add_to_cart', 70);
			remove_action('woocommerce_after_shop_loop_item_title', 'mydecor_template_loop_add_to_cart', 10004 );
		}
		if( isset($options['show_color_swatch']) && $options['show_color_swatch'] && function_exists('mydecor_template_loop_product_variable_color') ){
			add_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_product_variable_color', 90);
			if( isset($options['number_color_swatch']) ){
				$number_color_swatch = absint($options['number_color_swatch']);
				add_filter('mydecor_loop_product_variable_color_number', function() use ($number_color_swatch){
					return $number_color_swatch;
				});
			}
		}
		
		wc_set_loop_prop( 'is_shortcode', true );
	}
}

/*** Remove product hooks to default ***/
if( !function_exists('ts_restore_product_hooks') ){
	function ts_restore_product_hooks(){
		add_action('woocommerce_after_shop_loop_item_title', 'mydecor_template_loop_product_label', 1);
		add_action('woocommerce_before_shop_loop_item_title', 'mydecor_template_loop_product_thumbnail', 10);
		
		add_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_categories', 10);
		add_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_product_title', 20);
		add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 30);
		add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 40);
		add_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_product_sku', 50);
		add_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_short_description', 60); 
		add_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_add_to_cart', 70); 
		if( function_exists('mydecor_get_theme_options') && mydecor_get_theme_options('ts_product_hover_style') == 'hover-style-1' ){
			add_action('woocommerce_after_shop_loop_item_title', 'mydecor_template_loop_add_to_cart', 10004 );
		}
		remove_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_product_variable_color', 90);
		remove_all_filters('mydecor_loop_product_variable_color_number');
		
		wc_set_loop_prop( 'is_shortcode', false );
	}
}

/*** Change product query args ***/
function ts_filter_product_by_product_type( &$args = array(), $product_type = 'recent' ){
	switch( $product_type ){
		case 'sale':
			$args['post__in'] = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
		break;
		case 'featured':
			$args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
				'operator' => 'IN',
			);
		break;
		case 'best_selling':
			$args['meta_key'] 	= 'total_sales';
			$args['orderby'] 	= 'meta_value_num';
			$args['order'] 		= 'desc';
		break;
		case 'top_rated':
			$args['meta_key'] 	= '_wc_average_rating';
			$args['orderby'] 	= 'meta_value_num';
			$args['order'] 		= 'desc';
		break;
		case 'mixed_order':
			$args['orderby'] 	= 'rand';
		break;
		default: /* Recent */
			$args['orderby'] 	= 'date';
			$args['order'] 		= 'desc';
		break;
	}
}

/*** Get dicounted product ids ***/
function ts_get_product_deals_transient(){
	$key = 'all';
	if( defined('ICL_LANGUAGE_CODE') ){
		$key .= '-' . ICL_LANGUAGE_CODE;
	}
	$transient = get_transient('ts_product_deals_ids');
	if( $transient && isset($transient[$key]) && is_array($transient[$key]) ){
		return $transient[$key];
	}
	return false;
}

function ts_set_product_deals_transient( $value = array() ){
	$key = 'all';
	if( defined('ICL_LANGUAGE_CODE') ){
		$key .= '-' . ICL_LANGUAGE_CODE;
	}
	$transient = get_transient('ts_product_deals_ids');
	if( is_array($transient) ){
		$transient[$key] = $value;
	}
	else{
		$transient = array( $key => $value );
	}
	set_transient( 'ts_product_deals_ids', $transient, MONTH_IN_SECONDS );
}

add_action('wc_after_products_starting_sales', 'ts_delete_product_deals_transient');
add_action('wc_after_products_ending_sales', 'ts_delete_product_deals_transient');
add_action('woocommerce_delete_product_transients', 'ts_delete_product_deals_transient');
function ts_delete_product_deals_transient(){
	set_transient( 'ts_product_deals_ids', false, MONTH_IN_SECONDS );
}

function ts_get_product_deals_ids(){
	$product_ids = ts_get_product_deals_transient();
	if( !is_array($product_ids) ){
		global $post;
		$product_ids = array();
		$args = array(
			'post_type'				=> array('product', 'product_variation')
			,'post_status' 			=> 'publish'
			,'posts_per_page' 		=> -1
			,'meta_query' => array(
				array(
					'key'		=> '_sale_price_dates_to'
					,'value'	=> current_time( 'timestamp', true )
					,'compare'	=> '>'
					,'type'		=> 'numeric'
				)
				,array(
					'key'		=> '_sale_price_dates_from'
					,'value'	=> current_time( 'timestamp', true )
					,'compare'	=> '<'
					,'type'		=> 'numeric'
				)
			)
			,'tax_query'			=> array()
		);
		
		$products = new WP_Query( $args );
		
		if( $products->have_posts() ){
			while( $products->have_posts() ){
				$products->the_post();
				if( $post->post_type == 'product' ){
					$product_ids[] = $post->ID;
				}
				else{ /* Variation product */
					$product_ids[] = $post->post_parent;
				}
			}
		}
		$product_ids = array_unique($product_ids);
		ts_set_product_deals_transient($product_ids);
		wp_reset_postdata();
	}
	
	return $product_ids;
}

function ts_get_recently_viewed_products( $viewed_by_all_users = true ){
	$viewed_products = array();
	
	if( $viewed_by_all_users ){
		$saved_viewed_products = get_option('ts_recently_viewed_products', '');
		if( $saved_viewed_products ){
			$viewed_products = (array) explode( '|', $saved_viewed_products );
		}
	}
	else{
		if( !empty( $_COOKIE['ts_recently_viewed_products'] ) ){
			$viewed_products = (array) explode( '|', $_COOKIE['ts_recently_viewed_products'] );
		}
	}
	
	$viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );
	
	if( empty($viewed_products) ){
		$viewed_products = array(0); /* if empty, show nothing */
	}
	
	return $viewed_products;
}

/*** Product Counter ***/
if( !function_exists('ts_template_loop_time_deals') ){
	function ts_template_loop_time_deals(){
		global $product;
		$date_to = '';
		$date_from = '';
		if( $product->get_type() == 'variable' ){
			$children = $product->get_children();
			if( is_array($children) && count($children) > 0 ){
				foreach( $children as $children_id ){
					$date_to = get_post_meta($children_id, '_sale_price_dates_to', true);
					$date_from = get_post_meta($children_id, '_sale_price_dates_from', true);
					if( $date_to != '' ){
						break;
					}
				}
			}
		}
		else{
			$date_to = get_post_meta($product->get_id(), '_sale_price_dates_to', true);
			$date_from = get_post_meta($product->get_id(), '_sale_price_dates_from', true);
		}
		
		$current_time = current_time('timestamp', true);
		
		if( $date_to == '' || $date_from == '' || $date_from > $current_time || $date_to < $current_time ){
			return;
		}
		
		$seconds = $date_to - $current_time;
		
		ts_countdown( array( 'style' => 'inline', 'seconds' => $seconds ) );
	}
}

if( !function_exists('ts_daily_time_remain_html') ){
	function ts_daily_time_remain_html(){
		$current_time = current_time('timestamp');
		$total_seconds_of_day = 60 * 60 * 24;
		$pass_second = $current_time % $total_seconds_of_day;
		$remain_second = $total_seconds_of_day - $pass_second;
		ts_countdown( array( 'style' => 'inline', 'label' => __( 'Deals ends in:', 'themesky' ), 'seconds' => $remain_second ) );
	}
}

if( !function_exists('ts_countdown') ){
	function ts_countdown( $atts = array() ){
		$defaults = array(
				'style'				=> 'default' /* default / inline */
				,'label'			=> ''
				,'text_color_style'	=> 'text-default'
				,'seconds'			=> 0
			);
			
		$atts = wp_parse_args( $atts, $defaults );	
		
		extract( $atts );
		
		if( $seconds <= 0 ){
			return;
		}
		
		$delta = $seconds;
		
		$time_day = 60 * 60 * 24;
		$time_hour = 60 * 60;
		$time_minute = 60;
		
		$day = floor( $delta / $time_day );
		$delta -= $day * $time_day;
		
		$hour = floor( $delta / $time_hour );
		$delta -= $hour * $time_hour;
		
		$minute = floor( $delta / $time_minute );
		$delta -= $minute * $time_minute;
		
		if( $delta > 0 ){
			$second = $delta;
		}
		else{
			$second = 0;
		}
		
		$day = zeroise($day, 2);
		$hour = zeroise($hour, 2);
		$minute = zeroise($minute, 2);
		$second = zeroise($second, 2);
		
		$classes = array();
		$classes[] = 'style-' . $style;
		$classes[] = $text_color_style;
		
		$is_inline_style = $style == 'inline';
		?>
		<div class="ts-countdown <?php echo esc_attr( implode(' ', $classes) ); ?>">
			<?php if( $label ){ ?>
				<div class="label">
					<?php echo esc_html( $label ); ?>
				</div>
			<?php } ?>
			<div class="counter-wrapper days-<?php echo strlen($day); ?>">
				<div class="days <?php echo $day == '00' ? 'hidden' : ''; ?>">
					<div class="number-wrapper">
						<span class="number"><?php echo esc_html($day); ?></span>
					</div>
					<div class="ref-wrapper">
						<?php 
						if( $is_inline_style ){
							echo esc_html( _x('d', 'Countdown Timer', 'themesky') );
						}
						else{
							esc_html_e('Days', 'themesky');
						}
						?>
					</div>
				</div>
				<?php 
				if( $is_inline_style && $day != '00' ){
					echo '<span class="dots">:</span>';
				}
				?>
				<div class="hours">
					<div class="number-wrapper">
						<span class="number"><?php echo esc_html($hour); ?></span>
					</div>
					<div class="ref-wrapper">
						<?php 
						if( $is_inline_style ){
							echo esc_html( _x('h', 'Countdown Timer', 'themesky') );
						}
						else{
							esc_html_e('Hours', 'themesky');
						}
						?>
					</div>
				</div>
				<?php 
				if( $is_inline_style ){
					echo '<span class="dots">:</span>';
				}
				?>
				<div class="minutes">
					<div class="number-wrapper">
						<span class="number"><?php echo esc_html($minute); ?></span>
					</div>
					<div class="ref-wrapper">
						<?php 
						if( $is_inline_style ){
							echo esc_html( _x('m', 'Countdown Timer', 'themesky') );
						}
						else{
							esc_html_e('Mins', 'themesky');
						}
						?>
					</div>
				</div>
				<?php 
				if( $is_inline_style ){
					echo '<span class="dots">:</span>';
				}
				?>
				<div class="seconds">
					<div class="number-wrapper">
						<span class="number"><?php echo esc_html($second); ?></span>
					</div>
					<div class="ref-wrapper">
						<?php 
						if( $is_inline_style ){
							echo esc_html( _x('s', 'Countdown Timer', 'themesky') );
						}
						else{
							esc_html_e('Secs', 'themesky');
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}

/*** Product Availability Bar ***/
if( !function_exists('ts_product_availability_bar') ){
	function ts_product_availability_bar(){
		global $product;
		if( $product->get_type() != 'simple' ){
			return;
		}
		
		$total_sales = $product->get_total_sales();
		$stock_quantity = $product->get_stock_quantity();
		
		if( $stock_quantity ){
			$total = $total_sales + $stock_quantity;
			$percent = $stock_quantity * 100 / $total;
		?>
		<div class="availability-bar">
			<span class="sold">
				<?php esc_html_e('Sold:', 'themesky'); ?> 
				<span class="already-sold"><?php echo esc_html($total_sales) ?></span>
				<span class="sep">/</span>
				<span class="total"><?php echo esc_html($total) ?></span>
			</span>
			<div class="progress-bar">
				<span style="width:<?php echo number_format($percent, 2) ?>%"></span>
			</div>
		</div>
		<?php
		}
	}
}

/*** Check Crawler ***/
if( !function_exists('ts_crawler_detect') ){
	function ts_crawler_detect(){
		if( isset($_SERVER['HTTP_USER_AGENT']) ){
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			$crawlers = array(
				'Google' 			=> 'Google'
				,'MSN' 				=> 'msnbot'
				,'Rambler' 			=> 'Rambler'
				,'Yahoo' 			=> 'Yahoo'
				,'AbachoBOT' 		=> 'AbachoBOT'
				,'accoona' 			=> 'Accoona'
				,'AcoiRobot' 		=> 'AcoiRobot'
				,'ASPSeek' 			=> 'ASPSeek'
				,'CrocCrawler' 		=> 'CrocCrawler'
				,'Dumbot' 			=> 'Dumbot'
				,'FAST-WebCrawler' 	=> 'FAST-WebCrawler'
				,'GeonaBot' 		=> 'GeonaBot'
				,'Gigabot' 			=> 'Gigabot'
				,'Lycos spider' 	=> 'Lycos'
				,'MSRBOT' 			=> 'MSRBOT'
				,'Altavista robot' 	=> 'Scooter'
				,'AltaVista robot' 	=> 'Altavista'
				,'ID-Search Bot' 	=> 'IDBot'
				,'eStyle Bot' 		=> 'eStyle'
				,'Scrubby robot' 	=> 'Scrubby'
				,'Facebook' 		=> 'facebookexternalhit'
				,'robot' 			=> 'robot'
				,'spider' 			=> 'spider'
				,'crawler' 			=> 'crawler'
				,'curl' 			=> 'curl'
			);
			$crawlers_agents = implode('|', $crawlers);
			
			if( preg_match('/'.$crawlers_agents.'/i', $user_agent) ){
				return true;
			}
			return false;
		}
		return false;
	}
}

/*** Product - Blog Social Sharing ***/
if( !function_exists('ts_use_sharethis') ){
	function ts_use_sharethis(){
		if( !function_exists('mydecor_get_theme_options') ){
			return false;
		}
		$theme_options = mydecor_get_theme_options();
		$sharethis_key = '';
		if( is_singular('post') || is_singular('ts_portfolio') ){
			if( $theme_options['ts_blog_details_sharing_sharethis'] && $theme_options['ts_blog_details_sharing_sharethis_key'] ){
				$sharethis_key = $theme_options['ts_blog_details_sharing_sharethis_key'];
			}
		}
		if( is_singular('product') ){
			if( $theme_options['ts_prod_sharing_sharethis'] && $theme_options['ts_prod_sharing_sharethis_key'] ){
				$sharethis_key = $theme_options['ts_prod_sharing_sharethis_key'];
			}
		}
		return $sharethis_key;
	}
}

if( !function_exists('ts_template_social_sharing') ){
	function ts_template_social_sharing(){
		if( ts_use_sharethis() ){
			echo '<div class="ts-social-sharing">';
				echo '<span class="icon"></span><span>'.esc_html__('Share', 'themesky').'</span><span class="symbol">:</span>';
				echo '<div class="sharethis-inline-share-buttons"></div>';
			echo '</div>';
		}
		else{
			ob_start();
			include plugin_dir_path( __FILE__ ) . 'templates/social-sharing.php';
			$icons_html = ob_get_clean();
			echo apply_filters('ts_social_sharing_html', $icons_html);
		}
	}
}

add_action('wp_head', 'ts_add_sharethis_script');
if( !function_exists('ts_add_sharethis_script') ){
	function ts_add_sharethis_script(){
		$sharethis_key = ts_use_sharethis();
		if( $sharethis_key ){
		?>
		<script type='text/javascript' src='//platform-api.sharethis.com/js/sharethis.js#property=<?php echo esc_attr($sharethis_key) ?>&product=inline-share-buttons' async='async'></script>
		<?php
		}
	}
}