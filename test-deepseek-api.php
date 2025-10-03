<?php
/**
 * Test DeepSeek API Key
 * Run this script to test your DeepSeek API key directly
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

echo "🧪 TESTING DEEPSEEK API KEY\n";
echo "==========================\n\n";

// Get API key from settings
$api_key = get_option('tbp_ai_api_key', '');
$api_keys = get_option('tbp_api_keys', array());
$deepseek_key = isset($api_keys['deepseek']) ? $api_keys['deepseek'] : '';

echo "📊 API KEY STATUS:\n";
echo "==================\n";
echo "Main API Key: " . (empty($api_key) ? 'Not set' : 'Set (' . substr($api_key, 0, 8) . '...)') . "\n";
echo "DeepSeek Key: " . (empty($deepseek_key) ? 'Not set' : 'Set (' . substr($deepseek_key, 0, 8) . '...)') . "\n";

$test_key = !empty($deepseek_key) ? $deepseek_key : $api_key;

if (empty($test_key)) {
    echo "\n❌ No API key found. Please set your DeepSeek API key in the plugin settings.\n";
    exit;
}

echo "\n🔧 TESTING DEEPSEEK API:\n";
echo "========================\n";

// Test DeepSeek API directly
$response = wp_remote_post('https://api.deepseek.com/v1/chat/completions', array(
    'headers' => array(
        'Authorization' => 'Bearer ' . $test_key,
        'Content-Type' => 'application/json',
    ),
    'body' => json_encode(array(
        'model' => 'deepseek-chat',
        'messages' => array(
            array(
                'role' => 'user',
                'content' => 'Hello, this is a test. Please respond with "API key is working correctly."'
            )
        ),
        'max_tokens' => 50,
        'temperature' => 0.7,
    )),
    'timeout' => 30,
));

if (is_wp_error($response)) {
    echo "❌ HTTP Error: " . $response->get_error_message() . "\n";
} else {
    $response_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    echo "Response Code: " . $response_code . "\n";
    echo "Response Body: " . $body . "\n\n";
    
    if ($response_code === 200) {
        if (isset($data['choices'][0]['message']['content'])) {
            echo "✅ SUCCESS! DeepSeek API is working correctly!\n";
            echo "Response: " . $data['choices'][0]['message']['content'] . "\n";
        } else {
            echo "❌ Unexpected response format\n";
        }
    } else {
        echo "❌ API Error (HTTP " . $response_code . ")\n";
        if (isset($data['error']['message'])) {
            echo "Error Message: " . $data['error']['message'] . "\n";
        }
    }
}

echo "\n🎯 RECOMMENDATIONS:\n";
echo "===================\n";

if ($response_code === 401) {
    echo "1. Check if your API key is correct\n";
    echo "2. Make sure the key starts with 'sk-'\n";
    echo "3. Verify there are no extra spaces\n";
} elseif ($response_code === 429) {
    echo "1. You may have hit rate limits\n";
    echo "2. Wait a few minutes and try again\n";
} elseif ($response_code === 402) {
    echo "1. Your account may be out of credits\n";
    echo "2. Add credits to your DeepSeek account\n";
} elseif ($response_code === 200) {
    echo "1. Your API key is working correctly!\n";
    echo "2. The issue might be in the plugin configuration\n";
    echo "3. Try testing the webhook again\n";
} else {
    echo "1. Check your internet connection\n";
    echo "2. Verify the API key format\n";
    echo "3. Check DeepSeek service status\n";
}

echo "\n🔗 LINKS:\n";
echo "=========\n";
echo "DeepSeek Platform: https://platform.deepseek.com/api_keys\n";
echo "WordPress Settings: " . admin_url('admin.php?page=telegram-blog-publisher-settings') . "\n";
?>
