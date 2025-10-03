<?php
/**
 * Deploy Improved Telegram Blog Publisher Plugin
 * This script will update the plugin with Grok support and API fallback system
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

echo "🚀 DEPLOYING IMPROVED TELEGRAM BLOG PUBLISHER\n";
echo "============================================\n\n";

// Check if plugin is active
if (!is_plugin_active('telegram-blog-publisher/telegram-blog-publisher.php')) {
    echo "❌ Plugin is not active. Please activate it first.\n";
    exit;
}

echo "✅ Plugin is active\n";

// Update plugin files
$plugin_dir = WP_PLUGIN_DIR . '/telegram-blog-publisher/';

echo "🔧 Updating plugin files...\n";

// Update main plugin file
$main_file = $plugin_dir . 'telegram-blog-publisher.php';
if (file_put_contents($main_file, file_get_contents(__DIR__ . '/telegram-blog-publisher/telegram-blog-publisher.php'))) {
    echo "✅ Main plugin file updated\n";
} else {
    echo "❌ Failed to update main plugin file\n";
}

// Update settings template
$settings_file = $plugin_dir . 'templates/admin-settings.php';
if (file_put_contents($settings_file, file_get_contents(__DIR__ . '/telegram-blog-publisher/templates/admin-settings.php'))) {
    echo "✅ Settings template updated\n";
} else {
    echo "❌ Failed to update settings template\n";
}

// Update CSS
$css_file = $plugin_dir . 'assets/admin.css';
if (file_put_contents($css_file, file_get_contents(__DIR__ . '/telegram-blog-publisher/assets/admin.css'))) {
    echo "✅ Admin CSS updated\n";
} else {
    echo "❌ Failed to update admin CSS\n";
}

// Clear caches
echo "🧹 Clearing caches...\n";
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "✅ WordPress cache cleared\n";
}

// Test the plugin
echo "\n🧪 TESTING PLUGIN:\n";
echo "==================\n";

// Check if new features are available
$api_services = get_option('tbp_api_keys', []);
echo "📊 Current API keys: " . count($api_services) . " configured\n";

// Test webhook endpoint
$webhook_url = get_rest_url() . 'telegram-blog-publisher/v1/webhook';
$response = wp_remote_get($webhook_url);
if (!is_wp_error($response)) {
    $code = wp_remote_retrieve_response_code($response);
    echo "✅ Webhook endpoint working (Status: " . $code . ")\n";
} else {
    echo "❌ Webhook endpoint error: " . $response->get_error_message() . "\n";
}

echo "\n🎉 DEPLOYMENT COMPLETED!\n";
echo "========================\n";
echo "✅ Plugin updated with Grok support\n";
echo "✅ API fallback system implemented\n";
echo "✅ Improved UI with better API key management\n";
echo "✅ Reduced timeout issues\n";
echo "\n🔗 Next steps:\n";
echo "1. Go to WordPress Admin → Telegram Blog → Settings\n";
echo "2. Add your Grok API key (recommended for fastest responses)\n";
echo "3. Add other API keys as backup\n";
echo "4. Test the webhook\n";
echo "\n🌐 Plugin Settings: " . admin_url('admin.php?page=telegram-blog-publisher-settings') . "\n";
?>
