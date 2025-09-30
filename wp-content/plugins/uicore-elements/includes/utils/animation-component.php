<?php
namespace UiCoreElements\Utils;
use Elementor\Controls_Manager;

defined('ABSPATH') || exit();

trait Animation_Trait {

    private $animation;

    /**
     * Registers a hover animation control.
     *
     * @param string $name The name of the control.
     * @param array $conditions Control conditions. (optional)
     * @param array $filter Removes requested animations from the options. (optional)
     * @param string|null $custom_slug A custom slug for the control. If null, use $name as slug, formating it. (optional)
     * @param bool $use_new Include new animations not globally available. (optional)
     *
     * @return void
     */
    function TRAIT_register_hover_animation_control($name, $conditions = [], $filter = [], $custom_slug = null, $use_new = false)
    {
        // Custom slug or name converted to slug
        // TODO: use sanitize_title() instead of the 3 functions combined above
        $slug  = isset($custom_slug) ? $custom_slug : strtolower(preg_replace('/\s+/', '_', preg_replace('/[^a-zA-Z0-9\s]/', '', $name)));

        $this->add_control(
            $slug,
            [
                'label' => esc_html__( $name, 'uicore-elements' ),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'label_block' => true,
                'options' => $this->uicore_get_animations($filter, $use_new),
                'condition' => $conditions
            ]
        );
    }
    // Entrance Animations Controls.
    function TRAIT_register_entrance_animations_controls()
    {
        $this->add_control(
            'animate_items',
            [
                'label'              => esc_html__('Animate each Item', 'uicore-elements'),
                'type'               => Controls_Manager::SWITCHER,
                'default'            => '',
                'return_value'       => 'ui-e-grid-animate',
                'render_type'		 => 'none',
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'animate_item_type',
            [
                'label' => __( 'Animation', 'uicore-elements' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'fadeInUp',
                'options' => [
                    'fadeInUp'      => __( 'Fade In Up', 'uicore-elements' ),
					'fadeInDown'    => __( 'Fade In Down', 'uicore-elements' ),
					'fadeInLeft'    => __( 'Fade In Left', 'uicore-elements' ),
					'fadeInRight'   => __( 'Fade In Right', 'uicore-elements' ),
					'fadeIn'        => __( 'Fade In', 'uicore-elements' ),
					'zoomIn'        => __( 'Zoom In', 'uicore-elements' ),
                ],
                'condition' => array(
                    'animate_items' => 'ui-e-grid-animate',
                ),
                'frontend_available' => true,
            ]
        );
        $this->add_control(
            'animate_item_speed',
            [
                'label' => __( 'Speed', 'uicore-elements' ),
                'type' => Controls_Manager::SLIDER,
                'condition' => array(
                    'animate_items' => 'ui-e-grid-animate',
                ),
                'default'=> [
                    'unit' => 'px',
                    'size' => 1500,
                ],
                'range' => [
                    'px' => [
                        'min'  => 10,
                        'max'  => 3000,
                        'step' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '---ui-speed: {{SIZE}}ms',
                ],
            ]
        );
        $this->add_control(
            'animate_item_delay',
        [
                'label' => __( 'Animation Delay', 'uicore-elements' ),
                'type' => Controls_Manager::SLIDER,
                'default'=> [
                    'unit' => 'px',
                    'size' => 200,
                ],
                'range' => [
                    'px' => [
                        'min'  => 0,
                        'max'  => 1500,
                        'step' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '---ui-delay: {{SIZE}}ms',
                ],
                'condition' => [
                    'animate_items' => 'ui-e-grid-animate',
                ],
            ]
        );
        $this->add_control(
            'animate_item_stagger',
        [
                'label' => __( 'Stagger', 'uicore-elements' ),
                'type' => Controls_Manager::SLIDER,
                'condition' => array(
                    'animate_items' => 'ui-e-grid-animate',
                ),
                'default'=> [
                    'unit' => 'px',
                    'size' => 16,
                ],
                'range' => [
                    'px' => [
                        'min'  => 4,
                        'max'  => 500,
                        'step' => 2,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}}' => '---ui-stagger: {{SIZE}}ms',
                ],
            ]
        );
        $this->add_control(
			'hr',
			[
				'type' => \Elementor\Controls_Manager::DIVIDER,
                'condition' => array(
                    'animate_items' => 'ui-e-grid-animate',
                ),
			]
		);
    }

    /**
     * Retrieves the animations list available for the widget.
     *
     * @param array $filter_list Optional. An array of animation names to filter the results.
     * @param bool $use_new Optional. Include new animations not globally available. Default is false.
     *
     * @return array The list of animations available for the widget.
     */
    function uicore_get_animations($filter_list = [], $use_new = false)
    {
        // New animations (transfer to default animation_list only after testing and updating all affected widgets)
        $new_animations = [
            'show' => 'Show'
        ];

        // Solid animations
        $animation_list = [
            ''          => 'None',
            'translate' => 'Translate',
            'zoom'      => 'Zoom',
            'fade'      => 'Fade',
            'underline' => 'Underline',
        ];
        $list = [];
        $animation_list = wp_parse_args($this->animation, $animation_list);

        // Add new animations if requested
        if( $use_new ){
            $animation_list = array_merge($animation_list, $new_animations);
        }

        // Filter the list of animations
        if (!empty($filter_list)) {
            $animation_list = array_diff_key($animation_list, array_flip($filter_list));
        }

        // Format the list of animations, with translated strings, to be used as elementor option's array
        foreach ($animation_list as $key => $value) {
            $slug = $key !== '' ? "ui-e-item-anim-$key" : '';
            $list[$slug] = esc_html__($value, 'uicore-elements');
        }

        return $list;
    }

    /**
     * Update an existent animation control with newest animations.
     *
     * This method is meant to progressively add new animation to the previous
     * animations list without impacting all widgets at once. It must be removed
     * after a given animation is globaly available.
     *
     * @param $name The name of the existent control to be updated.
     * @param array $filter Animation Filter list. (optional)
     *
     * @return void
     */
    function TRAIT_extend_control_animations($name, $filter = [])
    {

        // Get animations with new animations enabled
        $animations = $this->uicore_get_animations($filter, true);

        // Update the required control
        $this->update_control(
            $name,
            [
                'options' => $animations
            ]
        );
    }
}