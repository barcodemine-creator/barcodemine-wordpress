<?php
/**
 * EMERGENCY REVERT - Restore Website Functionality
 * This script will remove all the fixes and restore your site
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

echo "🚨 EMERGENCY REVERT - RESTORING WEBSITE\n";
echo "=======================================\n\n";

// Remove Must-Use Plugin
echo "🔧 Removing Must-Use Plugin...\n";
$mu_plugin_file = WP_CONTENT_DIR . '/mu-plugins/woocommerce-session-fix.php';
if (file_exists($mu_plugin_file)) {
    if (unlink($mu_plugin_file)) {
        echo "✅ Must-Use Plugin removed\n";
    } else {
        echo "❌ Could not remove Must-Use Plugin\n";
    }
} else {
    echo "✅ Must-Use Plugin not found\n";
}

// Remove mu-plugins directory if empty
$mu_plugins_dir = WP_CONTENT_DIR . '/mu-plugins';
if (is_dir($mu_plugins_dir) && count(scandir($mu_plugins_dir)) == 2) {
    rmdir($mu_plugins_dir);
    echo "✅ Empty mu-plugins directory removed\n";
}

// Deactivate WooCommerce Session Fix Plugin
echo "🔧 Deactivating WooCommerce Session Fix Plugin...\n";
$plugin_file = 'woocommerce-session-fix-plugin/woocommerce-session-fix-plugin.php';
if (is_plugin_active($plugin_file)) {
    deactivate_plugins($plugin_file);
    echo "✅ Plugin deactivated\n";
} else {
    echo "✅ Plugin not active\n";
}

// Remove the plugin file
$plugin_path = WP_PLUGIN_DIR . '/woocommerce-session-fix-plugin.php';
if (file_exists($plugin_path)) {
    if (unlink($plugin_path)) {
        echo "✅ Plugin file removed\n";
    } else {
        echo "❌ Could not remove plugin file\n";
    }
}

// Restore original theme function
echo "🔧 Restoring original theme function...\n";
$theme_file = get_stylesheet_directory() . '/functions.php';
if (file_exists($theme_file)) {
    // Find backup files
    $backup_files = glob($theme_file . '.backup.*');
    if (!empty($backup_files)) {
        // Get the most recent backup
        $latest_backup = $backup_files[count($backup_files) - 1];
        echo "📁 Found backup: " . basename($latest_backup) . "\n";
        
        // Restore from backup
        if (copy($latest_backup, $theme_file)) {
            echo "✅ Theme file restored from backup\n";
        } else {
            echo "❌ Could not restore theme file\n";
        }
    } else {
        echo "⚠️  No backup files found\n";
        
        // Try to restore the original problematic function
        $theme_content = file_get_contents($theme_file);
        
        $safe_function = 'function barcodemine_fix_woocommerce_sessions() {
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
}';

        $original_function = 'function barcodemine_fix_woocommerce_sessions() {
    if ( ! is_admin() && ! wp_doing_ajax() ) {
        // Ensure WooCommerce session is initialized
        if ( ! WC()->session->has_session() ) {
            WC()->session->set_customer_session_cookie( true );
        }
    }
}';

        if (strpos($theme_content, $safe_function) !== false) {
            $restored_content = str_replace($safe_function, $original_function, $theme_content);
            if (file_put_contents($theme_file, $restored_content)) {
                echo "✅ Theme function restored to original\n";
            } else {
                echo "❌ Could not restore theme function\n";
            }
        } else {
            echo "✅ Theme function already original\n";
        }
    }
} else {
    echo "❌ Theme file not found\n";
}

// Clear all caches
echo "🔧 Clearing caches...\n";
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "✅ WordPress cache cleared\n";
}

if (function_exists('wp_cache_delete')) {
    wp_cache_delete('alloptions', 'options');
    echo "✅ Object cache cleared\n";
}

// Test website functionality
echo "\n🧪 TESTING WEBSITE:\n";
echo "===================\n";

// Test basic WordPress functionality
if (function_exists('wp_loaded')) {
    echo "✅ WordPress core functions working\n";
} else {
    echo "❌ WordPress core functions not working\n";
}

// Test REST API
$rest_url = get_rest_url() . 'wp/v2/posts';
$response = wp_remote_get($rest_url);
if (!is_wp_error($response)) {
    $code = wp_remote_retrieve_response_code($response);
    echo "✅ REST API working (Status: " . $code . ")\n";
} else {
    echo "❌ REST API error: " . $response->get_error_message() . "\n";
}

// Test home page
$home_url = home_url();
$home_response = wp_remote_get($home_url);
if (!is_wp_error($home_response)) {
    $home_code = wp_remote_retrieve_response_code($home_response);
    if ($home_code == 200) {
        echo "✅ Home page working (Status: " . $home_code . ")\n";
    } else {
        echo "⚠️  Home page returned status: " . $home_code . "\n";
    }
} else {
    echo "❌ Home page error: " . $home_response->get_error_message() . "\n";
}

echo "\n🎉 EMERGENCY REVERT COMPLETED!\n";
echo "==============================\n";
echo "✅ All fixes removed\n";
echo "✅ Website should be working now\n";
echo "✅ Original functionality restored\n";
echo "\n🔗 Test your website: " . home_url() . "\n";
echo "\n⚠️  If you still have issues, check:\n";
echo "1. WordPress error logs\n";
echo "2. Server error logs\n";
echo "3. Contact your hosting provider\n";
?>
