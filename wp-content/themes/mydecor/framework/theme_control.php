<?php 
/*** Template Redirect ***/
add_action('template_redirect', 'mydecor_template_redirect');
function mydecor_template_redirect(){
	global $wp_query, $post;
	
	/* Get Page Options */
	if( is_page() || is_tax( get_object_taxonomies( 'product' ) ) || is_post_type_archive('product') ){
		if( is_page() ){
			$page_id = $post->ID;
		}
		if( is_tax( get_object_taxonomies( 'product' ) ) || is_post_type_archive('product') ){
			$page_id = get_option('woocommerce_shop_page_id', 0);
		}
		$page_options = mydecor_set_global_page_options( $page_id );
		
		if( $page_options['ts_layout_fullwidth'] != 'default' ){
			mydecor_change_theme_options('ts_layout_fullwidth', $page_options['ts_layout_fullwidth']);
			if( $page_options['ts_layout_fullwidth'] ){
				mydecor_change_theme_options('ts_header_layout_fullwidth', $page_options['ts_header_layout_fullwidth']);
				mydecor_change_theme_options('ts_main_content_layout_fullwidth', $page_options['ts_main_content_layout_fullwidth']);
				mydecor_change_theme_options('ts_footer_layout_fullwidth', $page_options['ts_footer_layout_fullwidth']);
			}
		}
		
		if( $page_options['ts_layout_style'] != 'default' ){
			mydecor_change_theme_options('ts_layout_style', $page_options['ts_layout_style']);
		}
		
		if( $page_options['ts_header_layout'] != 'default' ){
			mydecor_change_theme_options('ts_header_layout', $page_options['ts_header_layout']);
		}
		
		if( $page_options['ts_breadcrumb_layout'] != 'default' ){
			mydecor_change_theme_options('ts_breadcrumb_layout', $page_options['ts_breadcrumb_layout']);
		}
		
		if( $page_options['ts_breadcrumb_bg_parallax'] != 'default' ){
			mydecor_change_theme_options('ts_breadcrumb_bg_parallax', $page_options['ts_breadcrumb_bg_parallax']);
		}
		
		if( trim($page_options['ts_bg_breadcrumbs']) != '' ){
			mydecor_change_theme_options('ts_bg_breadcrumbs', $page_options['ts_bg_breadcrumbs']);
		}
		
		if( trim($page_options['ts_logo']) != '' ){
			mydecor_change_theme_options('ts_logo', $page_options['ts_logo']);
		}
		
		if( trim($page_options['ts_logo_mobile']) != '' ){
			mydecor_change_theme_options('ts_logo_mobile', $page_options['ts_logo_mobile']);
		}
		
		if( trim($page_options['ts_logo_sticky']) != '' ){
			mydecor_change_theme_options('ts_logo_sticky', $page_options['ts_logo_sticky']);
		}
		
		if( $page_options['ts_menu_id'] || $page_options['ts_vertical_menu_id'] ){
			add_filter('wp_nav_menu_args', 'mydecor_filter_wp_nav_menu_args');
		}
		
		if( $page_options['ts_footer_block'] ){
			mydecor_change_theme_options('ts_footer_block', $page_options['ts_footer_block']);
		}
		
		if( $page_options['ts_header_transparent'] ){
			add_filter('body_class', function($classes) use ($page_options){
				$classes[] = 'header-transparent header-text-' . $page_options['ts_header_text_color'];
				return $classes;
			});
		}
	}
	
	/* Archive - Category product */
	if( is_tax( get_object_taxonomies( 'product' ) ) || is_post_type_archive('product') || (function_exists('dokan_is_store_page') && dokan_is_store_page()) ){
		mydecor_set_header_breadcrumb_layout_woocommerce_page( 'shop' );
		
		add_action('woocommerce_before_main_content', 'mydecor_remove_hooks_from_shop_loop');
		
		/* Update product category layout */
		if( is_tax('product_cat') ){
			$term = $wp_query->queried_object;
			if( !empty($term->term_id) ){
				$bg_breadcrumbs_id = get_term_meta($term->term_id, 'bg_breadcrumbs_id', true);
				$layout = get_term_meta($term->term_id, 'layout', true);
				$left_sidebar = get_term_meta($term->term_id, 'left_sidebar', true);
				$right_sidebar = get_term_meta($term->term_id, 'right_sidebar', true);
				
				if( $bg_breadcrumbs_id != '' ){
					$bg_breadcrumbs_src = wp_get_attachment_url( $bg_breadcrumbs_id );
					if( $bg_breadcrumbs_src !== false ){
						mydecor_change_theme_options('ts_bg_breadcrumbs', $bg_breadcrumbs_src);
					}
				}
				if( $layout != '' ){
					mydecor_change_theme_options('ts_prod_cat_layout', $layout);
				}
				if( $left_sidebar != '' ){
					mydecor_change_theme_options('ts_prod_cat_left_sidebar', $left_sidebar);
				}
				if( $right_sidebar != '' ){
					mydecor_change_theme_options('ts_prod_cat_right_sidebar', $right_sidebar);
				}
			}
		}
	}
	
	/* Single post */
	if( is_singular('post') ){
		$post_data = array();
		$post_custom = get_post_custom();
		foreach( $post_custom as $key => $value ){
			if( isset($value[0]) ){
				$post_data[$key] = $value[0];
			}
		}
		
		if( isset($post_data['ts_post_layout']) && $post_data['ts_post_layout'] != '0' ){
			mydecor_change_theme_options('ts_blog_details_layout', $post_data['ts_post_layout']);
		}
		if( isset($post_data['ts_post_left_sidebar']) && $post_data['ts_post_left_sidebar'] != '0' ){
			mydecor_change_theme_options('ts_blog_details_left_sidebar', $post_data['ts_post_left_sidebar']);
		}
		if( isset($post_data['ts_post_right_sidebar']) && $post_data['ts_post_right_sidebar'] != '0' ){
			mydecor_change_theme_options('ts_blog_details_right_sidebar', $post_data['ts_post_right_sidebar']);
		}
		if( isset($post_data['ts_bg_breadcrumbs']) && $post_data['ts_bg_breadcrumbs'] != '' ){
			mydecor_change_theme_options('ts_bg_breadcrumbs', $post_data['ts_bg_breadcrumbs']);
		}
	}
	
	/* Single product */
	if( is_singular('product') ){
		/* Remove hooks on Related and Up-Sell products */
		add_action('woocommerce_before_main_content', 'mydecor_remove_hooks_from_shop_loop');
		
		$theme_options = mydecor_get_theme_options();
	
		$prod_data = array();
		$post_custom = get_post_custom();
		foreach( $post_custom as $key => $value ){
			if( isset($value[0]) ){
				$prod_data[$key] = $value[0];
			}
		}
		if( isset($prod_data['ts_prod_layout']) && $prod_data['ts_prod_layout'] != '0' ){
			mydecor_change_theme_options('ts_prod_layout', $prod_data['ts_prod_layout']);
		}
		if( isset($prod_data['ts_prod_left_sidebar']) && $prod_data['ts_prod_left_sidebar'] != '0' ){
			mydecor_change_theme_options('ts_prod_left_sidebar', $prod_data['ts_prod_left_sidebar']);
		}
		if( isset($prod_data['ts_prod_right_sidebar']) && $prod_data['ts_prod_right_sidebar'] != '0' ){
			mydecor_change_theme_options('ts_prod_right_sidebar', $prod_data['ts_prod_right_sidebar']);
		}
		
		if( mydecor_get_theme_options('ts_prod_layout') != '0-1-0' ){
			mydecor_change_theme_options('ts_prod_summary_2_columns', 0);
			$theme_options['ts_prod_summary_2_columns'] = 0;
		}
		
		if( !$theme_options['ts_prod_thumbnail'] ){
			remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
		}
		
		if( $theme_options['ts_prod_title'] && $theme_options['ts_prod_title_in_content'] ){
			mydecor_change_theme_options('ts_prod_title', 0); /* remove title above breadcrumb */
			add_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 1);
		}
		
		if( !$theme_options['ts_prod_label'] ){
			remove_action('woocommerce_product_thumbnails', 'mydecor_template_loop_product_label', 99);
		}
		
		if( !$theme_options['ts_prod_rating'] ){
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_rating', 5);
		}
		
		if( !$theme_options['ts_prod_availability'] || $theme_options['ts_prod_summary_2_columns'] ){
			remove_action('woocommerce_single_product_summary', 'mydecor_template_single_availability', 10);
		}
		
		if( $theme_options['ts_prod_summary_2_columns'] ){
			remove_action('woocommerce_single_product_summary', 'mydecor_template_single_meta', 60);
			add_action('woocommerce_single_product_summary', 'mydecor_template_single_meta', 29);
			
			mydecor_change_theme_options('ts_prod_wfbt_in_summary', 0);
			$theme_options['ts_prod_wfbt_in_summary'] = 0;
		}
		
		if( !$theme_options['ts_prod_sharing'] || $theme_options['ts_prod_summary_2_columns'] ){
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 15);
		}
		
		if( $theme_options['ts_prod_sharing'] && $theme_options['ts_prod_summary_2_columns'] ){
			add_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 42);
		}
		
		if( !$theme_options['ts_prod_price'] ){
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 27);
			remove_action('woocommerce_single_variation', 'woocommerce_single_variation', 10);
		}
		
		if( !$theme_options['ts_prod_excerpt'] ){
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);
		}
		
		if( !$theme_options['ts_prod_add_to_cart'] || $theme_options['ts_enable_catalog_mode'] ){
			$terms        = get_the_terms( $post->ID, 'product_type' );
			$product_type = ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
			if( $product_type != 'variable' ){
				remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
			}
			else{
				remove_action('woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20);
			}
		}
		
		if( !$theme_options['ts_prod_upsells'] ){
			remove_action('woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15);
		}
		
		if( !$theme_options['ts_prod_related'] ){
			remove_action('woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);
		}
		
		/* Breadcrumb */
		if( isset($prod_data['ts_bg_breadcrumbs']) && $prod_data['ts_bg_breadcrumbs'] != '' ){
			mydecor_change_theme_options('ts_bg_breadcrumbs', $prod_data['ts_bg_breadcrumbs']);
		}
		
		/* Frequently Bought Together */
		if( $theme_options['ts_prod_wfbt_in_summary'] && function_exists('YITH_WFBT_Frontend') ){
			$wfbt_instance = YITH_WFBT_Frontend();
			remove_action( 'woocommerce_after_single_product_summary', array( $wfbt_instance, 'add_bought_together_form' ), 1 );
			add_action( 'woocommerce_single_product_summary', array( $wfbt_instance, 'add_bought_together_form' ), 45 );
		}
		
		/* Add extra classes to post */
		add_action('woocommerce_before_single_product', 'mydecor_woocommerce_before_single_product');
	}
	
	/* Single Portfolio */
	if( is_singular('ts_portfolio') ){
		$portfolio_data = array();
		$post_custom = get_post_custom();
		foreach( $post_custom as $key => $value ){
			if( isset($value[0]) ){
				$portfolio_data[$key] = $value[0];
			}
		}
		
		if( isset($portfolio_data['ts_portfolio_custom_field']) && $portfolio_data['ts_portfolio_custom_field'] == 1 ){
			if( isset($portfolio_data['ts_portfolio_custom_field_title']) ){
				mydecor_change_theme_options('ts_portfolio_custom_field_title', $portfolio_data['ts_portfolio_custom_field_title']);
			}
			if( isset($portfolio_data['ts_portfolio_custom_field_content']) ){
				mydecor_change_theme_options('ts_portfolio_custom_field_content', $portfolio_data['ts_portfolio_custom_field_content']);
			}
		}
		
		if( mydecor_get_theme_options('ts_portfolio_thumbnail_style') == 'gallery' && wp_is_mobile() ){
			mydecor_change_theme_options('ts_portfolio_thumbnail_style', 'slider');
		}
	}
	
	/* WooCommerce - Other pages */
	if( class_exists('WooCommerce') ){
		if( is_cart() ){
			mydecor_set_header_breadcrumb_layout_woocommerce_page( 'cart' );
			
			add_action('woocommerce_before_cart', 'mydecor_remove_hooks_from_shop_loop');
		}
		
		if( is_checkout() ){
			mydecor_set_header_breadcrumb_layout_woocommerce_page( 'checkout' );
		}
		
		if( is_account_page() ){
			mydecor_set_header_breadcrumb_layout_woocommerce_page( 'myaccount' );
		}
	}

	/* Header Cart - Wishlist */
	if( !class_exists('WooCommerce') ){
		mydecor_change_theme_options('ts_enable_tiny_shopping_cart', 0);
	}
	
	if( !class_exists('WooCommerce') || !class_exists('YITH_WCWL') ){
		mydecor_change_theme_options('ts_enable_tiny_wishlist', 0);
	}
	
	/* Right to left */
	if( is_rtl() ){
		mydecor_change_theme_options('ts_enable_rtl', 1);
	}
	
	/* Remove background image if not necessary */
	$load_bg = true;
	if( mydecor_get_theme_options('ts_layout_fullwidth') ){
		$load_bg = false;
	}
	if( is_page() && $load_bg && $layout_style = mydecor_get_page_options('ts_layout_style') ){
		if( $layout_style == 'wide' || ( $layout_style == 'default' && mydecor_get_theme_options('ts_layout_style') == 'wide' ) ){
			$load_bg = false;
		}
	}
	
	if( !$load_bg ){
		add_filter('theme_mod_background_image', '__return_empty_string');
	}
}

function mydecor_filter_wp_nav_menu_args( $args ){
	global $post;
	if( is_page() && !is_admin() && !empty($args['theme_location']) ){
		if( $args['theme_location'] == 'primary' ){
			$menu = get_post_meta($post->ID, 'ts_menu_id', true);
			if( $menu ){
				$args['menu'] = $menu;
			}
		}
		
		if( $args['theme_location'] == 'vertical' ){
			$menu = get_post_meta($post->ID, 'ts_vertical_menu_id', true);
			if( $menu ){
				$args['menu'] = $menu;
			}
		}
	}
	return $args;
}

add_filter('single_template', 'mydecor_change_single_portfolio_template');
function mydecor_change_single_portfolio_template( $single_template ){
	
	if( is_singular('ts_portfolio') && locate_template('single-portfolio.php') ){
		$single_template = locate_template('single-portfolio.php');
	}
	
	return $single_template;
}

function mydecor_remove_hooks_from_shop_loop(){
	$theme_options = mydecor_get_theme_options();
	
	if( ! $theme_options['ts_prod_cat_thumbnail'] ){
		remove_action('woocommerce_before_shop_loop_item_title', 'mydecor_template_loop_product_thumbnail', 10);
	}
	if( ! $theme_options['ts_prod_cat_label'] ){
		remove_action('woocommerce_after_shop_loop_item_title', 'mydecor_template_loop_product_label', 1);
	}
	
	if( ! $theme_options['ts_prod_cat_cat'] ){
		remove_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_categories', 10);
	}
	if( ! $theme_options['ts_prod_cat_title'] ){
		remove_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_product_title', 20);
	}
	if( ! $theme_options['ts_prod_cat_rating'] ){
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_rating', 30);
	}
	if( ! $theme_options['ts_prod_cat_price'] ){
		remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 40);
	}
	if( ! $theme_options['ts_prod_cat_sku'] ){
		remove_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_product_sku', 50);
	}
	if( ! $theme_options['ts_prod_cat_desc'] ){
		remove_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_short_description', 60);
	}
	if( ! $theme_options['ts_prod_cat_add_to_cart'] ){
		remove_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_add_to_cart', 70); 
		remove_action('woocommerce_after_shop_loop_item_title', 'mydecor_template_loop_add_to_cart', 10004 );
	}
	
	if( $theme_options['ts_prod_cat_color_swatch'] ){
		add_action('woocommerce_after_shop_loop_item', 'mydecor_template_loop_product_variable_color', 90);
		$number_color_swatch = absint( $theme_options['ts_prod_cat_number_color_swatch'] );
		add_filter('mydecor_loop_product_variable_color_number', function() use ($number_color_swatch){
			return $number_color_swatch;
		});
	}
	
	if( in_array( $theme_options['ts_prod_cat_loading_type'], array('infinity-scroll', 'load-more-button') ) ){
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
		mydecor_change_theme_options('ts_prod_cat_per_page_dropdown', 0);
	}
}

