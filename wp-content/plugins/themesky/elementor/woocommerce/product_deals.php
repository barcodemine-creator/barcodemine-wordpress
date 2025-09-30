<?php
use Elementor\Controls_Manager;

class TS_Elementor_Widget_Product_Deals extends TS_Elementor_Widget_Base{
	public function get_name(){
        return 'ts-product-deals';
    }
	
	public function get_title(){
        return esc_html__( 'TS Product Deals', 'themesky' );
    }
	
	public function get_categories(){
        return array( 'ts-elements', 'woocommerce-elements' );
    }
	
	public function get_icon(){
		return 'eicon-product-upsell';
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
                ,'default' 		=> 'slider'
				,'options'		=> array(
									'slider' 		=> esc_html__('Slider', 'themesky')
									,'grid' 		=> esc_html__('Grid', 'themesky')
								)		
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'product_type'
            ,array(
                'label' 		=> esc_html__( 'Product type', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> 'recent'
				,'options'		=> array(
									'recent' 		=> esc_html__('Recent', 'themesky')
									,'featured' 	=> esc_html__('Featured', 'themesky')
									,'best_selling' => esc_html__('Best Selling', 'themesky')
									,'top_rated' 	=> esc_html__('Top Rated', 'themesky')
									,'mixed_order' 	=> esc_html__('Mixed Order', 'themesky')
								)		
                ,'description' 	=> esc_html__( 'Select type of product', 'themesky' )
            )
        );
		
		$this->add_control(
            'columns'
            ,array(
                'label'     	=> esc_html__( 'Columns', 'themesky' )
                ,'type'     	=> Controls_Manager::NUMBER
				,'default'  	=> 4
				,'min'      	=> 1
            )
        );
		
		$this->add_control(
            'limit'
            ,array(
                'label'     	=> esc_html__( 'Limit', 'themesky' )
                ,'type'     	=> Controls_Manager::NUMBER
				,'default'  	=> 5
				,'min'      	=> 1
				,'description' 	=> esc_html__( 'Number of Products', 'themesky' )
            )
        );
		
		$this->add_control(
            'product_cats'
            ,array(
                'label' 		=> esc_html__( 'Product categories', 'themesky' )
                ,'type' 		=> 'ts_autocomplete'
                ,'default' 		=> array()
				,'options'		=> array()
				,'autocomplete'	=> array(
					'type'		=> 'taxonomy'
					,'name'		=> 'product_cat'
				)
				,'multiple' 	=> true
				,'sortable' 	=> false
				,'label_block' 	=> true
            )
        );
		
		$this->add_control(
            'ids'
            ,array(
                'label' 		=> esc_html__( 'Specific products', 'themesky' )
                ,'type' 		=> 'ts_autocomplete'
                ,'default' 		=> array()
				,'options'		=> array()
				,'autocomplete'	=> array(
					'type'		=> 'post'
					,'name'		=> 'product'
				)
				,'multiple' 	=> true
				,'sortable' 	=> false
				,'label_block' 	=> true
            )
        );
		$this->add_control(
            'has_background_item'
            ,array(
                'label' 		=> esc_html__( 'Has background item', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '0'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> esc_html__( 'Set background white for all items', 'themesky' )
            )
        );
		
		$this->add_control(
            'show_counter'
            ,array(
                'label' 		=> esc_html__( 'Show counter', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '1'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> esc_html__( 'Show counter on each product', 'themesky' )
            )
        );
		
		$this->add_control(
            'show_counter_today'
            ,array(
                'label' 		=> esc_html__( 'Show counter today', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '0'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> esc_html__( 'Show only one counter at the top', 'themesky' )
				,'condition'	=> array( 'show_counter' => '1' )
            )
        );
		
		$this->add_control(
            'show_availability_bar'
            ,array(
                'label' 		=> esc_html__( 'Show availability bar', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '0'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> ''
            )
        );
		
		$this->add_product_meta_controls();
		
		$this->end_controls_section();
		
		$this->start_controls_section(
            'section_slider'
            ,array(
                'label' 	=> esc_html__( 'Slider', 'themesky' )
                ,'tab'   	=> Controls_Manager::TAB_CONTENT
            )
        );
		
		$this->add_control(
            'rows'
            ,array(
                'label' 		=> esc_html__( 'Rows', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '1'
				,'options'		=> array(
									'1'		=> '1'
									,'2'	=> '2'
									,'3'	=> '3'
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
			'title'					=> ''
			,'title_style' 			=> 'title-default'
			,'layout' 				=> 'slider'
			,'product_type'			=> 'recent'
			,'columns' 				=> 4
			,'limit' 				=> 5
			,'product_cats'			=> array()
			,'ids'					=> array()
			,'has_background_item'	=> 0
			,'show_counter'			=> 1
			,'show_counter_today'	=> 0
			,'show_availability_bar'=> 0
			,'show_image' 			=> 1
			,'show_title' 			=> 1
			,'show_sku' 			=> 0
			,'show_price' 			=> 1
			,'show_short_desc'  	=> 0
			,'show_rating' 			=> 0
			,'show_label' 			=> 1	
			,'show_categories'		=> 0	
			,'show_add_to_cart' 	=> 1
			,'rows' 				=> 1
			,'show_nav'				=> 0
			,'auto_play'			=> 0
		);
		
		$settings = wp_parse_args( $settings, $default );
		
		extract( $settings );
		
		if( !class_exists('WooCommerce') ){
			return;
		}
		
		$product_ids_on_sale = ts_get_product_deals_ids();
		
		if( $ids ){
			$product_ids_on_sale = array_intersect($product_ids_on_sale, $ids);
		}
		
		if( !$product_ids_on_sale ){
			return;
		}
		
		if( $show_counter_today ){
			$show_counter = 0;
		}
		
		if( $show_counter ){
			add_action('woocommerce_after_shop_loop_item', 'ts_template_loop_time_deals', 1);
		}
		
		if( $show_availability_bar ){
			add_action('woocommerce_after_shop_loop_item', 'ts_product_availability_bar', 2);
		}
		
		/* Remove hook */
		$options = array(
				'show_image'		=> $show_image
				,'show_label'		=> $show_label
				,'show_title'		=> $show_title
				,'show_sku'			=> $show_sku
				,'show_price'		=> $show_price
				,'show_short_desc'	=> $show_short_desc
				,'show_categories'	=> $show_categories
				,'show_rating'		=> $show_rating
				,'show_add_to_cart'	=> $show_add_to_cart
			);
		ts_remove_product_hooks( $options );

		global $post, $product;
		if( (int)$columns <= 0 ){
			$columns = 5;
		}
		
		$old_woocommerce_loop_columns = wc_get_loop_prop('columns');
		wc_set_loop_prop('columns', $columns);
		
		$args = array(
			'post_type'				=> 'product'
			,'post_status' 			=> 'publish'
			,'posts_per_page' 		=> $limit
			,'orderby' 				=> 'date'
			,'order' 				=> 'desc'
			,'post__in'				=> $product_ids_on_sale
			,'meta_query' 			=> WC()->query->get_meta_query()
			,'tax_query'           	=> WC()->query->get_tax_query()
		);
		
		ts_filter_product_by_product_type($args, $product_type);
		
		if( $product_cats ){
			$args['tax_query'][] = array(
							'taxonomy' 	=> 'product_cat'
							,'terms' 	=> $product_cats
							,'field' 	=> 'term_id'
						);
		}
		
		$products = new WP_Query($args);
		
		if( $products->have_posts() ): 
			$classes = array();
			$classes[] = 'ts-product-deals-wrapper ts-shortcode ts-product woocommerce';
			$classes[] = 'columns-' . $columns;
			$classes[] = $show_image?'':'no-thumbnail';
			$classes[] = 'layout-' . $layout;
			$classes[] = $title_style;
			
			if( $show_counter_today ){
				$classes[] = 'show-counter-today';
			}
			if( $has_background_item ){
				$classes[] = 'has-background-item';
			}
			if( $show_counter ){
				$classes[] = 'show-counter';
			}
			
			if( $layout == 'slider' ){
				$classes[] = 'ts-slider';
				$classes[] = 'rows-' . $rows;
				if( $show_nav ){
					$classes[] = 'show-nav nav-middle middle-thumbnail';
				}
			}
			
			$classes = array_filter($classes);
			
			$data_attr = array();
			if( $layout == 'slider' ){
				$data_attr[] = 'data-nav="'.esc_attr($show_nav).'"';
				$data_attr[] = 'data-autoplay="'.esc_attr($auto_play).'"';
				$data_attr[] = 'data-columns="'.esc_attr($columns).'"';
			}
			?>
			<div class="<?php echo esc_attr( implode(' ', $classes) ); ?>" <?php echo implode(' ', $data_attr); ?>>
			
				<?php if( $title || $show_counter_today ): ?>
				<header class="shortcode-heading-wrapper">
					<?php if( $title ): ?>
					<h2 class="shortcode-title">
						<?php echo esc_html($title); ?>
					</h2>
					<?php endif;
					if( $show_counter_today ){
						ts_daily_time_remain_html();
					}
					?>
				</header>
				<?php endif; ?>
				
				<div class="content-wrapper <?php echo ($layout == 'slider')?'loading':''; ?>">
					<?php woocommerce_product_loop_start(); ?>				

					<?php 
					$count = 0;
					while( $products->have_posts() ){
						$products->the_post();
						if( $layout == 'slider' && $rows > 1 && $count % $rows == 0 ){
							echo '<div class="product-group">';
						}
						wc_get_template_part( 'content', 'product' );
						if( $layout == 'slider' && $rows > 1 && ($count % $rows == $rows - 1 || $count == $products->post_count - 1) ){
							echo '</div>';
						}
						$count++;
					}
					?>			

					<?php woocommerce_product_loop_end(); ?>
				</div>
				
			</div>
			<?php
		endif;
		
		wp_reset_postdata();
		
		/* restore hooks */
		if( $show_counter ){
			remove_action('woocommerce_after_shop_loop_item', 'ts_template_loop_time_deals', 1);
		}
		
		if( $show_availability_bar ){
			remove_action('woocommerce_after_shop_loop_item', 'ts_product_availability_bar', 2);
		}

		ts_restore_product_hooks();

		wc_set_loop_prop('columns', $old_woocommerce_loop_columns);
	}
}

$widgets_manager->register_widget_type( new TS_Elementor_Widget_Product_Deals() );