<?php
use Elementor\Controls_Manager;

class TS_Elementor_Widget_List_Of_Product_Categories extends TS_Elementor_Widget_Base{
	public function get_name(){
        return 'ts-list-of-product-categories';
    }
	
	public function get_title(){
        return esc_html__( 'TS List Of Product Categories', 'themesky' );
    }
	
	public function get_categories(){
        return array( 'ts-elements', 'woocommerce-elements' );
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
                'label'     	=> esc_html__( 'Title', 'themesky' )
                ,'type'     	=> Controls_Manager::TEXT
				,'default'  	=> ''
            )
        );
		
		$this->add_control(
            'limit'
            ,array(
                'label'     	=> esc_html__( 'Limit', 'themesky' )
                ,'type'     	=> Controls_Manager::NUMBER
				,'default'  	=> 12
				,'min'      	=> 1
            )
        );
		
		$this->add_control(
            'parent'
            ,array(
                'label' 		=> esc_html__( 'Parent', 'themesky' )
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
				,'description' 	=> esc_html__( 'Get children of this category', 'themesky' )
            )
        );
		
		$this->add_control(
            'direct_child'
            ,array(
                'label' 		=> esc_html__( 'Direct Children', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '1'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> esc_html__( 'Get direct children of Parent or all children', 'themesky' )
            )
        );
		
		$this->add_control(
            'include_parent'
            ,array(
                'label' 		=> esc_html__( 'Include parent', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '1'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)			
                ,'description' 	=> esc_html__( 'Show parent category at the first of list', 'themesky' )
            )
        );
		
		$this->add_control(
            'ids'
            ,array(
                'label' 		=> esc_html__( 'Specific categories', 'themesky' )
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
            'hide_empty'
            ,array(
                'label' 		=> esc_html__( 'Hide empty product categories', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '1'
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
			'title' 				=> ''
			,'limit'				=> 12
			,'parent'				=> array()
			,'direct_child'			=> 1
			,'include_parent'		=> 1
			,'ids'					=> array()
			,'hide_empty'			=> 1
		);
		
		$settings = wp_parse_args( $settings, $default );
		
		extract( $settings );
		
		if( !class_exists('WooCommerce') ){
			return;
		}
		
		if( is_array($parent) ){
			$parent = implode( '', $parent );
		}
		
		if( $parent && $include_parent ){
			$limit = absint($limit) - 1;
		}
		
		$args = array(
			'taxonomy'		=> 'product_cat'
			,'hide_empty'	=> $hide_empty
			,'number'		=> $limit
		);
		
		if( $parent ){
			if( $direct_child ){
				$args['parent'] = $parent;
			}
			else{
				$args['child_of'] = $parent;
			}
		}
		
		if( $ids ){
			$args['include'] = $ids;
			$args['orderby'] = 'include';
		}
		
		$list_categories = get_terms( $args );
		
		if( !is_array($list_categories) || empty($list_categories) ){
			return;
		}
		?>
		<div class="ts-list-of-product-categories-wrapper">		
			<div class="list-categories">
				<?php if( $title ): ?>		
				<h3 class="heading-title">
					<?php echo esc_html($title) ?>
				</h3>
				<?php endif; ?>
				
				<ul>
					<?php 
					if( $parent && $include_parent ){
						$parent_obj = get_term($parent, 'product_cat');
						if( isset($parent_obj->name) ){
					?>
						<li><a href="<?php echo get_term_link($parent_obj, 'product_cat'); ?>"><?php echo esc_html($parent_obj->name); ?></a></li>
					<?php
						}
					}
					?>
					
					<?php foreach( $list_categories as $category ){ ?>
					<li><a href="<?php echo get_term_link($category, 'product_cat'); ?>"><?php echo esc_html($category->name); ?></a></li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<?php
	}
}

$widgets_manager->register_widget_type( new TS_Elementor_Widget_List_Of_Product_Categories() );