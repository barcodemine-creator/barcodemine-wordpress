<?php
/**
 * Plugin Name: WooCommerce Session Fix
 * Plugin URI: https://barcodemine.com
 * Description: Permanent fix for WooCommerce session errors - prevents "Call to a member function has_session() on null" errors
 * Version: 1.0.0
 * Author: BarcodeMine
 * License: GPL v2 or later
 * Text Domain: woocommerce-session-fix
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * WooCommerce Session Fix Plugin
 */
class WooCommerceSessionFix {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'), 1);
    }
    
    /**
     * Initialize the fix
     */
    public function init() {
        // Override the problematic function
        $this->override_woocommerce_session_function();
        
        // Remove problematic theme hooks
        $this->remove_problematic_hooks();
        
        // Add safe hooks
        $this->add_safe_hooks();
    }
    
    /**
     * Override the WooCommerce session function
     */
    private function override_woocommerce_session_function() {
        if (!function_exists('barcodemine_fix_woocommerce_sessions')) {
            function barcodemine_fix_woocommerce_sessions() {
                // Check if WooCommerce is active and session exists
                if (!class_exists("WooCommerce") || !function_exists("WC")) {
                    return;
                }
                
                // Check if session object exists and is valid
                if (!WC()->session || !is_object(WC()->session)) {
                    return;
                }
                
                // Check if has_session method exists before calling it
                if (!method_exists(WC()->session, "has_session")) {
                    return;
                }
                
                // Only run for non-admin, non-ajax requests
                if ( ! is_admin() && ! wp_doing_ajax() ) {
                    // Now safely call has_session
                    if ( ! WC()->session->has_session() ) {
                        WC()->session->set_customer_session_cookie( true );
                    }
                }
            }
        }
    }
    
    /**
     * Remove problematic theme hooks
     */
    private function remove_problematic_hooks() {
        // Remove any existing problematic hooks
        remove_action('init', 'barcodemine_fix_woocommerce_sessions');
        remove_action('wp_loaded', 'barcodemine_fix_woocommerce_sessions');
        remove_action('wp_head', 'barcodemine_fix_woocommerce_sessions');
        remove_action('wp_footer', 'barcodemine_fix_woocommerce_sessions');
    }
    
    /**
     * Add safe hooks
     */
    private function add_safe_hooks() {
        // Add our safe version with higher priority
        add_action('init', 'barcodemine_fix_woocommerce_sessions', 5);
    }
}

// Initialize the fix
new WooCommerceSessionFix();

// Add admin notice
add_action('admin_notices', function() {
    if (current_user_can('manage_options')) {
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>WooCommerce Session Fix:</strong> Active and protecting your site from session errors.</p>';
        echo '</div>';
    }
});

// Add activation hook
register_activation_hook(__FILE__, function() {
    // Force the fix to be applied immediately
    if (function_exists('barcodemine_fix_woocommerce_sessions')) {
        // Function is already available
    } else {
        // Create the function
        if (!function_exists('barcodemine_fix_woocommerce_sessions')) {
            function barcodemine_fix_woocommerce_sessions() {
                // Check if WooCommerce is active and session exists
                if (!class_exists("WooCommerce") || !function_exists("WC")) {
                    return;
                }
                
                // Check if session object exists and is valid
                if (!WC()->session || !is_object(WC()->session)) {
                    return;
                }
                
                // Check if has_session method exists before calling it
                if (!method_exists(WC()->session, "has_session")) {
                    return;
                }
                
                // Only run for non-admin, non-ajax requests
                if ( ! is_admin() && ! wp_doing_ajax() ) {
                    // Now safely call has_session
                    if ( ! WC()->session->has_session() ) {
                        WC()->session->set_customer_session_cookie( true );
                    }
                }
            }
        }
    }
});
?>
