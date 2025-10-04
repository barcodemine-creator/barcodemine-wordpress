<?php
/**
 * Test Telegram Blog Publisher Plugin
 * This will diagnose any issues with the plugin
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

echo "🔍 TESTING TELEGRAM BLOG PUBLISHER PLUGIN\n";
echo "==========================================\n\n";

// Check if plugin is active
if (!is_plugin_active('telegram-blog-publisher/telegram-blog-publisher.php')) {
    echo "❌ Plugin is NOT active\n";
    echo "Please activate the plugin first\n";
    exit;
}

echo "✅ Plugin is active\n";

// Check plugin files
$plugin_dir = WP_PLUGIN_DIR . '/telegram-blog-publisher/';
$required_files = [
    'telegram-blog-publisher.php',
    'templates/admin-dashboard.php',
    'templates/admin-settings.php',
    'templates/admin-logs.php',
    'assets/admin.css',
    'assets/admin.js'
];

echo "\n📁 CHECKING PLUGIN FILES:\n";
echo "========================\n";

foreach ($required_files as $file) {
    $file_path = $plugin_dir . $file;
    if (file_exists($file_path)) {
        echo "✅ {$file}\n";
    } else {
        echo "❌ {$file} - MISSING\n";
    }
}

// Check plugin options
echo "\n⚙️ CHECKING PLUGIN OPTIONS:\n";
echo "===========================\n";

$webhook_secret = get_option('tbp_webhook_secret', '');
$api_keys = get_option('tbp_api_keys', []);
$license_status = get_option('tbp_license_status', 'invalid');

echo "Webhook Secret: " . (empty($webhook_secret) ? "❌ Not set" : "✅ Set") . "\n";
echo "API Keys: " . (empty($api_keys) ? "❌ None configured" : "✅ " . count($api_keys) . " configured") . "\n";
echo "License Status: " . ($license_status === 'valid' ? "✅ Valid" : "❌ Invalid") . "\n";

// Check REST API endpoint
echo "\n🌐 CHECKING REST API ENDPOINT:\n";
echo "==============================\n";

$webhook_url = get_rest_url() . 'telegram-blog-publisher/v1/webhook';
echo "Webhook URL: {$webhook_url}\n";

$response = wp_remote_get($webhook_url);
if (is_wp_error($response)) {
    echo "❌ REST API Error: " . $response->get_error_message() . "\n";
} else {
    $code = wp_remote_retrieve_response_code($response);
    echo "✅ REST API Response Code: {$code}\n";
    
    if ($code === 401) {
        echo "ℹ️  This is expected - webhook requires authentication\n";
    }
}

// Check admin menu
echo "\n📋 CHECKING ADMIN MENU:\n";
echo "=======================\n";

global $menu, $submenu;

$found_menu = false;
$found_submenus = 0;

if (isset($menu)) {
    foreach ($menu as $item) {
        if (isset($item[2]) && strpos($item[2], 'telegram-blog-publisher') !== false) {
            $found_menu = true;
            echo "✅ Main menu found: {$item[0]}\n";
            break;
        }
    }
}

if (isset($submenu['telegram-blog-publisher'])) {
    $found_submenus = count($submenu['telegram-blog-publisher']);
    echo "✅ Submenus found: {$found_submenus}\n";
    foreach ($submenu['telegram-blog-publisher'] as $submenu_item) {
        echo "  - {$submenu_item[0]}\n";
    }
}

if (!$found_menu) {
    echo "❌ Main menu not found\n";
}

// Test AJAX endpoints
echo "\n🔄 TESTING AJAX ENDPOINTS:\n";
echo "==========================\n";

$ajax_actions = [
    'tbp_save_settings',
    'tbp_test_webhook',
    'tbp_test_api_key',
    'tbp_reactivate_license'
];

foreach ($ajax_actions as $action) {
    if (has_action("wp_ajax_{$action}")) {
        echo "✅ {$action}\n";
    } else {
        echo "❌ {$action} - Not registered\n";
    }
}

// Check for JavaScript errors
echo "\n📜 CHECKING JAVASCRIPT:\n";
echo "=======================\n";

$js_file = $plugin_dir . 'assets/admin.js';
if (file_exists($js_file)) {
    $js_content = file_get_contents($js_file);
    
    // Check for common JS issues
    $js_issues = [];
    
    if (strpos($js_content, 'jQuery') === false) {
        $js_issues[] = "jQuery not found";
    }
    
    if (strpos($js_content, 'tbp_ajax') === false) {
        $js_issues[] = "tbp_ajax object not found";
    }
    
    if (empty($js_issues)) {
        echo "✅ JavaScript looks good\n";
    } else {
        echo "⚠️  JavaScript issues found:\n";
        foreach ($js_issues as $issue) {
            echo "  - {$issue}\n";
        }
    }
} else {
    echo "❌ JavaScript file not found\n";
}

// Test webhook functionality
echo "\n🧪 TESTING WEBHOOK FUNCTIONALITY:\n";
echo "=================================\n";

if (!empty($webhook_secret)) {
    $test_data = [
        'topic' => 'Test Blog Post',
        'title' => 'Test Title',
        'word_count' => 100,
        'tone' => 'professional'
    ];
    
    $response = wp_remote_post($webhook_url, [
        'headers' => [
            'Content-Type' => 'application/json',
            'X-Webhook-Secret' => $webhook_secret
        ],
        'body' => json_encode($test_data),
        'timeout' => 30
    ]);
    
    if (is_wp_error($response)) {
        echo "❌ Webhook test failed: " . $response->get_error_message() . "\n";
    } else {
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        echo "📊 Response Code: {$code}\n";
        echo "📄 Response Body: " . substr($body, 0, 200) . "...\n";
        
        if ($code === 200) {
            echo "✅ Webhook test successful\n";
        } else {
            echo "❌ Webhook test failed\n";
        }
    }
} else {
    echo "⚠️  Cannot test webhook - no secret configured\n";
}

// Check WordPress errors
echo "\n🚨 CHECKING FOR ERRORS:\n";
echo "=======================\n";

$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    $recent_errors = shell_exec("tail -n 20 " . escapeshellarg($error_log) . " 2>/dev/null");
    if ($recent_errors) {
        echo "Recent errors from error log:\n";
        echo $recent_errors . "\n";
    } else {
        echo "✅ No recent errors found\n";
    }
} else {
    echo "ℹ️  Error log not accessible\n";
}

echo "\n🎯 DIAGNOSIS COMPLETE\n";
echo "====================\n";
echo "If you see any ❌ errors above, those need to be fixed.\n";
echo "If everything shows ✅, the plugin should be working correctly.\n";
?>