function mydecor_set_header_breadcrumb_layout_woocommerce_page( $page = 'shop' ){
	/* Header Layout */
	$header_layout = get_post_meta(wc_get_page_id( $page ), 'ts_header_layout', true);
	if( $header_layout != 'default' && $header_layout != '' ){
		mydecor_change_theme_options('ts_header_layout', $header_layout);
	}
	
	/* Breadcrumb Layout */
	$breadcrumb_layout = get_post_meta(wc_get_page_id( $page ), 'ts_breadcrumb_layout', true);
	if( $breadcrumb_layout != 'default' && $breadcrumb_layout != '' ){
		mydecor_change_theme_options('ts_breadcrumb_layout', $breadcrumb_layout);
	}
}

function mydecor_woocommerce_before_single_product(){
	add_filter('post_class', 'mydecor_single_product_post_class_filter');
}

function mydecor_single_product_post_class_filter( $classes ){
	global $product;
	
	$theme_options = mydecor_get_theme_options();
	
	if( $theme_options['ts_prod_tabs_show_content_default'] ){
		$classes[] = 'show-tabs-content-default';
	}
	
	if( $theme_options['ts_prod_summary_2_columns'] ){
		$classes[] = 'summary-2-columns';
	}
	
	if( $theme_options['ts_prod_size_chart'] && $theme_options['ts_prod_size_chart_style'] == 'popup' ){
		$classes[] = 'summary-has-size-chart';
	}
	
	if( $theme_options['ts_prod_wfbt_in_summary'] ){
		$classes[] = 'wfbt-in-summary';
	}
	
	if( !$theme_options['ts_prod_add_to_cart'] || $theme_options['ts_enable_catalog_mode'] ){
		$classes[] = 'no-addtocart';
	}
	if( !$theme_options['ts_prod_thumbnail'] ){
		$classes[] = 'no-product-thumbnail';
	}
	remove_filter('post_class', 'mydecor_single_product_post_class_filter');
	return $classes;
}
?>