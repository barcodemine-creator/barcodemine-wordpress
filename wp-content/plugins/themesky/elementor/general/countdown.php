<?php
use Elementor\Controls_Manager;

class TS_Elementor_Widget_Countdown extends TS_Elementor_Widget_Base{
	public function get_name(){
        return 'ts-countdown';
    }
	
	public function get_title(){
        return esc_html__( 'TS Countdown', 'themesky' );
    }
	
	public function get_categories(){
        return array( 'ts-elements', 'general' );
    }
	
	public function get_icon(){
		return 'eicon-countdown';
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
            'date'
            ,array(
                'label' 			=> esc_html__( 'Date', 'themesky' )
                ,'type' 			=> Controls_Manager::DATE_TIME
                ,'default' 			=> date( 'Y-m-d', strtotime('+1 day') )
            )
        );
		
		$this->add_control(
            'text_color_style'
            ,array(
                'label' 		=> esc_html__( 'Text color style', 'themesky' )
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
		
		if( empty($settings['date']) ){
			return;
		}
		
		$time = strtotime($settings['date']);
		
		if( $time === false ){
			return;
		}
		
		$current_time = current_time('timestamp');
		
		if( $time < $current_time ){
			return;
		}
		
		$settings['seconds'] = $time - $current_time;
		
		ts_countdown( $settings );
	}
}

$widgets_manager->register_widget_type( new TS_Elementor_Widget_Countdown() );