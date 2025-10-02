<?php 
require_once get_stylesheet_directory().'/dompdf/autoload.inc.php';
require_once get_stylesheet_directory().'/dompdf/src/Options.php';
use Shuchkin\SimpleXLSX;
use Dompdf\Dompdf;
use Dompdf\options;

function mydecor_child_register_scripts(){
    $parent_style = 'mydecor-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css', array('font-awesome-5', 'mydecor-reset'), mydecor_get_theme_version() );
    wp_enqueue_style( 'mydecor-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        '1.0.2'
    );

    // Register Styles 
    wp_register_script( 
        'barcode-main', 
        get_stylesheet_directory_uri() . '/assets/js/main.js', 
        array( 'jquery' ),
        '1.1.9', 
        true
    );

    // Enqueue Styles 
    wp_enqueue_script( 'barcode-main' );

    // Localize Scripts 
    wp_localize_script(
        'barcode-main',
        'barcodemain',
        array( 
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
        ) 
    );
}
add_action( 'wp_enqueue_scripts', 'mydecor_child_register_scripts', 99 );

// Create analytics table on theme activation
function barcodemine_create_analytics_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'barcode_search_analytics';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        barcode varchar(50) NOT NULL,
        search_type varchar(20) NOT NULL DEFAULT 'single',
        found tinyint(1) NOT NULL DEFAULT 0,
        ip_address varchar(45) NOT NULL,
        user_agent text,
        referrer varchar(500),
        search_time datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY barcode_idx (barcode),
        KEY search_time_idx (search_time),
        KEY found_idx (found)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Add option to track table creation
    add_option('barcodemine_analytics_table_created', '1');
}

// Hook to create table on theme switch or first load
function barcodemine_check_analytics_table() {
    if (!get_option('barcodemine_analytics_table_created')) {
        barcodemine_create_analytics_table();
    }
}
add_action('after_switch_theme', 'barcodemine_create_analytics_table');
add_action('init', 'barcodemine_check_analytics_table');

// Fix WooCommerce Cart and Checkout Issues
function barcodemine_fix_woocommerce_cart_checkout() {
    // Ensure WooCommerce scripts are loaded properly
    if ( is_cart() || is_checkout() ) {
        wp_enqueue_script( 'wc-cart' );
        wp_enqueue_script( 'wc-checkout' );
        wp_enqueue_script( 'woocommerce' );
        wp_enqueue_script( 'wc-cart-fragments' );
        wp_enqueue_script( 'wc-add-to-cart' );
    }
}
add_action( 'wp_enqueue_scripts', 'barcodemine_fix_woocommerce_cart_checkout', 100 );

// Fix cart fragments and AJAX issues
function barcodemine_fix_cart_fragments() {
    if ( is_admin() ) return;
    
    // Ensure cart fragments work properly
    if ( ! wp_script_is( 'wc-cart-fragments', 'enqueued' ) ) {
        wp_enqueue_script( 'wc-cart-fragments' );
    }
}
add_action( 'wp_enqueue_scripts', 'barcodemine_fix_cart_fragments', 101 );

// Fix the problematic green color #00c49a
function barcodemine_fix_green_color_clash() {
    ?>
    <style type="text/css">
    /* Force override the problematic UiCore color variables */
    :root {
        --e-global-color-uicore_primary: #333333 !important;
        --e-global-color-primary: #333333 !important;
    }
    
    /* Target all buttons that use the problematic green color */
    .woocommerce .button,
    .woocommerce button.button,
    .woocommerce input.button,
    .woocommerce a.button,
    .elementor-button,
    button,
    input[type="button"],
    input[type="submit"],
    .single_add_to_cart_button,
    .woocommerce div.product form.cart .button,
    .woocommerce-cart .wc-proceed-to-checkout a.checkout-button,
    .woocommerce #payment #place_order {
        background-color: #333333 !important;
        border-color: #333333 !important;
        color: #ffffff !important;
        padding: 8px 16px !important; /* Fix excessive padding */
        font-size: 14px !important; /* Normalize font size */
        line-height: 1.4 !important; /* Fix line height */
    }
    
    .woocommerce .button:hover,
    .woocommerce button.button:hover,
    .woocommerce input.button:hover,
    .woocommerce a.button:hover,
    .elementor-button:hover,
    button:hover,
    input[type="button"]:hover,
    input[type="submit"]:hover,
    .single_add_to_cart_button:hover,
    .woocommerce div.product form.cart .button:hover,
    .woocommerce-cart .wc-proceed-to-checkout a.checkout-button:hover,
    .woocommerce #payment #place_order:hover {
        background-color: #222222 !important;
        border-color: #222222 !important;
        color: #ffffff !important;
        padding: 8px 16px !important; /* Keep consistent padding on hover */
        font-size: 14px !important; /* Keep consistent font size on hover */
        line-height: 1.4 !important; /* Keep consistent line height on hover */
    }
    
    /* Override any inline styles with the problematic color */
    *[style*="#00c49a"] {
        background-color: #333333 !important;
        border-color: #333333 !important;
    }
    
    /* Fix quantity buttons specifically */
    .woocommerce .quantity .plus,
    .woocommerce .quantity .minus,
    .quantity .plus,
    .quantity .minus {
        padding: 4px 8px !important;
        font-size: 14px !important;
        line-height: 1.2 !important;
        width: auto !important;
        height: auto !important;
        min-width: 30px !important;
        min-height: 30px !important;
    }
    
    /* Fix cart table buttons */
    .woocommerce-cart table.cart .button,
    .woocommerce-cart .cart-collaterals .button {
        padding: 6px 12px !important;
        font-size: 13px !important;
    }
    
    /* Specific barcode search button */
    .barcode-search-btn,
    button.barcode-search-btn {
        background-color: #007cba !important;
        border-color: #007cba !important;
        color: #ffffff !important;
        padding: 8px 16px !important;
        font-size: 14px !important;
        line-height: 1.4 !important;
    }
    
    .barcode-search-btn:hover,
    button.barcode-search-btn:hover {
        background-color: #005a87 !important;
        border-color: #005a87 !important;
        color: #ffffff !important;
        padding: 8px 16px !important;
        font-size: 14px !important;
        line-height: 1.4 !important;
    }
    </style>
    <?php
}
add_action( 'wp_head', 'barcodemine_fix_green_color_clash', 999 );

// WooCommerce styling is now handled in style.css for better performance

// Fix WooCommerce AJAX issues
function barcodemine_fix_woocommerce_ajax() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        // Fix cart update issues
        $(document.body).on('updated_wc_div', function() {
            // Re-initialize any custom scripts after cart update
            console.log('Cart updated');
        });
        
        // Fix checkout form validation
        $('form.checkout').on('checkout_place_order', function() {
            var $form = $(this);
            var isValid = true;
            
            // Check required fields
            $form.find('input[required], select[required]').each(function() {
                if (!$(this).val()) {
                    $(this).addClass('error');
                    isValid = false;
                } else {
                    $(this).removeClass('error');
                }
            });
            
            if (!isValid) {
                $('html, body').animate({
                    scrollTop: $form.find('.error').first().offset().top - 100
                }, 500);
                return false;
            }
            
            return true;
        });
        
        // Fix quantity update
        $(document).on('change', 'input.qty', function() {
            var $form = $(this).closest('form');
            if ($form.length) {
                $form.find('[name="update_cart"]').prop('disabled', false).trigger('click');
            }
        });
        
        // Fix add to cart button
        $(document).on('click', '.single_add_to_cart_button', function(e) {
            var $button = $(this);
            var $form = $button.closest('form.cart');
            
            if ($form.length) {
                var isValid = true;
                
                // Check if variation is selected (for variable products)
                $form.find('select[name^="attribute_"]').each(function() {
                    if ($(this).val() === '') {
                        isValid = false;
                        $(this).addClass('error');
                    } else {
                        $(this).removeClass('error');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please select all product options before adding to cart.');
                    return false;
                }
            }
        });
    });
    </script>
    <?php
}
add_action( 'wp_footer', 'barcodemine_fix_woocommerce_ajax' );

