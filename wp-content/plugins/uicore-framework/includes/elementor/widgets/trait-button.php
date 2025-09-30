<?php
namespace UiCore\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

use UiCore\Assets;
use UiCore\Portfolio;
use UiCore\Blog;
use UiCore\Helper;
use UiCore\Elementor\Generic\Post_Filter;
use UiCore\Elementor\Generic\Query;

defined('ABSPATH') || exit();

trait Button {
    function get_content_controls()
    {
        $this->add_control('button_type',[
                'label' => __( 'Button Type', 'uicore-framework' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'button',
                'options' => [
                    'button'  => __( 'Classic', 'uicore-framework' ),
                    'link' => __( 'Link', 'uicore-framework' ),
                ],
                'prefix_class' => 'ui-e-',
            ]
        );
        $this->add_control('button_text',[
                'label' => __( 'Text', 'uicore-framework' ),
                'type' => Controls_Manager::TEXT,
                'placeholder' => __( 'my_custom_tax', 'uicore-framework' ),
                'default'   =>__( 'Read More', 'uicore-framework' )
            ]
        );
    }
}