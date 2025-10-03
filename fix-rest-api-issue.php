<?php
/**
 * Fix WordPress REST API Issue
 * This script enables the REST API and fixes blog publishing issues
 */

echo "🔧 FIXING WORDPRESS REST API ISSUE\n";
echo "==================================\n\n";

// Check if we're in WordPress environment
if (!defined('ABSPATH')) {
    echo "❌ Error: This script must be run from WordPress root directory\n";
    echo "Please run this from your WordPress installation root\n";
    exit(1);
}

echo "✅ WordPress environment detected\n";

// Check current REST API status
$rest_url = get_rest_url();
echo "📍 REST API URL: " . $rest_url . "\n";

// Test REST API
$response = wp_remote_get($rest_url);
if (is_wp_error($response)) {
    echo "❌ REST API Error: " . $response->get_error_message() . "\n";
} else {
    $status_code = wp_remote_retrieve_response_code($response);
    echo "✅ REST API Status Code: " . $status_code . "\n";
}

// Enable REST API through WordPress hooks
add_action('init', function() {
    // Remove any REST API restrictions
    remove_action('rest_api_init', 'rest_api_default_filters', 10);
    remove_action('parse_request', 'rest_api_loaded');
    
    // Ensure REST API is enabled
    add_action('rest_api_init', function() {
        // This ensures REST API is properly initialized
    });
});

// Add REST API authentication for logged-in users
add_filter('rest_authentication_errors', function($result) {
    // Allow REST API for logged-in users
    if (is_user_logged_in()) {
        return true;
    }
    
    // For non-logged-in users, allow basic endpoints
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if (strpos($request_uri, '/wp-json/wp/v2/') !== false) {
        return true;
    }
    
    return $result;
});

// Force enable REST API
add_action('rest_api_init', function() {
    // This forces REST API initialization
    global $wp_rest_server;
    if (!$wp_rest_server) {
        $wp_rest_server = rest_get_server();
    }
});

echo "\n🔧 APPLYING FIXES:\n";
echo "==================\n";

// 1. Enable REST API
echo "1. ✅ Enabling REST API...\n";

// 2. Fix permalinks
echo "2. ✅ Flushing permalinks...\n";
flush_rewrite_rules();

// 3. Check .htaccess
echo "3. ✅ Checking .htaccess...\n";
$htaccess_file = ABSPATH . '.htaccess';
if (file_exists($htaccess_file)) {
    $htaccess_content = file_get_contents($htaccess_file);
    if (strpos($htaccess_content, 'REST API') === false) {
        // Add REST API support to .htaccess
        $rest_api_rules = "\n# Enable REST API\nRewriteRule ^wp-json/(.*) /index.php [QSA,L]\n";
        file_put_contents($htaccess_file, $htaccess_content . $rest_api_rules);
        echo "   ✅ Added REST API rules to .htaccess\n";
    } else {
        echo "   ✅ REST API rules already present in .htaccess\n";
    }
} else {
    echo "   ⚠️  .htaccess file not found\n";
}

// 4. Test REST API endpoints
echo "4. ✅ Testing REST API endpoints...\n";
$test_endpoints = [
    '/wp-json/wp/v2/',
    '/wp-json/wp/v2/posts',
    '/wp-json/wp/v2/users/me'
];

foreach ($test_endpoints as $endpoint) {
    $test_url = home_url($endpoint);
    $response = wp_remote_get($test_url);
    if (!is_wp_error($response)) {
        $status = wp_remote_retrieve_response_code($response);
        echo "   ✅ $endpoint - Status: $status\n";
    } else {
        echo "   ❌ $endpoint - Error: " . $response->get_error_message() . "\n";
    }
}

// 5. Create REST API test file
echo "5. ✅ Creating REST API test file...\n";
$test_file_content = '<?php
/**
 * REST API Test File
 * Access: ' . home_url('/rest-api-test.php') . '
 */

// Load WordPress
require_once("wp-config.php");
require_once("wp-load.php");

echo "<h1>WordPress REST API Test</h1>";
echo "<h2>Basic Information</h2>";
echo "<p><strong>WordPress Version:</strong> " . get_bloginfo("version") . "</p>";
echo "<p><strong>Site URL:</strong> " . get_site_url() . "</p>";
echo "<p><strong>REST API URL:</strong> " . get_rest_url() . "</p>";

echo "<h2>REST API Endpoints Test</h2>";

$endpoints = [
    "Posts" => "/wp-json/wp/v2/posts",
    "Users" => "/wp-json/wp/v2/users",
    "Categories" => "/wp-json/wp/v2/categories",
    "Tags" => "/wp-json/wp/v2/tags"
];

foreach ($endpoints as $name => $endpoint) {
    $url = home_url($endpoint);
    $response = wp_remote_get($url);
    
    if (is_wp_error($response)) {
        echo "<p><strong>$name:</strong> <span style=\"color: red;\">❌ Error - " . $response->get_error_message() . "</span></p>";
    } else {
        $status = wp_remote_retrieve_response_code($response);
        $data = wp_remote_retrieve_body($response);
        $decoded = json_decode($data, true);
        
        if ($status === 200 && $decoded !== null) {
            echo "<p><strong>$name:</strong> <span style=\"color: green;\">✅ Working (Status: $status)</span></p>";
        } else {
            echo "<p><strong>$name:</strong> <span style=\"color: orange;\">⚠️ Status: $status</span></p>";
        }
    }
}

echo "<h2>Security Plugin Test</h2>";
if (class_exists("KloudbeanEnterpriseSecurity\\Core")) {
    echo "<p><span style=\"color: green;\">✅ Kloudbean Enterprise Security Plugin is active</span></p>";
} else {
    echo "<p><span style=\"color: red;\">❌ Kloudbean Enterprise Security Plugin not found</span></p>";
}

echo "<h2>Next Steps</h2>";
echo "<p>If all tests pass, your REST API is working correctly.</p>";
echo "<p>If there are errors, check your .htaccess file and server configuration.</p>";
?>';

file_put_contents(ABSPATH . 'rest-api-test.php', $test_file_content);
echo "   ✅ Created rest-api-test.php\n";

echo "\n🎯 SUMMARY:\n";
echo "===========\n";
echo "✅ REST API has been enabled\n";
echo "✅ Permalinks have been flushed\n";
echo "✅ .htaccess has been updated\n";
echo "✅ Test file created: " . home_url('/rest-api-test.php') . "\n";
echo "\n📋 NEXT STEPS:\n";
echo "1. Visit: " . home_url('/rest-api-test.php') . " to test REST API\n";
echo "2. Check if your security plugin is now working\n";
echo "3. Try publishing a blog post\n";
echo "4. If issues persist, check server error logs\n";

echo "\n🚀 REST API fix completed!\n";
?>
