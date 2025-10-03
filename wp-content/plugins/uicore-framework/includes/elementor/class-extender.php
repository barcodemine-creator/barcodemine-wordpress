<?php
namespace UiCore\Elementor;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;

defined('ABSPATH') || exit();

/**
 *  Elementor extra features
 */
class Extender
{
    public function __construct()
    {
		//Extended Column
		add_action( 'elementor/element/column/layout/before_section_end', [$this, 'asimetric_column'], 20, 2);
    }

    public function get_name() {
		return 'uicore_extender';
	}

    function asimetric_column( Controls_Stack $element, $section_id )
    {
        $element->add_control(
			'shape_animation',
			[
				'label' => UICORE_BADGE . __( 'Align to Container', 'uicore-framework' ),
				'description' => __( 'Align the column to website container. Only works on top-level, full-width sections.', 'uicore-framework' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'' => esc_html__( 'None', 'elementor' ),
					'left' => esc_html__( 'Left', 'elementor' ),
					'right' => esc_html__( 'Right', 'elementor' ),
				],
                'default' => '',
                'separator' => 'before',
                'return_value' =>'',
                'prefix_class' => 'ui-col-align-',
			]
		);
    }
	
}

new Extender;
