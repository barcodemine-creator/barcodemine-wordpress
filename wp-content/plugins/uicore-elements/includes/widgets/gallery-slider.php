<?php
namespace UiCoreElements;

use Elementor\Controls_Manager;

defined('ABSPATH') || exit();

/**
 * Gallery Slider
 *
 * Use Gallery Carousel as base
 *
 * @author Lucas Marini Falbo <lucas95@uicore.co>
 * @since 1.0.14
 */

class GallerySlider extends GalleryCarousel
{
    public function get_name()
    {
        return 'uicore-gallery-slider';
    }
    public function get_title()
    {
        return esc_html__('Gallery Slider', 'uicore-elements');
    }
    public function get_icon()
    {
        return 'eicon-slides ui-e-widget';
    }
    public function get_styles()
    {
        $styles = parent::get_styles();

        // replace 'gallery-carousel' for 'gallery-slider'
        unset($styles['gallery-carousel']);
        $styles[] = 'gallery-slider';

        return $styles;
    }
    protected function register_controls()
    {
        parent::register_controls(); // keep original controls

        // Add special animation slide
        $this->update_control(
            'animation_style',
            [
                'default' => 'fade',
                'options' => [
                    'coverflow'  => esc_html__('Coverflow', 'uicore-elements'),
                    'fade'  => esc_html__('Fade', 'uicore-elements'),
                    'cards'	  => esc_html__('Cards', 'uicore-elements'),
                    'flip'	  => esc_html__('Flip', 'uicore-elements'),
                    'creative'	  => esc_html__('Creative', 'uicore-elements'),
                    'stacked'	  => esc_html__('Stacked', 'uicore-elements'),
                ]
            ]
        );

        // Remove conditions from image height
        $this->update_responsive_control(
            'image_height',
            [
                'conditions' => false,
            ]
        );

        // Remove item entrance and hover animation
        $this->remove_control('animate_items');
        $this->remove_control('item_hover_animation');

        // Remove item active state styles
        $this->remove_control('tab_item_active');
        $this->remove_control('item_active_background');
        $this->remove_control('item_active_border_color');
        $this->remove_control('item_active_box_shadow');

        // Remove controls that are meant for carousel, not slide type widgets
        $this->remove_responsive_control('slides_per_view');
        $this->remove_control('show_hidden');
        $this->remove_control('fade_edges');
        $this->remove_control('fade_edges_alert');
        $this->remove_control('match_height');
        $this->remove_control('carousel_gap');
        //$this->remove_control('main_image'); TODO: remove this control after releasing it on carousel

        // Add vertical content alignment control
        $this->start_injection([
            'of' => 'layout',
            'at' => 'after',
        ]);

            $this->add_responsive_control(
                'content_v_alignment',
                [
                    'label'     => __('Vertical Alignment', 'uicore-elements'),
                    'type'      => Controls_Manager::CHOOSE,
                    'default' => 'start',
                    'options'   => [
                        'start'    => [
                            'title' => __('Left', 'uicore-elements'),
                            'icon'  => 'eicon-align-start-v',
                        ],
                        'center'  => [
                            'title' => __('Center', 'uicore-elements'),
                            'icon'  => 'eicon-align-center-v',
                        ],
                        'end'   => [
                            'title' => __('Right', 'uicore-elements'),
                            'icon'  => 'eicon-align-end-v',
                        ],
                    ],
                    'condition' => [
                        'layout' => 'ui-e-overlay'
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .ui-e-content' => 'justify-content: {{VALUE}};',
                    ]
                ]
            );

        $this->end_injection();
    }
}
\Elementor\Plugin::instance()->widgets_manager->register(new GallerySlider());