// Fix WooCommerce checkout field validation
function barcodemine_checkout_field_validation( $fields, $errors ) {
    // Ensure required fields are properly validated
    foreach ( $fields as $key => $field ) {
        if ( isset( $field['required'] ) && $field['required'] && empty( $_POST[$key] ) ) {
            $errors->add( 'validation', sprintf( __( '%s is a required field.', 'woocommerce' ), $field['label'] ) );
        }
    }
}
add_action( 'woocommerce_after_checkout_validation', 'barcodemine_checkout_field_validation', 10, 2 );

// Fix cart item removal and updates
function barcodemine_fix_cart_item_removal() {
    // Ensure cart updates work properly
    if ( ! is_admin() && ( is_cart() || is_checkout() ) ) {
        // Handle cart item removal
        if ( isset( $_GET['remove_item'] ) && ! empty( $_GET['remove_item'] ) ) {
            $cart_item_key = sanitize_text_field( $_GET['remove_item'] );
            WC()->cart->remove_cart_item( $cart_item_key );
            wp_safe_redirect( wc_get_cart_url() );
            exit;
        }
        
        // Handle quantity updates
        if ( isset( $_POST['update_cart'] ) && ! empty( $_POST['cart'] ) ) {
            foreach ( $_POST['cart'] as $cart_item_key => $values ) {
                if ( isset( $values['qty'] ) ) {
                    $quantity = intval( $values['qty'] );
                    if ( $quantity <= 0 ) {
                        WC()->cart->remove_cart_item( $cart_item_key );
                    } else {
                        WC()->cart->set_quantity( $cart_item_key, $quantity );
                    }
                }
            }
        }
    }
}
add_action( 'wp_loaded', 'barcodemine_fix_cart_item_removal' );

// Fix WooCommerce session handling
function barcodemine_fix_woocommerce_sessions() {
    if ( ! is_admin() && ! wp_doing_ajax() ) {
        // Ensure WooCommerce session is initialized
        if ( ! WC()->session->has_session() ) {
            WC()->session->set_customer_session_cookie( true );
        }
    }
}
add_action( 'wp_loaded', 'barcodemine_fix_woocommerce_sessions', 5 );

// Fix checkout process
function barcodemine_fix_checkout_process() {
    // Ensure checkout fields are processed correctly
    if ( is_admin() || ! is_checkout() ) {
        return;
    }
    
    // Add custom validation for checkout
    add_action( 'woocommerce_checkout_process', function() {
        // Validate required fields
        $required_fields = array(
            'billing_first_name' => 'First Name',
            'billing_last_name' => 'Last Name',
            'billing_email' => 'Email Address',
            'billing_phone' => 'Phone Number',
            'billing_address_1' => 'Address',
            'billing_city' => 'City',
            'billing_postcode' => 'Postal Code',
            'billing_country' => 'Country'
        );
        
        foreach ( $required_fields as $field => $label ) {
            if ( empty( $_POST[$field] ) ) {
                wc_add_notice( sprintf( __( '%s is a required field.', 'woocommerce' ), $label ), 'error' );
            }
        }
        
        // Validate email format
        if ( ! empty( $_POST['billing_email'] ) && ! is_email( $_POST['billing_email'] ) ) {
            wc_add_notice( __( 'Please enter a valid email address.', 'woocommerce' ), 'error' );
        }
    });
}
add_action( 'wp', 'barcodemine_fix_checkout_process' );

// Fix WooCommerce notices display
function barcodemine_fix_woocommerce_notices() {
    if ( is_cart() || is_checkout() ) {
        // Ensure notices are displayed properly
        if ( ! has_action( 'woocommerce_before_cart', 'wc_print_notices' ) ) {
            add_action( 'woocommerce_before_cart', 'wc_print_notices', 10 );
        }
        
        if ( ! has_action( 'woocommerce_before_checkout_form', 'wc_print_notices' ) ) {
            add_action( 'woocommerce_before_checkout_form', 'wc_print_notices', 10 );
        }
    }
}
add_action( 'wp', 'barcodemine_fix_woocommerce_notices' );

// Fix payment gateway issues
function barcodemine_fix_payment_gateways() {
    // Ensure payment gateways are loaded properly
    if ( is_checkout() && ! is_admin() ) {
        // Force refresh payment methods
        add_action( 'wp_footer', function() {
            ?>
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                // Refresh payment methods when checkout form changes
                $('body').on('change', 'input[name="payment_method"]', function() {
                    $('body').trigger('update_checkout');
                });
                
                // Handle payment method selection
                $(document).on('click', '.payment_methods input[type="radio"]', function() {
                    $('.payment_box').hide();
                    $(this).closest('li').find('.payment_box').show();
                });
                
                // Show first payment method by default
                $('.payment_methods li:first-child input[type="radio"]').prop('checked', true).trigger('click');
            });
            </script>
            <?php
        });
    }
}
add_action( 'wp', 'barcodemine_fix_payment_gateways' );

// REST API endpoint for barcode verification
add_action( 'rest_api_init', function() {
    register_rest_route( 'barcodemine/v1', '/verify/(?P<barcode>[0-9]+)', array(
        'methods' => 'GET',
        'callback' => 'barcodemine_api_verify_barcode',
        'permission_callback' => '__return_true',
        'args' => array(
            'barcode' => array(
                'validate_callback' => function($param, $request, $key) {
                    return is_numeric($param) && strlen($param) >= 8 && strlen($param) <= 13;
                }
            ),
        ),
    ));
    
    register_rest_route( 'barcodemine/v1', '/bulk-verify', array(
        'methods' => 'POST',
        'callback' => 'barcodemine_api_bulk_verify',
        'permission_callback' => '__return_true',
        'args' => array(
            'barcodes' => array(
                'required' => true,
                'validate_callback' => function($param, $request, $key) {
                    return is_array($param) && count($param) <= 50;
                }
            ),
        ),
    ));
});

function barcodemine_api_verify_barcode( $request ) {
    $barcode = $request['barcode'];
    
    // Clean barcode
    $clean_barcode = preg_replace('/[^0-9]/', '', $barcode);
    
    if ( empty( $clean_barcode ) ) {
        return new WP_Error( 'invalid_barcode', 'Invalid barcode format', array( 'status' => 400 ) );
    }
    
    $result = barcodemine_search_single_barcode( $clean_barcode );
    
    if ( ! $result['found'] ) {
        return new WP_REST_Response( array(
            'found' => false,
            'barcode' => $clean_barcode,
            'message' => 'Barcode not found in registry'
        ), 404 );
    }
    
    // Format response for API
    $data = $result['data'];
    $response = array(
        'found' => true,
        'barcode' => $clean_barcode,
        'registration' => array(
            'company' => $data['company'],
            'owner' => array(
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'full_name' => trim( $data['first_name'] . ' ' . $data['last_name'] )
            ),
            'address' => array(
                'line1' => $data['address_1'],
                'line2' => $data['address_2'],
                'city' => $data['city'],
                'state' => $data['state'],
                'postal_code' => $data['postcode'],
                'country' => $data['country'],
                'formatted' => barcodemine_format_address( $data )
            ),
            'contact' => array(
                'phone' => $data['phone'],
                'email' => $data['email']
            ),
            'barcode_range' => array(
                'first' => $data['barcode_range']['first'],
                'last' => $data['barcode_range']['last'],
                'total_count' => $data['barcode_range']['count']
            ),
            'registration_id' => $data['order_id'],
            'verified_at' => current_time( 'mysql' )
        )
    );
    
    return new WP_REST_Response( $response, 200 );
}

