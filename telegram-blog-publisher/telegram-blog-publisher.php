<?php
/**
 * Plugin Name: Telegram Blog Publisher
 * Plugin URI: https://github.com/barcodemine-creator/barcodemine-wordpress
 * Description: Publish blog posts from Telegram via n8n webhooks with AI content generation
 * Version: 2.0.3
 * Author: Barcodemine
 * License: GPL v2 or later
 * Text Domain: telegram-blog-publisher
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TBP_VERSION', '2.0.3');
define('TBP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TBP_PLUGIN_PATH', plugin_dir_path(__FILE__));

class TelegramBlogPublisher {
    
    private $api_keys = [];
    private $api_services = [
        'openai' => 'OpenAI GPT-4',
        'deepseek' => 'DeepSeek Chat',
        'claude' => 'Claude 3.5 Sonnet',
        'gemini' => 'Gemini Pro',
        'grok' => 'Grok (X.AI)'
    ];
    
    public function __construct() {
        add_action('init', [$this, 'init']);
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('wp_ajax_tbp_save_settings', [$this, 'saveSettings']);
        add_action('wp_ajax_tbp_test_webhook', [$this, 'testWebhook']);
        add_action('wp_ajax_tbp_test_api_key', [$this, 'testApiKey']);
        add_action('wp_ajax_tbp_reactivate_license', [$this, 'reactivateLicense']);
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
        
        // Load API keys
        $this->loadApiKeys();
    }
    
    public function init() {
        // Plugin initialization
    }
    
    private function loadApiKeys() {
        $this->api_keys = get_option('tbp_api_keys', []);
    }
    
    public function addAdminMenu() {
        add_menu_page(
            'Telegram Blog Publisher',
            'Telegram Blog',
            'manage_options',
            'telegram-blog-publisher',
            [$this, 'renderDashboard'],
            'dashicons-format-chat',
            30
        );
        
        add_submenu_page(
            'telegram-blog-publisher',
            'Settings',
            'Settings',
            'manage_options',
            'telegram-blog-publisher-settings',
            [$this, 'renderSettings']
        );
        
        add_submenu_page(
            'telegram-blog-publisher',
            'Activity Logs',
            'Activity Logs',
            'manage_options',
            'telegram-blog-publisher-logs',
            [$this, 'renderLogs']
        );
    }
    
    public function enqueueAdminScripts($hook) {
        if (strpos($hook, 'telegram-blog-publisher') === false) {
            return;
        }
        
        wp_enqueue_style('tbp-admin-css', TBP_PLUGIN_URL . 'assets/admin.css', [], TBP_VERSION);
        wp_enqueue_script('tbp-admin-js', TBP_PLUGIN_URL . 'assets/admin.js', ['jquery'], TBP_VERSION, true);
        
        wp_localize_script('tbp-admin-js', 'tbp_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tbp_nonce'),
            'webhook_url' => get_rest_url() . 'telegram-blog-publisher/v1/webhook'
        ]);
    }
    
    public function registerRestRoutes() {
        register_rest_route('telegram-blog-publisher/v1', '/webhook', [
            'methods' => 'POST',
            'callback' => [$this, 'handleWebhook'],
            'permission_callback' => [$this, 'verifyWebhook']
        ]);
    }
    
    public function verifyWebhook($request) {
        $webhook_secret = get_option('tbp_webhook_secret', '');
        if (empty($webhook_secret)) {
            return false;
        }
        
        $headers = $request->get_headers();
        $received_secret = '';
        
        if (isset($headers['x_webhook_secret'])) {
            $received_secret = $headers['x_webhook_secret'][0];
        } elseif (isset($headers['x-webhook-secret'])) {
            $received_secret = $headers['x-webhook-secret'][0];
        } elseif (isset($headers['http_x_webhook_secret'])) {
            $received_secret = $headers['http_x_webhook_secret'][0];
        }
        
        return hash_equals($webhook_secret, $received_secret);
    }
    
    public function handleWebhook($request) {
        // Increase execution time and memory
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        
        $data = $request->get_json_params();
        
        if (empty($data['topic'])) {
            return new WP_Error('missing_topic', 'Topic is required', ['status' => 400]);
        }
        
        // Generate blog content using AI with fallback
        $content = $this->generateBlogContentWithFallback($data);
        
        if (is_wp_error($content)) {
            return $content;
        }
        
        // Create blog post
        $post_data = [
            'post_title' => $data['title'] ?? $data['topic'],
            'post_content' => $content,
            'post_status' => $data['status'] ?? 'publish',
            'post_type' => 'post',
            'post_author' => get_current_user_id()
        ];
        
        if (isset($data['category'])) {
            $post_data['post_category'] = [$data['category']];
        }
        
        if (isset($data['tags'])) {
            $post_data['tags_input'] = $data['tags'];
        }
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            return new WP_Error('post_creation_failed', 'Failed to create post', ['status' => 500]);
        }
        
        // Log the activity
        $this->logActivity('post_created', [
            'post_id' => $post_id,
            'title' => $post_data['post_title'],
            'topic' => $data['topic']
        ]);
        
        return [
            'success' => true,
            'post_id' => $post_id,
            'post_url' => get_permalink($post_id),
            'message' => 'Blog post created successfully'
        ];
    }
    
    private function generateBlogContentWithFallback($data) {
        $topic = $data['topic'];
        $word_count = $data['word_count'] ?? 500;
        $tone = $data['tone'] ?? 'professional';
        
        // Try APIs in order of preference (fastest first)
        $api_order = ['grok', 'deepseek', 'openai', 'claude', 'gemini'];
        
        foreach ($api_order as $service) {
            if (empty($this->api_keys[$service])) {
                continue;
            }
            
            $content = $this->callAI($service, $topic, $word_count, $tone);
            
            if (!is_wp_error($content)) {
                $this->logActivity('ai_success', [
                    'service' => $service,
                    'topic' => $topic
                ]);
                return $content;
            }
            
            $this->logActivity('ai_failed', [
                'service' => $service,
                'error' => $content->get_error_message(),
                'topic' => $topic
            ]);
        }
        
        return new WP_Error('all_apis_failed', 'All AI services failed to generate content');
    }
    
    private function callAI($service, $topic, $word_count, $tone) {
        $api_key = $this->api_keys[$service];
        
        switch ($service) {
            case 'grok':
                return $this->callGrok($api_key, $topic, $word_count, $tone);
            case 'deepseek':
                return $this->callDeepSeek($api_key, $topic, $word_count, $tone);
            case 'openai':
                return $this->callOpenAI($api_key, $topic, $word_count, $tone);
            case 'claude':
                return $this->callClaude($api_key, $topic, $word_count, $tone);
            case 'gemini':
                return $this->callGemini($api_key, $topic, $word_count, $tone);
            default:
                return new WP_Error('unknown_service', 'Unknown AI service');
        }
    }
    
    private function callGrok($api_key, $topic, $word_count, $tone) {
        $prompt = "Write a comprehensive blog post about {$topic} in a {$tone} tone. Target word count: {$word_count} words. Include an engaging introduction, detailed main content with subheadings, and a compelling conclusion.";
        
        $response = wp_remote_post('https://api.x.ai/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'model' => 'grok-beta',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 2000,
                'temperature' => 0.7
            ]),
            'timeout' => 30
        ]);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['choices'][0]['message']['content'])) {
            return $data['choices'][0]['message']['content'];
        }
        
        return new WP_Error('grok_error', 'Grok API error: ' . $body);
    }
    
    private function callDeepSeek($api_key, $topic, $word_count, $tone) {
        $prompt = "Write a comprehensive blog post about {$topic} in a {$tone} tone. Target word count: {$word_count} words. Include an engaging introduction, detailed main content with subheadings, and a compelling conclusion.";
        
        $response = wp_remote_post('https://api.deepseek.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'model' => 'deepseek-chat',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 1500,
                'temperature' => 0.5
            ]),
            'timeout' => 45
        ]);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['choices'][0]['message']['content'])) {
            return $data['choices'][0]['message']['content'];
        }
        
        return new WP_Error('deepseek_error', 'DeepSeek API error: ' . $body);
    }
    
    private function callOpenAI($api_key, $topic, $word_count, $tone) {
        $prompt = "Write a comprehensive blog post about {$topic} in a {$tone} tone. Target word count: {$word_count} words. Include an engaging introduction, detailed main content with subheadings, and a compelling conclusion.";
        
        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ],
                'max_tokens' => 2000,
                'temperature' => 0.7
            ]),
            'timeout' => 60
        ]);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['choices'][0]['message']['content'])) {
            return $data['choices'][0]['message']['content'];
        }
        
        return new WP_Error('openai_error', 'OpenAI API error: ' . $body);
    }
    
    private function callClaude($api_key, $topic, $word_count, $tone) {
        $prompt = "Write a comprehensive blog post about {$topic} in a {$tone} tone. Target word count: {$word_count} words. Include an engaging introduction, detailed main content with subheadings, and a compelling conclusion.";
        
        $response = wp_remote_post('https://api.anthropic.com/v1/messages', [
            'headers' => [
                'x-api-key' => $api_key,
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01'
            ],
            'body' => json_encode([
                'model' => 'claude-3-5-sonnet-20241022',
                'max_tokens' => 2000,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt]
                ]
            ]),
            'timeout' => 60
        ]);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['content'][0]['text'])) {
            return $data['content'][0]['text'];
        }
        
        return new WP_Error('claude_error', 'Claude API error: ' . $body);
    }
    
    private function callGemini($api_key, $topic, $word_count, $tone) {
        $prompt = "Write a comprehensive blog post about {$topic} in a {$tone} tone. Target word count: {$word_count} words. Include an engaging introduction, detailed main content with subheadings, and a compelling conclusion.";
        
        // Use the latest Gemini models (in order of preference)
        $models = [
            'gemini-2.5-flash',        // Fastest and most efficient
            'gemini-flash-latest',     // Always latest flash model
            'gemini-2.5-pro',          // Most capable
            'gemini-pro-latest'        // Always latest pro model
        ];
        
        foreach ($models as $model) {
            $response = wp_remote_post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $api_key, [
                'headers' => [
                    'Content-Type' => 'application/json'
                ],
                'body' => json_encode([
                    'contents' => [
                        ['parts' => [['text' => $prompt]]]
                    ],
                    'generationConfig' => [
                        'maxOutputTokens' => 2000,
                        'temperature' => 0.7,
                        'topP' => 0.8,
                        'topK' => 40
                    ],
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                            'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                        ]
                    ]
                ]),
                'timeout' => 45
            ]);
            
            if (is_wp_error($response)) {
                error_log("Gemini API Error with {$model}: " . $response->get_error_message());
                continue; // Try next model
            }
            
            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                error_log("Gemini API Success with model: {$model}");
                return $data['candidates'][0]['content']['parts'][0]['text'];
            }
            
            // If we get an error, try next model
            if (isset($data['error'])) {
                error_log("Gemini API Error with {$model}: " . $data['error']['message']);
                continue;
            }
        }
        
        return new WP_Error('gemini_error', 'All Gemini models failed to generate content');
    }
    
    public function saveSettings() {
        check_ajax_referer('tbp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $webhook_secret = sanitize_text_field($_POST['webhook_secret']);
        $api_keys = [];
        
        foreach ($this->api_services as $service => $name) {
            if (isset($_POST["api_key_{$service}"])) {
                $api_keys[$service] = sanitize_text_field($_POST["api_key_{$service}"]);
            }
        }
        
        update_option('tbp_webhook_secret', $webhook_secret);
        update_option('tbp_api_keys', $api_keys);
        
        $this->api_keys = $api_keys;
        
        wp_send_json_success('Settings saved successfully');
    }
    
    public function testWebhook() {
        check_ajax_referer('tbp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $webhook_url = get_rest_url() . 'telegram-blog-publisher/v1/webhook';
        $webhook_secret = get_option('tbp_webhook_secret', '');
        
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
            wp_send_json_error('Webhook test failed: ' . $response->get_error_message());
        }
        
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($code === 200) {
            wp_send_json_success('Webhook test successful');
        } else {
            wp_send_json_error("Webhook test failed: HTTP {$code} - {$body}");
        }
    }
    
    public function testApiKey() {
        check_ajax_referer('tbp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $service = sanitize_text_field($_POST['service']);
        $api_key = sanitize_text_field($_POST['api_key']);
        
        if (empty($api_key)) {
            wp_send_json_error('API key is required');
        }
        
        $result = $this->testAIService($service, $api_key);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        } else {
            wp_send_json_success('API key is valid and working');
        }
    }
    
    private function testAIService($service, $api_key) {
        $test_prompt = "Write a short test message about barcodes.";
        
        switch ($service) {
            case 'grok':
                return $this->callGrok($api_key, 'test', 50, 'professional');
            case 'deepseek':
                return $this->callDeepSeek($api_key, 'test', 50, 'professional');
            case 'openai':
                return $this->callOpenAI($api_key, 'test', 50, 'professional');
            case 'claude':
                return $this->callClaude($api_key, 'test', 50, 'professional');
            case 'gemini':
                // Test with the latest Gemini model
                $response = wp_remote_post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $api_key, [
                    'headers' => [
                        'Content-Type' => 'application/json'
                    ],
                    'body' => json_encode([
                        'contents' => [
                            ['parts' => [['text' => $test_prompt]]]
                        ],
                        'generationConfig' => [
                            'maxOutputTokens' => 100,
                            'temperature' => 0.7
                        ]
                    ]),
                    'timeout' => 30
                ]);
                
                if (is_wp_error($response)) {
                    return $response;
                }
                
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    return $data['candidates'][0]['content']['parts'][0]['text'];
                }
                
                return new WP_Error('gemini_test_error', 'Gemini test failed: ' . $body);
            default:
                return new WP_Error('unknown_service', 'Unknown AI service');
        }
    }
    
    public function reactivateLicense() {
        check_ajax_referer('tbp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        update_option('tbp_license_key', 'free-license-' . time());
        update_option('tbp_license_status', 'valid');
        
        wp_send_json_success('License reactivated successfully');
    }
    
    private function logActivity($action, $data = []) {
        $log_entry = [
            'timestamp' => current_time('mysql'),
            'action' => $action,
            'data' => $data,
            'user_id' => get_current_user_id()
        ];
        
        $logs = get_option('tbp_activity_logs', []);
        $logs[] = $log_entry;
        
        // Keep only last 100 logs
        if (count($logs) > 100) {
            $logs = array_slice($logs, -100);
        }
        
        update_option('tbp_activity_logs', $logs);
    }
    
    public function renderDashboard() {
        include TBP_PLUGIN_PATH . 'templates/admin-dashboard.php';
    }
    
    public function renderSettings() {
        include TBP_PLUGIN_PATH . 'templates/admin-settings.php';
    }
    
    public function renderLogs() {
        include TBP_PLUGIN_PATH . 'templates/admin-logs.php';
    }
}

// Initialize the plugin
new TelegramBlogPublisher();
?>