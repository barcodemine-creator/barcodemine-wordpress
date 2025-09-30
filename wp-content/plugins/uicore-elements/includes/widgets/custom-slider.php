<?php
namespace UiCoreElements;
use Elementor\Plugin;

defined('ABSPATH') || exit();

/**
 * Custom Slider
 *
 * @author Lucas Marini Falbo <lucas@uicore.co>
 * @since 1.0.7
 */

class CustomSlider extends CustomCarousel {

    public function get_name()
    {
        return 'uicore-custom-slider';
    }
    public function get_title()
    {
        return esc_html__('Custom Slider', 'uicore-elements');
    }
    public function get_icon()
    {
        return 'eicon-slides ui-e-widget';
    }
    public function get_categories()
    {
        return ['uicore'];
    }
    public function get_keywords()
    {
        return ['slide', 'carousel', 'nested'];
    }
    public function get_styles()
    {
        $styles = [
            'custom-slider',
            'carousel',
            'animation', // hover animations
            'entrance', // entrance basic style
        ];
        if(!class_exists('\UiCore\Core') && !class_exists('\UiCoreAnimate\Base')){
            $styles['e-animations'] = [ // entrance animations
                'external' => true,
            ];
        }
        return $styles;
    }

    protected function register_controls(bool $is_slider = false)
    {

        parent::register_controls(true); // inherit original controls and enables slider height

        // Change default border radius to zero
        $this->update_control( 'item_border_radius', [
            'default' => [
                'top' => 0,
                'right' => 0,
                'bottom' => 0,
                'left' => 0,
                'unit' => 'px',
                'isLinked' => true,
            ],
        ]);
        // Add special animation slide
        $this->update_control('animation_style',[
            'default' => 'fade',
            'options' => [
                'coverflow'  => esc_html__('Coverflow', 'uicore-elements'),
                'fade'  => esc_html__('Fade', 'uicore-elements'),
                'cards'	  => esc_html__('Cards', 'uicore-elements'),
                'flip'	  => esc_html__('Flip', 'uicore-elements'),
                'creative'	  => esc_html__('Creative', 'uicore-elements'),
                'stacked'	  => esc_html__('Stacked', 'uicore-elements'),
            ]
        ]);
        // Decrease default item padding
        $this->update_control('item_padding', [
            'default' => [
                'top' => 25,
                'right' => 25,
                'bottom' => 25,
                'left' => 25,
                'unit' => 'px',
                'isLinked' => true,
            ],
        ]);

        // Remove controls that are meant for carousel, not slide type widgets
        $this->remove_responsive_control('slides_per_view');
        $this->remove_control('show_hidden');
        $this->remove_control('fade_edges');
        $this->remove_control('fade_edges_alert');
        $this->remove_control('match_height');
        $this->remove_control('carousel_gap');
    }
}
\Elementor\Plugin::instance()->widgets_manager->register(new CustomSlider());