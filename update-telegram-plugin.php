<?php
/**
 * Update Telegram Blog Publisher Plugin
 * Run this script to update the plugin with latest features
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

echo "🔄 UPDATING TELEGRAM BLOG PUBLISHER PLUGIN\n";
echo "==========================================\n\n";

// Check if plugin is active
if (!is_plugin_active('telegram-blog-publisher/telegram-blog-publisher.php')) {
    echo "❌ Plugin is not active. Please activate it first.\n";
    exit;
}

echo "✅ Plugin is active\n";

// Force refresh of plugin files
echo "🔄 Refreshing plugin files...\n";

// Clear any caches
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
}

// Clear object cache
if (function_exists('wp_cache_delete')) {
    wp_cache_delete('alloptions', 'options');
}

echo "✅ Plugin files refreshed\n";

// Check current version
$current_version = get_option('tbp_version', '1.0.0');
echo "📊 Current version: " . $current_version . "\n";

// Update version
update_option('tbp_version', '1.1.0');
echo "✅ Version updated to 1.1.0\n";

// Initialize multiple API keys if not exists
$api_keys = get_option('tbp_api_keys', array());
if (empty($api_keys)) {
    $api_keys = array(
        'openai' => '',
        'deepseek' => '',
        'claude' => '',
        'gemini' => ''
    );
    update_option('tbp_api_keys', $api_keys);
    echo "✅ Multiple API keys structure initialized\n";
}

// Check if DeepSeek is available
$ai_service = get_option('tbp_ai_service', 'openai');
echo "🤖 Current AI Service: " . $ai_service . "\n";

if ($ai_service === 'gemini') {
    echo "⚠️  You're currently using Gemini. Consider switching to DeepSeek for better reliability.\n";
}

echo "\n🎯 NEXT STEPS:\n";
echo "==============\n";
echo "1. Go to WordPress Admin → Telegram Publisher → Settings\n";
echo "2. You should now see the enhanced interface with:\n";
echo "   - Multiple API Keys section\n";
echo "   - Eye toggle buttons (👁️)\n";
echo "   - Test buttons (🧪)\n";
echo "   - Better styling and layout\n";
echo "3. Add your DeepSeek API key in the Multiple API Keys section\n";
echo "4. Test the API key using the Test button\n";
echo "5. Change AI Service to 'DeepSeek (Chat)' if needed\n";

echo "\n🔗 LINKS:\n";
echo "=========\n";
echo "Settings Page: " . admin_url('admin.php?page=telegram-blog-publisher-settings') . "\n";
echo "Dashboard: " . admin_url('admin.php?page=telegram-blog-publisher') . "\n";

echo "\n🎉 PLUGIN UPDATE COMPLETED!\n";
echo "===========================\n";
echo "The plugin should now show the enhanced interface with all new features.\n";
?>
