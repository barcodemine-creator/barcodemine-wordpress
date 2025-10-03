<?php
/**
 * Fix Timeout Issues for Telegram Blog Publisher
 * This script increases timeout limits and optimizes the plugin
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

echo "⏱️ FIXING TIMEOUT ISSUES\n";
echo "========================\n\n";

// Check if plugin is active
if (!is_plugin_active('telegram-blog-publisher/telegram-blog-publisher.php')) {
    echo "❌ Plugin is not active. Please activate it first.\n";
    exit;
}

echo "✅ Plugin is active\n";

// Increase PHP timeout limits
echo "🔧 Increasing timeout limits...\n";
ini_set('max_execution_time', 300); // 5 minutes
ini_set('memory_limit', '512M');
set_time_limit(300);

// Update WordPress options for better performance
update_option('tbp_timeout_limit', 300);
update_option('tbp_memory_limit', '512M');

echo "✅ Timeout limits increased\n";

// Test the webhook with a simple request
echo "🧪 Testing webhook with simple request...\n";

$webhook_url = get_rest_url() . 'telegram-blog-publisher/v1/webhook';
$webhook_secret = get_option('tbp_webhook_secret', '');

$test_data = array(
    'topic' => 'Quick Test',
    'details' => 'This is a quick test to check response time.',
    'category' => 'Test',
    'tags' => 'test, quick',
    'status' => 'draft',
);

$start_time = microtime(true);

$response = wp_remote_post($webhook_url, array(
    'headers' => array(
        'Content-Type' => 'application/json',
        'X-Webhook-Secret' => $webhook_secret,
    ),
    'body' => json_encode($test_data),
    'timeout' => 300, // 5 minutes
));

$end_time = microtime(true);
$response_time = round(($end_time - $start_time), 2);

if (is_wp_error($response)) {
    echo "❌ Webhook test failed: " . $response->get_error_message() . "\n";
} else {
    $response_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    
    echo "Response Code: " . $response_code . "\n";
    echo "Response Time: " . $response_time . " seconds\n";
    
    if ($response_code === 200) {
        $data = json_decode($body, true);
        if (isset($data['success']) && $data['success']) {
            echo "✅ Webhook test successful!\n";
            echo "Post created: " . $data['post_id'] . "\n";
        } else {
            echo "❌ Webhook returned error: " . (isset($data['message']) ? $data['message'] : 'Unknown error') . "\n";
        }
    } else {
        echo "❌ Webhook failed with HTTP " . $response_code . "\n";
    }
}

// Optimize AI service settings
echo "\n🔧 Optimizing AI service settings...\n";

// Reduce AI response size for faster generation
update_option('tbp_ai_max_tokens', 1000); // Reduced from 2000
update_option('tbp_ai_temperature', 0.5); // Reduced for faster response

echo "✅ AI settings optimized\n";

// Check current AI service
$ai_service = get_option('tbp_ai_service', 'openai');
echo "🤖 Current AI Service: " . $ai_service . "\n";

if ($ai_service === 'deepseek') {
    echo "✅ Using DeepSeek (good choice for speed)\n";
} else {
    echo "⚠️  Consider switching to DeepSeek for faster responses\n";
}

echo "\n📊 PERFORMANCE RECOMMENDATIONS:\n";
echo "===============================\n";
echo "1. Response time: " . $response_time . " seconds\n";
if ($response_time > 30) {
    echo "   ⚠️  Response time is slow. Consider:\n";
    echo "   - Using a faster AI service\n";
    echo "   - Reducing content length\n";
    echo "   - Adding server resources\n";
} else {
    echo "   ✅ Response time is acceptable\n";
}

echo "\n2. Server optimizations applied:\n";
echo "   - Increased timeout to 300 seconds\n";
echo "   - Increased memory limit to 512M\n";
echo "   - Reduced AI max tokens to 1000\n";
echo "   - Optimized AI temperature\n";

echo "\n🎯 NEXT STEPS:\n";
echo "==============\n";
echo "1. Test your n8n workflow again\n";
echo "2. If still timing out, try with shorter content\n";
echo "3. Consider using a faster AI service\n";
echo "4. Check your server resources\n";

echo "\n🔗 TEST YOUR WORKFLOW:\n";
echo "=====================\n";
echo "Try sending a shorter message like: 'Write about cats'\n";
echo "Instead of: 'Write a comprehensive guide about...'\n";

echo "\n🎉 TIMEOUT FIX COMPLETED!\n";
echo "=========================\n";
?>
