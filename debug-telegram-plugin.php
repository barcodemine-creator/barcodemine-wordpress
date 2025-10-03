<?php
/**
 * Debug Telegram Blog Publisher Plugin
 * Upload this to your WordPress root and run it
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

echo "🔍 DEBUGGING TELEGRAM BLOG PUBLISHER\n";
echo "===================================\n\n";

// Check if plugin is active
if (!is_plugin_active('telegram-blog-publisher/telegram-blog-publisher.php')) {
    echo "❌ Plugin is not active\n";
    exit;
}

echo "✅ Plugin is active\n";

// Check current settings
echo "\n📊 CURRENT SETTINGS:\n";
echo "===================\n";
echo "AI Service: " . get_option('tbp_ai_service', 'not set') . "\n";
echo "Webhook Secret: " . (get_option('tbp_webhook_secret', '') ? 'Set' : 'Not set') . "\n";
echo "AI API Key: " . (get_option('tbp_ai_api_key', '') ? 'Set' : 'Not set') . "\n";

// Test webhook endpoint
echo "\n🔧 TESTING WEBHOOK ENDPOINT:\n";
echo "============================\n";

$webhook_url = get_rest_url() . 'telegram-blog-publisher/v1/webhook';
$webhook_secret = get_option('tbp_webhook_secret', '');

echo "Webhook URL: " . $webhook_url . "\n";
echo "Webhook Secret: " . substr($webhook_secret, 0, 8) . "...\n";

// Test the webhook
$test_data = array(
    'topic' => 'Debug Test',
    'details' => 'This is a debug test to check if the webhook is working.',
    'category' => 'Test',
    'tags' => 'debug, test',
    'status' => 'draft',
);

echo "\n🧪 SENDING TEST REQUEST:\n";
echo "========================\n";

$response = wp_remote_post($webhook_url, array(
    'headers' => array(
        'Content-Type' => 'application/json',
        'X-Webhook-Secret' => $webhook_secret,
    ),
    'body' => json_encode($test_data),
    'timeout' => 30,
));

if (is_wp_error($response)) {
    echo "❌ HTTP Error: " . $response->get_error_message() . "\n";
} else {
    $response_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    
    echo "Response Code: " . $response_code . "\n";
    echo "Response Body: " . $body . "\n";
    
    if ($response_code === 200) {
        $data = json_decode($body, true);
        if (isset($data['success']) && $data['success']) {
            echo "✅ Webhook test successful!\n";
            echo "Post ID: " . $data['post_id'] . "\n";
            echo "Post URL: " . $data['post_url'] . "\n";
        } else {
            echo "❌ Webhook returned error: " . (isset($data['message']) ? $data['message'] : 'Unknown error') . "\n";
        }
    } else {
        echo "❌ Webhook failed with HTTP " . $response_code . "\n";
    }
}

// Check AI service configuration
echo "\n🤖 AI SERVICE CONFIGURATION:\n";
echo "============================\n";

$ai_service = get_option('tbp_ai_service', 'openai');
$ai_api_key = get_option('tbp_ai_api_key', '');

echo "AI Service: " . $ai_service . "\n";
echo "API Key: " . (empty($ai_api_key) ? 'Not set' : 'Set (' . substr($ai_api_key, 0, 8) . '...)') . "\n";

if ($ai_service === 'deepseek') {
    echo "✅ DeepSeek is configured\n";
} else {
    echo "⚠️  AI Service is set to: " . $ai_service . " (should be 'deepseek')\n";
}

if (empty($ai_api_key)) {
    echo "❌ API Key is not set\n";
} else {
    echo "✅ API Key is set\n";
}

echo "\n🎯 RECOMMENDATIONS:\n";
echo "===================\n";

if ($ai_service !== 'deepseek') {
    echo "1. Change AI Service to 'DeepSeek (Chat)' in WordPress Admin\n";
}

if (empty($ai_api_key)) {
    echo "2. Set your DeepSeek API key in WordPress Admin\n";
}

echo "3. Test the webhook again\n";
echo "4. Check n8n workflow execution\n";

echo "\n🔗 LINKS:\n";
echo "=========\n";
echo "WordPress Admin: " . admin_url('admin.php?page=telegram-blog-publisher-settings') . "\n";
echo "DeepSeek API Keys: https://platform.deepseek.com/api_keys\n";
?>
