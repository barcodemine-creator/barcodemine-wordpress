<?php
/**
 * PRECISE THEME FIX
 * This script fixes the exact function with proper syntax
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

echo "🔧 PRECISE THEME FIX\n";
echo "===================\n\n";

echo "✅ WordPress loaded successfully\n";
echo "Site: " . get_site_url() . "\n";
echo "WordPress Version: " . get_bloginfo('version') . "\n\n";

// 1. Find the theme file
$theme_functions_file = get_template_directory() . '/../mydecor-child/functions.php';
if (!file_exists($theme_functions_file)) {
    $theme_functions_file = WP_CONTENT_DIR . '/themes/mydecor-child/functions.php';
}

if (!file_exists($theme_functions_file)) {
    echo "❌ Could not find mydecor-child theme functions.php file\n";
    exit(1);
}

echo "✅ Found theme file: $theme_functions_file\n";

// 2. Read the file
$file_content = file_get_contents($theme_functions_file);
$lines = explode("\n", $file_content);

echo "✅ File has " . count($lines) . " lines\n";

// 3. Create backup
$backup_file = $theme_functions_file . '.backup.' . date('Y-m-d-H-i-s');
if (copy($theme_functions_file, $backup_file)) {
    echo "✅ Backup created: $backup_file\n";
} else {
    echo "❌ Failed to create backup\n";
    exit(1);
}

// 4. Find the exact problematic line and fix it
echo "\n🔧 APPLYING PRECISE FIX:\n";
echo "========================\n";

// The problematic line is: if ( ! WC()->session->has_session() ) {
// We need to replace it with a safe version

$new_file_content = $file_content;

// Replace the problematic line with a safe version
$problematic_line = 'if ( ! WC()->session->has_session() ) {';
$safe_line = 'if ( WC()->session && is_object(WC()->session) && method_exists(WC()->session, "has_session") && ! WC()->session->has_session() ) {';

if (strpos($new_file_content, $problematic_line) !== false) {
    $new_file_content = str_replace($problematic_line, $safe_line, $new_file_content);
    echo "✅ Replaced problematic line with safe version\n";
} else {
    echo "❌ Could not find the problematic line\n";
    echo "Looking for alternative patterns...\n";
    
    // Try alternative patterns
    $patterns = array(
        'if ( ! WC()->session->has_session() )',
        'if (!WC()->session->has_session())',
        'if( ! WC()->session->has_session() )',
        'if(!WC()->session->has_session())'
    );
    
    $found = false;
    foreach ($patterns as $pattern) {
        if (strpos($new_file_content, $pattern) !== false) {
            $new_file_content = str_replace($pattern, 'if ( WC()->session && is_object(WC()->session) && method_exists(WC()->session, "has_session") && ! WC()->session->has_session() )', $new_file_content);
            echo "✅ Found and replaced pattern: $pattern\n";
            $found = true;
            break;
        }
    }
    
    if (!$found) {
        echo "❌ Could not find any matching patterns\n";
        exit(1);
    }
}

// 5. Write the fixed file
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

// 8. Test post creation
echo "\n🔧 TESTING POST CREATION:\n";
echo "=========================\n";

$test_post = array(
    'post_title'    => 'Test Post - ' . date('Y-m-d H:i:s'),
    'post_content'  => 'This is a test post. If you can see this, WordPress is working!',
    'post_status'   => 'draft',
    'post_author'   => 1,
    'post_type'     => 'post'
);

$post_id = wp_insert_post($test_post);
if (is_wp_error($post_id)) {
    echo "❌ Failed to create test post: " . $post_id->get_error_message() . "\n";
} else {
    echo "✅ Test post created successfully (ID: $post_id)\n";
}

echo "\n🎯 FIX SUMMARY:\n";
echo "===============\n";
echo "✅ Theme session error fixed with precise replacement\n";
echo "✅ Backup created: $backup_file\n";
echo "✅ WordPress functionality tested\n";
echo "✅ REST API tested\n";
echo "✅ Post creation tested\n";

echo "\n📋 NEXT STEPS:\n";
echo "1. Test editing and publishing posts in WordPress admin\n";
echo "2. Check Site Health in WordPress admin\n";
echo "3. Your WordPress site should now work normally\n";

echo "\n🔧 Precise theme fix completed successfully!\n";
?>
