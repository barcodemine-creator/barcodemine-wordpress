<?php
namespace UiCore\Elementor;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use UiCore\Helper;

defined('ABSPATH') || exit();

/**
 * Highlighted Text
 *
 * @author Andrei Voica <andrei@uicore.co>
 * @since 1.0.3
 */
class HighlightedText extends Widget_Base
{
    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
    }

    public function get_name()
    {
        return 'highlighted-text';
    }
    public function get_categories()
    {
        return ['uicore'];
    }

    public function get_title()
    {
        return __('Highlighted Text', 'uicore-framework');
    }

    public function get_icon()
    {
        return 'eicon-animated-headline ui-e-widget';
    }

    public function get_keywords()
    {
        return [ 'headline', 'heading', 'animation', 'title', 'text' ];
    }

    public function get_style_depends() {
        Helper::register_widget_style('highlighted-text');
		return [ 'ui-e-highlighted-text' ];
	}

    protected function register_controls()
    {
        $this->start_controls_section(
			'text_elements',
			[
				'label' => __( 'Text', 'uicore-framework' ),
			]
        );
        $repeater = new Repeater();

		$repeater->add_control(
			'type',
			[
				'label' => esc_html__( 'Type', 'uicore-framework' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'text',
				'options' => [
					'text' => esc_html__( 'Text', 'uicore-framework' ),
					'icon'  => esc_html__( 'Icon', 'uicore-framework' ),
					'image' => esc_html__( 'Image', 'uicore-framework' ),
				],
				'selectors' => [
					'{{WRAPPER}} .uicore-repeater-selector' => 'border-style: {{VALUE}};',
				],
				'render_type' => 'template',
			]
		);

        $repeater->add_control(
            'text',
            [
                'label' => __( 'Text', 'elementor' ),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
                'placeholder' => __( 'Some Text', 'elementor' ),
                'default' => __( 'Some Text', 'elementor' ),
				'condition' => [ 'type' => 'text' ],
            ]
        );

		$repeater->add_control(
			'headline_style',
			[
				'label' => __( 'Highlight Style', 'uicore-framework' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => __( 'None', 'uicore-framework' ),
					'color'	=> __( 'No Stroke', 'uicore-framework' ),
					'stroke1' => __( 'Stroke 1', 'uicore-framework' ),
					'stroke2' => __( 'Stroke 2', 'uicore-framework' ),
					'stroke3' => __( 'Stroke 3', 'uicore-framework' ),
					'stroke4' => __( 'Stroke 4', 'uicore-framework' ),
					'stroke5' => __( 'Stroke 5', 'uicore-framework' ),
					'stroke6' => __( 'Stroke 6', 'uicore-framework' ),
					'stroke7' => __( 'Stroke 7', 'uicore-framework' ),
					'stroke8' => __( 'Stroke 8', 'uicore-framework' ),
					'stroke9' => __( 'Stroke 9', 'uicore-framework' ),
				],
				'render_type' => 'template',
				'condition' => [ 'type' => 'text' ],
			]
        );

		$repeater->add_control(
			'icon',
			[
				'label' => esc_html__( 'Icon', 'uicore-framework' ),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-circle',
					'library' => 'fa-solid',
				],
				'recommended' => [
					'fa-solid' => [
						'circle',
						'dot-circle',
						'square-full',
					],
					'fa-regular' => [
						'circle',
						'dot-circle',
						'square-full',
					],
				],
				'condition' => [ 'type' => 'icon' ],
			]
		);

		$repeater->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Color', 'uicore-framework' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}  {{CURRENT_ITEM}}' => 'color: {{VALUE}} !important',
				],
				'condition' => [ 'type' => 'icon' ],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label' => esc_html__( 'Choose Image', 'uicore-framework' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
				'condition' => [ 'type' => 'image' ],
			]
		);

		$repeater->add_control(
			'size',
			[
				'label' => __('Size', 'uicore-framework'),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => '--ui-e-asset-size: calc({{SIZE}} / 100);',
				],
				'condition' => [ 'type' => ['icon', 'image'] ],
			]
		);
		
        $this->add_control(
            'content',
            [
                'label' => __( 'Content', 'uicore-framework' ),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'text' => __( 'This is my awesome', 'uicore-framework' ),
                        'headline_style' => 'none',
                    ],
                    [
                        'text' => __( 'highlight', 'uicore-framework' ),
                        'headline_style' => 'stroke1',
                    ],
                    [
                        'text' => __( 'text.', 'uicore-framework' ),
                        'headline_style' => 'none',
                    ],
                ],
                'title_field' => '{{{ type == "text" ? text : type }}}',
                'render_type' => 'template',
            ]
            );

		$this->add_responsive_control(
			'alignment',
			[
				'label' => __( 'Alignment', 'uicore-framework' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'uicore-framework' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'uicore-framework' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'uicore-framework' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'tag',
			[
				'label' => __( 'HTML Tag', 'uicore-framework' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
					'span' => 'span',
					'p' => 'p',
				],
				'default' => 'h2',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_marker',
			[
				'label' => __( 'Shape', 'uicore-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'marker_color',
			[
				'label' => __( 'Color', 'uicore-framework' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
				'selectors' => [
					'{{WRAPPER}} .ui-e-headline-text path' => 'stroke: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'stroke_width',
			[
				'label' => __( 'Width', 'uicore-framework' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
                ],
                'default' => [
                    'size' => '40',
                    'unit' => 'px'
				],
				'selectors' => [
					'{{WRAPPER}} .ui-e-headline-text path' => 'stroke-width: {{SIZE}}',
				],
			]
        );

		$this->add_control(
			'vertical_offset',
			[
				'label' => __( 'Vertical Offset', 'uicore-framework' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
					],
					'px' => [
						'min' => -100,
						'max' => 100,
					],
                ],
                'default' => [
                    'size' => '0',
                    'unit' => '%',
				],
                'size_units'=>['%', 'px'],
				'selectors' => [
					'{{WRAPPER}} .ui-e-headline-text svg' => 'bottom: {{SIZE}}{{UNIT}}',
				],
			]
        );
        $this->add_control(
			'shape_animation',
			[
				'label' => __( 'Animation', 'uicore-framework' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'On', 'uicore-framework' ),
				'label_off' => __( 'Off', 'uicore-framework' ),
                'default' => 'animate',
                'separator' => 'before',
                'return_value' =>'animate',
                'prefix_class' => 'ui-e-a-',
			]
		);

		$this->add_control(
			'shape_animation_delay',
			[
				'label' => __( 'Animation Delay', 'uicore-framework' ) . ' (ms)',
				'type' => Controls_Manager::NUMBER,
				'default' => '',
				'min' => 0,
				'step' => 100,
				'condition' => [
					'shape_animation' => 'animate',
				],
				'render_type' => 'template'
				// 'render_type' => 'none',
				// 'selectors' => [
				// 	'{{WRAPPER}} .ui-e-headline-text .uicore-svg-wrapper' => 'animation-delay: {{SIZE}}ms',
				// ]
			]
		);


		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label' => __( 'Headline', 'uicore-framework' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_color',
			[
				'label' => __( 'Text Color', 'uicore-framework' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .ui-e-highlight-icon, {{WRAPPER}} .ui-e-headline-text' => 'color: {{VALUE}}',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .ui-e-highlight-icon, {{WRAPPER}} .ui-e-headline-text, {{WRAPPER}} .ui-e-highlight-image',
			]
		);

		$this->add_control(
			'higlighted_title_color',
			[
				'label' => __( 'Highlighted Text Color', 'uicore-framework' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .ui-e-headline-highlighted' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'higlighted_title_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
				'selector' => '{{WRAPPER}} .ui-e-headline-highlighted',
			]
		);


		$this->end_controls_section();

		//add Split text animation support
		// Extender::split_animation($this);
    }


    protected function get_svg($return,$type='')
    {
        $paths = [
            'none'=> null,
            'color'=> null,
            'stroke1'=>"<path d='M15.2,133.3L15.2,133.3c121.9-7.6,244-9.9,366.1-6.8c34.6,0.9,69.1,2.3,103.7,4'/>",

            'stroke2'=>"<path d='M479,122c-13.3-1-26.8-1-40.4-2.3c-31.8-1.2-63.7,0.4-95.3,0.6c-38.5,1.5-77,2.3-115.5,5.2
				c-41.6,1.2-83.3,5-124.9,5.2c-5.4,0.4-11,1-16.4,0.8c-21.9-0.4-44.1,1.9-65.6-3.5'/>",

            'stroke3'=>"<path d='M15,133.4c19-12.7,48.1-11.4,69.2-8.2
				c6.3,1.1,12.9,2.1,19.2,3.4c16.5,3.2,33.5,6.3,50.6,5.5c12.7-0.6,24.9-3.4,36.7-6.1c11-2.5,22.4-5.1,34.2-5.9
				c24.3-1.9,48.5,3.4,71.9,8.4c27.6,6.1,53.8,11.8,80.4,6.8c9.9-1.9,19.2-5.3,28.3-8.4c8.2-3,16.9-5.9,25.9-8
				c20.3-4.4,45.8-1.1,53.6,12.2'/>",

            'stroke4'=>"<path d='M18,122.6c42.3-4.6,87.4-5.1,130.3-1.6'/>
				<path d='M166.7,121.3c29.6,1.6,60,3.3,90.1,1.8c12.4-0.5,24.8-1.6,36.9-2.7c7.3-0.7,14.8-1.3,22.3-1.8
				c55.5-4.2,112.6-1.8,166,1.1'/>
				<path d='M57.8,133c30.8-0.7,62,1.1,92.1,2.7c30.5,1.8,62,3.6,93.2,2.7c20.4-0.5,41.1-2.4,61.1-4
					c37.6-3.1,76.5-6.4,113.7-2'/>",

            'stroke5'=>"<path d='M53.4,135.8c-12.8-1.5-25.6-1.3-38.3,0.7'/>
				<path d='M111.2,136c-12.2-0.2-24.4-0.5-36.7-0.8'/>
				<path d='M163.3,135.2c-12.2,0.2-24.4,0.5-36.6,0.8'/>
				<path d='M217.8,134.7c-12.5,0.6-24.9,1.2-37.4,1.8'/>
				<path d='M274.7,135.5c-12.8,0.1-25.5,0.1-38.3,0.2'/>
				<path d='M327.6,135.1c-13.6-0.8-27.2-0.3-40.7,1.4'/>
				<path d='M378.8,134.7c-12.2,0.6-24.4,1.2-36.6,1.8'/>
				<path d='M432.5,136.4c-12.2-0.6-24.4-1.1-36.6-1.7'/>
				<path d='M487.9,136.1c-11.6-1.3-23.3-1.4-35-0.2'/>",

            'stroke6'=>"<path d='M14.4,111.6c0,0,202.9-33.7,471.2,0c0,0-194-8.9-397.3,24.7c0,0,141.9-5.9,309.2,0'/>",

            'stroke7'=>"<path d='M15.2 133.3H485'/>",
			'stroke8'=>'<path d="M1.65186 148.981C1.65186 148.981 73.8781 98.5943 206.859 93.0135C339.841 87.4327 489.874 134.065 489.874 134.065"/>',
			'stroke9'=>'<path d="M7 74.5C7 74.5 104 127 252 117C400 107 494.5 49 494.5 49C494.5 49 473.5 59 461.5 74.5C449.5 90 449.5 107 449.5 107"/>
			<path d="M20.5 101.5C20.5 101.5 93 133.5 180.5 142.5C268 151.5 347 127.5 347 127.5"/>'
        ];

		if($return === 'list'){
			foreach($paths as $name => $path){
				if($name === 'none'){
					$svg[$name] = "";
				}else{
					$svg[$name] = '<span class="uicore-svg-wrapper"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none">'.$path.'</svg></span>';
				}
			}
		}else{
			if($type === 'none'){
                $svg = "";
            }else{
                $svg = '<span class="uicore-svg-wrapper"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 150" preserveAspectRatio="none">'.$paths[$type].'</svg></span>';
            }
		}

     	return $svg;
    }

	protected function render()
    {
        $settings = $this->get_settings_for_display();
        $tag = $settings['tag'];
        $content = $settings['content'];
		$delay =  $settings['shape_animation_delay'];
        ?>
		<<?php echo $tag;  echo $delay ? ' data-delay="'.$delay.'"' : ''; ?> class="ui-e--highlighted-text" >
        <?php


        foreach($content as $item){
            $svg_markup = null;
			switch ($item['type']) {
				case 'text':
					if($item['headline_style'] === 'none'){
						echo '<span class="ui-e-headline-text">' . $item['text'] . '</span>';
					}else{
						$svg_markup = $this->get_svg('single',$item['headline_style']);
						echo 	'<span class="ui-e-headline-text ui-e-headline-'.$item['headline_style'].'">
									<span class="whitespace"> </span><span><span class="ui-e-headline-text ui-e-headline-highlighted">'.$item['text'] .'</span>' . $svg_markup .'</span><span class="whitespace"> </span></span>';
					}
					break;
				case 'icon':
					echo "<span class='ui-e-highlight-icon elementor-repeater-item-" . esc_attr($item['_id']) . "'>";
					\Elementor\Icons_Manager::render_icon($item['icon'], ['aria-hidden' => 'true', 'class'=>'char']);
					echo "</span>";
					break;
				case 'image':
					echo '<span class="ui-e-highlight-image elementor-repeater-item-' . esc_attr($item['_id']) . '">'.wp_get_attachment_image($item['image']['id'], "medium", false, ["loading" => false, "class"=>'char']).'</span>';
					break;
				default:
					break;
			}

        }
        ?>
        </<?php echo $tag; ?>>
        <?php

    }
    protected function content_template() {
        ?>
        <#
        var svgs = <?php echo json_encode($this->get_svg('list')); ?>;
        #>
        <{{{ settings.tag }}} class="ui-e--highlighted-text">
        <#
        settings.content.forEach( function (item){
			switch (item.type) {
				case 'text':
					if(item.headline_style === 'none'){
            		    #><span class="ui-e-headline-text">{{{ item.text }}}</span><#
            		}else{
            		    #><span class="ui-e-headline-text {{{ 'ui-e-headline-' + item.headline_style }}}"><span class="whitespace"> </span><span><span class="ui-e-headline-highlighted">{{{ item.text }}}</span>{{{svgs[item.headline_style]}}}</span><span class="whitespace"> </span></span><#
            		}
					break;
				case 'icon':
					#><span class="char ui-e-highlight-icon elementor-repeater-item-{{{ item._id }}}">{{{ elementor.helpers.renderIcon( view, item.icon, { 'aria-hidden': true }, 'i' , 'value' ) }}}</span><#
					break;
				case 'image':
					var image = {
						id: item.image.id,
						url: item.image.url,
						size: item.thumbnail_size,
						dimension: item.thumbnail_custom_dimension,
						model: view.getEditModel()
					};
					var image_url = elementor.imagesManager.getImageUrl( image );
					#><span class="ui-e-highlight-image elementor-repeater-item-{{{ item._id }}}"><img class="char" src="{{{ image_url }}}" /></span><#
					break;
				default:
					break;
			}
        });
        #>
        </{{{ settings.tag }}}>
		<?php
	}
}
\Elementor\Plugin::instance()->widgets_manager->register(new HighlightedText());
