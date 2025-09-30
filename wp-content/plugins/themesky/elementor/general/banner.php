<?php
use Elementor\Controls_Manager;

class TS_Elementor_Widget_Banner extends TS_Elementor_Widget_Base{
	public function get_name(){
        return 'ts-banner';
    }
	
	public function get_title(){
        return esc_html__( 'TS Banner', 'themesky' );
    }
	
	public function get_categories(){
        return array( 'ts-elements', 'general' );
    }
	
	public function get_icon(){
		return 'eicon-image';
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
            'style'
            ,array(
                'label' => esc_html__( 'Style', 'themesky' )
                ,'type' => Controls_Manager::SELECT
                ,'default' 	=> 'style-default'
				,'options'	=>array(
							'style-default'		=> esc_html__( 'Default', 'themesky' )
							,'style-simple'		=> esc_html__( 'Simple', 'themesky' )
							,'style-button'		=> esc_html__( 'Button', 'themesky' )
							)			
                ,'description' => ''
            )
        );
		
		$this->add_control(
            'img_bg'
            ,array(
                'label' 		=> esc_html__( 'Background Image', 'themesky' )
                ,'type' 		=> Controls_Manager::MEDIA
                ,'default' 		=> array( 'id' => '', 'url' => '' )		
                ,'description' 	=> ''
				,'condition'	=> array( 'style' => ['style-default','style-button'] )
            )
        );
		
		$this->add_control(
            'background_color'
            ,array(
                'label' 		=> esc_html__( 'Background Color', 'themesky' )
                ,'type'     	=> Controls_Manager::COLOR
				,'default'  	=> '#a20401'
				,'selectors'	=> array(
					'{{WRAPPER}} .banner-wrapper' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .banner-wrapper:before' => 'border-color: {{VALUE}}'
				)
				,'condition'	=> array( 'style' => 'style-simple' )
            )
        );
		
