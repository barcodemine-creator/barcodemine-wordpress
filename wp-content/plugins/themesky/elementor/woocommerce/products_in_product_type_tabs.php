<?php
use Elementor\Controls_Manager;

class TS_Elementor_Widget_Products_In_Product_Type_Tabs extends TS_Elementor_Widget_Base{
	public function get_name(){
        return 'ts-products-in-product-type-tabs';
    }
	
	public function get_title(){
        return esc_html__( 'TS Products In Product Type Tabs', 'themesky' );
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
		
		$tabs = array( '1' => 'featured', '2' => 'best_selling' , '3' => 'sale', '4' => 'top_rated', '5' => 'recent' );
		foreach( $tabs as $i => $type ){
			$this->add_control(
				'tab_' . $i
				,array(
					'label' 		=> sprintf( esc_html__( 'Tab %s', 'themesky' ), $i )
					,'type' 		=> Controls_Manager::SELECT
					,'default' 		=> '1'
					,'options'		=> array(
										'0'		=> esc_html__( 'No', 'themesky' )
										,'1'	=> esc_html__( 'Yes', 'themesky' )
									)			
					,'description' 	=> ''
					,'separator' 	=> 'before'
				)
			);
			
			$this->add_control(
				'tab_' . $i . '_heading'
				,array(
					'label' 		=> sprintf( esc_html__( 'Tab %s heading', 'themesky' ), $i )
					,'type' 		=> Controls_Manager::TEXT
					,'default' 		=> ucwords( str_replace( '_', ' ', $type ) )
					,'description' 	=> ''
					,'condition'	=> array( 'tab_' . $i => '1' )
				)
			);
			
			$this->add_control(
				'tab_' . $i . '_product_type'
				,array(
					'label' 		=> sprintf( esc_html__( 'Tab %s product type', 'themesky' ), $i )
					,'type' 		=> Controls_Manager::SELECT
					,'default' 		=> $type
					,'options'		=> array(
										'recent' 		=> esc_html__('Recent', 'themesky')
										,'sale' 		=> esc_html__('Sale', 'themesky')
										,'featured' 	=> esc_html__('Featured', 'themesky')
										,'best_selling' => esc_html__('Best Selling', 'themesky')
										,'top_rated' 	=> esc_html__('Top Rated', 'themesky')
										,'mixed_order' 	=> esc_html__('Mixed Order', 'themesky')
									)		
					,'description' 	=> ''
					,'condition'	=> array( 'tab_' . $i => '1' )
				)
			);
		}
		
		$this->add_control(
            'active_tab'
            ,array(
                'label' 		=> esc_html__( 'Active tab', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '1'
				,'options'		=> array(
									'1'		=> '1'
									,'2'	=> '2'
									,'3'	=> '3'
									,'4'	=> '4'
									,'5'	=> '5'
								)			
                ,'description' 	=> ''
                ,'separator' 	=> 'before'
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
				,'default'  	=> 6
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
		
		$this->add_product_meta_controls();
		
		$this->add_product_color_swatch_controls();
		
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
                ,'default' 		=> '1'
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
			,'tab_1'					    => 1
			,'tab_1_heading'				=> 'Featured'
			,'tab_1_product_type'			=> 'featured'
			,'tab_2'					    => 1
			,'tab_2_heading'				=> 'Best Selling'
			,'tab_2_product_type'			=> 'best_selling'
			,'tab_3'					    => 1
			,'tab_3_heading'				=> 'On Sale'
			,'tab_3_product_type'			=> 'sale'
			,'tab_4'					    => 1
			,'tab_4_heading'				=> 'Top Rated'
			,'tab_4_product_type'			=> 'top_rated'
			,'tab_5'					    => 1
			,'tab_5_heading'				=> 'Recent'
			,'tab_5_product_type'			=> 'recent'
			,'active_tab'					=> 1
			,'columns' 						=> 4
			,'limit' 						=> 6
			,'product_cats'					=> array()
			,'include_children' 			=> 1
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
			,'is_slider' 					=> 1
			,'only_slider_mobile'			=> 0
			,'rows' 						=> 1
			,'show_nav' 					=> 0
			,'auto_play' 					=> 1
		);
		
		$settings = wp_parse_args( $settings, $default );
		
		extract( $settings );
		
		if ( !class_exists('WooCommerce') ){
			return;
		}
		
		if( !$tab_1 && !$tab_2 && !$tab_3 && !$tab_4 && !$tab_5 ){
			return;
		}
		
		if( $only_slider_mobile && !wp_is_mobile() ){
			$is_slider = 0;
		}
		
		$tabs = array();
		for( $i = 1; $i <= 5; $i++ ){
			if( ${'tab_' . $i} ){
				$tabs[] = array(
					'heading'		=> ${'tab_' . $i . '_heading'}
					,'product_type'	=> ${'tab_' . $i . '_product_type'}
				);
			}
		}
		
		if( $active_tab > count($tabs) ){
			$active_tab = 1;
		}
		
		$product_type = $tabs[$active_tab-1]['product_type'];
		
		$product_cats = implode(',', $product_cats);
		
		$atts = compact('columns', 'rows', 'limit', 'product_cats', 'include_children', 'product_type'
						,'show_image', 'show_title', 'show_sku', 'show_price', 'show_short_desc', 'show_rating', 'show_label'
						,'show_categories', 'show_add_to_cart', 'show_color_swatch', 'number_color_swatch', 'is_slider', 'show_nav', 'auto_play');
		
		$classes = array();
		$classes[] = 'ts-product-in-product-type-tab-wrapper ts-shortcode ts-product';
		$classes[] = $title_style;
		
		if( $show_color_swatch ){
			$classes[] = 'show-color-swatch';
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
			$data_attr[] = 'data-autoplay="'.$auto_play.'"';
			$data_attr[] = 'data-columns="'.$columns.'"';
		}
		
		$classes = array_filter($classes);
		
		$rand_id = 'ts-product-in-product-type-tab-' . mt_rand(0, 1000);
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
				
				<ul class="tabs">
				<?php
				foreach( $tabs as $i => $tab ):
				?>
					<li class="tab-item <?php echo ($active_tab == $i + 1)?'current':''; ?>" data-product_type="<?php echo esc_attr($tab['product_type']) ?>"><?php echo esc_html($tab['heading']) ?></li>
				<?php
				endforeach;
				?>
				</ul>
			</div>
			
			<div class="column-content">
				<div class="column-products woocommerce columns-<?php echo esc_attr($columns) ?> <?php echo $product_type; ?> <?php echo $is_slider?'loading':''; ?>">
					<?php ts_get_product_content_in_category_tab($atts, $product_cats); ?>
				</div>
			</div>
		</div>
		<?php
	}
}

$widgets_manager->register_widget_type( new TS_Elementor_Widget_Products_In_Product_Type_Tabs() );