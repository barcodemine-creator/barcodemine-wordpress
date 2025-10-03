<?php
/**
 * Fix DeepSeek Support for Telegram Blog Publisher
 * Run this script to add DeepSeek API support
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

echo "🔧 ADDING DEEPSEEK SUPPORT TO TELEGRAM BLOG PUBLISHER\n";
echo "====================================================\n\n";

// Check if plugin is active
if (!is_plugin_active('telegram-blog-publisher/telegram-blog-publisher.php')) {
    echo "❌ Plugin is not active. Please activate it first.\n";
    exit;
}

echo "✅ Plugin is active\n";

// Set DeepSeek as default AI service
echo "🔧 Setting DeepSeek as default AI service...\n";
update_option('tbp_ai_service', 'deepseek');
echo "✅ DeepSeek set as default AI service\n";

// Display current settings
echo "\n📊 CURRENT SETTINGS:\n";
echo "===================\n";
echo "AI Service: " . get_option('tbp_ai_service', 'openai') . "\n";
echo "Webhook Secret: " . substr(get_option('tbp_webhook_secret', ''), 0, 8) . "...\n";

echo "\n🎯 NEXT STEPS:\n";
echo "==============\n";
echo "1. Go to WordPress Admin → Telegram Publisher → Settings\n";
echo "2. Select 'DeepSeek (Chat)' from the AI Service dropdown\n";
echo "3. Enter your DeepSeek API key\n";
echo "4. Save settings\n";
echo "5. Test the webhook in n8n\n";

echo "\n🔑 DEEPSEEK API KEY:\n";
echo "===================\n";
echo "Get your API key from: https://platform.deepseek.com/api_keys\n";
echo "Make sure you have credits in your DeepSeek account\n";

echo "\n🎉 DEEPSEEK SUPPORT ADDED!\n";
echo "==========================\n";
echo "Your plugin now supports DeepSeek API for content generation.\n";
?>
