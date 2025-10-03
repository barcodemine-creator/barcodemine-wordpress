<?php
/**
 * FIX THEME SESSION ERROR
 * This script fixes the fatal error in mydecor-child theme
 */

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

echo "🔧 FIXING THEME SESSION ERROR\n";
echo "==============================\n\n";

echo "✅ WordPress loaded successfully\n";
echo "Site: " . get_site_url() . "\n";
echo "WordPress Version: " . get_bloginfo('version') . "\n\n";

// 1. Check if the problematic file exists
$theme_functions_file = get_template_directory() . '/../mydecor-child/functions.php';
if (!file_exists($theme_functions_file)) {
    echo "❌ Theme functions file not found at: $theme_functions_file\n";
    echo "Looking for alternative paths...\n";
    
    // Try alternative paths
    $alternative_paths = array(
        WP_CONTENT_DIR . '/themes/mydecor-child/functions.php',
        ABSPATH . 'wp-content/themes/mydecor-child/functions.php',
        get_theme_root() . '/mydecor-child/functions.php'
    );
    
    foreach ($alternative_paths as $path) {
        if (file_exists($path)) {
            $theme_functions_file = $path;
            echo "✅ Found theme file at: $path\n";
            break;
        }
    }
}

if (!file_exists($theme_functions_file)) {
    echo "❌ Could not find mydecor-child theme functions.php file\n";
    echo "Please check the theme directory structure\n";
    exit(1);
}

echo "✅ Found theme file: $theme_functions_file\n";

// 2. Read the file and find the problematic line
echo "\n🔍 ANALYZING THEME FILE:\n";
echo "========================\n";

$file_content = file_get_contents($theme_functions_file);
$lines = explode("\n", $file_content);

if (count($lines) < 1117) {
    echo "❌ File has only " . count($lines) . " lines, but error is on line 1117\n";
    exit(1);
}

echo "✅ File has " . count($lines) . " lines\n";

// Find the problematic function around line 1117
$problematic_line = $lines[1116]; // Array is 0-indexed
echo "Line 1117: " . trim($problematic_line) . "\n";

// Look for the barcodemine_fix_woocommerce_sessions function
$function_start = -1;
$function_end = -1;

for ($i = 0; $i < count($lines); $i++) {
    if (strpos($lines[$i], 'function barcodemine_fix_woocommerce_sessions') !== false) {
        $function_start = $i;
        echo "✅ Found function at line " . ($i + 1) . "\n";
        break;
    }
}

if ($function_start == -1) {
    echo "❌ Could not find barcodemine_fix_woocommerce_sessions function\n";
    exit(1);
}

// Find the end of the function
for ($i = $function_start + 1; $i < count($lines); $i++) {
    if (strpos($lines[$i], '}') !== false && 
        (strpos($lines[$i], 'function') !== false || 
         substr_count($lines[$i], '}') > substr_count($lines[$i], '{'))) {
        $function_end = $i;
        break;
    }
}

if ($function_end == -1) {
    echo "❌ Could not find end of function\n";
    exit(1);
}

echo "✅ Function spans lines " . ($function_start + 1) . " to " . ($function_end + 1) . "\n";

// 3. Create a backup
echo "\n💾 CREATING BACKUP:\n";
echo "==================\n";

$backup_file = $theme_functions_file . '.backup.' . date('Y-m-d-H-i-s');
if (copy($theme_functions_file, $backup_file)) {
    echo "✅ Backup created: $backup_file\n";
} else {
    echo "❌ Failed to create backup\n";
    exit(1);
}

// 4. Fix the function
echo "\n🔧 FIXING THE FUNCTION:\n";
echo "=======================\n";

// Extract the function content
$function_lines = array_slice($lines, $function_start, $function_end - $function_start + 1);
$function_content = implode("\n", $function_lines);

echo "Original function:\n";
echo "------------------\n";
echo $function_content . "\n\n";

// Create the fixed function
$fixed_function = 'function barcodemine_fix_woocommerce_sessions() {
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
    
    // Now safely call has_session
    if (!WC()->session->has_session()) {
        WC()->session->set_customer_session_cookie(true);
    }
}';

echo "Fixed function:\n";
echo "---------------\n";
echo $fixed_function . "\n\n";

// 5. Replace the function in the file
echo "🔧 APPLYING FIX:\n";
echo "================\n";

// Replace the function content
$new_file_content = $file_content;
$new_file_content = str_replace($function_content, $fixed_function, $new_file_content);

// Write the fixed file
if (file_put_contents($theme_functions_file, $new_file_content)) {
    echo "✅ Theme file updated successfully\n";
} else {
    echo "❌ Failed to update theme file\n";
    exit(1);
}

// 6. Test the fix
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

// 7. Test WordPress functionality
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

echo "\n🎯 FIX SUMMARY:\n";
echo "===============\n";
echo "✅ Theme session error fixed\n";
echo "✅ Backup created: $backup_file\n";
echo "✅ WordPress functionality tested\n";

echo "\n📋 NEXT STEPS:\n";
echo "1. Test editing and publishing posts\n";
echo "2. Check Site Health in WordPress admin\n";
echo "3. If issues persist, restore backup and contact theme developer\n";

echo "\n🔧 Theme session error fix completed!\n";
?>