		$this->add_control(
            'heading_title'
            ,array(
                'label' 		=> esc_html__( 'Heading Text', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> ''		
                ,'description' 	=> ''
				,'condition'	=> array( 'style' => ['style-default','style-simple'] )
            )
        );
		
		$this->add_control(
            'heading_title_2'
            ,array(
                'label' 		=> esc_html__( 'Heading Text 2', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> ''		
                ,'description' 	=> ''
				,'condition'	=> array( 'style' => ['style-default','style-simple'] )
            )
        );
		
		$this->add_control(
            'heading_text_color'
            ,array(
                'label'     	=> esc_html__( 'Heading Text Color', 'themesky' )
                ,'type'     	=> Controls_Manager::COLOR
				,'default'  	=> '#ffffff'
				,'selectors'	=> array(
					'{{WRAPPER}} .box-content h4,
					{{WRAPPER}} .header-content .banner-bottom' => 'color: {{VALUE}}'
				)
				,'condition'	=> array( 'style' => ['style-default','style-simple'] )
            )
        );
		
		$this->add_control(
            'text_align'
            ,array(
                'label' 		=> esc_html__( 'Text Align', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> 'text-left'
				,'options'		=> array(
									'text-left'		=> esc_html__( 'Left', 'themesky' )
									,'text-center'	=> esc_html__( 'Center', 'themesky' )
									,'text-right'	=> esc_html__( 'Right', 'themesky' )
								)			
                ,'description' 	=> ''
				,'condition'	=> array( 'style' => 'style-default' )
				,'condition'	=> array( 'style' => ['style-default','style-simple'] )
            )
        );
		
		$this->add_control(
            'show_button'
            ,array(
                'label' 		=> esc_html__( 'Show Button', 'themesky' )
				,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> '1'
				,'options'		=> array(
									'0'		=> esc_html__( 'No', 'themesky' )
									,'1'	=> esc_html__( 'Yes', 'themesky' )
								)
            )
        );
		
		$this->add_control(
            'button_text'
            ,array(
                'label'     	=> esc_html__( 'Button Text', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> 'View More'		
                ,'description' 	=> ''
				,'condition'	=> array( 'style' => ['style-simple','style-button'] )
            )
        );
		
		$this->add_control(
            'button_text_color'
            ,array(
                'label'     	=> esc_html__( 'Button Text Color', 'themesky' )
                ,'type'     	=> Controls_Manager::COLOR
				,'default'  	=> '#ffffff'
				,'selectors'	=> array(
					'{{WRAPPER}} .button,
					{{WRAPPER}} .banner-bottom a' => 'color: {{VALUE}}'
				)
				,'condition'	=> array( 'show_button' => '1' )
            )
        );
		
		$this->add_control(
            'button_background_color'
            ,array(
                'label'     	=> esc_html__( 'Button Background Color', 'themesky' )
                ,'type'     	=> Controls_Manager::COLOR
				,'default'  	=> '#161616'
				,'selectors'	=> array(
					'{{WRAPPER}} .button' => 'background: {{VALUE}}'
				)
				,'condition'	=> array( 'show_button' => '1', 'style' => ['style-default','style-button'])
            )
        );
		
		$this->add_control(
            'button_border_color'
            ,array(
                'label'     	=> esc_html__( 'Button Border Color', 'themesky' )
                ,'type'     	=> Controls_Manager::COLOR
				,'default'  	=> '#161616'
				,'selectors'	=> array(
					'{{WRAPPER}} .button' => 'border-color: {{VALUE}}'
				)
				,'condition'	=> array( 'show_button' => '1', 'style' => ['style-default','style-button'])
            )
        );
		
		$this->add_control(
            'button_text_hover'
            ,array(
                'label'     	=> esc_html__( 'Button Text Hover Color', 'themesky' )
                ,'type'     	=> Controls_Manager::COLOR
				,'default'  	=> '#ffffff'
				,'selectors'	=> array(
					'{{WRAPPER}}:hover .button' => 'color: {{VALUE}}'
				)
				,'condition'	=> array( 'show_button' => '1', 'style' => ['style-default','style-button'])
            )
        );
		
		$this->add_control(
            'button_background_hover'
            ,array(
                'label'     	=> esc_html__( 'Button Background Hover Color', 'themesky' )
                ,'type'     	=> Controls_Manager::COLOR
				,'default'  	=> '#a20401'
				,'selectors'	=> array(
					'{{WRAPPER}}:hover .button' => 'background: {{VALUE}}'
				)
				,'condition'	=> array( 'show_button' => '1', 'style' => ['style-default','style-button'])
            )
        );
		
		$this->add_control(
            'button_border_hover'
            ,array(
                'label'     	=> esc_html__( 'Button Border Hover Color', 'themesky' )
                ,'type'     	=> Controls_Manager::COLOR
				,'default'  	=> '#a20401'
				,'selectors'	=> array(
					'{{WRAPPER}}:hover .button' => 'border-color: {{VALUE}}'
				)
				,'condition'	=> array( 'show_button' => '1', 'style' => ['style-default','style-button'])
            )
        );
		
		$this->add_control(
            'text_position'
            ,array(
                'label' 		=> esc_html__( 'Banner Text Position', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> 'left-top'
				,'options'		=> array(
									'left-top'			=> esc_html__( 'Left Top', 'themesky' )
									,'left-bottom'		=> esc_html__( 'Left Bottom', 'themesky' )
									,'left-center'		=> esc_html__( 'Left Center', 'themesky' )
									,'right-top'		=> esc_html__( 'Right Top', 'themesky' )
									,'right-bottom'		=> esc_html__( 'Right Bottom', 'themesky' )
									,'right-center'		=> esc_html__( 'Right Center', 'themesky' )
									,'center-top'		=> esc_html__( 'Center Top', 'themesky' )
									,'center-bottom'	=> esc_html__( 'Center Bottom', 'themesky' )
									,'center-center'	=> esc_html__( 'Center Center', 'themesky' )
								)			
                ,'description' 	=> ''
				,'condition'	=> array( 'style' => 'style-default' )
            )
        );
		
		$this->add_control(
            'link'
            ,array(
                'label'     	=> esc_html__( 'Link', 'themesky' )
                ,'type'     	=> Controls_Manager::URL
				,'default'  	=> array( 'url' => '', 'is_external' => true, 'nofollow' => true )
				,'show_external'=> true
            )
        );
		
		$this->add_control(
            'style_effect'
            ,array(
                'label' 		=> esc_html__( 'Style Effect', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> 'background-scale'
				,'options'		=> array(									
									'background-scale'					=> esc_html__('Background Scale', 'themesky')
									,'background-scale-opacity' 		=> esc_html__('Background Scale Opacity', 'themesky')
									,'background-scale-dark' 			=> esc_html__('Background Scale Dark', 'themesky')
									,'background-scale-and-line' 		=> esc_html__('Background Scale and Line', 'themesky')
									,'background-scale-opacity-line' 	=> esc_html__('Background Scale Opacity and Line', 'themesky')
									,'background-scale-dark-line' 		=> esc_html__('Background Scale Dark and Line', 'themesky')
									,'background-scale-rotate' 			=> esc_html__('Background Scale and Rotate', 'themesky')
									,'background-opacity-and-line' 		=> esc_html__('Background Opacity and Line', 'themesky')
									,'background-dark-and-line' 		=> esc_html__('Background Dark and Line', 'themesky')
									,'background-opacity' 				=> esc_html__('Background Opacity', 'themesky')
									,'background-dark' 					=> esc_html__('Background Dark', 'themesky')
									,'eff-line' 						=> esc_html__('Line', 'themesky')
									,'eff-image-gray' 					=> esc_html__('Gray', 'themesky')
									,'no-effect' 						=> esc_html__('None', 'themesky')
								)			
                ,'description' 	=> ''
            )
        );
		
		$this->end_controls_section();
	}
	
	protected function render(){
		$settings = $this->get_settings_for_display();
		
		$default = array(
			'img_bg'							=> array( 'id' => '', 'url' => '' )
			,'background_color'					=> '#a20401'
			,'style'							=> 'style-default'
			,'heading_title'					=> '#a20401'
			,'heading_title_2'					=> ''
			,'heading_text_color'				=> '#ffffff'
			,'text_align'						=> 'text-left'
			,'text_position'					=> 'left-top'
			,'show_button'						=> 1
			,'button_text'						=> 'View More'
			,'button_text_color'				=> '#ffffff'
			,'button_text_hover'				=> '#ffffff'
			,'button_background_color'			=> '#161616'
			,'button_background_hover'			=> '#a20401'
			,'button_border_color'				=> '#161616'
			,'button_border_hover'				=> '#a20401'
			,'link' 							=> array( 'url' => '', 'is_external' => true, 'nofollow' => true )
			,'style_effect'						=> 'background-scale'
		);
		
		$settings = wp_parse_args( $settings, $default );
		
		extract( $settings );
		
		$link_attr = $this->generate_link_attributes( $link );
		
		$classes = array();
		if(	$style == 'style-button' ){
			$text_align = 'text-center';
			$text_position = 'center-center';
		}
		$classes[] = $text_align;
		$classes[] = $style_effect;
		$classes[] = $text_position;
		$classes[] = $style;
		if(	$show_button ){
			$classes[] = 'show-button';
		}
		?>
		<div class="ts-banner <?php echo esc_attr( implode(' ', $classes) ); ?>">
			<div class="banner-wrapper">
			
				<?php if( $link_attr ): ?>
				<a class="banner-link" <?php echo implode(' ', $link_attr); ?>></a>
				<?php endif;?>
					
				<?php if( $style == 'style-default' || $style == 'style-button' ): ?>
				<div class="banner-bg">
					<div class="bg-content">
					<?php echo wp_get_attachment_image($img_bg['id'], 'full', 0, array('class'=>'img')); ?>
					</div>
				</div>
				<?php endif; ?>
							
				<div class="box-content">
					<div class="header-content">
					
						<?php if( $style == 'style-default' && ( $heading_title || $heading_title_2) ): ?>				
						<h4>
							<span><?php echo esc_attr($heading_title) ?></span>
							<span><?php echo esc_attr($heading_title_2) ?></span>
						</h4>
						<?php endif; ?>
						
						<?php if( $style == 'style-simple' && $heading_title ): ?>				
						<h4><?php echo esc_attr($heading_title) ?></h4>
						<?php endif; ?>
						
						<?php if( $style == 'style-simple' && ( $heading_title_2 || $show_button ) ): ?>
						<div class="banner-bottom">
							<?php if( $style == 'style-simple' && $heading_title_2 ): ?>				
							<span><?php echo esc_attr($heading_title_2) ?></span>
							<?php endif; ?>
							
							<?php if( $style == 'style-simple' && $show_button ):?>
							<a <?php echo implode(' ', $link_attr); ?>><?php echo esc_attr($button_text) ?></a>
						<?php endif; ?>
						</div>
						<?php endif; ?>
						
						<?php if( ($style == 'style-default' || $style == 'style-button') && $show_button ):?>
						<div class="ts-banner-button">
							<a class="button" <?php echo implode(' ', $link_attr); ?>>
							<?php if( $style == 'style-button'): ?>
								<?php echo esc_attr($button_text); ?>
							<?php endif; ?>
							</a>
						</div>
						<?php endif; ?>
						
					</div>
				</div>
				
			</div>
		</div>
		<?php
	}
}

$widgets_manager->register_widget_type( new TS_Elementor_Widget_Banner() );