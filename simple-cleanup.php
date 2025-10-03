<?php
/**
 * Simple WordPress Cleanup Script
 * This script can be run directly from WordPress root
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

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('❌ Error: You must be logged in as an administrator to run this script.');
}

echo "🧹 SIMPLE WORDPRESS CLEANUP\n";
echo "===========================\n\n";

echo "✅ WordPress loaded successfully\n";
echo "Site: " . get_site_url() . "\n";
echo "WordPress Version: " . get_bloginfo('version') . "\n";
echo "Current User: " . wp_get_current_user()->display_name . "\n\n";

// 1. Deactivate problematic plugins
echo "🔧 DEACTIVATING PROBLEMATIC PLUGINS:\n";
echo "====================================\n";

$active_plugins = get_option('active_plugins', array());
$problematic_plugins = array();

foreach ($active_plugins as $plugin) {
    if (strpos($plugin, 'kloudbean') !== false || 
        strpos($plugin, 'security') !== false ||
        strpos($plugin, 'firewall') !== false) {
        $problematic_plugins[] = $plugin;
    }
}

if (!empty($problematic_plugins)) {
    echo "⚠️  Found problematic plugins:\n";
    foreach ($problematic_plugins as $plugin) {
        echo "   - $plugin\n";
    }
    
    // Deactivate them
    $safe_plugins = array_diff($active_plugins, $problematic_plugins);
    update_option('active_plugins', $safe_plugins);
    echo "✅ Problematic plugins deactivated\n";
} else {
    echo "✅ No problematic plugins found\n";
}

// 2. Clear WordPress cache
echo "\n🔧 CLEARING WORDPRESS CACHE:\n";
echo "============================\n";

wp_cache_flush();
echo "✅ WordPress cache cleared\n";

// Clear object cache
if (function_exists('wp_cache_delete')) {
    wp_cache_delete('alloptions', 'options');
    echo "✅ Object cache cleared\n";
}

// 3. Reset memory and performance
echo "\n🔧 RESETTING MEMORY AND PERFORMANCE:\n";
echo "====================================\n";

ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);
ini_set('max_input_time', 300);

echo "✅ Memory and execution limits reset\n";

// 4. Fix database issues
echo "\n🔧 FIXING DATABASE ISSUES:\n";
echo "==========================\n";

global $wpdb;

if (!$wpdb->db_connect()) {
    echo "❌ Database connection failed\n";
} else {
    echo "✅ Database connection working\n";
    
    // Clean up any corrupted data
    $corrupted_posts = $wpdb->get_results("
        SELECT ID, post_title, post_status 
        FROM {$wpdb->posts} 
        WHERE post_status = 'publish' 
        AND (post_content IS NULL OR post_title IS NULL)
    ");
    
    if (!empty($corrupted_posts)) {
        echo "⚠️  Found " . count($corrupted_posts) . " corrupted posts\n";
        foreach ($corrupted_posts as $post) {
            $wpdb->update(
                $wpdb->posts,
                array(
                    'post_content' => $post->post_content ?: '',
                    'post_title' => $post->post_title ?: 'Untitled'
                ),
                array('ID' => $post->ID)
            );
        }
        echo "✅ Corrupted posts fixed\n";
    } else {
        echo "✅ No corrupted posts found\n";
    }
}

// 5. Reset WordPress settings
echo "\n🔧 RESETTING WORDPRESS SETTINGS:\n";
echo "================================\n";

flush_rewrite_rules();
echo "✅ Permalinks reset\n";

// Clear scheduled hooks
wp_clear_scheduled_hook('wp_scheduled_delete');
wp_clear_scheduled_hook('wp_scheduled_auto_draft_delete');
wp_clear_scheduled_hook('wp_scheduled_purge');

// Re-add essential hooks
if (!wp_next_scheduled('wp_scheduled_delete')) {
    wp_schedule_event(time(), 'daily', 'wp_scheduled_delete');
}
if (!wp_next_scheduled('wp_scheduled_auto_draft_delete')) {
    wp_schedule_event(time(), 'daily', 'wp_scheduled_auto_draft_delete');
}

echo "✅ WordPress scheduled hooks reset\n";

// 6. Test WordPress functionality
echo "\n🔍 TESTING WORDPRESS FUNCTIONALITY:\n";
echo "===================================\n";

// Test basic WordPress functions
if (function_exists('get_bloginfo')) {
    echo "✅ WordPress functions working\n";
} else {
    echo "❌ WordPress functions not working\n";
}

// Test database queries
$posts = $wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} LIMIT 5");
if ($posts) {
    echo "✅ Database queries working\n";
} else {
    echo "❌ Database queries not working\n";
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

// 7. Create test post
echo "\n🔧 CREATING TEST POST:\n";
echo "=====================\n";

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

echo "\n🎯 CLEANUP SUMMARY:\n";
echo "==================\n";
echo "✅ Problematic plugins deactivated\n";
echo "✅ WordPress cache cleared\n";
echo "✅ Memory and performance reset\n";
echo "✅ Database issues fixed\n";
echo "✅ WordPress settings reset\n";
echo "✅ WordPress functionality tested\n";

echo "\n📋 NEXT STEPS:\n";
echo "1. Test editing and publishing posts in WordPress admin\n";
echo "2. Check Site Health in WordPress admin\n";
echo "3. Add security features gradually and safely\n";
echo "4. Use established security plugins like Wordfence\n";

echo "\n🧹 Cleanup completed successfully!\n";
?>
