<?php 
if( !class_exists('TS_Importer') ){
	class TS_Importer{

		function __construct(){
			add_filter( 'ocdi/plugin_page_title', array($this, 'import_notice') );
			
			add_filter( 'pt-ocdi/plugin_page_setup', array($this, 'import_page_setup') );
			add_action( 'pt-ocdi/before_widgets_import', array($this, 'before_widgets_import') );
			add_filter( 'pt-ocdi/import_files', array($this, 'import_files') );
			add_filter( 'pt-ocdi/regenerate_thumbnails_in_content_import', '__return_false' );
			add_action( 'pt-ocdi/after_import', array($this, 'after_import_setup') );
			
			add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );
		}
		
		function import_notice( $plugin_title ){
			$allowed_html = array(
				'a' => array( 'href' => array(), 'target' => array() )
			);
			ob_start();
			?>
			<div class="ts-ocdi-notice-info">
				<p>
					<i class="fas fa-exclamation-circle"></i>
					<span><?php echo wp_kses( __('If you have any problem with importer, please read this article <a href="https://ocdi.com/import-issues/" target="_blank">https://ocdi.com/import-issues/</a> and check your hosting configuration, or contact our support team here <a href="https://skygroup.ticksy.com/" target="_blank">https://skygroup.ticksy.com/</a>.', 'themesky'), $allowed_html ); ?></span>
				</p>
			</div>
			<?php
			$plugin_title .= ob_get_clean();
			return $plugin_title;
		}
		
		function import_page_setup( $default_settings ){
			$default_settings['parent_slug'] = 'themes.php';
			$default_settings['page_title']  = esc_html__( 'MyDecor - Import Demo Content' , 'themesky' );
			$default_settings['menu_title']  = esc_html__( 'MyDecor Importer' , 'themesky' );
			$default_settings['capability']  = 'import';
			$default_settings['menu_slug']   = 'mydecor-importer';
			return $default_settings;
		}
		
		function before_widgets_import(){
			global $wp_registered_sidebars;
			$file_path = dirname(__FILE__) . '/data/custom_sidebars.txt';
			if( file_exists($file_path) ){
				$file_url = plugin_dir_url(__FILE__) . 'data/custom_sidebars.txt';
				$custom_sidebars = wp_remote_get( $file_url );
				$custom_sidebars = maybe_unserialize( trim( $custom_sidebars['body'] ) );
				update_option('ts_custom_sidebars', $custom_sidebars);
				
				if( is_array($custom_sidebars) && !empty($custom_sidebars) ){
					foreach( $custom_sidebars as $name ){
						$custom_sidebar = array(
											'name' 			=> ''.$name.''
											,'id' 			=> sanitize_title($name)
											,'description' 	=> ''
											,'class'		=> 'ts-custom-sidebar'
										);
						if( !isset($wp_registered_sidebars[$custom_sidebar['id']]) ){
							$wp_registered_sidebars[$custom_sidebar['id']] = $custom_sidebar;
						}
					}
				}
			}
		}
		
		function import_files(){
			return array(
				array(
					'import_file_name'           => 'Demo Import',
					'import_file_url'            => plugin_dir_url( __FILE__ ) . 'data/content.xml',
					'import_widget_file_url'     => plugin_dir_url( __FILE__ ) . 'data/widget_data.wie',
					'import_redux'               => array(
						array(
							'file_url'    => plugin_dir_url( __FILE__ ) . 'data/redux.json',
							'option_name' => 'mydecor_theme_options',
						),
					)
				)
			);
		}
		
		function after_import_setup(){
			set_time_limit(0);
			$this->woocommerce_settings();
			$this->menu_locations();
			$this->set_homepage();
			$this->import_revslider();
			$this->change_url();
			$this->set_elementor_site_settings();
			$this->update_product_category_id_in_homepage_content();
			$this->update_mega_menu_content();
			$this->update_footer_content();
			$this->delete_transients();
			$this->update_woocommerce_lookup_table();
		}
		
		/* WooCommerce Settings */
		function woocommerce_settings(){
			$woopages = array(
				'woocommerce_shop_page_id' 			=> 'Shop'
				,'woocommerce_cart_page_id' 		=> 'Cart'
				,'woocommerce_checkout_page_id' 	=> 'Checkout'
				,'woocommerce_myaccount_page_id' 	=> 'My Account'
				,'yith_wcwl_wishlist_page_id' 		=> 'Wishlist'
			);
			foreach( $woopages as $woo_page_name => $woo_page_title ) {
				$woopage = get_page_by_title( $woo_page_title );
				if( isset( $woopage->ID ) && $woopage->ID ) {
					update_option($woo_page_name, $woopage->ID);
				}
			}
			
			if( class_exists('YITH_Woocompare') ){
				update_option('yith_woocompare_compare_button_in_products_list', 'yes');
			}
			
			if( class_exists('YITH_WCWL') ){
				update_option('yith_wcwl_show_on_loop', 'yes');
			}

			if( class_exists('WC_Admin_Notices') ){
				WC_Admin_Notices::remove_notice('install');
			}
			delete_transient( '_wc_activation_redirect' );
			
			flush_rewrite_rules();
		}
		
		/* Menu Locations */
		function menu_locations(){
			$locations = get_theme_mod( 'nav_menu_locations' );
			$menus = wp_get_nav_menus();

			if( $menus ){
				foreach( $menus as $menu ){
					if( $menu->name == 'Menu' ){
						$locations['primary'] = $menu->term_id;
					}
					if( $menu->name == 'Categories' ){
						$locations['vertical'] = $menu->term_id;
					}
					if( $menu->name == 'Menu mobile' ){
						$locations['mobile'] = $menu->term_id;
					}
				}
			}
			set_theme_mod( 'nav_menu_locations', $locations );
		}
		
		/* Set Homepage */
		function set_homepage(){
			$homepage = get_page_by_title( 'Home 01' );
			if( isset( $homepage->ID ) ){
				update_option('show_on_front', 'page');
				update_option('page_on_front', $homepage->ID);
			}
		}
		
		/* Import Revolution Slider */
		function import_revslider(){
			if ( class_exists( 'RevSliderSliderImport' ) ) {
				$rev_directory = dirname(__FILE__) . '/data/revslider/';
			
				foreach( glob( $rev_directory . '*.zip' ) as $file ){
					$import = new RevSliderSliderImport();
					$import->import_slider(true, $file);  
				}
			}
		}
		
		/* Change url */
		function change_url(){
			global $wpdb;
			$wp_prefix = $wpdb->prefix;
			$import_url = 'https://demo.theme-sky.com/mydecor-import';
			$site_url = get_option( 'siteurl', '' );
			$wpdb->query("update `{$wp_prefix}posts` set `guid` = replace(`guid`, '{$import_url}', '{$site_url}');");
			$wpdb->query("update `{$wp_prefix}posts` set `post_content` = replace(`post_content`, '{$import_url}', '{$site_url}');");
			$wpdb->query("update `{$wp_prefix}posts` set `post_title` = replace(`post_title`, '{$import_url}', '{$site_url}') where post_type='nav_menu_item';");
			$wpdb->query("update `{$wp_prefix}postmeta` set `meta_value` = replace(`meta_value`, '{$import_url}', '{$site_url}');");
			$wpdb->query("update `{$wp_prefix}postmeta` set `meta_value` = replace(`meta_value`, '" . str_replace( '/', '\\\/', $import_url ) . "', '" . str_replace( '/', '\\\/', $site_url ) . "') where `meta_key` = '_elementor_data';");
			
			$option_name = 'mydecor_theme_options';
			$option_ids = array(
						'ts_logo'
						,'ts_logo_mobile'
						,'ts_logo_sticky'
						,'ts_favicon'
						,'ts_custom_loading_image'
						,'ts_bg_breadcrumbs'
						,'ts_prod_placeholder_img'
						);
			$theme_options = get_option($option_name);
			if( is_array($theme_options) ){
				foreach( $option_ids as $option_id ){
					if( isset($theme_options[$option_id]) ){
						$theme_options[$option_id] = str_replace($import_url, $site_url, $theme_options[$option_id]);
					}
				}
				update_option($option_name, $theme_options);
			}
			
			/* Slider Revolution */
			if ( class_exists( 'RevSliderSliderImport' ) ) {
				$slides = $wpdb->get_results('select * from '.$wp_prefix.'revslider_slides');
				if( is_array($slides) ){
					foreach( $slides as $slide ){
						$layers = json_decode($slide->layers);
						if( is_object($layers) ){
							foreach( $layers as $key => $layer ){
								if( isset($layers->$key->actions->action) && is_array($layers->$key->actions->action) ){
									foreach( $layers->$key->actions->action as $k => $a ){
										if( isset($layers->$key->actions->action[$k]->image_link) ){
											$layers->$key->actions->action[$k]->image_link = str_replace($import_url, $site_url, $layers->$key->actions->action[$k]->image_link);
										}
									}
								}
							}
						}
						
						$layers = addslashes(json_encode($layers));
						
						$wpdb->query( "update `{$wp_prefix}revslider_slides` set `layers`='{$layers}' where `id`={$slide->id}" );
					}
				}
			}
		}
		
		/* Set Elementor Site Settings */
		function set_elementor_site_settings(){
			$id = 0;
			
			$args = array(
				'post_type' 		=> 'elementor_library'
				,'post_status' 		=> 'public'
				,'posts_per_page'	=> 1
				,'orderby'			=> 'date'
				,'order'			=> 'ASC' /* Date is not changed when import. Use imported post */
			);
			
			$posts = new WP_Query( $args );
			if( $posts->have_posts() ){
				$id = $posts->post->ID;
				update_option('elementor_active_kit', $id);
			}
			
			if( $id ){ /* Fixed width, space, ... if query does not return the imported post */
				$page_settings = get_post_meta($id, '_elementor_page_settings', true);
			
				if( !is_array($page_settings) ){
					$page_settings = array();
				}
					
				if( !isset($page_settings['container_width']) ){
					$page_settings['container_width'] = array();
				}
				
				$page_settings['container_width']['unit'] = 'px';
				$page_settings['container_width']['size'] = 1320;
				$page_settings['container_width']['sizes'] = array();
				
				if( !isset($page_settings['space_between_widgets']) ){
					$page_settings['space_between_widgets'] = array();
				}
				
				$page_settings['space_between_widgets']['unit'] = 'px';
				$page_settings['space_between_widgets']['size'] = 20;
				$page_settings['space_between_widgets']['sizes'] = array();
				
				$page_settings['page_title_selector'] = 'h1.entry-title';
				$page_settings['stretched_section_container'] = '#main';
				
				update_post_meta($id, '_elementor_page_settings', $page_settings);
			}
			
			/* Use color, font from theme */
			update_option('elementor_disable_color_schemes', 'yes');
			update_option('elementor_disable_typography_schemes', 'yes');
		}
		
		/* Update Product Category Id In Homepage Content */
		function update_product_category_id_in_homepage_content(){
			global $wpdb;
			$wp_prefix = $wpdb->prefix;
			
			$pages = array(
					'Home 01'	=> array(
							array(
								'612,613,636,614,629,625,654,615,619,616,620,630'
								,array( 'Furniture', 'Outdoor', 'Bed & Bath', 'Decor & Pillows', 'Rugs', 'Lighting', 'Renovation', 'Appliances', 'Kitchen', 'Baby & Kids', 'Offices', 'Organization' )
								,'ids'
							)
							,array(
								'612,613,614,615,636,616'
								,array( 'Furniture', 'Outdoor', 'Decor & Pillows', 'Appliances', 'Bed & Bath', 'Baby & Kids' )
								,'product_cats'
							)
					)
					,'Home 02'	=> array(
							array(
								'612'
								,array( 'Furniture' )
								,'parent'
							)
							,array(
								'613'
								,array( 'Outdoor' )
								,'parent'
							)
							,array(
								'614'
								,array( 'Decor & Pillows' )
								,'parent'
							)
							,array(
								'615'
								,array( 'Appliances' )
								,'parent'
							)
							,array(
								'636'
								,array( 'Bed & Bath' )
								,'parent'
							)
							,array(
								'616'
								,array( 'Baby & Kids' )
								,'parent'
							)
							,array(
								'612,613,614,615'
								,array( 'Furniture', 'Outdoor', 'Decor & Pillows', 'Appliances' )
								,'product_cats'
							)
					)
					,'Home 03'	=> array(
							array(
								'612'
								,array( 'Furniture' )
								,'parent'
							)
							,array(
								'613'
								,array( 'Outdoor' )
								,'parent'
							)
							,array(
								'614'
								,array( 'Decor & Pillows' )
								,'parent'
							)
							,array(
								'615'
								,array( 'Appliances' )
								,'parent'
							)
							,array(
								'636'
								,array( 'Bed & Bath' )
								,'parent'
							)
							,array(
								'616'
								,array( 'Baby & Kids' )
								,'parent'
							)
							,array(
								'612,613,614,615'
								,array( 'Furniture', 'Outdoor', 'Decor & Pillows', 'Appliances' )
								,'product_cats'
							)
					)
					,'Home 04'	=> array(
							array(
								'612'
								,array( 'Furniture' )
								,'parent'
							)
							,array(
								'613'
								,array( 'Outdoor' )
								,'parent'
							)
							,array(
								'614'
								,array( 'Decor & Pillows' )
								,'parent'
							)
							,array(
								'615'
								,array( 'Appliances' )
								,'parent'
							)
							,array(
								'636'
								,array( 'Bed & Bath' )
								,'parent'
							)
							,array(
								'616'
								,array( 'Baby & Kids' )
								,'parent'
							)
							,array(
								'612,613,614,615'
								,array( 'Furniture', 'Outdoor', 'Decor & Pillows', 'Appliances' )
								,'product_cats'
							)
					)
					,'Home 05'	=> array(
							array(
								'612,613,636,614,629,625,654,615,619,616,620,630'
								,array( 'Furniture', 'Outdoor', 'Bed & Bath', 'Decor & Pillows', 'Rugs', 'Lighting', 'Renovation', 'Appliances', 'Kitchen', 'Baby & Kids', 'Offices', 'Organization' )
								,'ids'
							)
							,array(
								'612,613,614,615'
								,array( 'Furniture', 'Outdoor', 'Decor & Pillows', 'Appliances' )
								,'product_cats'
							)
					)
					,'Home 06'	=> array(
							array(
								'612,613,636,614,629,625,654,615,619,616,620,630'
								,array( 'Furniture', 'Outdoor', 'Bed & Bath', 'Decor & Pillows', 'Rugs', 'Lighting', 'Renovation', 'Appliances', 'Kitchen', 'Baby & Kids', 'Offices', 'Organization' )
								,'ids'
							)
							,array(
								'612,613,614,615,636,616'
								,array( 'Furniture', 'Outdoor', 'Decor & Pillows', 'Appliances', 'Bed & Bath', 'Baby & Kids' )
								,'product_cats'
							)
					)
			);
			
			$loaded_categories = array();
			
			foreach( $pages as $page_title => $cat_ids_names ){
				$page = get_page_by_title( $page_title );
				if( is_object( $page ) ){
					foreach( $cat_ids_names as $cat_id_name ){
						$key = isset($cat_id_name[2]) ? $cat_id_name[2] : 'ids';
						
						$old_ids = explode(',', $cat_id_name[0]);
						
						$new_ids = array();
						foreach( $cat_id_name[1] as $cat_name ){
							$loaded_id = array_search($cat_name, $loaded_categories);
							if( $loaded_id ){
								$new_ids[] = $loaded_id;
							}
							else{
								$cat = get_term_by('name', $cat_name, 'product_cat');
								if( isset($cat->term_id) ){
									$new_ids[] = $cat->term_id;
									$loaded_categories[$cat->term_id] = $cat_name;
								}
							}
						}
						
						if( $key == 'parent' || $key == 'parent_cat' ){ /* not multi */
							$old_string = '"' . $key . '":"' . implode('', $old_ids) . '"';
							$new_string = '"' . $key . '":"' . implode('', $new_ids) . '"';
						}
						else{
							$old_string = '"' . $key . '":["' . implode('","', $old_ids) . '"]';
							$new_string = '"' . $key . '":["' . implode('","', $new_ids) . '"]';
						}
						
						$wpdb->query("update `{$wp_prefix}postmeta` set `meta_value` = replace(`meta_value`, '" . $old_string . "', '" . $new_string . "') where `meta_key` = '_elementor_data' and post_id=" . $page->ID . ";");
					}
				}
			}
		}
		
		/* Update Mega Menu Content */
		function update_mega_menu_content(){
			global $wpdb;
			$wp_prefix = $wpdb->prefix;
			
			$mega_menus = array(
				'Product Menu'	=> array(289 => 'Menu shop layouts', 684 => 'Menu product tyles', 609 => 'Menu product details layouts 01', 685 => 'Menu product details layouts 02', 652 => 'Menu product 01')
				,'Shop Menu'	=> array(289 => 'Menu shop layouts', 684 => 'Menu product tyles')
				,'Page Menu'	=> array(309 => 'Menu page 01', 310 => 'Menu page 02')
			);
			
			foreach( $mega_menus as $title => $menus ){
				$mega_menu_post = get_page_by_title( $title, OBJECT, 'ts_mega_menu' );
				if( is_object( $mega_menu_post ) ){
					foreach( $menus as $old_id => $menu_name ){
						$menu = get_term_by( 'name', $menu_name, 'nav_menu' );
						if( isset($menu->term_id) ){
							$old_string = '"nav_menu":"' . $old_id . '"';
							$new_string = '"nav_menu":"' . $menu->term_id . '"';
							$wpdb->query("update `{$wp_prefix}postmeta` set `meta_value` = replace(`meta_value`, '" . $old_string . "', '" . $new_string . "') where `meta_key` = '_elementor_data' and post_id=" . $mega_menu_post->ID . ";");
						}
					}
				}
			}
		}
		
		/* Update Footer Content */
		function update_footer_content(){
			global $wpdb;
			$wp_prefix = $wpdb->prefix;
			
			$footers = array(
					'Footer 01'	=> array(
							array(
								'612'
								,array( 'Furniture' )
								,'parent'
							)
							,array(
								'613'
								,array( 'Outdoor' )
								,'parent'
							)
							,array(
								'614'
								,array( 'Decor & Pillows' )
								,'parent'
							)
							,array(
								'615'
								,array( 'Appliances' )
								,'parent'
							)
							,array(
								'636'
								,array( 'Bed & Bath' )
								,'parent'
							)
							,array(
								'616'
								,array( 'Baby & Kids' )
								,'parent'
							)
					)
					,'Footer 06'	=> array(
							array(
								'612'
								,array( 'Furniture' )
								,'parent'
							)
							,array(
								'613'
								,array( 'Outdoor' )
								,'parent'
							)
							,array(
								'614'
								,array( 'Decor & Pillows' )
								,'parent'
							)
							,array(
								'615'
								,array( 'Appliances' )
								,'parent'
							)
							,array(
								'636'
								,array( 'Bed & Bath' )
								,'parent'
							)
							,array(
								'616'
								,array( 'Baby & Kids' )
								,'parent'
							)
					)
			);
			
			$loaded_categories = array();
			
			foreach( $footers as $title => $cat_ids_names ){
				$footer_post = get_page_by_title( $title, OBJECT, 'ts_footer_block' );
				if( is_object( $footer_post ) ){
					foreach( $cat_ids_names as $cat_id_name ){
						$key = isset($cat_id_name[2]) ? $cat_id_name[2] : 'ids';
						
						$old_ids = explode(',', $cat_id_name[0]);
						
						$new_ids = array();
						foreach( $cat_id_name[1] as $cat_name ){
							$loaded_id = array_search($cat_name, $loaded_categories);
							if( $loaded_id ){
								$new_ids[] = $loaded_id;
							}
							else{
								$cat = get_term_by('name', $cat_name, 'product_cat');
								if( isset($cat->term_id) ){
									$new_ids[] = $cat->term_id;
									$loaded_categories[$cat->term_id] = $cat_name;
								}
							}
						}
						
						if( $key == 'parent' || $key == 'parent_cat' ){ /* not multi */
							$old_string = '"' . $key . '":"' . implode('', $old_ids) . '"';
							$new_string = '"' . $key . '":"' . implode('', $new_ids) . '"';
						}
						else{
							$old_string = '"' . $key . '":["' . implode('","', $old_ids) . '"]';
							$new_string = '"' . $key . '":["' . implode('","', $new_ids) . '"]';
						}
						
						$wpdb->query("update `{$wp_prefix}postmeta` set `meta_value` = replace(`meta_value`, '" . $old_string . "', '" . $new_string . "') where `meta_key` = '_elementor_data' and post_id=" . $footer_post->ID . ";");
					}
				}
			}
			
			$footers = array(
				'Footer 04'	=> array(677 => 'Polices', 678 => 'Informations', 679 => 'Address')
			);
			
			foreach( $footers as $title => $footer ){
				$footer_post = get_page_by_title( $title, OBJECT, 'ts_footer_block' );
				if( is_object( $footer_post ) ){
					foreach( $footer as $old_id => $menu_name ){
						$menu = get_term_by( 'name', $menu_name, 'nav_menu' );
						if( isset($menu->term_id) ){
							$old_string = '"nav_menu":"' . $old_id . '"';
							$new_string = '"nav_menu":"' . $menu->term_id . '"';
							$wpdb->query("update `{$wp_prefix}postmeta` set `meta_value` = replace(`meta_value`, '" . $old_string . "', '" . $new_string . "') where `meta_key` = '_elementor_data' and post_id=" . $footer_post->ID . ";");
						}
					}
				}
			}
		}
		
		/* Delete transient */
		function delete_transients(){
			delete_transient('ts_mega_menu_custom_css');
			delete_transient('ts_product_deals_ids');
			delete_transient('wc_products_onsale');
		}
		
		/* Update WooCommerce Loolup Table */
		function update_woocommerce_lookup_table(){
			if( function_exists('wc_update_product_lookup_tables_is_running') && function_exists('wc_update_product_lookup_tables') ){
				if( !wc_update_product_lookup_tables_is_running() ){
					if( !defined('WP_CLI') ){
						define('WP_CLI', true);
					}
					wc_update_product_lookup_tables();
				}
			}
		}
	}
	new TS_Importer();
}
?>