<?php
namespace UiCoreElements\Widgets\ThemeBuilder;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use UiCoreElements\Helper;
use UiCoreElements\Utils\Meta_Trait;

defined('ABSPATH') || exit();

/**
 * The Title widget.
 *
 * @since 1.0.0
 */
class PostMeta extends Widget_Base {

	use Meta_Trait;

	public function get_name() {
		return 'uicore-post-meta';
	}
	public function get_title() {
		return esc_html__( 'Post Meta', 'uicore-elements' );
	}
	public function get_icon() {
		return 'eicon-post-info ui-e-widget';
	}
	public function get_categories() {
		return ['uicore', 'uicore-theme-builder' ];
	}

	public function get_style_depends() {
		return [ 'post-meta' ];
	}
	public function get_keywords() {
		return [ 'post', 'meta', 'info' ];
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'uicore-elements' ),
			]
		);

		$this->add_control('meta_list',[
			'label' => esc_html__( 'Meta Data', 'uicore-elements' ),
			'type' => \Elementor\Controls_Manager::REPEATER,
			'fields' => $this->get_meta_content_controls(),
			'default' => [
				[
					'type' => 'author'
				],
			],
				'title_field' => '<span style="text-transform: capitalize">{{{ type }}}</span>',
				'prevent_empty' => false,
				'separator'=> 'before'
			]
		);
        $this->add_responsive_control('content_align',
			[
				'label' => esc_html__( 'Content Alignment', 'uicore-elements' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'uicore-elements' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'uicore-elements' ),
						'icon' => 'eicon-text-align-center',
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section(
			'section_meta_style',
			[
				'label' => esc_html__( 'Style', 'uicore-elements' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->get_meta_style_controls();
		$this->end_controls_section();

	}

    /**
     * Match each meta icon size (if SVG) with the correspondent font size set by the user.
     *
     * Based on the post component similar function.
     */
    function set_meta_font_size_to_svg()
    {
        // Required specifically for SVG icons
        if( \Elementor\Plugin::$instance->experiments->is_feature_active('e_font_icon_svg') == false ){
            return;
        }

        $font_size = $this->get_settings_for_display('tb-meta_meta_typography_font_size');
        $default_size = '16px';

        // Check if theres font-size set.
        $size = isset( $font_size ) ? $font_size : $default_size;

        // Check if the font-size value is not empty (user might customize other infos but not the font-size)
        $size = !empty( $size['size'] )
                            ? $size['size'] . $size['unit']
                            : $default_size;

        // Creates the css variable
        $metas_css = '--post_meta-font-size: ' . esc_html($size) . ';';

        // print it on the main widget wrapper
        $this->add_render_attribute(
            '_wrapper',
            'style',
            $metas_css
        );
    }

	protected function render() {

        if( !\Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            $title = wp_title(null,false);
            $title = $title ? $title : get_bloginfo('name');
        }else{
            $title = __( 'This is a dummy title.', 'uicore-elements' );
        }

        $this->set_meta_font_size_to_svg();

		$meta_list = $this->get_settings_for_display( 'meta_list' );
        if(!isset($meta_list[0]) || $meta_list[0]['type'] == ''){
            return;
        }

            echo '<div class="ui-e-post-meta ui-e-tb-meta">';
            foreach ($meta_list as $meta) {
                if($meta['type'] != 'none'){
                    $this->display_meta($meta);

                    if( next( $meta_list ) && $this->get_settings_for_display( 'tb-meta_meta_separator' ) ) {
                        echo '<span class="ui-e-separator">'.esc_html($this->get_settings_for_display('tb-meta_meta_separator' )).'</span>';
                    }
                }
            }
            echo '</div>';

	}

	protected function content_template() {
	}
}
\Elementor\Plugin::instance()->widgets_manager->register(new PostMeta());
