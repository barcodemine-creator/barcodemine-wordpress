<?php
use Elementor\Controls_Manager;

class TS_Elementor_Widget_Mailchimp extends TS_Elementor_Widget_Base{
	public function get_name(){
        return 'ts-mailchimp';
    }
	
	public function get_title(){
        return esc_html__( 'TS Mailchimp', 'themesky' );
    }
	
	public function get_categories(){
        return array( 'ts-elements', 'general' );
    }
	
	public function get_icon(){
		return 'eicon-email-field';
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
            'form'
            ,array(
                'label' 		=> esc_html__( 'Form', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> ''
				,'options'		=> $this->get_custom_post_options( 'mc4wp-form' )			
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'title'
            ,array(
                'label' 		=> esc_html__( 'Title', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXT
                ,'default' 		=> ''		
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'intro_text'
            ,array(
                'label' 		=> esc_html__( 'Intro text', 'themesky' )
                ,'type' 		=> Controls_Manager::TEXTAREA
                ,'default' 		=> ''		
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'text_align'
            ,array(
                'label' 		=> esc_html__( 'Text align', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> ''
				,'options'		=> array(
								''				=> esc_html__( 'Default', 'themesky' )
								,'text-center'	=> esc_html__( 'Center', 'themesky' )
							)		
                ,'description' 	=> ''
            )
        );
		
		$this->add_control(
            'text_style'
            ,array(
                'label' 		=> esc_html__( 'Text style', 'themesky' )
                ,'type' 		=> Controls_Manager::SELECT
                ,'default' 		=> 'text-default'
				,'options'		=> array(
								'text-default'	=> esc_html__( 'Default', 'themesky' )
								,'text-light'	=> esc_html__( 'Light', 'themesky' )
							)		
                ,'description' 	=> ''
            )
        );
		
		$this->end_controls_section();
	}
	
	protected function render(){
		$settings = $this->get_settings_for_display();
		
		$default = array(
			'title'				=> ''
			,'intro_text'		=> ''
			,'form'				=> ''
			,'style'			=> 'vertical-button-icon'
			,'text_align'		=> ''
			,'text_style'		=> 'text-default'
		);
		
		$settings = wp_parse_args( $settings, $default );
		
		extract( $settings );
		
		if( !class_exists('TS_Mailchimp_Subscription_Widget') ){
			return;
		}
		
		$intro_html = '';
		if( $intro_text ){
			$intro_html = '<div class="newsletter"><p>'.esc_html($intro_text).'</p></div>';
			$intro_text = '';
		}
		
		$args = array(
			'before_widget' => '<section class="widget-container %s">'
			,'after_widget' => '</section>'
			,'before_title' => '<div class="widget-title-wrapper"><h3 class="widget-title heading-title">'
			,'after_title'  => '</h3>'.$intro_html.'</div>'
		);
		
		$instance = compact('title', 'intro_text', 'form');
		
		$classes = array();
		$classes[] = $style;
		$classes[] = $text_style;
		$classes[] = $text_align;
		
		echo '<div class="ts-mailchimp-subscription-shortcode '.implode(' ', $classes).'" >';
		
		the_widget('TS_Mailchimp_Subscription_Widget', $instance, $args);
		
		echo '</div>';
	}
}

$widgets_manager->register_widget_type( new TS_Elementor_Widget_Mailchimp() );