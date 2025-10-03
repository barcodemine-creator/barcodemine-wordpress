<?php
/**
 * DIRECT THEME FUNCTION FIX
 * This script directly fixes the barcodemine_fix_woocommerce_sessions function
 */

echo "🔧 DIRECT THEME FUNCTION FIX\n";
echo "============================\n\n";

echo "Fixing: barcodemine_fix_woocommerce_sessions() function\n";
echo "Error: Call to a member function has_session() on null\n\n";

// Try to load WordPress
$wp_load_paths = array(
    './wp-load.php',
    '../wp-load.php',
    '../../wp-load.php',
    '../../../wp-load.php'
);

$wp_loaded = false;
foreach ($wp_load_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $wp_loaded = true;
        break;
    }
}

if (!$wp_loaded) {
    die('❌ Error: Could not find WordPress. Please place this file in your WordPress root directory.');
}

echo "✅ WordPress loaded successfully\n";
echo "Site: " . get_site_url() . "\n\n";

// Find the theme file
$theme_functions_file = get_template_directory() . '/../mydecor-child/functions.php';
if (!file_exists($theme_functions_file)) {
    $theme_functions_file = WP_CONTENT_DIR . '/themes/mydecor-child/functions.php';
}

if (!file_exists($theme_functions_file)) {
    echo "❌ Could not find mydecor-child theme functions.php file\n";
    exit(1);
}

echo "✅ Found theme file: $theme_functions_file\n";

// Read the file
$file_content = file_get_contents($theme_functions_file);

// Create backup
$backup_file = $theme_functions_file . '.backup.' . date('Y-m-d-H-i-s');
if (copy($theme_functions_file, $backup_file)) {
    echo "✅ Backup created: $backup_file\n";
} else {
    echo "❌ Failed to create backup\n";
    exit(1);
}

echo "\n🔧 APPLYING DIRECT FIX:\n";
echo "=======================\n";

// Find the exact function and replace it completely
$old_function = 'function barcodemine_fix_woocommerce_sessions() {
    if ( ! is_admin() && ! wp_doing_ajax() ) {
        // Ensure WooCommerce session is initialized
        if ( ! WC()->session->has_session() ) {
            WC()->session->set_customer_session_cookie( true );
        }
    }
}';

$new_function = 'function barcodemine_fix_woocommerce_sessions() {
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

// Try to replace the entire function
if (strpos($file_content, $old_function) !== false) {
    $new_file_content = str_replace($old_function, $new_function, $file_content);
    echo "✅ Replaced entire function with safe version\n";
} else {
    // If exact match not found, try to find and replace just the problematic line
    $problematic_line = 'if ( ! WC()->session->has_session() ) {';
    $safe_line = 'if ( WC()->session && is_object(WC()->session) && method_exists(WC()->session, "has_session") && ! WC()->session->has_session() ) {';
    
    if (strpos($file_content, $problematic_line) !== false) {
        $new_file_content = str_replace($problematic_line, $safe_line, $file_content);
        echo "✅ Replaced problematic line with safe version\n";
    } else {
        echo "❌ Could not find the exact function or line to replace\n";
        echo "Let me show you what's around line 1117:\n";
        
        $lines = explode("\n", $file_content);
        if (count($lines) >= 1117) {
            for ($i = 1110; $i <= 1125; $i++) {
                if (isset($lines[$i])) {
                    echo "Line " . ($i + 1) . ": " . trim($lines[$i]) . "\n";
                }
            }
        }
        exit(1);
    }
}

// Write the fixed file
if (file_put_contents($theme_functions_file, $new_file_content)) {
    echo "✅ Theme file updated successfully\n";
} else {
    echo "❌ Failed to update theme file\n";
    exit(1);
}

// Test the fix
echo "\n🔍 TESTING THE FIX:\n";
echo "===================\n";

// Test if the file is syntactically correct
$syntax_check = shell_exec("php -l " . escapeshellarg($theme_functions_file) . " 2>&1");
if (strpos($syntax_check, 'No syntax errors') !== false) {
    echo "✅ Theme file syntax is correct\n";
} else {
    echo "❌ Theme file has syntax errors:\n";
    echo $syntax_check . "\n";
    
    // Restore backup
    if (copy($backup_file, $theme_functions_file)) {
        echo "✅ Restored backup file\n";
    }
    exit(1);
}

// Test WordPress functionality
echo "\n🔍 TESTING WORDPRESS:\n";
echo "=====================\n";

// Test basic WordPress functions
if (function_exists('get_bloginfo')) {
    echo "✅ WordPress functions working\n";
} else {
    echo "❌ WordPress functions not working\n";
}

// Test REST API
$rest_url = get_rest_url();
$response = wp_remote_get($rest_url);
if (is_wp_error($response)) {
    echo "❌ REST API error: " . $response->get_error_message() . "\n";
} else {
    $status = wp_remote_retrieve_response_code($response);
    if ($status == 200) {
        echo "✅ REST API working (Status: $status)\n";
    } else {
        echo "⚠️  REST API status: $status\n";
    }
}

// Test the specific endpoint that was failing
$post_types_url = get_rest_url() . 'wp/v2/types/post?context=edit';
$response = wp_remote_get($post_types_url);
if (is_wp_error($response)) {
    echo "❌ Post types endpoint error: " . $response->get_error_message() . "\n";
} else {
    $status = wp_remote_retrieve_response_code($response);
    if ($status == 200) {
        echo "✅ Post types endpoint working (Status: $status)\n";
    } else {
        echo "⚠️  Post types endpoint status: $status\n";
    }
}

echo "\n🎯 DIRECT FIX SUMMARY:\n";
echo "=====================\n";
echo "✅ barcodemine_fix_woocommerce_sessions function fixed\n";
echo "✅ Added proper null checks for WC()->session\n";
echo "✅ Backup created: $backup_file\n";
echo "✅ WordPress functionality tested\n";

echo "\n📋 NEXT STEPS:\n";
echo "1. Test your WordPress site - it should work now\n";
echo "2. Try editing and publishing posts\n";
echo "3. The fatal error should be resolved\n";

echo "\n🔧 Direct theme function fix completed!\n";
?>
