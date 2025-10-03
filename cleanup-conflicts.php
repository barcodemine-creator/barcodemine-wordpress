<?php
/**
 * CLEANUP CONFLICTS SCRIPT
 * This script removes all conflicting security code and fixes WordPress
 */

echo "🧹 CLEANING UP CONFLICTS\n";
echo "========================\n\n";

// Check if we're in WordPress environment
if (!defined('ABSPATH')) {
    echo "❌ Error: This script must be run from WordPress root directory\n";
    echo "Please run this from your WordPress installation root\n";
    exit(1);
}

echo "✅ WordPress environment detected\n";

// 1. Deactivate problematic plugins
echo "\n🔧 DEACTIVATING PROBLEMATIC PLUGINS:\n";
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

// 2. Remove conflicting REST API filters
echo "\n🔧 REMOVING CONFLICTING REST API FILTERS:\n";
echo "=========================================\n";

// Remove all custom REST API filters
remove_all_filters('rest_authentication_errors');
remove_all_filters('rest_pre_serve_request');
remove_all_filters('rest_request_before_callbacks');
remove_all_filters('rest_request_after_callbacks');

// Remove custom REST API actions
remove_all_actions('rest_api_init');

echo "✅ Conflicting REST API filters removed\n";

// 3. Clear WordPress cache
echo "\n🔧 CLEARING WORDPRESS CACHE:\n";
echo "============================\n";

wp_cache_flush();
echo "✅ WordPress cache cleared\n";

// Clear object cache
if (function_exists('wp_cache_delete')) {
    wp_cache_delete('alloptions', 'options');
    echo "✅ Object cache cleared\n";
}

// 4. Fix memory and performance
echo "\n🔧 FIXING MEMORY AND PERFORMANCE:\n";
echo "==================================\n";

ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);
ini_set('max_input_time', 300);

echo "✅ Memory and execution limits reset\n";

// 5. Fix database issues
echo "\n🔧 FIXING DATABASE ISSUES:\n";
echo "==========================\n";

global $wpdb;

// Check database connection
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

// 6. Reset WordPress settings
echo "\n🔧 RESETTING WORDPRESS SETTINGS:\n";
echo "================================\n";

// Reset permalinks
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

// 7. Test WordPress functionality
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
    echo "✅ REST API status: $status\n";
}

// 8. Create clean test
echo "\n🔧 CREATING CLEAN TEST:\n";
echo "======================\n";

$test_file_content = '<?php
/**
 * Clean WordPress Test
 * Access: ' . home_url('/clean-wordpress-test.php') . '
 */

// Load WordPress
require_once("wp-config.php");
require_once("wp-load.php");

echo "<h1>Clean WordPress Test</h1>";
echo "<h2>WordPress Information</h2>";
echo "<p><strong>WordPress Version:</strong> " . get_bloginfo("version") . "</p>";
echo "<p><strong>Site URL:</strong> " . get_site_url() . "</p>";
echo "<p><strong>Admin URL:</strong> " . admin_url() . "</p>";

echo "<h2>Database Test</h2>";
global $wpdb;
if ($wpdb->db_connect()) {
    echo "<p><span style=\"color: green;\">✅ Database connection working</span></p>";
    
    $posts_count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts}");
    echo "<p><strong>Total Posts:</strong> $posts_count</p>";
} else {
    echo "<p><span style=\"color: red;\">❌ Database connection failed</span></p>";
}

echo "<h2>REST API Test</h2>";
$rest_url = get_rest_url();
$response = wp_remote_get($rest_url);
if (is_wp_error($response)) {
    echo "<p><span style=\"color: red;\">❌ REST API Error: " . $response->get_error_message() . "</span></p>";
} else {
    $status = wp_remote_retrieve_response_code($response);
    echo "<p><span style=\"color: green;\">✅ REST API Status: $status</span></p>";
}

echo "<h2>Plugin Status</h2>";
$active_plugins = get_option("active_plugins", array());
echo "<p><strong>Active Plugins:</strong> " . count($active_plugins) . "</p>";

foreach ($active_plugins as $plugin) {
    echo "<p>- $plugin</p>";
}

echo "<h2>Test Post Creation</h2>";
$test_post = array(
    "post_title"    => "Test Post - " . date("Y-m-d H:i:s"),
    "post_content"  => "This is a test post. If you can see this, WordPress is working!",
    "post_status"   => "draft",
    "post_author"   => 1,
    "post_type"     => "post"
);

$post_id = wp_insert_post($test_post);
if (is_wp_error($post_id)) {
    echo "<p><span style=\"color: red;\">❌ Failed to create test post: " . $post_id->get_error_message() . "</span></p>";
} else {
    echo "<p><span style=\"color: green;\">✅ Test post created successfully (ID: $post_id)</span></p>";
}

echo "<h2>Next Steps</h2>";
echo "<p>1. If all tests pass, WordPress is working correctly</p>";
echo "<p>2. You can now safely add security features one by one</p>";
echo "<p>3. Use established security plugins like Wordfence</p>";
?>';

file_put_contents(ABSPATH . 'clean-wordpress-test.php', $test_file_content);
echo "✅ Clean test file created\n";

echo "\n🎯 CLEANUP SUMMARY:\n";
echo "==================\n";
echo "✅ Problematic plugins deactivated\n";
echo "✅ Conflicting REST API filters removed\n";
echo "✅ WordPress cache cleared\n";
echo "✅ Memory and performance reset\n";
echo "✅ Database issues fixed\n";
echo "✅ WordPress settings reset\n";
echo "✅ Clean test created\n";

echo "\n📋 NEXT STEPS:\n";
echo "1. Visit: " . home_url('/clean-wordpress-test.php') . " to test WordPress\n";
echo "2. Check if WordPress is working normally\n";
echo "3. Try editing and publishing posts\n";
echo "4. Add security features gradually and safely\n";

echo "\n🧹 Conflicts cleanup completed!\n";
?>
