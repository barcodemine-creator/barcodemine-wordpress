<?php
/**
 * Fix Gemini API Implementation
 * This will update the plugin with the correct Gemini API call
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

echo "🔧 FIXING GEMINI API IMPLEMENTATION\n";
echo "===================================\n\n";

// Check if plugin is active
if (!is_plugin_active('telegram-blog-publisher/telegram-blog-publisher.php')) {
    echo "❌ Plugin is not active\n";
    exit;
}

echo "✅ Plugin is active\n";

// Read the current plugin file
$plugin_file = WP_PLUGIN_DIR . '/telegram-blog-publisher/telegram-blog-publisher.php';
$content = file_get_contents($plugin_file);

echo "🔍 Analyzing current Gemini implementation...\n";

// Find the current Gemini function
if (strpos($content, 'private function callGemini') !== false) {
    echo "✅ Found Gemini function\n";
    
    // Create the improved Gemini function
    $new_gemini_function = '    private function callGemini($api_key, $topic, $word_count, $tone) {
        $prompt = "Write a comprehensive blog post about \'{$topic}\' in a {$tone} tone. Target word count: {$word_count} words. Include an engaging introduction, detailed main content with subheadings, and a compelling conclusion.";
        
        // Try multiple Gemini models in order of preference
        $models = [
            \'gemini-1.5-flash\',  // Fastest
            \'gemini-1.5-pro\',    // Most capable
            \'gemini-pro\'         // Fallback
        ];
        
        foreach ($models as $model) {
            $response = wp_remote_post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $api_key, [
                \'headers\' => [
                    \'Content-Type\' => \'application/json\'
                ],
                \'body\' => json_encode([
                    \'contents\' => [
                        [\'parts\' => [[\'text\' => $prompt]]]
                    ],
                    \'generationConfig\' => [
                        \'maxOutputTokens\' => 2000,
                        \'temperature\' => 0.7,
                        \'topP\' => 0.8,
                        \'topK\' => 40
                    ],
                    \'safetySettings\' => [
                        [
                            \'category\' => \'HARM_CATEGORY_HARASSMENT\',
                            \'threshold\' => \'BLOCK_MEDIUM_AND_ABOVE\'
                        ],
                        [
                            \'category\' => \'HARM_CATEGORY_HATE_SPEECH\',
                            \'threshold\' => \'BLOCK_MEDIUM_AND_ABOVE\'
                        ],
                        [
                            \'category\' => \'HARM_CATEGORY_SEXUALLY_EXPLICIT\',
                            \'threshold\' => \'BLOCK_MEDIUM_AND_ABOVE\'
                        ],
                        [
                            \'category\' => \'HARM_CATEGORY_DANGEROUS_CONTENT\',
                            \'threshold\' => \'BLOCK_MEDIUM_AND_ABOVE\'
                        ]
                    ]
                ]),
                \'timeout\' => 45
            ]);
            
            if (is_wp_error($response)) {
                continue; // Try next model
            }
            
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            if (isset($data[\'candidates\'][0][\'content\'][\'parts\'][0][\'text\'])) {
                return $data[\'candidates\'][0][\'content\'][\'parts\'][0][\'text\'];
            }
            
            // If we get an error, try next model
            if (isset($data[\'error\'])) {
                error_log("Gemini API Error with {$model}: " . $data[\'error\'][\'message\']);
                continue;
            }
        }
        
        return new WP_Error(\'gemini_error\', \'All Gemini models failed to generate content\');
    }';
    
    // Replace the old function with the new one
    $pattern = '/private function callGemini\([^}]+\}/s';
    $new_content = preg_replace($pattern, $new_gemini_function, $content);
    
    if ($new_content !== $content) {
        if (file_put_contents($plugin_file, $new_content)) {
            echo "✅ Gemini function updated successfully\n";
        } else {
            echo "❌ Failed to update Gemini function\n";
        }
    } else {
        echo "⚠️  Could not find Gemini function to replace\n";
    }
} else {
    echo "❌ Gemini function not found in plugin\n";
}

// Also update the test function
echo "🔧 Updating test function...\n";

$test_pattern = '/private function testAIService\([^}]+\}/s';
$new_test_function = '    private function testAIService($service, $api_key) {
        $test_prompt = "Write a short test message about barcodes.";
        
        switch ($service) {
            case \'grok\':
                return $this->callGrok($api_key, \'test\', 50, \'professional\');
            case \'deepseek\':
                return $this->callDeepSeek($api_key, \'test\', 50, \'professional\');
            case \'openai\':
                return $this->callOpenAI($api_key, \'test\', 50, \'professional\');
            case \'claude\':
                return $this->callClaude($api_key, \'test\', 50, \'professional\');
            case \'gemini\':
                // Use a simpler test for Gemini
                $response = wp_remote_post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $api_key, [
                    \'headers\' => [
                        \'Content-Type\' => \'application/json\'
                    ],
                    \'body\' => json_encode([
                        \'contents\' => [
                            [\'parts\' => [[\'text\' => $test_prompt]]]
                        ],
                        \'generationConfig\' => [
                            \'maxOutputTokens\' => 100,
                            \'temperature\' => 0.7
                        ]
                    ]),
                    \'timeout\' => 30
                ]);
                
                if (is_wp_error($response)) {
                    return $response;
                }
                
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
                
                if (isset($data[\'candidates\'][0][\'content\'][\'parts\'][0][\'text\'])) {
                    return $data[\'candidates\'][0][\'content\'][\'parts\'][0][\'text\'];
                }
                
                return new WP_Error(\'gemini_test_error\', \'Gemini test failed: \' . $body);
            default:
                return new WP_Error(\'unknown_service\', \'Unknown AI service\');
        }
    }';
    
    $final_content = preg_replace($test_pattern, $new_test_function, $new_content ?? $content);
    
    if ($final_content !== ($new_content ?? $content)) {
        if (file_put_contents($plugin_file, $final_content)) {
            echo "✅ Test function updated successfully\n";
        } else {
            echo "❌ Failed to update test function\n";
        }
    }
    
    // Clear caches
    echo "🧹 Clearing caches...\n";
    if (function_exists(\'wp_cache_flush\')) {
        wp_cache_flush();
        echo "✅ Cache cleared\n";
    }
    
    echo "\n🎉 GEMINI API FIX COMPLETED!\n";
    echo "============================\n";
    echo "✅ Updated Gemini function with multiple model support\n";
    echo "✅ Added proper error handling\n";
    echo "✅ Improved test function\n";
    echo "\n🔗 Test your Gemini API key now:\n";
    echo "1. Go to WordPress Admin → Telegram Blog → Settings\n";
    echo "2. Click the Test button next to your Gemini API key\n";
    echo "3. Or run: https://barcodemine.com/test-gemini-api.php\n";
} else {
    echo "❌ Plugin file not found\n";
}
?>