function barcodemine_api_bulk_verify( $request ) {
    $barcodes = $request['barcodes'];
    
    if ( empty( $barcodes ) || ! is_array( $barcodes ) ) {
        return new WP_Error( 'invalid_barcodes', 'Invalid barcodes array', array( 'status' => 400 ) );
    }
    
    if ( count( $barcodes ) > 50 ) {
        return new WP_Error( 'too_many_barcodes', 'Maximum 50 barcodes allowed per request', array( 'status' => 400 ) );
    }
    
    $results = array();
    $found_count = 0;
    $not_found_count = 0;
    
    foreach ( $barcodes as $barcode ) {
        $clean_barcode = preg_replace('/[^0-9]/', '', $barcode);
        
        if ( empty( $clean_barcode ) ) {
            $results[] = array(
                'barcode' => $barcode,
                'found' => false,
                'error' => 'Invalid barcode format'
            );
            $not_found_count++;
            continue;
        }
        
        $result = barcodemine_search_single_barcode( $clean_barcode );
        
        if ( $result['found'] ) {
            $data = $result['data'];
            $results[] = array(
                'barcode' => $clean_barcode,
                'found' => true,
                'registration' => array(
                    'company' => $data['company'],
                    'owner' => trim( $data['first_name'] . ' ' . $data['last_name'] ),
                    'barcode_range' => array(
                        'first' => $data['barcode_range']['first'],
                        'last' => $data['barcode_range']['last'],
                        'total_count' => $data['barcode_range']['count']
                    ),
                    'registration_id' => $data['order_id']
                )
            );
            $found_count++;
        } else {
            $results[] = array(
                'barcode' => $clean_barcode,
                'found' => false,
                'message' => 'Barcode not found in registry'
            );
            $not_found_count++;
        }
    }
    
    return new WP_REST_Response( array(
        'success' => true,
        'summary' => array(
            'total_searched' => count( $barcodes ),
            'found' => $found_count,
            'not_found' => $not_found_count
        ),
        'results' => $results,
        'verified_at' => current_time( 'mysql' )
    ), 200 );
}

// Helper function to format address
function barcodemine_format_address( $data ) {
    $address_parts = array();
    
    if ( ! empty( $data['address_1'] ) ) {
        $address_parts[] = $data['address_1'];
    }
    
    if ( ! empty( $data['address_2'] ) ) {
        $address_parts[] = $data['address_2'];
    }
    
    $city_state_zip = array();
    if ( ! empty( $data['city'] ) ) {
        $city_state_zip[] = $data['city'];
    }
    
    if ( ! empty( $data['state'] ) ) {
        $city_state_zip[] = $data['state'];
    }
    
    if ( ! empty( $data['postcode'] ) ) {
        $city_state_zip[] = $data['postcode'];
    }
    
    if ( ! empty( $city_state_zip ) ) {
        $address_parts[] = implode( ' ', $city_state_zip );
    }
    
    if ( ! empty( $data['country'] ) ) {
        $countries = WC()->countries->get_countries();
        if ( isset( $countries[ $data['country'] ] ) ) {
            $address_parts[] = $countries[ $data['country'] ];
        }
    }
    
    return implode( ', ', $address_parts );
}

// Barcode search analytics and tracking
function barcodemine_track_search( $barcode, $found = false, $search_type = 'single' ) {
    global $wpdb;
    
    // Create analytics table if it doesn't exist
    $table_name = $wpdb->prefix . 'barcode_search_analytics';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        barcode varchar(20) NOT NULL,
        search_type varchar(20) NOT NULL DEFAULT 'single',
        found tinyint(1) NOT NULL DEFAULT 0,
        ip_address varchar(45),
        user_agent text,
        referer text,
        search_time datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY barcode (barcode),
        KEY search_time (search_time),
        KEY found (found)
    ) $charset_collate;";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    
    // Insert search record
    $wpdb->insert(
        $table_name,
        array(
            'barcode' => $barcode,
            'search_type' => $search_type,
            'found' => $found ? 1 : 0,
            'ip_address' => barcodemine_get_client_ip(),
            'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '',
            'referer' => isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( $_SERVER['HTTP_REFERER'] ) : '',
            'search_time' => current_time( 'mysql' )
        ),
        array( '%s', '%s', '%d', '%s', '%s', '%s', '%s' )
    );
}

// Get client IP address
function barcodemine_get_client_ip() {
    $ip_keys = array( 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR' );
    
    foreach ( $ip_keys as $key ) {
        if ( array_key_exists( $key, $_SERVER ) === true ) {
            $ip = sanitize_text_field( $_SERVER[ $key ] );
            if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) !== false ) {
                return $ip;
            }
        }
    }
    
    return isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '0.0.0.0';
}

// Add analytics to admin dashboard
add_action( 'wp_dashboard_setup', 'barcodemine_add_dashboard_widgets' );

function barcodemine_add_dashboard_widgets() {
    // Only show to administrators
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    wp_add_dashboard_widget(
        'barcodemine_search_analytics',
        '📊 Barcode Search Analytics',
        'barcodemine_dashboard_analytics_widget'
    );
}

// Alternative: Add analytics as admin menu page if widget doesn't show
add_action( 'admin_menu', 'barcodemine_add_analytics_menu' );

function barcodemine_add_analytics_menu() {
    add_submenu_page(
        'tools.php',
        'Barcode Analytics',
        'Barcode Analytics',
        'manage_options',
        'barcode-analytics',
        'barcodemine_analytics_page'
    );
}

function barcodemine_analytics_page() {
    ?>
    <div class="wrap">
        <h1>📊 Barcode Search Analytics</h1>
        <?php barcodemine_dashboard_analytics_widget(); ?>
    </div>
    <?php
}

