<?php
namespace UiCore\Elementor;

defined('ABSPATH') || exit();

/**
 * Scripts and Styles Class
 */
class Widgets
{
    public function __construct()
    {

        //those were moved to the Elements plugin
        //TODO: remove this file in the future
        if(!class_exists('\UiCoreElements\Base')){

            require_once UICORE_INCLUDES . '/elementor/generic/meta-component.php';
            // require_once UICORE_INCLUDES . '/elementor/generic/button-settings.php';

            add_action('elementor/elements/categories_registered', [$this, 'create_custom_category'], 999);
            add_action('elementor/controls/register', [$this, 'init_controls']);
            add_action('elementor/widgets/register', [$this, 'init_widgets']);
        }
    }

    public function init_widgets()
    {
        require_once UICORE_INCLUDES . '/elementor/widgets/post-grid.php';
        require_once UICORE_INCLUDES . '/elementor/widgets/highlighted-text.php';
        // require_once UICORE_INCLUDES . '/elementor/widgets/counter.php';
        // require_once UICORE_INCLUDES . '/elementor/widgets/icon-box.php';
        //require_once UICORE_INCLUDES . '/elementor/widgets/accordion.php';
        require_once UICORE_INCLUDES . '/elementor/theme-builder/widgets/the-content.php';
        require_once UICORE_INCLUDES . '/elementor/theme-builder/widgets/the-title.php';
        require_once UICORE_INCLUDES . '/elementor/theme-builder/widgets/post-meta.php';
        require_once UICORE_INCLUDES . '/elementor/theme-builder/widgets/page-description.php';
        require_once UICORE_INCLUDES . '/elementor/theme-builder/widgets/advanced-post-grid.php';
    }

    public function init_controls()
    {
    //Controls
    require UICORE_INCLUDES . '/elementor/generic/class-post-filter-control.php';
    }

    function create_custom_category($elements_manager)
    {
        $elements_manager->add_category('uicore', [
            'title' => __('UiCore', 'uicore'),
            'icon' => 'fa fa-plug',
        ]);
    }
}
new Widgets();
