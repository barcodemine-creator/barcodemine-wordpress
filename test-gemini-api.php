<?php
/**
 * Test Gemini API Key - Direct Testing
 * This will help us identify why Gemini isn't working
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

echo "🔍 TESTING GEMINI API KEY\n";
echo "========================\n\n";

// Get your Gemini API key
$gemini_key = get_option('tbp_api_keys', [])['gemini'] ?? '';

if (empty($gemini_key)) {
    echo "❌ No Gemini API key found in plugin settings\n";
    echo "Please add your Gemini API key in WordPress Admin → Telegram Blog → Settings\n";
    exit;
}

echo "✅ Found Gemini API key: " . substr($gemini_key, 0, 10) . "...\n\n";

// Test 1: Current plugin implementation
echo "🧪 TEST 1: Current Plugin Implementation\n";
echo "========================================\n";

$prompt = "Write a short test message about barcodes.";
$response = wp_remote_post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $gemini_key, [
    'headers' => [
        'Content-Type' => 'application/json'
    ],
    'body' => json_encode([
        'contents' => [
            ['parts' => [['text' => $prompt]]]
        ],
        'generationConfig' => [
            'maxOutputTokens' => 2000,
            'temperature' => 0.7
        ]
    ]),
    'timeout' => 60
]);

if (is_wp_error($response)) {
    echo "❌ Error: " . $response->get_error_message() . "\n";
} else {
    $code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    
    echo "📊 Response Code: " . $code . "\n";
    echo "📄 Response Body: " . substr($body, 0, 500) . "...\n\n";
    
    if ($code === 200 && isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        echo "✅ SUCCESS! Generated content:\n";
        echo $data['candidates'][0]['content']['parts'][0]['text'] . "\n\n";
    } else {
        echo "❌ FAILED! Error details:\n";
        if (isset($data['error'])) {
            echo "Error: " . $data['error']['message'] . "\n";
        } else {
            echo "Unexpected response format\n";
        }
    }
}

// Test 2: Alternative Gemini endpoint
echo "🧪 TEST 2: Alternative Gemini Endpoint\n";
echo "=====================================\n";

$response2 = wp_remote_post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $gemini_key, [
    'headers' => [
        'Content-Type' => 'application/json'
    ],
    'body' => json_encode([
        'contents' => [
            ['parts' => [['text' => $prompt]]]
        ],
        'generationConfig' => [
            'maxOutputTokens' => 2000,
            'temperature' => 0.7
        ]
    ]),
    'timeout' => 60
]);

if (is_wp_error($response2)) {
    echo "❌ Error: " . $response2->get_error_message() . "\n";
} else {
    $code2 = wp_remote_retrieve_response_code($response2);
    $body2 = wp_remote_retrieve_body($response2);
    $data2 = json_decode($body2, true);
    
    echo "📊 Response Code: " . $code2 . "\n";
    echo "📄 Response Body: " . substr($body2, 0, 500) . "...\n\n";
    
    if ($code2 === 200 && isset($data2['candidates'][0]['content']['parts'][0]['text'])) {
        echo "✅ SUCCESS with alternative endpoint!\n";
        echo $data2['candidates'][0]['content']['parts'][0]['text'] . "\n\n";
    } else {
        echo "❌ FAILED with alternative endpoint\n";
        if (isset($data2['error'])) {
            echo "Error: " . $data2['error']['message'] . "\n";
        }
    }
}

// Test 3: Check API key validity
echo "🧪 TEST 3: API Key Validation\n";
echo "============================\n";

$validation_response = wp_remote_get('https://generativelanguage.googleapis.com/v1beta/models?key=' . $gemini_key, [
    'timeout' => 30
]);

if (is_wp_error($validation_response)) {
    echo "❌ Error validating API key: " . $validation_response->get_error_message() . "\n";
} else {
    $val_code = wp_remote_retrieve_response_code($validation_response);
    $val_body = wp_remote_retrieve_body($validation_response);
    
    echo "📊 Validation Response Code: " . $val_code . "\n";
    
    if ($val_code === 200) {
        echo "✅ API key is valid!\n";
        $val_data = json_decode($val_body, true);
        if (isset($val_data['models'])) {
            echo "📋 Available models: " . count($val_data['models']) . "\n";
            foreach ($val_data['models'] as $model) {
                echo "  - " . $model['name'] . "\n";
            }
        }
    } else {
        echo "❌ API key validation failed\n";
        echo "Response: " . substr($val_body, 0, 500) . "\n";
    }
}

echo "\n🎯 DIAGNOSIS COMPLETE\n";
echo "====================\n";
echo "Check the results above to see what's wrong with your Gemini API key.\n";
echo "Common issues:\n";
echo "1. Wrong API key format\n";
echo "2. API key doesn't have proper permissions\n";
echo "3. Billing not set up on Google Cloud\n";
echo "4. Wrong model name in the request\n";
echo "5. Rate limiting or quota exceeded\n";
?>