function barcodemine_dashboard_analytics_widget() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'barcode_search_analytics';
    
    // Check if table exists
    if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
        echo '<div style="padding: 15px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px; margin: 10px 0;">';
        echo '<h4>📊 Analytics Setup</h4>';
        echo '<p><strong>Analytics table not found.</strong> The table will be created automatically when the first barcode search is performed.</p>';
        echo '<p><strong>To test:</strong> Go to your website and search for any barcode using the search form.</p>';
        echo '<p><strong>Table name:</strong> <code>' . $table_name . '</code></p>';
        echo '</div>';
        return;
    }
    
    // Get statistics
    $total_searches = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
    $successful_searches = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE found = 1" );
    $today_searches = $wpdb->get_var( $wpdb->prepare( 
        "SELECT COUNT(*) FROM $table_name WHERE DATE(search_time) = %s", 
        current_time( 'Y-m-d' ) 
    ));
    $week_searches = $wpdb->get_var( $wpdb->prepare( 
        "SELECT COUNT(*) FROM $table_name WHERE search_time >= %s", 
        date( 'Y-m-d H:i:s', strtotime( '-7 days' ) ) 
    ));
    
    // Get top searched barcodes
    $top_barcodes = $wpdb->get_results( 
        "SELECT barcode, COUNT(*) as search_count, 
         SUM(found) as found_count 
         FROM $table_name 
         GROUP BY barcode 
         ORDER BY search_count DESC 
         LIMIT 5", 
        ARRAY_A 
    );
    
    $success_rate = $total_searches > 0 ? round( ( $successful_searches / $total_searches ) * 100, 1 ) : 0;
    
    ?>
    <div class="barcode-analytics-widget">
        <div class="analytics-stats">
            <div class="stat-item">
                <h4><?php echo number_format( $total_searches ); ?></h4>
                <p>Total Searches</p>
            </div>
            <div class="stat-item">
                <h4><?php echo $success_rate; ?>%</h4>
                <p>Success Rate</p>
            </div>
            <div class="stat-item">
                <h4><?php echo number_format( $today_searches ); ?></h4>
                <p>Today</p>
            </div>
            <div class="stat-item">
                <h4><?php echo number_format( $week_searches ); ?></h4>
                <p>This Week</p>
            </div>
        </div>
        
        <?php if ( ! empty( $top_barcodes ) ): ?>
        <div class="top-searches">
            <h4>Most Searched Barcodes</h4>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Barcode</th>
                        <th>Searches</th>
                        <th>Found</th>
                        <th>Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $top_barcodes as $barcode ): ?>
                    <tr>
                        <td><code><?php echo esc_html( $barcode['barcode'] ); ?></code></td>
                        <td><?php echo number_format( $barcode['search_count'] ); ?></td>
                        <td><?php echo number_format( $barcode['found_count'] ); ?></td>
                        <td><?php echo round( ( $barcode['found_count'] / $barcode['search_count'] ) * 100, 1 ); ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <style>
        .barcode-analytics-widget .analytics-stats {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .barcode-analytics-widget .stat-item {
            flex: 1;
            text-align: center;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
        }
        .barcode-analytics-widget .stat-item h4 {
            margin: 0 0 5px 0;
            font-size: 24px;
            color: #007cba;
        }
        .barcode-analytics-widget .stat-item p {
            margin: 0;
            font-size: 12px;
            color: #666;
        }
        .barcode-analytics-widget .top-searches h4 {
            margin-bottom: 10px;
        }
        .barcode-analytics-widget table {
            font-size: 12px;
        }
        .barcode-analytics-widget table th,
        .barcode-analytics-widget table td {
            padding: 8px;
        }
        </style>
    </div>
    <?php
}

function barcodemine_barcode_excel_upload_meta_box($object) {
    $_excel_file_data = get_post_meta( $object->ID, '_excel_file_data', true );
    $_excel_file = get_post_meta( $object->ID, '_excel_file', true );
    $_certificate_file = get_post_meta( $object->ID, '_certificate_file', true );
    $disabled = ! empty ( $_excel_file_data ) ? 'disabled' : '';
    
    wp_nonce_field(basename(__FILE__), "excel-upload-box-nonce");
    ?>
    <style>
        .excel-upload-meta-wrap .excel-upload-label{
            display: block;
            padding-bottom: 0.5em;
            font-weight: 600;
            color: #000;
        }
        .excel-upload-item{
            padding: 1.6em;
            background: #f8f8f8;
            margin-bottom: 15px;
        }
        .excel-upload-inner-label{
            width: 100%;
            display: block;
            padding: 10px 0;
        }

        .excel-upload-meta-wrap .button{
            margin-top: 10px;
        }

        .excel-upload-meta-wrap input, .excel-upload-meta-wrap textarea{
            width: 100%;
        }
        .excel-upload-meta-wrap .currency-symbol, .excel-upload-meta-wrap .price-input {
            display: inline-block;
        }
        .excel-upload-meta-wrap .currency-symbol {
            width: 12%;
            margin: 0;
            border: none;
            float: left;
        }
        .excel-upload-meta-wrap .price-input {
            width: 88%;
            margin: 0;
        }
    </style>
    <?php
    if( empty ( $_excel_file_data ) ){
        ?>
            <div class="excel-upload-meta-wrap">
                <h4>Upload a Barcode Excel File</h4>
                <div class="excel-upload-item">
                    <label class="excel-upload-inner-label">Excel File</label>
                    <input type="file" name="excel_file" value="" <?php echo $disabled; ?> />
                    <button type="submit" class="button save_order button-primary" name="excel_upload_order" value="" <?php echo $disabled; ?>>Upload</button>
                </div>
            </div>
        <?php
    } else {
        ?>
            <div class="excel-upload-meta-wrap">
                <a href="<?php echo $_excel_file['url']; ?>" class="button button-primary">Download Excel File</a>
                <a href="<?php echo $_certificate_file['url']; ?>" class="button button-primary" download>Download Certificate</a>
            </div>
        <?php
    }
}

function barcodemine_barcode_zip_upload_meta_box( $object ){
    $_zip_file = get_post_meta( $object->ID, '_zip_file', true );
    $zip_disabled = ! empty ( $_zip_file ) ? 'disabled' : '';
    if( empty ( $_zip_file ) ){
        ?>
            <div class="excel-upload-meta-wrap">
                <h4>Upload a Zip File</h4>
                <div class="excel-upload-item">
                    <label class="excel-upload-inner-label">Zip File</label>
                    <input type="file" name="zip_file" value="" <?php echo $zip_disabled; ?> />
                    <button type="submit" class="button save_order button-primary" name="zip_upload_order" value="" <?php echo $zip_disabled; ?>>Upload</button>
                </div>
            </div>
        <?php
    } else {
        ?>
            <div class="excel-upload-meta-wrap">
                <a href="<?php echo $_zip_file['url']; ?>" class="button button-primary">Download Zip File</a>
            </div>
        <?php
    }
}
function barcodemine_add_order_meta_box(){   
    add_meta_box("excel-upload-meta-box", "Barcode File Upload / Certification Download", "barcodemine_barcode_excel_upload_meta_box", "shop_order", "side", "default", null);
    add_meta_box("zip-upload-meta-box", "Zip File Upload / Zip Download", "barcodemine_barcode_zip_upload_meta_box", "shop_order", "side", "default", null);
}

add_action("add_meta_boxes", "barcodemine_add_order_meta_box");

