<?php
/**
 * Fix Gemini Models - Update to Latest Model Names
 * This will fix the Gemini API by using the correct model names
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

echo "🔧 FIXING GEMINI MODELS\n";
echo "======================\n\n";

// Check if plugin is active
if (!is_plugin_active('telegram-blog-publisher/telegram-blog-publisher.php')) {
    echo "❌ Plugin is not active\n";
    exit;
}

echo "✅ Plugin is active\n";

// Read the current plugin file
$plugin_file = WP_PLUGIN_DIR . '/telegram-blog-publisher/telegram-blog-publisher.php';
$content = file_get_contents($plugin_file);

echo "🔍 Updating Gemini models to latest versions...\n";

// Update the callGemini function with correct models
$new_gemini_function = '    private function callGemini($api_key, $topic, $word_count, $tone) {
        $prompt = "Write a comprehensive blog post about \'{$topic}\' in a {$tone} tone. Target word count: {$word_count} words. Include an engaging introduction, detailed main content with subheadings, and a compelling conclusion.";
        
        // Use the latest Gemini models (in order of preference)
        $models = [
            \'gemini-2.5-flash\',        // Fastest and most efficient
            \'gemini-flash-latest\',     // Always latest flash model
            \'gemini-2.5-pro\',          // Most capable
            \'gemini-pro-latest\'        // Always latest pro model
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
                error_log("Gemini API Error with {$model}: " . $response->get_error_message());
                continue; // Try next model
            }
            
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            if (isset($data[\'candidates\'][0][\'content\'][\'parts\'][0][\'text\'])) {
                error_log("Gemini API Success with model: {$model}");
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

// Replace the old function
$pattern = '/private function callGemini\([^}]+\}/s';
$new_content = preg_replace($pattern, $new_gemini_function, $content);

if ($new_content !== $content) {
    if (file_put_contents($plugin_file, $new_content)) {
        echo "✅ Gemini function updated with latest models\n";
    } else {
        echo "❌ Failed to update Gemini function\n";
        exit;
    }
} else {
    echo "❌ Could not find Gemini function to replace\n";
    exit;
}

// Also update the test function
echo "🔧 Updating test function...\n";

$test_pattern = '/case \'gemini\':[^}]+}/s';
$new_gemini_test = 'case \'gemini\':
                // Test with the latest Gemini model
                $response = wp_remote_post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $api_key, [
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
                
                return new WP_Error(\'gemini_test_error\', \'Gemini test failed: \' . $body);';

$final_content = preg_replace($test_pattern, $new_gemini_test, $new_content);

if ($final_content !== $new_content) {
    if (file_put_contents($plugin_file, $final_content)) {
        echo "✅ Test function updated with latest model\n";
    } else {
        echo "❌ Failed to update test function\n";
    }
}

// Clear caches
echo "🧹 Clearing caches...\n";
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "✅ Cache cleared\n";
}

echo "\n🎉 GEMINI MODELS FIXED!\n";
echo "======================\n";
echo "✅ Updated to use latest Gemini models:\n";
echo "  - gemini-2.5-flash (fastest)\n";
echo "  - gemini-flash-latest (always latest)\n";
echo "  - gemini-2.5-pro (most capable)\n";
echo "  - gemini-pro-latest (always latest)\n";
echo "\n🧪 Test your Gemini API key now:\n";
echo "1. Go to WordPress Admin → Telegram Blog → Settings\n";
echo "2. Click the Test button next to your Gemini API key\n";
echo "3. It should work now! 🚀\n";
?>
