<?php
namespace UiCore\Elementor\ThemeBuilder\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use UiCore\Helper;
use UiCore\Elementor\Generic\Meta_Trait;

defined('ABSPATH') || exit();

/**
 * The Title widget.
 *
 * @since 4.0.0
 */
class PostMeta extends Widget_Base {

	use Meta_Trait;

	/**
	 * Get widget name.
	 *
	 * Retrieve heading widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'uicore-post-meta';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve heading widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Post Meta', 'elementor' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve heading widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-post-info ui-e-widget';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the heading widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * @since 2.0.0
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return ['uicore', 'uicore-theme-builder' ];
	}

	public function get_style_depends() {
        Helper::register_widget_style('meta');
		return [ 'ui-e-meta' ];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 2.1.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return [ 'post', 'meta', 'info' ];
	}

	/**
	 * Register heading widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 3.1.0
	 * @access protected
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Content', 'elementor' ),
			]
		);

		$this->add_control('meta_list',[
			'label' => esc_html__( 'Meta Data', 'uicore-framework' ),
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
				'label' => esc_html__( 'Content Alignment', 'uicore-framework' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'uicore-framework' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'uicore-framework' ),
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
				'label' => esc_html__( 'Style', 'elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->get_meta_style_controls();
		$this->end_controls_section();

	}

	/**
	 * Render heading widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {

        if( !\Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            $title = wp_title(null,false);
            $title = $title ? $title : get_bloginfo('name');
        }else{
            $title = __( 'This is a dummy title.', 'uicore-framework' );
        }

		$meta_list = $this->get_settings_for_display( 'meta_list' );
        if(!isset($meta_list[0]) || $meta_list[0]['type'] == ''){
            return;
        }

            echo '<div class="ui-e-post-meta ui-e-tb-meta">';
            foreach ($meta_list as $meta) {
                if($meta['type'] != 'none'){
                    echo '<div class="ui-e-meta-item">';
                    $this->display_meta($meta);
                    echo '</div>';

                    if( next( $meta_list ) && $this->get_settings_for_display( 'tb-meta_meta_separator' ) ) {
                        echo '<span class="ui-e-separator">'.$this->get_settings_for_display('tb-meta_meta_separator' ).'</span>';
                    }
                }
            }
            echo '</div>';

	}

	/**
	 * Render heading widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @since 2.9.0
	 * @access protected
	 */
	protected function content_template() {
	}
}
\Elementor\Plugin::instance()->widgets_manager->register(new PostMeta());