function barcodemine_save_order_meta_box($post_id, $post, $update){
    // Check for charge item and call charge api if availble charge items 
    if ( isset( $_POST["excel_upload_order"] ) && ! empty( $_FILES["excel_file"] ) ){
        $supported_types = array( 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv', 'application/xlsb' );
		$arr_file_type = wp_check_filetype( basename( $_FILES['excel_file']['name'] ) );
		$uploaded_type = $arr_file_type['type'];

		if ( in_array( $uploaded_type, $supported_types ) ) {
            $order = wc_get_order( $post_id );
			$upload = wp_upload_bits($_FILES['excel_file']['name'], null, file_get_contents($_FILES['excel_file']['tmp_name']));
			if ( isset( $upload['error'] ) && $upload['error'] != 0 ) {
				wp_die( 'There was an error uploading your file. The error is: ' . $upload['error'] );
			} else {
				update_post_meta( $post_id, '_excel_file', $upload );
                require_once get_stylesheet_directory().'/SimpleXLSX.php';
                // require_once get_stylesheet_directory().'dompdf/autoload.inc.php';

                if ($xlsx = SimpleXLSX::parse($upload['file'])) {

                    $excel_values = $xlsx->rows();
                    array_shift( $excel_values );
                    $excel_values = wp_list_pluck( $excel_values, '0' );
                    update_post_meta( $post_id, '_excel_file_data', $excel_values );
                    // If you don't have the WC_Order object (from a dynamic $order_id)
                    $user_id = get_current_user_id();

                    // Get user data by user id
                    $user = get_userdata( $user_id );

                    $display_name = $user->display_name;

                    // The text for the note
                    $note = __("".$display_name." have uploaded barcode excel file sucessfully!!!");

                    // Add the note
                    $order->add_order_note( $note );
                } else {
                    update_post_meta( $post_id, '_excel_file_data', SimpleXLSX::parseError() );
                }

                $billing_address= $order->get_address('billing');
                $excel_data = get_post_meta( $post_id, '_excel_file_data', true );
                
                $filename = $post_id.'_qr_code.png';
                $uploaddir = wp_upload_dir();
                $uploadfile = $uploaddir['path'] . '/' . $filename;

                $contents= file_get_contents('https://api.qrserver.com/v1/create-qr-code/?size=100x100&data='.get_site_url().'/barcode-information-search/?barcode_number='.$excel_data[0].'');
                $savefile = fopen($uploadfile, 'w');
                fwrite($savefile, $contents);
                fclose($savefile);

                $wp_filetype = wp_check_filetype(basename($filename), null );

                $attachment = array(
                    'post_mime_type' => $wp_filetype['type'],
                    'post_title' => $filename,
                    'post_content' => '',
                    'post_status' => 'inherit'
                );

                $attach_id = wp_insert_attachment( $attachment, $uploadfile );

                $imagenew = get_post( $attach_id );
                $fullsizepath = get_attached_file( $imagenew->ID );
                // $attach_data = wp_generate_attachment_metadata( $attach_id, $fullsizepath );
                // wp_update_attachment_metadata( $attach_id, $attach_data );

                ob_start();
        
                    ?>  
                    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
                    <html xmlns="http://www.w3.org/1999/xhtml">
                        <head>
                            <link rel="preconnect" href="https://fonts.googleapis.com">
                            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
                            <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
                            <style type="text/css">
                                * {
                                    box-sizing: border-box;
                                }

                                .cert-container {
                                    width:100%;
                                    display: flex; 
                                    justify-content: center;
                                }

                                body, html, .cert-container {
                                    margin: 0;
                                    padding: 0;
                                }

                                .cert {
                                    width: 100%;
                                    /* padding:5px 30px; */
                                    position: relative; 
                                    z-index: -1;
                                }

                                .cert-bg {
                                    position: absolute; 
                                    left: 0px; 
                                    top: 0; 
                                    z-index: -1;
                                    width: 100%;
                                }

                                .cert-content {
                                    font-family: 'Open Sans', sans-serif;
                                    width:800px;
                                    height:300px; 
                                    padding:85px 60px 20px 60px; 
                                    text-align:center;
                                }

                                .bottom-content {
                                    font-family: 'Open Sans', sans-serif;
                                    width:200px;
                                    height:200px;
                                    margin-left: 610px;
                                    font-size: 19px;
                                    line-height: 26px;
                                    /* background: White; */
                                }

                                h1 {
                                    font-size:44px;
                                }

                                p {
                                    font-size:25px;
                                }

                                small {
                                    font-size: 14px;
                                    line-height: 12px;
                                }

                                .bottom-txt {
                                padding: 12px 5px; 
                                display: flex; 
                                justify-content: space-between;
                                font-size: 16px;
                                }

                                .bottom-txt * {
                                white-space: nowrap !important;
                                }

                                .other-font {
                                    margin-top: 8px;
                                    color: #1d6498;
                                    display: inline-block;
                                    margin-right: 70px;
                                    font-size: 24px;
                                    margin-left: 25px;
                                    line-height: 28px;
                                }
                                .other-font-to {
                                    margin-top: 8px;
                                    color: #1d6498;
                                    display: inline-block;
                                    font-size: 24px;
                                    line-height: 28px;
                                }

                                .other-font-qty{
                                    margin-top: 10px;
                                    display: inline-block;
                                    font-size: 20px;
                                    margin-left: 170px;
                                    line-height: 20px;
                                }
                                .one-line-content{
                                    margin-top: 50px;
                                    display: block;
                                }
                                .ml-215 {
                                margin-left: 215px;
                                }
                                .company-title{
                                    margin-top: 240px;
                                    margin-left: 40px;
                                }
                                .account-name{
                                    margin-top: 80px;
                                }
                                .qr-content {
                                    width: 10%;
                                    display: inline-block;
                                    margin-top: 35px;
                                    margin-left: 25px;
                                }
                                .header-content {
                                    width: 90%;
                                    display: inline-block;
                                }
                            </style>
                        </head>
                        <body>
                            <div class="main">
                            <div class="cert-container print-m-0">
                                <div id="content2" class="cert">
                                    <img
                                    src="<?php echo get_stylesheet_directory_uri(). '/assets/images/certificate.png' ;?>"
                                    class="cert-bg"
                                    alt=""
                                    />
                                    <div class="cert-content">
                                        <div class="qr-content">
                                            <img
                                            src="<?php echo wp_get_attachment_image_url( $attach_id, 'full' ); ?>"
                                            class="qr-code"
                                            alt=""
                                            />
                                        </div>
                                        <div class="header-content">
                                            <h1 class="company-title"><?php echo $billing_address['company']; ?></h1>
                                            <div class="one-line-content">
                                                <div class="other-font-qty">(<?php echo count($excel_data); ?>)</div>
                                                <div class="other-font"><?php echo $excel_data[0]; ?></div>
                                                <div class="other-font-to"><?php echo end( $excel_data ); ?></div> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bottom-content">
                                        <div class="multi-line-content">
                                            <div class="account-name">789850</div>
                                            <div class=""><?php echo $billing_address['first_name']; ?> <?php echo $billing_address['last_name']; ?></div>
                                            <div class="">BM<?php echo $post_id; ?></div>
                                            <?php
                                                $date_modified = $order->get_date_created();
                                            ?>
                                            <div class=""><?php echo date('d/m/Y'); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </body>
                    </html>

                    <?php
                $content = ob_get_contents();
                ob_end_clean();

                // instantiate and use the dompdf class
                $options = new Options();
                $options->set('isRemoteEnabled', true);
                $dompdf = new Dompdf($options);
                // $dompdf = new Dompdf();
                $dompdf->loadHtml($content);

                // (Optional) Setup the paper size and orientation
                $dompdf->setPaper('A4', 'landscape');

                // Render the HTML as PDF
                $dompdf->render();

                // Output the generated PDF to Browser
                $output = $dompdf->output();
                $certificate_upload = wp_upload_bits( $post_id.'_certificate.pdf', null, $output );
                if ( isset( $certificate_upload['error'] ) && $certificate_upload['error'] != 0 ) {
                    wp_die( 'There was an error uploading your file. The error is: ' . $certificate_upload['error'] );
                } else {
                    update_post_meta( $post_id, '_certificate_file', $certificate_upload );
                }
                
			}
		}
		else {
			wp_die( "The file type that you've uploaded is not a Excel." );
		}
    }

    if ( isset( $_POST["zip_upload_order"] ) && ! empty( $_FILES["zip_file"] ) ){
        $supported_types = array( 'application/zip' );
		$arr_file_type = wp_check_filetype( basename( $_FILES['zip_file']['name'] ) );
        // print_r( $arr_file_type );die;
		$uploaded_type = $arr_file_type['type'];

		if ( in_array( $uploaded_type, $supported_types ) ) {
            $order = wc_get_order( $post_id );
			$upload = wp_upload_bits($_FILES['zip_file']['name'], null, file_get_contents($_FILES['zip_file']['tmp_name']));
			if ( isset( $upload['error'] ) && $upload['error'] != 0 ) {
				wp_die( 'There was an error uploading your file. The error is: ' . $upload['error'] );
			} else {
                update_post_meta( $post_id, '_zip_file', $upload );
                // If you don't have the WC_Order object (from a dynamic $order_id)
                $user_id = get_current_user_id();

                // Get user data by user id
                $user = get_userdata( $user_id );

                $display_name = $user->display_name;

                // The text for the note
                $note = __("".$display_name." have uploaded zip file sucessfully!!!");

                // Add the note
                $order->add_order_note( $note );
			}
		}
		else {
			wp_die( "The file type that you've uploaded is not a Excel." );
		}
    }
}

add_action("save_post", "barcodemine_save_order_meta_box", 10, 3);

add_action( 'post_edit_form_tag', 'update_edit_form' );
function update_edit_form() {
    echo ' enctype="multipart/form-data"';
}

add_action( 'woocommerce_after_order_details', 'barcodemine_add_barcode_certification_download' );
function barcodemine_add_barcode_certification_download( $order ){
    $_excel_file = ! empty( $order->get_meta('_excel_file') ) ? $order->get_meta('_excel_file') : array();
    $_certificate_file = ! empty( $order->get_meta('_certificate_file') ) ? $order->get_meta('_certificate_file') : array();
    $_zip_file = ! empty( $order->get_meta('_zip_file') ) ? $order->get_meta('_zip_file') : array();
    ?>
        <section class="woocommerce-customer-details">
	        <h2 class="woocommerce-barcode-details__title">Barcode Certification Download</h2>
            <?php
                if( ! empty( $_excel_file ) ){
                    ?>
                        <div class="download-client-area">
                            <a href="<?php echo $_excel_file['url'];?>" class="button">Download Barcode</a>
                            <a href="<?php echo $_certificate_file['url'];?>" download class="button">Download Barcode Certificate</a>
                            <?php
                                if( ! empty( $_zip_file ) ){
                                    ?>
                                        <a href="<?php echo $_zip_file['url'];?>" download class="button">Download Zip</a>
                                    <?php
                                }
                            ?>
                        </div>
                    <?php
                } else {
                    ?>
                        <div class="download-client-area">
                            <h5>Note: Please wait for 24 hours after placed a order!!!</h5>
                        </div>
                    <?php
                }
            ?>
        </section>
    <?php
}

// function that runs when shortcode is called
function barcodemine_search_shortcode() { 
    
    ob_start();
    ?>
        <div class="barcode-search-container">
            <form method="post" class="search-gepir-data">
                <div class="search-input-group">
                    <input placeholder="Enter GTIN-13 EAN / GTIN-12 UPC barcode" 
                           class="elementor-search-form__input barcode-input" 
                           type="text" 
                           name="geiper_name" 
                           title="Search for barcode" 
                           value=""
                           maxlength="13"
                           pattern="[0-9]*"
                           autocomplete="off">
                    <button type="submit" class="barcode-search-btn">
                        <span class="btn-text">Search</span>
                        <i class="fas fa-search search-icon"></i>
                        <i class="fas fa-spinner fa-spin search-loader" style="display: none;"></i>
                    </button>
                </div>
                <div class="search-help">
                    <small>Enter a 12 or 13 digit barcode number (UPC/EAN format)</small>
                </div>
            </form>

            <div class="search-result"></div>
        </div>
        
        <style>
        .barcode-search-container {
            max-width: 600px;
            margin: 20px auto;
        }
        .search-input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        .barcode-input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        .barcode-input:focus {
            outline: none;
            border-color: #007cba;
            box-shadow: 0 0 5px rgba(0, 124, 186, 0.3);
        }
        .barcode-search-btn {
            padding: 12px 20px;
            background: #007cba;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            min-width: 100px;
        }
        .barcode-search-btn:hover {
            background: #005a87;
        }
        .barcode-search-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .search-help {
            text-align: center;
            color: #666;
        }
        .search-loader {
            margin-left: 5px;
        }
        .barcode-not-found {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 5px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .bct-gepir-results__table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .bct-gepir-results__table th,
        .bct-gepir-results__table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .bct-gepir-results__table th {
            background: #f8f9fa;
            font-weight: bold;
        }
        .bct-gepir-results__company {
            max-width: 300px;
        }
        @media (max-width: 768px) {
            .search-input-group {
                flex-direction: column;
            }
            .bct-gepir-results__table {
                font-size: 14px;
            }
            .bct-gepir-results__table th,
            .bct-gepir-results__table td {
                padding: 8px;
            }
        }
        </style>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}
// register shortcode
add_shortcode('barcode_search', 'barcodemine_search_shortcode');

// Bulk barcode search shortcode
function barcodemine_bulk_search_shortcode() {
    ob_start();
    ?>
    <div class="bulk-barcode-search-container">
        <h3>Bulk Barcode Search</h3>
        <form method="post" class="bulk-search-form">
            <div class="bulk-input-group">
                <textarea placeholder="Enter multiple barcodes (one per line or comma-separated)" 
                         class="bulk-barcode-input" 
                         name="bulk_barcodes" 
                         rows="5"
                         title="Enter multiple barcodes"></textarea>
                <button type="submit" class="bulk-search-btn">
                    <span class="btn-text">Search All</span>
                    <i class="fas fa-search search-icon"></i>
                    <i class="fas fa-spinner fa-spin search-loader" style="display: none;"></i>
                </button>
            </div>
            <div class="bulk-search-help">
                <small>Enter multiple barcodes separated by commas or new lines (max 50 at once)</small>
            </div>
        </form>
        
        <div class="bulk-search-results"></div>
    </div>
    
    <style>
    .bulk-barcode-search-container {
        max-width: 800px;
        margin: 30px auto;
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #ddd;
    }
    .bulk-input-group {
        display: flex;
        gap: 15px;
        margin-bottom: 10px;
        align-items: flex-start;
    }
    .bulk-barcode-input {
        flex: 1;
        padding: 12px 15px;
        border: 2px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
        font-family: monospace;
        resize: vertical;
        min-height: 120px;
    }
    .bulk-barcode-input:focus {
        outline: none;
        border-color: #007cba;
        box-shadow: 0 0 5px rgba(0, 124, 186, 0.3);
    }
    .bulk-search-btn {
        padding: 12px 20px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        min-width: 120px;
        height: fit-content;
    }
    .bulk-search-btn:hover {
        background: #218838;
    }
    .bulk-search-results {
        margin-top: 20px;
    }
    .bulk-result-item {
        background: white;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 10px;
        padding: 15px;
    }
    .bulk-result-found {
        border-left: 4px solid #28a745;
    }
    .bulk-result-not-found {
        border-left: 4px solid #dc3545;
    }
    .bulk-summary {
        background: #e3f2fd;
        border: 1px solid #2196f3;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 20px;
    }
    @media (max-width: 768px) {
        .bulk-input-group {
            flex-direction: column;
        }
        .bulk-search-btn {
            width: 100%;
        }
    }
    </style>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}
add_shortcode('bulk_barcode_search', 'barcodemine_bulk_search_shortcode');

add_action( 'wp_ajax_barcode_search' , 'barcodemine_barcode_search' );
add_action( 'wp_ajax_nopriv_barcode_search' , 'barcodemine_barcode_search' );

// Bulk barcode search AJAX handler
add_action( 'wp_ajax_bulk_barcode_search' , 'barcodemine_bulk_barcode_search' );
add_action( 'wp_ajax_nopriv_bulk_barcode_search' , 'barcodemine_bulk_barcode_search' );

function barcodemine_bulk_barcode_search() {
    $bulk_barcodes = ! empty( $_POST['bulk_barcodes'] ) ? sanitize_textarea_field( $_POST['bulk_barcodes'] ) : '';
    
    if ( empty( $bulk_barcodes ) ) {
        echo json_encode(array('error' => 'Please enter at least one barcode.'));
        die();
    }
    
    // Parse barcodes (split by comma, newline, or space)
    $barcodes = preg_split('/[\s,\n\r]+/', $bulk_barcodes);
    $barcodes = array_filter(array_map('trim', $barcodes)); // Remove empty values
    $barcodes = array_slice($barcodes, 0, 50); // Limit to 50 barcodes
    
    $results = array();
    $found_count = 0;
    $not_found_count = 0;
    
    foreach ($barcodes as $barcode) {
        // Clean barcode
        $clean_barcode = preg_replace('/[^0-9]/', '', $barcode);
        
        if (empty($clean_barcode)) {
            continue;
        }
        
        // Search for this barcode
        $result = barcodemine_search_single_barcode($clean_barcode);
        
        if ($result['found']) {
            $found_count++;
            $results[] = array(
                'barcode' => $clean_barcode,
                'found' => true,
                'data' => $result['data']
            );
            // Track successful bulk search
            barcodemine_track_search( $clean_barcode, true, 'bulk' );
        } else {
            $not_found_count++;
            $results[] = array(
                'barcode' => $clean_barcode,
                'found' => false,
                'message' => 'Barcode not found in registry'
            );
            // Track failed bulk search
            barcodemine_track_search( $clean_barcode, false, 'bulk' );
        }
    }
    
    echo json_encode(array(
        'success' => true,
        'total' => count($barcodes),
        'found' => $found_count,
        'not_found' => $not_found_count,
        'results' => $results
    ));
    die();
}

// Helper function for single barcode search
function barcodemine_search_single_barcode($geiper_name) {
    // Enhanced query with more order statuses and better meta query
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => -1,
        'post_status' => array( 'wc-processing', 'wc-completed', 'wc-on-hold', 'wc-pending' ),
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => '_excel_file_data',
                'value' => $geiper_name,
                'compare' => 'LIKE',
            ),
            array(
                'key' => '_excel_file_data',
                'value' => serialize($geiper_name),
                'compare' => 'LIKE',
            )
        )
    );
    
    $query = new WP_Query( $args );
    $order_id = null;
    
    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $current_order_id = get_the_ID();
            $excel_data = get_post_meta( $current_order_id, '_excel_file_data', true );
            
            if ( is_array( $excel_data ) && in_array( $geiper_name, $excel_data ) ) {
                $order_id = $current_order_id;
                break;
            }
        }
        wp_reset_postdata();
    }
    
    if ( empty( $order_id ) ) {
        // Try padded searches
        $padded_searches = array(
            str_pad($geiper_name, 12, '0', STR_PAD_LEFT),
            str_pad($geiper_name, 13, '0', STR_PAD_LEFT),
            ltrim($geiper_name, '0')
        );
        
        foreach ( $padded_searches as $search_term ) {
            if ( $search_term === $geiper_name ) continue;
            
            $query = new WP_Query( array(
                'post_type' => 'shop_order',
                'posts_per_page' => -1,
                'post_status' => array( 'wc-processing', 'wc-completed', 'wc-on-hold', 'wc-pending' ),
                'meta_query' => array(
                    array(
                        'key' => '_excel_file_data',
                        'value' => $search_term,
                        'compare' => 'LIKE',
                    )
                )
            ));
            
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $current_order_id = get_the_ID();
                    $excel_data = get_post_meta( $current_order_id, '_excel_file_data', true );
                    
                    if ( is_array( $excel_data ) && in_array( $search_term, $excel_data ) ) {
                        $order_id = $current_order_id;
                        break 2;
                    }
                }
                wp_reset_postdata();
            }
        }
    }
    
    if ( empty( $order_id ) ) {
        return array('found' => false);
    }
    
    $order = new WC_Order( $order_id );
    $billing_address = $order->get_address('billing');
    $excel_data = get_post_meta( $order_id, '_excel_file_data', true );
    
    return array(
        'found' => true,
        'data' => array(
            'order_id' => $order_id,
            'company' => $billing_address['company'],
            'first_name' => $billing_address['first_name'],
            'last_name' => $billing_address['last_name'],
            'address_1' => $billing_address['address_1'],
            'address_2' => $billing_address['address_2'],
            'city' => $billing_address['city'],
            'state' => $billing_address['state'],
            'postcode' => $billing_address['postcode'],
            'country' => $billing_address['country'],
            'phone' => $billing_address['phone'],
            'email' => $billing_address['email'],
            'barcode_range' => array(
                'first' => $excel_data[0],
                'last' => end($excel_data),
                'count' => count($excel_data)
            )
        )
    );
}

