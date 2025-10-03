<?php
namespace UiCoreElements\Utils;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
* Carousel / Slider Component Trait
*/

trait Carousel_Trait {

	/**
	 * Sets all navigation conditions in one place
	 *
     * @param string $type Control name
     * @param array|null $extras (Optional) Terms attributes. ['name' => 'control, 'operator' => '==', 'value' => 'yes'], [..]
	 * @param string|null $relation (Optional) Accepts 'or' & 'and' values. Default is 'and'
	 * @return array Elementor API Conditions
     */
	function nav_conditions($type, $extras = false, $relation = 'and')
	{
		if ($type == 'arrows') {
			$options = ['arrows', 'arrows-dots', 'arrows-fraction'];
		} elseif ($type == 'dots') {
			$options = ['dots', 'arrows-dots'];
		} elseif ($type == 'fraction') {
			$options = ['fraction', 'arrows-fraction'];
		}

		$conditions['terms'][] = [
			'name' => 'navigation',
			'operator' => 'in',
			'value' => $options,
		];
		$conditions['relation'] = $relation;

		// Check for extra conditions
		if($extras != false){
			foreach ($extras as $extra){
				$conditions['terms'][] = $extra;
			}
		}

		return $conditions;
	}

    /**
     * Returns the Carousel/Slider widget scripts.
     *
     * @param bool $use_entrance If the entrance script should be enqueued or not. Default is `true`.
     * @param array $extra_conditions Extra conditions to be added to the scripts.
     *
     * @return array The array of scripts for the widget.
     */
    function TRAIT_get_carousel_scripts($use_entrance = true, $extra_conditions = null) {

        $base = [ 'carousel' ];

        // Add extra conditions to carousel script if requested
        if( isset($extra_conditions) ){
			$base['carousel'] = ['condition' => []];

            foreach ($extra_conditions as $key => $value){
                $base['carousel']['condition'][$key] = $value;
            }
        }

        if($use_entrance) {
            $base['entrance'] = [
                'condition' => [
                    'animate_items' => 'ui-e-grid-animate'
                ],
            ];
        }

        // Specific Slider scripts
        if( strpos($this->get_name(), 'slide') !== false ){
            $type = [
                'stacked-carousel' => [
                    'condition' => [
                        'animation_style' => 'stacked',
                    ]
                ],
                // TODO: move out and pass as extra
                'circular-avatar-carousel' => [
                    'condition' => [
                        'animation_style' => 'circular_avatar',
                    ]
                ]
            ];

        // Specific Carousel scripts - if is not slider, is Carousel :)
        } else {
            $type = [
                'circular-carousel' => [
                    'condition' => [
                        'animation_style' => 'circular',
                    ]
                ],
                'fade-blur-carousel' => [
                    'condition' => [
                        'animation_style' => 'fade_blur',
                    ]
                ]
            ];
        }

        // Merge and return the scripts
        return array_merge($base, $type);
    }

