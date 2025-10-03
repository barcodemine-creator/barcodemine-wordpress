<?php
use Elementor\Controls_Manager;

class TS_Elementor_Widget_Products_In_Category_Tabs extends TS_Elementor_Widget_Base{
	public function get_name(){
        return 'ts-products-in-category-tabs';
    }
	
	public function get_title(){
        return esc_html__( 'TS Products In Category Tabs', 'themesky' );
    }
	
	public function get_categories(){
        return array( 'ts-elements', 'woocommerce-elements' );
    }
	
	public function get_icon(){
		return 'eicon-product-tabs';
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
            'product_type'
            ,array(
                'label' 		=> esc_html__( 'Product type', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> 'recent'
				,'options'		=> array(
									'recent' 		=> esc_html__('Recent', 'themesky')
									,'sale' 		=> esc_html__('Sale', 'themesky')
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
				,'default'  	=> 8
				,'min'      	=> 1
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
				,'label_block' 	=> true
            )
        );
		
		$this->add_control(
            'parent_cat'
            ,array(
                'label' 		=> esc_html__( 'Parent category', 'themesky' )
                ,'type' 		=> 'ts_autocomplete'
                ,'default' 		=> array()
				,'options'		=> array()
				,'autocomplete'	=> array(
					'type'		=> 'taxonomy'
					,'name'		=> 'product_cat'
				)
				,'multiple' 	=> false
				,'sortable' 	=> false
				,'label_block' 	=> true
				,'description' 	=> esc_html__( 'Each tab will be a sub category of this category. This option is available when the Product categories option is empty', 'themesky' )
            )
        );
		
		$this->add_control(
            'include_children'
            ,array(
                'label' 		=> esc_html__( 'Include children', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '0'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> esc_html__( 'Load the products of sub categories in each tab', 'themesky' )
            )
        );
		
		$this->add_control(
            'show_general_tab'
            ,array(
                'label' 		=> esc_html__( 'Show general tab', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '0'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> esc_html__( 'Get products from all categories or sub categories', 'themesky' )
            )
        );
		
		$this->add_control(
            'general_tab_heading'
            ,array(
                'label' 		=> esc_html__( 'General tab heading', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> ''		
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'product_type_general_tab'
            ,array(
                'label' 		=> esc_html__( 'Product type of general tab', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> 'recent'
				,'options'		=> array(
									'recent' 		=> esc_html__('Recent', 'themesky')
									,'sale' 		=> esc_html__('Sale', 'themesky')
									,'featured' 	=> esc_html__('Featured', 'themesky')
									,'best_selling' => esc_html__('Best Selling', 'themesky')
									,'top_rated' 	=> esc_html__('Top Rated', 'themesky')
									,'mixed_order' 	=> esc_html__('Mixed Order', 'themesky')
								)		
                ,'description' 	=> esc_html__( 'Select type of product', 'themesky' )
            )
        );
		
		$this->add_control(
            'item_layout'
            ,array(
                'label' => esc_html__( 'Item layout', 'themesky' )
                ,'type' => Controls_Manager::SELECT
                ,'default' 	=> 'item-layout-grid'
				,'options'	=>array(
							'item-layout-grid'		=> esc_html__( 'Grid', 'themesky' )
							,'item-layout-list'		=> esc_html__( 'List', 'themesky' )
							)			
                ,'description' => ''
            )
        );
		
		$this->add_product_meta_controls();
		
		$this->add_product_color_swatch_controls();
		
		$this->add_control(
            'show_shop_more_button'
            ,array(
                'label' 		=> esc_html__( 'Show shop more button', 'themesky' )
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
            'show_shop_more_general_tab'
            ,array(
                'label' 		=> esc_html__( 'Show shop more button in general tab', 'themesky' )
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
            'shop_more_button_text'
            ,array(
                'label' 		=> esc_html__( 'Shop more button label', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> 'Shop more'		
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
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'only_slider_mobile'
            ,array(
                'label' 		=> esc_html__( 'Only show slider on mobile', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '0'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> esc_html__( 'Show Grid on desktop and only enable Slider on mobile', 'themesky' )
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
			'title'							=> ''
			,'title_style'					=> 'title-default'
			,'product_type'					=> 'recent'
			,'columns' 						=> 4
			,'limit' 						=> 8
			,'product_cats'					=> array()
			,'parent_cat' 					=> array()
			,'include_children' 			=> 0
			,'show_general_tab' 			=> 0
			,'general_tab_heading' 			=> ''
			,'product_type_general_tab' 	=> 'recent'
			,'item_layout'					=> 'item-layout-grid'
			,'show_image' 					=> 1
			,'show_title' 					=> 1
			,'show_sku' 					=> 0
			,'show_price' 					=> 1
			,'show_short_desc'  			=> 0
			,'show_rating' 					=> 0
			,'show_label' 					=> 1
			,'show_categories'				=> 0	
			,'show_add_to_cart' 			=> 1
			,'show_color_swatch' 			=> 0
			,'number_color_swatch' 			=> 3
			,'show_shop_more_button' 		=> 0
			,'show_shop_more_general_tab' 	=> 0
			,'shop_more_button_text' 		=> 'Shop more'
			,'is_slider' 					=> 0
			,'only_slider_mobile'			=> 0
			,'rows' 						=> 1
			,'show_nav' 					=> 0
			,'show_dots' 					=> 0
			,'auto_play' 					=> 1
		);
		
		$settings = wp_parse_args( $settings, $default );
		
		extract( $settings );
		
		if ( !class_exists('WooCommerce') ){
			return;
		}
		
		$is_elementor_editor = ( isset($_GET['action']) && $_GET['action'] == 'elementor' ) || wp_doing_ajax();
		
		$product_cats = implode(',', $product_cats);
		$parent_cat = is_array($parent_cat) ? implode('', $parent_cat) : $parent_cat;
		
		if( !$product_cats && !$parent_cat ){
			if( $is_elementor_editor ){
				esc_html_e( 'Please select at least one product category', 'themesky' );
			}
			return;
		}
		
		if( !$product_cats ){
			$sub_cats = get_terms(array('taxonomy' => 'product_cat', 'parent' => $parent_cat, 'fields' => 'ids', 'orderby' => 'none'));
			if( is_array($sub_cats) && !empty($sub_cats) ){
				$product_cats = implode(',', $sub_cats);
			}
			else{
				if( $is_elementor_editor ){
					esc_html_e( 'The selected parent category does not have children', 'themesky' );
				}
				return;
			}
		}
		else{
			$parent_cat = '';
		}
		
		if( $only_slider_mobile && !wp_is_mobile() ){
			$is_slider = 0;
		}
		
		if( $show_dots){
			$show_nav = 0;
		}
		
		$atts = compact('product_type', 'columns', 'rows', 'limit' ,'product_cats', 'include_children'
						,'show_image', 'show_title', 'show_sku', 'show_price', 'show_short_desc', 'show_rating', 'show_label' ,'show_categories', 'show_add_to_cart', 'show_color_swatch', 'number_color_swatch'
						,'show_shop_more_button', 'show_shop_more_general_tab', 'show_general_tab', 'product_type_general_tab', 'is_slider', 'show_nav', 'show_dots', 'auto_play');
		
		$classes = array();
		$classes[] = 'ts-product-in-category-tab-wrapper ts-shortcode ts-product';
		$classes[] = $product_type;
		$classes[] = $title_style;
		$classes[] = $item_layout;
		if( $show_color_swatch ){
			$classes[] = 'show-color-swatch';
		}
		if( $show_dots ){
			$classes[] = 'show-dots';
		}
		if( $show_shop_more_button ){
			$classes[] = 'has-shop-more-button';
		}
		else{
			$classes[] = 'no-shop-more-button';
		}
		
		if( $is_slider ){
			$classes[] = 'ts-slider';
			$classes[] = 'rows-' . $rows;
			if( $show_nav ){
				$classes[] = 'show-nav nav-middle';
				if( $rows < 2){
					$classes[] = 'middle-thumbnail';
				}
			}
		}
		
		$data_attr = array();
		if( $is_slider ){
			$data_attr[] = 'data-nav="'.$show_nav.'"';
			$data_attr[] = 'data-dots="'.$show_dots.'"';
			$data_attr[] = 'data-autoplay="'.$auto_play.'"';
			$data_attr[] = 'data-columns="'.$columns.'"';
		}
		
		$current_cat = '';
		$is_general_tab = false;
		$shop_more_link = '#';
		
		$rand_id = 'ts-product-in-category-tab-' . mt_rand(0, 1000);
		?>
		<div class="<?php echo esc_attr(implode(' ', $classes)); ?>" id="<?php echo esc_attr($rand_id) ?>" data-atts="<?php echo htmlentities(json_encode($atts)); ?>" <?php echo implode(' ', $data_attr); ?>>
			<div class="column-tabs">
				
				<?php if( $title ): ?>
				<header class="heading-tab">
					<h2 class="heading-title">
						<?php echo esc_html($title); ?>
					</h2>
				</header>
				<?php endif; ?>
				
				<div class="list-categories">
					<ul class="tabs">
					<?php 
					if( $show_general_tab ){
						if( $parent_cat ){
							$current_cat = $parent_cat;
							$shop_more_link = get_term_link((int)$parent_cat, 'product_cat');
							if( is_wp_error($shop_more_link) ){
								$shop_more_link = wc_get_page_permalink('shop');
							}
						}
						else{
							$current_cat = $product_cats;
							$shop_more_link = wc_get_page_permalink('shop');
						}
						$is_general_tab = true;
					?>
						<li class="tab-item general-tab current" data-product_cat="<?php echo $current_cat; ?>" data-link="<?php echo esc_url($shop_more_link) ?>">
							<span><?php echo esc_html($general_tab_heading) ?></span>
						</li>
					<?php
					}
					
					$product_cats = array_map('trim', explode(',', $product_cats));
					foreach( $product_cats as $k => $product_cat ):
						$term = get_term_by( 'term_id', $product_cat, 'product_cat');
						if( !isset($term->name) ){
							continue;
						}
						$current_tab = false;
						if( $current_cat == '' ){
							$current_tab = true;
							$current_cat = $product_cat;
							$shop_more_link = get_term_link($term, 'product_cat');
						}
					?>
						<li class="tab-item <?php echo ($current_tab)?'current':''; ?>" data-product_cat="<?php echo esc_attr($product_cat) ?>" data-link="<?php echo esc_url(get_term_link($term, 'product_cat')) ?>">
							<span><?php echo esc_html($term->name) ?></span>
						</li>
					<?php
					endforeach;
					?>
					</ul>
					
				</div>
			</div>
			
			<div class="column-content">
				<div class="column-products woocommerce columns-<?php echo esc_attr($columns) ?> <?php echo $is_slider?'loading':''; ?>">
					<?php ts_get_product_content_in_category_tab($atts, $current_cat, $is_general_tab); ?>
				</div>
				
				<?php if( $show_shop_more_button ): ?>
				<div class="shop-more">
					<a class="button shop-more-button" href="<?php echo esc_url($shop_more_link) ?>"><?php echo esc_html($shop_more_button_text) ?></a>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}
}

$widgets_manager->register_widget_type( new TS_Elementor_Widget_Products_In_Category_Tabs() );