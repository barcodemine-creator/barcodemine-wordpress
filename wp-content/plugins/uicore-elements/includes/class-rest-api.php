<?php
namespace UiCoreElements;
use \Elementor\Plugin;
use UiCoreElements\Helper;
use UiCoreElements\Utils\Contact_Form_Service;

/**
 * REST_API Handler
 */
class REST_API {

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes(){
        register_rest_route('uielem/v1', '/load_more/', [
            'methods' => 'GET',
            'show_in_index' => true,
            'callback' => [$this, 'apg_load_more'],
            'permission_callback' => '__return_true',
            'args' => [
                'widget_id' => [
                    'required' => true,
                ],
                'widget_type' => [
                    'required' => true,
                ],
                'page' => [
                    'required' => false,
                    'default' => 1,
                ],
                'type' => [
                    'required' => false,
                    'default' => '',
                ],
                'term' => [
                    'required' => false,
                    'default' => '',
                ],
            ],
        ]);
        register_rest_route('uielem/v1', '/submit_actions/', [
            'methods' => 'POST',
            'callback' => [$this, 'process_submission'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function check_for_permission()
    {
        return current_user_can('manage_options');
    }

    public function apg_load_more(\WP_REST_Request $request) {
        $current_query = false;

        // Identify the widget
        $widget_id   = $request->get_param('widget_id');
        $widget_type = $request->get_param('widget_type');
        $is_product  = strpos($widget_type, 'product') !== false;

        // Get widget settings
        $settings = get_transient('ui_elements_widgetdata_'.$widget_id);

        // Check if is an "Advanced Product.." widget to determine the proper control slug
        $slug = $is_product ? 'product-filter' : 'posts-filter';

        // Set extra settings from request params
        $tax = $slug . '_' . $request->get_param('type') . '_ids';
        $settings[$tax] = $request->get_param('term');
        $settings['__current_page'] = $request->get_param('page');

        // Build current query if requested
        if( $settings[$slug . '_post_type'] == 'current' ){
            $current_query = $request->get_param('current_query');
            $current_query = json_decode( urldecode($current_query), true ); //decode url and transform to array
            $current_query['paged'] = $request->get_param('page');
        }

        // Create the element data
        $widget = [
            'elType'     => 'widget',
            'widgetType' => $widget_type,
            'id'         => $widget_id,
        ];

        // Generate a new instance of the widget with those settings and return the markup
        //$widget = new AdvancedPostGrid($widget, $settings); todo: discover why this method don't work
        $widget = Plugin::instance()->elements_manager->create_element_instance( $widget, $settings );
        $widget->set_settings($settings);

        $data = $widget->render_ajax( $current_query );

        return [
            'markup' => $data['markup'],
            'total_pages' => $is_product ? $data['total_pages'] : $widget->get_query()->max_num_pages,
        ];
    }

    public function process_submission(\WP_REST_Request $request) {

        // Get referer origin, form and widget data
        $form_data = $request->get_params();
        $files = $request->get_file_params();
        $settings = get_transient('ui_e_form_widget_settings_' . $form_data['widget_id']);

        // Request the contact form service and return the response
        $service = new Contact_Form_Service($form_data, $settings, $files);
        $response = $service->handle();
        return $response;
    }
}