function barcodemine_barcode_search(){
    // Enhanced input validation and sanitization
    $geiper_name = ! empty( $_POST['geiper_name'] ) ? sanitize_text_field( $_POST['geiper_name'] ) : '';
    
    // Remove any non-numeric characters and leading zeros for better matching
    $geiper_name = preg_replace('/[^0-9]/', '', $geiper_name);
    
    if ( empty( $geiper_name ) ) {
        echo '<h2>Search results</h2><p>Please enter a valid barcode number.</p>';
        die();
    }
    
    // Add debugging
    error_log('Barcode Search: Looking for barcode - ' . $geiper_name);

    // Enhanced query with more order statuses and better meta query
    $args = array(
        'post_type' => 'shop_order',
        'posts_per_page' => -1,
        'post_status' => array( 'wc-processing', 'wc-completed', 'wc-on-hold', 'wc-pending' ),
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => '_excel_file_data',
                'value' => $geiper_name,
                'compare' => 'LIKE',
            ),
            array(
                'key' => '_excel_file_data',
                'value' => serialize($geiper_name),
                'compare' => 'LIKE',
            )
        )
    );
    
    // The Query
    $query = new WP_Query( $args );
    $order_id = null;
    $found_orders = array();
    
    if ( $query->have_posts() ) {
        // Check all orders to find exact barcode match
        while ( $query->have_posts() ) {
            $query->the_post();
            $current_order_id = get_the_ID();
            $excel_data = get_post_meta( $current_order_id, '_excel_file_data', true );
            
            // Check if barcode exists in the range
            if ( is_array( $excel_data ) && in_array( $geiper_name, $excel_data ) ) {
                $order_id = $current_order_id;
                $found_orders[] = $current_order_id;
                error_log('Barcode Search: Found exact match in order ID - ' . $current_order_id);
                break; // Use first exact match
            }
        }
        wp_reset_postdata();
    }
    
    // If no exact match, try broader search
    if ( empty( $order_id ) ) {
        error_log('Barcode Search: No exact match found, trying broader search');
        
        // Try searching with different padding (leading zeros)
        $padded_searches = array(
            str_pad($geiper_name, 12, '0', STR_PAD_LEFT), // UPC-A format
            str_pad($geiper_name, 13, '0', STR_PAD_LEFT), // EAN-13 format
            ltrim($geiper_name, '0') // Remove leading zeros
        );
        
        foreach ( $padded_searches as $search_term ) {
            if ( $search_term === $geiper_name ) continue; // Skip if same as original
            
            $query = new WP_Query( array(
                'post_type' => 'shop_order',
                'posts_per_page' => -1,
                'post_status' => array( 'wc-processing', 'wc-completed', 'wc-on-hold', 'wc-pending' ),
                'meta_query' => array(
                    array(
                        'key' => '_excel_file_data',
                        'value' => $search_term,
                        'compare' => 'LIKE',
                    )
                )
            ));
            
            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $current_order_id = get_the_ID();
                    $excel_data = get_post_meta( $current_order_id, '_excel_file_data', true );
                    
                    if ( is_array( $excel_data ) && in_array( $search_term, $excel_data ) ) {
                        $order_id = $current_order_id;
                        error_log('Barcode Search: Found padded match (' . $search_term . ') in order ID - ' . $current_order_id);
                        break 2; // Break both loops
                    }
                }
                wp_reset_postdata();
            }
        }
    }
    
    if ( empty( $order_id ) ) {
        // Track failed search
        barcodemine_track_search( $geiper_name, false, 'single' );
        
        echo '<h2>Search results</h2><div class="barcode-not-found"><p><strong>Barcode not found.</strong></p><p>Please check your barcode number and try again. Make sure you\'re entering a valid GTIN-12 (UPC) or GTIN-13 (EAN) barcode.</p><p><small>Searched for: ' . esc_html($geiper_name) . '</small></p></div>';
        die();
    }
    
    // Track successful search
    barcodemine_track_search( $geiper_name, true, 'single' );
    
    $order = new WC_Order( $order_id );
    $billing_address= $order->get_address('billing');

    $excel_data = get_post_meta( $order_id, '_excel_file_data', true );
    ob_start();
    ?>
        <h2>Search results</h2>
        <?php
            if( ! empty( $order_id ) && ! empty( $geiper_name ) ){
                ?>
                    <table class="table table-striped bct-gepir-results__table">
                        <thead>
                            <tr>
                                <th>GLN</th>
                                <th class="bct-gepir-results__company">Company</th>
                                <th>UPC Coordinator</th>
                                <th>Contact</th>
                                <th>GTIN-12 (Quantity)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo ltrim($geiper_name, '0'); ?></td>
                                <td class="bct-gepir-results__company">
                                    <div class="company-name">
                                        <strong><?php echo $billing_address['company']; ?></strong>
                                    </div>
                                    <p translate="no">
                                        <?php
                                            if( ! empty( $billing_address['first_name'] ) ) {
                                                ?>
                                                    <span class="given-name"><?php echo $billing_address['first_name']; ?></span> 
                                                <?php
                                            }
                                        ?>
                                        <?php
                                            if( ! empty( $billing_address['last_name'] ) ) {
                                                ?>
                                                    <span class="family-name"><?php echo $billing_address['last_name']; ?></span><br>
                                                <?php
                                            }
                                        ?>
                                        <?php
                                            if( ! empty( $billing_address['company'] ) ) {
                                                ?>
                                                    <span class="organization"><?php echo $billing_address['company']; ?>,</span><br>
                                                <?php
                                            }
                                        ?>
                                        <?php
                                            if( ! empty( $billing_address['address_1'] ) ) {
                                                ?>
                                                    <span class="address-line1"><?php echo $billing_address['address_1']; ?>,</span><br>
                                                <?php
                                            }
                                        ?>
                                        <?php
                                            if( ! empty( $billing_address['address_2'] ) ) {
                                                ?>
                                                    <span class="address-line2"><?php echo $billing_address['address_2']; ?>,</span><br>
                                                <?php
                                            }
                                        ?>
                                        <span class="locality"><?php echo $billing_address['city']; ?></span> <span class="postal-code"><?php echo $billing_address['postcode']; ?></span><br>
                                        <span class="administrative-area"><?php echo WC()->countries->get_states( $billing_address['country'] )[$billing_address['state']]; ?></span><br>
                                        <span class="country"><?php echo WC()->countries->countries[$billing_address['country']]; ?></span>
                                    </p>
                                </td>
                                <td>
                                </td>
                                <td>
                                    <?php echo $billing_address['phone']; ?>
                                </td>
                                <td><?php echo $excel_data[0]; ?> thru <?php echo end( $excel_data ); ?> (<?php echo count($excel_data); ?>)</td>
                            </tr>
                        </tbody>
                    </table>
                <?php
            } else {
                ?>
                    <p>Barcode not found. Please refine your search.</p>
                <?php
            }
        ?>
    <?php
    $output = ob_get_contents();
    ob_end_clean();

    echo $output;
    die();
}