	// Navigation Controls
	function TRAIT_register_navigation_controls()
	{

		$this->start_controls_section(
            'section_content_navigation',
            [
                'label' => __('Navigation', 'uicore-elements'),
            ]
        );

			$this->add_control(
				'navigation',
				[
					'label'        => __('Navigation', 'uicore-elements'),
					'type'         => Controls_Manager::SELECT,
					'default'      => 'arrows-dots',
					'options'      => [
						'arrows'    => esc_html__('Arrows', 'uicore-elements'),
						'dots'      => esc_html__('Dots', 'uicore-elements'),
						'fraction'  => esc_html__('Fractions', 'uicore-elements'),
						'arrows-dots'    => esc_html__('Arrows and Dots', 'uicore-elements'),
						'arrows-fraction'    => esc_html__('Arrows and Fraction', 'uicore-elements'),
						'none'		=> esc_html__('None', 'uicore-elements'),
					],
					'label_block' => true,
					'render_type'  => 'template',
					'frontend_available' => true,
				]
			);
			$this->add_control(
				'hr_arrows',
				[
					'label' => esc_html__( 'Arrows', 'uicore-elements' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'conditions' =>  $this->nav_conditions('arrows'),
				]
			);
			$this->start_controls_tabs(
				'arrows_tabs'
			);

				$this->start_controls_tab(
					'previous_tab',
					[
						'label' => esc_html__( 'Previous', 'uicore-elements' ),
						'conditions' =>  $this->nav_conditions('arrows'),
					]
				);

					$this->add_control(
						'previous_arrow',
						[
							'label' => esc_html__( 'Previous Arrow Icon', 'uicore-elements' ),
							'type' => Controls_Manager::ICONS,
							'default' => [
								'value' => 'fas fa-arrow-left',
								'library' => 'fa-solid',
							],
							'recommended' => [
								'fa-solid' => [
									'arrow-alt-circle-left',
									'caret-square-left',
									'angle-double-left',
									'angle-left',
									'arrow-alt-circle-left',
									'arrow-left',
									'caret-left',
									'caret-square-left',
									'chevron-circle-left',
									'chevron-left',
									'long-arrow-alt-left'
								]
							],
							'label_block' => false,
							'skin' => 'inline',
							'conditions' =>  $this->nav_conditions('arrows'),
						]
					);
					$this->add_responsive_control(
						'previous_arrow_h_position',
						[
							'label'   => __('Horizontal Orientation', 'uicore-elements'),
							'type'    => Controls_Manager::CHOOSE,
							'options' => [
								'left: 0; right: auto;' => [
									'title' => __('Left', 'uicore-elements'),
									'icon'  => 'eicon-h-align-left',
								],
								'left: 0; right: 0; margin: auto;' => [
									'title' => __('Center', 'uicore-elements'),
									'icon'  => 'eicon-h-align-center',
								],
								'left: auto; right: 0;' => [
									'title' => __('Right', 'uicore-elements'),
									'icon'  => 'eicon-h-align-right',
								],
							],
							'default' => 'left: 0; right: auto;',
							'selectors' => [
								'{{WRAPPER}} .ui-e-previous' => '{{VALUE}};'
							],
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->add_responsive_control(
						'previous_arrow_v_position',
						[
							'label'   => __('Vertical Orientation', 'uicore-elements'),
							'type'    => Controls_Manager::CHOOSE,
							'options' => [
								'top: 0; bottom: auto;' => [
									'title' => __('Top', 'uicore-elements'),
									'icon'  => 'eicon-v-align-top',
								],
								'top: 0; bottom: 0; margin: auto;' => [
									'title' => __('Center', 'uicore-elements'),
									'icon'  => 'eicon-v-align-middle',
								],
								'top: auto; bottom: 0' => [
									'title' => __('Bottom', 'uicore-elements'),
									'icon'  => 'eicon-v-align-bottom',
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ui-e-previous' => '{{VALUE}};'
							],
							'default' => 'top: 0; bottom: 0; margin: auto;',
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->add_control(
						'previous_advanced_position',
						[
							'label' => esc_html__( 'Arrow Offset', 'uicore-elements' ),
							'type' => Controls_Manager::POPOVER_TOGGLE,
							'label_off' => esc_html__( 'Default', 'uicore-elements' ),
							'label_on' => esc_html__( 'Custom', 'uicore-elements' ),
							'return_value' => 'yes',
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->start_popover();

						$this->add_responsive_control(
							'prev_arrow_h_offset',
							[
								'label' => esc_html__( 'Horizontal Offset', 'uicore-elements' ),
								'type' => Controls_Manager::SLIDER,
								'size_units' => [ 'px', '%',],
								'range' => [
									'px' => [
										'min' => -200,
										'max' => 200,
										'step' => 5,
									],
									'%' => [
										'min' => -100,
										'max' => 100,
									],
								],
								'selectors' => [
									'{{WRAPPER}}' => '--ui-e-prev-arrow-h-off:{{SIZE}}{{UNIT}};',
								],
								'condition' => [
									'previous_advanced_position' => 'yes'
								]
							]
						);
						$this->add_responsive_control(
							'prev_arrow_v_offset',
							[
								'label' => esc_html__( 'Vertical Offset', 'uicore-elements' ),
								'type' => Controls_Manager::SLIDER,
								'size_units' => [ 'px', '%',],
								'range' => [
									'px' => [
										'min' => -200,
										'max' => 200,
										'step' => 5,
									],
									'%' => [
										'min' => -100,
										'max' => 100,
									],
								],
								'selectors' => [
									'{{WRAPPER}}' => '--ui-e-prev-arrow-v-off:{{SIZE}}{{UNIT}};',
								],
								'condition' => [
									'previous_advanced_position' => 'yes'
								]
							]
						);
						$this->add_responsive_control(
							'prev_arrow_rotate',
							[
								'label' => esc_html__( 'Rotation', 'uicore-elements' ),
								'type' => Controls_Manager::SLIDER,
								'size_units' => ['px'],
								'range' => [
									'px' => [
										'min' => 0,
										'max' => 360,
										'step' => 5,
									],
								],
								'selectors' => [
									'{{WRAPPER}} .ui-e-previous > *' => 'rotate:{{SIZE}}deg;',
								],
								'condition' => [
									'previous_advanced_position' => 'yes'
								]
							]
						);

					$this->end_popover();

				$this->end_controls_tab();

				$this->start_controls_tab(
					'next_tab',
					[
						'label' => esc_html__( 'Next', 'uicore-elements' ),
						'conditions' => $this->nav_conditions('arrows'),
					]
				);

					$this->add_control(
						'next_arrow',
						[
							'label' => esc_html__( 'Previous Arrow Icon', 'uicore-elements' ),
							'type' => Controls_Manager::ICONS,
							'default' => [
								'value' => 'fas fa-arrow-right',
								'library' => 'fa-solid',
							],
							'recommended' => [
								'fa-solid' => [
									'arrow-alt-circle-right',
									'caret-square-right',
									'angle-double-right',
									'angle-right',
									'arrow-alt-circle-right',
									'arrow-right',
									'caret-right',
									'caret-square-right',
									'chevron-circle-right',
									'chevron-right',
									'long-arrow-alt-right'
								]
							],
							'label_block' => false,
							'skin' => 'inline',
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->add_responsive_control(
						'next_arrow_h_position',
						[
							'label'   => __('Horizontal Orientation', 'uicore-elements'),
							'type'    => Controls_Manager::CHOOSE,
							'options' => [
								'left: 0; right: auto;' => [
									'title' => __('Left', 'uicore-elements'),
									'icon'  => 'eicon-h-align-left',
								],
								'left: 0; right: 0; margin: auto;' => [
									'title' => __('Center', 'uicore-elements'),
									'icon'  => 'eicon-h-align-center',
								],
								'left: auto; right: 0;' => [
									'title' => __('Right', 'uicore-elements'),
									'icon'  => 'eicon-h-align-right',
								],
							],
							'default' => 'left: auto; right: 0;',
							'selectors' => [
								'{{WRAPPER}} .ui-e-next' => '{{VALUE}};'
							],
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->add_responsive_control(
						'next_arrow_v_position',
						[
							'label'   => __('Vertical Orientation', 'uicore-elements'),
							'type'    => Controls_Manager::CHOOSE,
							'options' => [
								'top: 0; bottom: auto;' => [
									'title' => __('Top', 'uicore-elements'),
									'icon'  => 'eicon-v-align-top',
								],
								'top: 0; bottom: 0; margin: auto;' => [
									'title' => __('Center', 'uicore-elements'),
									'icon'  => 'eicon-v-align-middle',
								],
								'top: auto; bottom: 0' => [
									'title' => __('Bottom', 'uicore-elements'),
									'icon'  => 'eicon-v-align-bottom',
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ui-e-next' => '{{VALUE}};'
							],
							'default' => 'top: 0; bottom: 0; margin: auto;',
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->add_control(
						'next_advanced_position',
						[
							'label' => esc_html__( 'Arrow Offset', 'uicore-elements' ),
							'type' => Controls_Manager::POPOVER_TOGGLE,
							'label_off' => esc_html__( 'Default', 'uicore-elements' ),
							'label_on' => esc_html__( 'Custom', 'uicore-elements' ),
							'return_value' => 'yes',
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->start_popover();
						$this->add_responsive_control(
							'next_arrow_h_offset',
							[
								'label' => esc_html__( 'Horizontal Offset', 'uicore-elements' ),
								'type' => Controls_Manager::SLIDER,
								'size_units' => [ 'px', '%',],
								'range' => [
									'px' => [
										'min' => -200,
										'max' => 200,
										'step' => 5,
									],
									'%' => [
										'min' => -100,
										'max' => 100,
									],
								],
								'selectors' => [
									'{{WRAPPER}}' => '--ui-e-next-arrow-h-off:{{SIZE}}{{UNIT}};',
								],
								'condition' => [
									'next_advanced_position' => 'yes'
								]
							]
						);
						$this->add_responsive_control(
							'next_arrow_v_offset',
							[
								'label' => esc_html__( 'Vertical Offset', 'uicore-elements' ),
								'type' => Controls_Manager::SLIDER,
								'size_units' => [ 'px', '%',],
								'range' => [
									'px' => [
										'min' => -200,
										'max' => 200,
										'step' => 5,
									],
									'%' => [
										'min' => -100,
										'max' => 100,
									],
								],
								'selectors' => [
									'{{WRAPPER}}' => '--ui-e-next-arrow-v-off:{{SIZE}}{{UNIT}};',
								],
								'condition' => [
									'next_advanced_position' => 'yes'
								]
							]
						);
						$this->add_responsive_control(
							'next_arrow_rotate',
							[
								'label' => esc_html__( 'Rotation', 'uicore-elements' ),
								'type' => Controls_Manager::SLIDER,
								'size_units' => ['px'],
								'range' => [
									'px' => [
										'min' => 0,
										'max' => 360,
										'step' => 5,
									],
								],
								'selectors' => [
									'{{WRAPPER}} .ui-e-next > *' => 'rotate:{{SIZE}}deg;',
								],
								'condition' => [
									'previous_advanced_position' => 'yes'
								]
							]
						);

					$this->end_popover();

				$this->end_controls_tab();

			$this->end_controls_tabs();

            $this->add_control(
                'arrows_mobile',
                [
                    'label' => esc_html__( 'Hide arrows on Mobile', 'uicore-elements' ),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'selectors' => [
                        '(mobile){{WRAPPER}} .ui-e-button' => 'display: none',
                    ],
                    'conditions' => $this->nav_conditions('arrows'),
                ]
            );

			$this->add_control(
				'hr_fraction',
				[
					'label' => esc_html__( 'Fraction', 'uicore-elements' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'conditions' => $this->nav_conditions('fraction'),
				]
			);
			$this->add_responsive_control(
				'fraction_h_position',
				[
					'label'   => __('Horizontal Orientation', 'uicore-elements'),
					'type'    => Controls_Manager::CHOOSE,
					'options' => [
						'left: 0; right: auto;' => [
							'title' => __('Left', 'uicore-elements'),
							'icon'  => 'eicon-h-align-left',
						],
						'left: 0; right: 0; margin: auto;' => [
							'title' => __('Center', 'uicore-elements'),
							'icon'  => 'eicon-h-align-center',
						],
						'left: auto; right: 0;' => [
							'title' => __('Right', 'uicore-elements'),
							'icon'  => 'eicon-h-align-right',
						],
					],
					'default' => 'left: 0; right: auto;',
					'selectors' => [
						'{{WRAPPER}} .ui-e-fraction' => '{{VALUE}};'
					],
					'conditions' => $this->nav_conditions('fraction'),
				]
			);
			$this->add_responsive_control(
				'fraction_v_position',
				[
					'label'   => __('Vertical Orientation', 'uicore-elements'),
					'type'    => Controls_Manager::CHOOSE,
					'options' => [
						'top: -25px; bottom: auto;' => [
							'title' => __('Top', 'uicore-elements'),
							'icon'  => 'eicon-v-align-top',
						],
						'top: 0; bottom: 0; margin: auto;' => [
							'title' => __('Center', 'uicore-elements'),
							'icon'  => 'eicon-v-align-middle',
						],
						'top: auto; bottom: -25px;' => [
							'title' => __('Bottom', 'uicore-elements'),
							'icon'  => 'eicon-v-align-bottom',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ui-e-fraction' => '{{VALUE}};'
					],
					'default' => 'bottom: -25px;',
					'conditions' => $this->nav_conditions('fraction'),
				]
			);
			$this->add_control(
				'fraction_offset_toggle',
				[
					'label' => esc_html__( 'Fraction Offset', 'uicore-elements' ),
					'type' => Controls_Manager::POPOVER_TOGGLE,
					'label_off' => esc_html__( 'Default', 'uicore-elements' ),
					'label_on' => esc_html__( 'Custom', 'uicore-elements' ),
					'return_value' => 'yes',
					'conditions' => $this->nav_conditions('fraction'),
				]
			);
			$this->start_popover();
				$this->add_responsive_control(
					'fraction_h_offset',
					[
						'label' => esc_html__( 'Horizontal Offset', 'uicore-elements' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%',],
						'range' => [
							'px' => [
								'min' => -80,
								'max' => 80,
								'step' => 5,
							],
							'%' => [
								'min' => -100,
								'max' => 100,
							],
						],
						'selectors' => [
							'{{WRAPPER}}' => '--ui-e-fraction-h-off:{{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'fraction_offset_toggle' => 'yes'
						]
					]
				);
				$this->add_responsive_control(
					'fraction_v_offset',
					[
						'label' => esc_html__( 'Vertical Offset', 'uicore-elements' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%',],
						'range' => [
							'px' => [
								'min' => -80,
								'max' => 80,
								'step' => 5,
							],
							'%' => [
								'min' => -100,
								'max' => 100,
							],
						],
						'selectors' => [
							'{{WRAPPER}}' => '--ui-e-fraction-v-off:{{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'fraction_offset_toggle' => 'yes'
						]
					]
				);

			$this->end_popover();

			$this->add_control(
				'hr_dots',
				[
					'label' => esc_html__( 'Dots', 'uicore-elements' ),
					'type' => Controls_Manager::HEADING,
					'separator' => 'before',
					'conditions' =>  $this->nav_conditions('dots'),
				]
			);
			$this->add_responsive_control(
				'dots_h_position',
				[
					'label'   => __('Horizontal Orientation', 'uicore-elements'),
					'type'    => Controls_Manager::CHOOSE,
					'options' => [
						'left: 0; right: auto;' => [
							'title' => __('Left', 'uicore-elements'),
							'icon'  => 'eicon-h-align-left',
						],
						'left: 0; right: 0; margin: auto;' => [
							'title' => __('Center', 'uicore-elements'),
							'icon'  => 'eicon-h-align-center',
						],
						'left: auto; right: 0;' => [
							'title' => __('Right', 'uicore-elements'),
							'icon'  => 'eicon-h-align-right',
						],
					],
					'default' => 'left: 0; right: 0; margin: auto;',
					'selectors' => [
						'{{WRAPPER}} .ui-e-dots' => '{{VALUE}};'
					],
					'conditions' =>  $this->nav_conditions('dots'),
				]
			);
			$this->add_responsive_control(
				'dots_v_position',
				[
					'label'   => __('Vertical Orientation', 'uicore-elements'),
					'type'    => Controls_Manager::CHOOSE,
					'options' => [
						'top: -25px; bottom: auto;' => [
							'title' => __('Top', 'uicore-elements'),
							'icon'  => 'eicon-v-align-top',
						],
						'top: 0; bottom: 0; margin: auto' => [
							'title' => __('Center', 'uicore-elements'),
							'icon'  => 'eicon-v-align-middle',
						],
						'top: auto; bottom: -25px !important;' => [
							'title' => __('Bottom', 'uicore-elements'),
							'icon'  => 'eicon-v-align-bottom',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .ui-e-dots' => '{{VALUE}}'
					],
					'default' => 'top: auto; bottom: -25px !important;',
					'conditions' =>  $this->nav_conditions('dots'),
				]
			);
			$this->add_control(
				'dots_advanced',
				[
					'label' => esc_html__( 'Dots Offset', 'uicore-elements' ),
					'type' => Controls_Manager::POPOVER_TOGGLE,
					'label_off' => esc_html__( 'Default', 'uicore-elements' ),
					'label_on' => esc_html__( 'Custom', 'uicore-elements' ),
					'return_value' => 'yes',
					'conditions' =>  $this->nav_conditions('dots'),
				]
			);
			$this->start_popover();
				$this->add_responsive_control(
					'dots_h_offset',
					[
						'label' => esc_html__( 'Horizontal Offset', 'uicore-elements' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%',],
						'range' => [
							'px' => [
								'min' => -100,
								'max' => 100,
								'step' => 5,
							],
							'%' => [
								'min' => -100,
								'max' => 100,
							],
						],
						'selectors' => [
							'{{WRAPPER}}' => '--ui-e-dots-h-off:{{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'dots_advanced' => 'yes'
						]
					]
				);
				$this->add_responsive_control(
					'dots_v_offset',
					[
						'label' => esc_html__( 'Vertical Offset', 'uicore-elements' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%',],
						'range' => [
							'px' => [
								'min' => -100,
								'max' => 100,
								'step' => 5,
							],
							'%' => [
								'min' => -100,
								'max' => 100,
							],
						],
						'selectors' => [
							'{{WRAPPER}}' => '--ui-e-dots-v-off:{{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'dots_advanced' => 'yes'
						]
					]
				);
				$this->add_responsive_control(
					'dots_rotate',
					[
						'label' => esc_html__( 'Rotation', 'uicore-elements' ),
						'type' => Controls_Manager::SLIDER,
						'size_units' => [ 'px'],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 360,
								'step' => 5,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ui-e-dots' => 'rotate: {{SIZE}}deg;',
						],
						'condition' => [
							'dots_advanced' => 'yes'
						]
					]
				);
			$this->end_popover();

		$this->end_controls_section();
	}
	function TRAIT_register_carousel_settings_controls()
	{
		$this->add_control(
			'animation_style',
			[
				'label'        => __('Animation', 'uicore-elements'),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'circular',
				'options'      => [
					'circular' 	  => esc_html__('Circular', 'uicore-elements'),
					'fade_blur'   => esc_html__('Fade Blur', 'uicore-elements'),
				],
				'prefix_class' => 'ui-e-animation-',
				'render_type'  => 'template',
				'frontend_available' => true,
			]
		);
		$this->add_responsive_control(
			'slides_per_view',
			[
				'label'           => __( 'Slides per View', 'uicore-elements' ),
				'type'            => Controls_Manager::SELECT,
				'desktop_default' => 3,
				'tablet_default'  => 2,
				'mobile_default'  => 1,
				'options'         => [
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
					6 => '6',
					7 => '7',
					8 => '8',
				],
				'render_type' => 'template',
				'frontend_available' => true,
				'condition' => [
					'animation_style!' => ['horizontal']
				]
			]
		);
		$this->add_control(
			'show_hidden',
			[
				'label' => esc_html__( 'Show Hidden Items', 'uicore-elements' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'hidden',
				'options' => [
					'hidden'  => esc_html__( 'Hidden', 'uicore-elements' ),
					'left'    => esc_html__( 'Show Left', 'uicore-elements' ),
					'right'   => esc_html__( 'Show Right', 'uicore-elements' ),
				],
				'condition' => [
					'animation_style' => ['fade_blur', 'circular']
				],
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'fade_edges',
			[
				'label'   => __('Fade Edges', 'uicore-elements'),
				'type'    => Controls_Manager::SWITCHER,
				'prefix_class' => 'ui-e-fade-edges-',
			]
		);
		// Add warning control with 'warning' type
		$this->add_control(
			'fade_edges_alert',
			[
				'type' => Controls_Manager::ALERT,
				'alert_type' => 'warning',
				'heading' => esc_html__( 'Careful', 'uicore-elements'),
				'content' => esc_html__( 'Fade edges will hide your navigation if outside the carousel wrapper', 'uicore-elements' ),
				'condition' => [
					'fade_edges' => 'yes',
				],
			]
		);
		// Kept for now to see if users will request it
		// $this->add_responsive_control(
		// 	'slide_position',
		// 	[
		// 		'label'   => __('Active Slide Alignment', 'uicore-elements'),
		// 		'type'    => Controls_Manager::CHOOSE,
		// 		'options' => [
		// 			'left' => [
		// 				'title' => __('Left', 'uicore-elements'),
		// 				'icon'  => 'eicon-h-align-left',
		// 			],
		// 			'center' => [
		// 				'title' => __('Center', 'uicore-elements'),
		// 				'icon'  => 'eicon-h-align-center',
		// 			],
		// 			// right alingment not supported by swiper
		// 		],
		// 		'default' => 'center',
		// 		'frontend_available' => true,
		// 	]
		// );
		$this->add_control(
			'autoplay',
			[
				'label'   => __('Autoplay', 'uicore-elements'),
				'type'    => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__('Autoplay Speed', 'uicore-elements'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'frontend_available' => true,
				'condition' => [
					'autoplay' => 'yes',
				]
			]
		);
		$this->add_control(
			'pause_on_hover',
			[
				'label' => esc_html__('Pause on Hover', 'uicore-elements'),
				'type'  => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'frontend_available' => true,
                'separator' => 'after',
				'condition' => [
					'autoplay' => 'yes',
				]
			]
		);
		$this->add_control(
			'grab_cursor',
			[
				'label' => __('Grab Cursor', 'uicore-elements'),
				'type'  => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'loop',
			[
				'label'   => __('Loop', 'uicore-elements'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'true',
				'return_value' => 'true',
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'overflow_container',
			[
				'label' => esc_html__( 'Overflow Container', 'uicore-elements' ),
				'type' => \Elementor\Controls_Manager::HIDDEN,
				'default' => Plugin::$instance->experiments->is_feature_active('container') ? 'container' : 'column',
				'frontend_available' => true, // return on the JS the container type (flexbox or column) for overflow
			]
		);
		$this->add_control(
			'observer',
			[
				'label'       => __('Observer', 'uicore-elements'),
				'description' => __('Use it when the carousel is placed in a hidden place (ex: tab, accordion).', 'uicore-elements'),
				'type'        => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'frontend_available' => true,
			]
		);
	}
	//Navigation Style Controls
	function TRAIT_register_navigation_style_controls()
	{

		$this->start_controls_section(
            'section_style_navigation',
            [
                'label'     => esc_html__('Navigation', 'uicore-elements'),
                'tab'       => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name' => 'navigation',
							'operator' => '!=',
							'value' => '',
						],
					],
				],
            ]
        );

			$this->add_control(
				'arrows_heading',
				[
					'label'     => __('Arrows', 'uicore-elements'),
					'type'      => Controls_Manager::HEADING,
					'conditions' => $this->nav_conditions('arrows'),
				]
			);

			$this->start_controls_tabs('tabs_navigation_arrows_style');

				$this->start_controls_tab(
					'tabs_nav_arrows_normal',
					[
						'label'     => __('Normal', 'uicore-elements'),
						'conditions' => $this->nav_conditions('arrows'),
					]
				);

					$this->add_control(
						'arrows_color',
						[
							'label'     => __('Color', 'uicore-elements'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ui-e-button i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ui-e-button svg' => 'fill: {{VALUE}}',
							],
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->add_control(
						'arrows_background',
						[
							'label'     => __('Background', 'uicore-elements'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ui-e-button' => 'background-color: {{VALUE}}',
							],
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->add_group_control(
						Group_Control_Border::get_type(),
						[
							'name'      => 'nav_arrows_border',
							'selector'  => '{{WRAPPER}} .ui-e-button',
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->add_control(
						'arrows_radius',
						[
							'label'      => __('Border Radius', 'uicore-elements'),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => ['px', '%'],
							'selectors'  => [
								'{{WRAPPER}} .ui-e-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->add_control(
						'arrows_padding',
						[
							'label'      => esc_html__('Padding', 'uicore-elements'),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => ['px', 'em', '%'],
							'selectors'  => [
								'{{WRAPPER}} .ui-e-button' => 'padding: {{TOP}}{{UNIT || 0}} {{RIGHT}}{{UNIT || 0}} {{BOTTOM}}{{UNIT || 0}} {{LEFT}}{{UNIT || 0}};',
							],
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->add_responsive_control(
						'arrows_size',
						[
							'label'     => __('Size', 'uicore-elements'),
							'type'      => Controls_Manager::SLIDER,
							'range'     => [
								'px' => [
									'min' => 10,
									'max' => 100,
								],
							],
							'default' => [
								'size' => 16,
								'unit' => 'px'
							],
							'selectors' => [
								'{{WRAPPER}} .ui-e-button i' => 'font-size: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
								'{{WRAPPER}} .ui-e-button svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
							],
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name'     => 'arrows_box_shadow',
							'selector' => '{{WRAPPER}} .ui-e-button',
							'conditions' => $this->nav_conditions('arrows'),
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tabs_nav_arrows_hover',
					[
						'label'     => __('Hover', 'uicore-elements'),
						'conditions' => $this->nav_conditions('arrows'),
					]
				);

					$this->add_control(
						'arrows_hover_color',
						[
							'label'     => __('Color', 'uicore-elements'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ui-e-button:hover i' => 'color: {{VALUE}}',
								'{{WRAPPER}} .ui-e-button:hover svg' => 'fill: {{VALUE}}',
							],
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->add_control(
						'arrows_hover_background',
						[
							'label'     => __('Background', 'uicore-elements'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ui-e-button:hover' => 'background-color: {{VALUE}}',

							],
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->add_control(
						'nav_arrows_hover_border_color',
						[
							'label'     => __('Border Color', 'uicore-elements'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ui-e-button:hover' => 'border-color: {{VALUE}};',
							],
							'conditions' => $this->nav_conditions('arrows'),
						]
					);
					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name'     => 'arrows_hover_box_shadow',
							'selector' => '{{WRAPPER}} .ui-e-button:hover',
							'conditions' => $this->nav_conditions('arrows'),
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'hr_1',
				[
					'type'      => Controls_Manager::DIVIDER,
					'conditions' =>  $this->nav_conditions('dots'),
				]
			);
			$this->add_control(
				'dots_heading',
				[
					'label'     => __('Dots', 'uicore-elements'),
					'type'      => Controls_Manager::HEADING,
					'conditions' =>  $this->nav_conditions('dots'),
				]
			);

			$this->start_controls_tabs('tabs_navigation_dots_style');

				$this->start_controls_tab(
					'tabs_nav_dots_normal',
					[
						'label'     => __('Normal', 'uicore-elements'),
						'conditions' =>  $this->nav_conditions('dots'),
					]
				);

					$this->add_control(
						'dots_color',
						[
							'label'     => __('Color', 'uicore-elements'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ui-e-dots .dot' => 'background-color: {{VALUE}}',
							],
							'conditions' =>  $this->nav_conditions('dots'),
						]
					);
					$this->add_control(
						'dots_space_between',
						[
							'label'     => __('Space Between Dots', 'uicore-elements'),
							'type'      => Controls_Manager::SLIDER,
							'selectors' => [
								'{{WRAPPER}} .ui-e-dots .dot' => 'margin: 0 {{SIZE}}{{UNIT}};',
							],
							'range' => [
								'px' =>[
									'min' => 0,
									'max' => 15,
									'step' => 1,
								]
							],
							'default' => [
								'unit' => 'px',
								'size' => 8,
							],
							'conditions' =>  $this->nav_conditions('dots'),
						]
					);
					$this->add_responsive_control(
						'dots_size',
						[
							'label'     => __('Size', 'uicore-elements'),
							'type'      => Controls_Manager::SLIDER,
							'range'     => [
								'px' => [
									'min' => 5,
									'max' => 20,
								],
							],
							'default' => [
								'unit' => 'px',
								'size' => 8,
							],
							'selectors' => [
								'{{WRAPPER}} .ui-e-dots .dot' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
							],
							'conditions' =>  $this->nav_conditions(
								'dots',
								[
									[
									'name' => 'advanced_dots_size',
									'operator' => '===',
									'value' => '',
									]
								]
							),
						]
					);
					$this->add_control(
						'advanced_dots_size',
						[
							'label'     => __('Advanced Size', 'uicore-elements'),
							'type'      => Controls_Manager::SWITCHER,
							'conditions' =>  $this->nav_conditions('dots'),
						]
					);
					$this->add_responsive_control(
						'advanced_dots_width',
						[
							'label'     => __('Width', 'uicore-elements'),
							'type'      => Controls_Manager::SLIDER,
							'range'     => [
								'px' => [
									'min' => 1,
									'max' => 50,
								],
							],
							'default' => [
								'size' => 6,
								'unit' => 'px',
							],
							'selectors' => [
								'{{WRAPPER}} .ui-e-dots .dot' => 'width: {{SIZE}}{{UNIT}};',
							],
							'conditions' =>  $this->nav_conditions(
								'dots',
								[
									[
									'name' => 'advanced_dots_size',
									'operator' => '==',
									'value' => 'yes',
									]
								]
							),
						]
					);
					$this->add_responsive_control(
						'advanced_dots_height',
						[
							'label'     => __('Height', 'uicore-elements'),
							'type'      => Controls_Manager::SLIDER,
							'range'     => [
								'px' => [
									'min' => 1,
									'max' => 50,
								],
							],
							'default' => [
								'size' => 6,
								'unit' => 'px',
							],
							'selectors' => [
								'{{WRAPPER}} .ui-e-dots .dot' => 'height: {{SIZE}}{{UNIT}};',
							],
							'conditions' =>  $this->nav_conditions(
								'dots',
								[
									[
									'name' => 'advanced_dots_size',
									'operator' => '==',
									'value' => 'yes',
									]
								]
							),
						]
					);
					$this->add_control(
						'advanced_dots_radius',
						[
							'label'      => esc_html__('Border Radius', 'uicore-elements'),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => ['px', '%'],
							'selectors'  => [
								'{{WRAPPER}} .ui-e-dots .dot' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
							'conditions' =>  $this->nav_conditions(
								'dots',
								[
									[
									'name' => 'advanced_dots_size',
									'operator' => '==',
									'value' => 'yes',
									]
								]
							),
						]
					);
					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name'     => 'dots_box_shadow',
							'selector' => '{{WRAPPER}} .ui-e-dots .dot',
							'conditions' =>  $this->nav_conditions('dots'),
						]
					);

				$this->end_controls_tab();

				$this->start_controls_tab(
					'tabs_nav_dots_active',
					[
						'label'     => __('Active', 'uicore-elements'),
						'conditions' =>  $this->nav_conditions('dots'),
					]
				);

					$this->add_control(
						'active_dot_color',
						[
							'label'     => __('Color', 'uicore-elements'),
							'type'      => Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ui-e-dots .dot.is-selected' => 'background-color: {{VALUE}}',
							],
							'conditions' =>  $this->nav_conditions('dots'),
						]
					);
					$this->add_responsive_control(
						'active_dots_size',
						[
							'label'     => __('Size', 'uicore-elements'),
							'type'      => Controls_Manager::SLIDER,
							'range'     => [
								'px' => [
									'min' => 5,
									'max' => 20,
								],
							],
							'selectors' => [
								'{{WRAPPER}} .ui-e-dots .dot.is-selected' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
							],
							'conditions' =>  $this->nav_conditions(
								'dots',
								[
									[
									'name' => 'advanced_dots_size',
									'operator' => '===',
									'value' => '',
									]
								]
							),
						]
					);
					$this->add_responsive_control(
						'active_advanced_dots_width',
						[
							'label'     => __('Width', 'uicore-elements'),
							'type'      => Controls_Manager::SLIDER,
							'range'     => [
								'px' => [
									'min' => 1,
									'max' => 50,
								],
							],
							'default' => [
								'size' => 15,
								'unit' => 'px',
							],
							'selectors' => [
								'{{WRAPPER}} .ui-e-dots .dot.is-selected' => 'width: {{SIZE}}{{UNIT}};',
							],
							'conditions' =>  $this->nav_conditions(
								'dots',
								[
									[
									'name' => 'advanced_dots_size',
									'operator' => '==',
									'value' => 'yes',
									]
								]
							),
						]
					);
					$this->add_responsive_control(
						'active_advanced_dots_height',
						[
							'label'     => __('Height', 'uicore-elements'),
							'type'      => Controls_Manager::SLIDER,
							'range'     => [
								'px' => [
									'min' => 1,
									'max' => 50,
								],
							],
							'default' => [
								'size' => 6,
								'unit' => 'px',
							],
							'selectors' => [
								'{{WRAPPER}} .ui-e-dots .dot.is-selected' => 'height: {{SIZE}}{{UNIT}};',
							],
							'conditions' =>  $this->nav_conditions(
								'dots',
								[
									[
									'name' => 'advanced_dots_size',
									'operator' => '==',
									'value' => 'yes',
									]
								]
							),
						]
					);
					$this->add_control(
						'active_advanced_dots_radius',
						[
							'label'      => esc_html__('Border Radius', 'uicore-elements'),
							'type'       => Controls_Manager::DIMENSIONS,
							'size_units' => ['px', '%'],
							'selectors'  => [
								'{{WRAPPER}} .ui-e-dots .dot.is-selected' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
							'conditions' =>  $this->nav_conditions(
								'dots',
								[
									[
									'name' => 'advanced_dots_size',
									'operator' => '==',
									'value' => 'yes',
									]
								]
							),
						]
					);
					$this->add_group_control(
						Group_Control_Box_Shadow::get_type(),
						[
							'name'     => 'dots_active_box_shadow',
							'selector' => '{{WRAPPER}} .ui-e-dots .dot.is-selected',
							'conditions' =>  $this->nav_conditions('dots'),
						]
					);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'hr_2',
				[
					'type'      => Controls_Manager::DIVIDER,
					'conditions' => $this->nav_conditions('fraction'),
				]
			);
			$this->add_control(
				'fraction_heading',
				[
					'label'     => __('Fractions', 'uicore-elements'),
					'type'      => Controls_Manager::HEADING,
					'conditions' => $this->nav_conditions('fraction'),
				]
			);
			$this->add_control(
				'fraction_bg_color',
				[
					'label'     => __('Background Color', 'uicore-elements'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ui-e-fraction' => 'background-color: {{VALUE}}',
					],
					'conditions' => $this->nav_conditions('fraction'),
				]
			);
			$this->add_control(
				'fraction_padding',
				[
					'label'      => esc_html__('Padding', 'uicore-elements'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', 'em', '%'],
					'selectors'  => [
						'{{WRAPPER}} .ui-e-fraction' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'conditions' => $this->nav_conditions('fraction'),
				]
			);
			$this->add_control(
				'fraction_radius',
				[
					'label'      => esc_html__('Border Radius', 'uicore-elements'),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', 'em', '%'],
					'selectors'  => [
						'{{WRAPPER}} .ui-e-fraction' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'conditions' => $this->nav_conditions('fraction'),
				]
			);
			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'      => 'fraction_border',
					'selector'  => '{{WRAPPER}} .ui-e-fraction',
					'conditions' => $this->nav_conditions('fraction'),
				]
			);
			$this->add_control(
				'fraction_color',
				[
					'label'     => __('Color', 'uicore-elements'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ui-e-fraction, {{WRAPPER}} .ui-e-fraction .ui-e-total' => 'color: {{VALUE}}',
					],
					'conditions' => $this->nav_conditions('fraction'),
				]
			);
			$this->add_control(
				'active_fraction_color',
				[
					'label'     => __('Active Color', 'uicore-elements'),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .ui-e-fraction .swiper-pagination-current' => 'color: {{VALUE}}',
					],
					'conditions' => $this->nav_conditions('fraction'),
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'      => 'fraction_typography',
					'label'     => esc_html__('Typography', 'uicore-elements'),
					'selector'  => '{{WRAPPER}} .ui-e-fraction span, {{WRAPPER}} .ui-e-fraction',
					'conditions' => $this->nav_conditions('fraction'),
				]
			);

		$this->end_controls_section();
	}
	/**
     * Register Additional Controls that might change depending on the widget
     *
     * @param bool $is_slider - Enable specific slider control(s)
     */
	function TRAIT_register_carousel_additional_controls($is_slider = false)
	{
		$this->add_control(
			'carousel_gap',
			[
				'label'   => __('Item Gap', 'uicore-elements'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range'   => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'frontend_available' => true,
			]
		);
		$this->add_control(
			'match_height',
			[
				'label'     => __('Match Item Height', 'uicore-elements'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'prefix_class' => 'ui-e-match-height-',
				'selectors' => [
					'{{WRAPPER}} .ui-e-wrp' => 'height: auto',
					'{{WRAPPER}} .ui-e-animations-wrp, {{WRAPPER}} .ui-e-item' => 'height: 100%'
				]
			]
		);
        if($is_slider) {
            $this->add_control(
                'slider_height',
                [
                    'label'   => __('Slide Height', 'uicore-elements'),
                    'type'    => Controls_Manager::SLIDER,
                    'size_units' => [ 'px', '%', 'em', 'rem' ],
                    'range'   => [
                        'px' => [
                            'min' => 80,
                            'max' => 1000,
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ui-e-wrp' => 'height: {{SIZE}}{{UNIT}}; max-height: {{SIZE}}{{UNIT}}',
                        '{{WRAPPER}} .ui-e-item' => 'height: {{SIZE}}{{UNIT}}; max-height: {{SIZE}}{{UNIT}}',
                    ]
                ]
            );
        }
	}

	// Navigation rendering
	function render_carousel_dots()
	{
		?>
			<div class="swiper-pagination ui-e-dots"></div>
		<?php
	}
	function render_carousel_arrows()
	{
		$settings = $this->get_settings_for_display();
		?>
			<div class="ui-e-button ui-e-previous" role="button" aria-label="Previous slide">
				<?php Icons_Manager::render_icon( $settings['previous_arrow'], [ 'aria-hidden' => 'true' ] ); ?>
			</div>
			<div class="ui-e-button ui-e-next" role="button" aria-label="Next slide">
				<?php Icons_Manager::render_icon( $settings['next_arrow'], [ 'aria-hidden' => 'true' ] ); ?>
			</div>
		<?php
	}
	function render_carousel_fraction()
	{
		?>
			<div class="ui-e-fraction">
				<span class="ui-e-current"></span>
				/
				<span class="ui-e-total"></span>
			</div>
		<?php
	}
	function TRAIT_render_carousel_navigations()
	{
		$navigation = $this->get_settings_for_display('navigation');

		// Migration code from old navigation select2 array values - TODO: removed to soon apparently, keep it for more releases.
		if(is_array($navigation)) {
			$navigation = implode('-', $navigation);
		}

		if(strpos($navigation, 'dots') !== false) {
			$this->render_carousel_dots();
		}
		if(strpos($navigation, 'arrows') !== false) {
			$this->render_carousel_arrows();
		}
		if(strpos($navigation, 'fraction') !== false) {
			$this->render_carousel_fraction();
		}
	}
}