add_filter( 'manage_edit-shop_order_columns', 'barcodemine_add_new_order_admin_list_column' );
 
function barcodemine_add_new_order_admin_list_column( $columns ) {
    $columns['barcode_numbers'] = 'Barcode Numbers';
    $columns['download_barcode'] = 'Barcode Files';
    return $columns;
}
 
add_action( 'manage_shop_order_posts_custom_column', 'barcodemine_add_new_order_admin_list_column_content' );
 
function barcodemine_add_new_order_admin_list_column_content( $column ) {
   
    global $post;
 
    if ( 'barcode_numbers' === $column ) {
        $excel_data = get_post_meta( $post->ID, '_excel_file_data', true );
        if( ! empty( $excel_data ) ){
            echo '<strong>'.$excel_data[0].'</strong> to <br/><strong>'. end( $excel_data ).'</strong> ( '. count($excel_data) .' )';
        } else {
            echo '-';
        }
    }
    if ( 'download_barcode' === $column ) {
        $_excel_file = get_post_meta( $post->ID, '_excel_file', true );
        $_certificate_file = get_post_meta( $post->ID, '_certificate_file', true );
        if( ! empty( $_excel_file ) && ! empty( $_certificate_file ) ){
            ?>
                <a href="<?php echo $_excel_file['url']; ?>" class="button button-primary">Download Excel File</a>
                <br/><br/>
                <a href="<?php echo $_certificate_file['url']; ?>" class="button button-primary" download>Download Certificate</a>
            <?php
        } else {
            echo '-';
        }
        
    }
}

add_filter( 'woocommerce_admin_order_preview_get_order_details', 'admin_order_preview_add_custom_meta_data', 10, 2 );
function admin_order_preview_add_custom_meta_data( $data, $order ) {
    // Replace '_custom_meta_key' by the correct postmeta key
    if( $custom_value = $order->get_meta('_custom_meta_key') )
        $data['custom_key'] = $custom_value; // <= Store the value in the data array.

    return $data;
}

// Display custom values in Order preview
add_action( 'woocommerce_admin_order_preview_end', 'custom_display_order_data_in_admin' );
function custom_display_order_data_in_admin(){
    global $post;
    $excel_data = get_post_meta( $post->ID, '_excel_file_data', true );
    // Call the stored value and display it
    if( ! empty( $excel_data ) ){
        echo '<div class="wc-order-preview-addresses"><div class="wc-order-preview-address"><h2>Barcode Numbers: </h2>';
        foreach( $excel_data as $value ){
            echo $value.', ';
        }
        echo '</div></div>';
    }
}