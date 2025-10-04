<?php
/**
 * Plugin Name: Telegram Blog Publisher
 * Plugin URI: https://github.com/barcodemine-creator/barcodemine-wordpress
 * Description: Publish blog posts from Telegram via n8n webhooks with AI content generation
 * Version: 3.0.0
 * Author: Barcodemine
 * License: GPL v2 or later
 * Text Domain: telegram-blog-publisher
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TBP_VERSION', '3.0.0');
define('TBP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TBP_PLUGIN_PATH', plugin_dir_path(__FILE__));

class TelegramBlogPublisher {
    
    public function __construct() {
        add_action('init', [$this, 'init']);
        add_action('admin_menu', [$this, 'addAdminMenu']);
        add_action('wp_ajax_tbp_save_settings', [$this, 'saveSettings']);
        add_action('wp_ajax_tbp_test_api', [$this, 'testApi']);
        add_action('wp_ajax_tbp_generate_content', [$this, 'generateContent']);
        add_action('rest_api_init', [$this, 'registerRestRoutes']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
    }
    
    public function init() {
        // Plugin initialization
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
    }
    
    public function enqueueAdminScripts($hook) {
        if (strpos($hook, 'telegram-blog-publisher') === false) {
            return;
        }
        
        wp_enqueue_script('jquery');
        wp_localize_script('jquery', 'tbp_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tbp_nonce')
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
        }
        
        return hash_equals($webhook_secret, $received_secret);
    }
    
    public function handleWebhook($request) {
        set_time_limit(300);
        ini_set('memory_limit', '512M');
        
        $data = $request->get_json_params();
        
        if (empty($data['topic'])) {
            return new WP_Error('missing_topic', 'Topic is required', ['status' => 400]);
        }
        
        // Generate content
        $content = $this->generateContentFromAI($data);
        
        if (is_wp_error($content)) {
            return $content;
        }
        
        // Create post
        $post_data = [
            'post_title' => $data['title'] ?? $data['topic'],
            'post_content' => $content,
            'post_status' => $data['status'] ?? 'publish',
            'post_type' => 'post',
            'post_author' => get_current_user_id()
        ];
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            return new WP_Error('post_creation_failed', 'Failed to create post', ['status' => 500]);
        }
        
        return [
            'success' => true,
            'post_id' => $post_id,
            'post_url' => get_permalink($post_id),
            'message' => 'Blog post created successfully'
        ];
    }
    
    private function generateContentFromAI($data) {
        $topic = $data['topic'];
        $word_count = $data['word_count'] ?? 500;
        $tone = $data['tone'] ?? 'professional';
        
        // Get API keys
        $gemini_key = get_option('tbp_gemini_key', '');
        $deepseek_key = get_option('tbp_deepseek_key', '');
        
        // Try Gemini first
        if (!empty($gemini_key)) {
            $content = $this->callGeminiAPI($gemini_key, $topic, $word_count, $tone);
            if (!is_wp_error($content)) {
                return $content;
            }
        }
        
        // Try DeepSeek
        if (!empty($deepseek_key)) {
            $content = $this->callDeepSeekAPI($deepseek_key, $topic, $word_count, $tone);
            if (!is_wp_error($content)) {
                return $content;
            }
        }
        
        return new WP_Error('no_ai_available', 'No working AI service available');
    }
    
    private function callGeminiAPI($api_key, $topic, $word_count, $tone) {
        $prompt = "Write a comprehensive blog post about {$topic} in a {$tone} tone. Target word count: {$word_count} words. Include an engaging introduction, detailed main content with subheadings, and a compelling conclusion.";
        
        $response = wp_remote_post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . $api_key, [
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
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }
        
        return new WP_Error('gemini_error', 'Gemini API error: ' . $body);
    }
    
    private function callDeepSeekAPI($api_key, $topic, $word_count, $tone) {
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
        
        return new WP_Error('deepseek_error', 'DeepSeek API error: ' . $body);
    }
    
    public function saveSettings() {
        check_ajax_referer('tbp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $webhook_secret = sanitize_text_field($_POST['webhook_secret']);
        $gemini_key = sanitize_text_field($_POST['gemini_key']);
        $deepseek_key = sanitize_text_field($_POST['deepseek_key']);
        
        update_option('tbp_webhook_secret', $webhook_secret);
        update_option('tbp_gemini_key', $gemini_key);
        update_option('tbp_deepseek_key', $deepseek_key);
        
        wp_send_json_success('Settings saved successfully');
    }
    
    public function testApi() {
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
            wp_send_json_success('API key is working!');
        }
    }
    
    private function testAIService($service, $api_key) {
        $test_prompt = "Write a short test message about barcodes.";
        
        if ($service === 'gemini') {
            return $this->callGeminiAPI($api_key, 'test', 50, 'professional');
        } elseif ($service === 'deepseek') {
            return $this->callDeepSeekAPI($api_key, 'test', 50, 'professional');
        }
        
        return new WP_Error('unknown_service', 'Unknown service');
    }
    
    public function generateContent() {
        check_ajax_referer('tbp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $topic = sanitize_text_field($_POST['topic']);
        $details = sanitize_text_field($_POST['details']);
        
        if (empty($topic)) {
            wp_send_json_error('Topic is required');
        }
        
        $data = [
            'topic' => $topic,
            'word_count' => 500,
            'tone' => 'professional'
        ];
        
        $content = $this->generateContentFromAI($data);
        
        if (is_wp_error($content)) {
            wp_send_json_error($content->get_error_message());
        } else {
            wp_send_json_success(['content' => $content]);
        }
    }
    
    public function renderDashboard() {
        $webhook_url = get_rest_url() . 'telegram-blog-publisher/v1/webhook';
        $webhook_secret = get_option('tbp_webhook_secret', '');
        ?>
        <div class="wrap">
            <h1>Telegram Blog Publisher</h1>
            
            <div class="card">
                <h2>Quick Test</h2>
                <form id="quick-test-form">
                    <table class="form-table">
                        <tr>
                            <th>Topic:</th>
                            <td><input type="text" id="test-topic" name="topic" class="regular-text" placeholder="Enter topic here..." /></td>
                        </tr>
                        <tr>
                            <th>Details:</th>
                            <td><textarea id="test-details" name="details" rows="3" cols="50" placeholder="Enter additional details..."></textarea></td>
                        </tr>
                    </table>
                    <p class="submit">
                        <button type="submit" class="button button-primary">Generate Content</button>
                    </p>
                </form>
                
                <div id="generated-content" style="margin-top: 20px; padding: 15px; border: 1px solid #ddd; background: #f9f9f9; display: none;">
                    <h3>Generated Content:</h3>
                    <div id="content-result"></div>
                </div>
            </div>
            
            <div class="card">
                <h2>Webhook Information</h2>
                <p><strong>Webhook URL:</strong> <code><?php echo esc_html($webhook_url); ?></code></p>
                <p><strong>Webhook Secret:</strong> <code><?php echo esc_html($webhook_secret); ?></code></p>
                <button onclick="navigator.clipboard.writeText('<?php echo esc_js($webhook_url); ?>')" class="button">Copy URL</button>
                <button onclick="navigator.clipboard.writeText('<?php echo esc_js($webhook_secret); ?>')" class="button">Copy Secret</button>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#quick-test-form').on('submit', function(e) {
                e.preventDefault();
                
                var topic = $('#test-topic').val();
                var details = $('#test-details').val();
                
                if (!topic) {
                    alert('Please enter a topic');
                    return;
                }
                
                $.ajax({
                    url: tbp_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'tbp_generate_content',
                        nonce: tbp_ajax.nonce,
                        topic: topic,
                        details: details
                    },
                    beforeSend: function() {
                        $('#generated-content').show();
                        $('#content-result').html('Generating content...');
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#content-result').html('<div style="white-space: pre-wrap;">' + response.data.content + '</div>');
                        } else {
                            $('#content-result').html('<div style="color: red;">Error: ' + response.data + '</div>');
                        }
                    },
                    error: function() {
                        $('#content-result').html('<div style="color: red;">Network error occurred</div>');
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    public function renderSettings() {
        $webhook_secret = get_option('tbp_webhook_secret', '');
        $gemini_key = get_option('tbp_gemini_key', '');
        $deepseek_key = get_option('tbp_deepseek_key', '');
        ?>
        <div class="wrap">
            <h1>Telegram Blog Publisher Settings</h1>
            
            <form method="post" id="tbp-settings-form">
                <table class="form-table">
                    <tr>
                        <th scope="row">Webhook Secret</th>
                        <td>
                            <input type="text" name="webhook_secret" value="<?php echo esc_attr($webhook_secret); ?>" class="regular-text" />
                            <p class="description">Secret key for webhook authentication</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Gemini API Key</th>
                        <td>
                            <input type="password" id="gemini_key" name="gemini_key" value="<?php echo esc_attr($gemini_key); ?>" class="regular-text" />
                            <button type="button" onclick="togglePassword('gemini_key')" class="button">Show/Hide</button>
                            <button type="button" onclick="testAPI('gemini', document.getElementById('gemini_key').value)" class="button">Test</button>
                            <p class="description">Get your Gemini API key from <a href="https://makersuite.google.com/app/apikey" target="_blank">Google AI Studio</a></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">DeepSeek API Key</th>
                        <td>
                            <input type="password" id="deepseek_key" name="deepseek_key" value="<?php echo esc_attr($deepseek_key); ?>" class="regular-text" />
                            <button type="button" onclick="togglePassword('deepseek_key')" class="button">Show/Hide</button>
                            <button type="button" onclick="testAPI('deepseek', document.getElementById('deepseek_key').value)" class="button">Test</button>
                            <p class="description">Get your DeepSeek API key from <a href="https://platform.deepseek.com/api_keys" target="_blank">DeepSeek Platform</a></p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <button type="submit" class="button button-primary">Save Settings</button>
                </p>
            </form>
        </div>
        
        <script>
        function togglePassword(fieldId) {
            var field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }
        
        function testAPI(service, apiKey) {
            if (!apiKey) {
                alert('Please enter an API key first');
                return;
            }
            
            var button = event.target;
            var originalText = button.textContent;
            button.textContent = 'Testing...';
            button.disabled = true;
            
            jQuery.ajax({
                url: tbp_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'tbp_test_api',
                    nonce: tbp_ajax.nonce,
                    service: service,
                    api_key: apiKey
                },
                success: function(response) {
                    if (response.success) {
                        alert('API key is working!');
                    } else {
                        alert('API test failed: ' + response.data);
                    }
                },
                error: function() {
                    alert('Network error occurred');
                },
                complete: function() {
                    button.textContent = originalText;
                    button.disabled = false;
                }
            });
        }
        
        jQuery(document).ready(function($) {
            $('#tbp-settings-form').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: tbp_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'tbp_save_settings',
                        nonce: tbp_ajax.nonce,
                        webhook_secret: $('input[name="webhook_secret"]').val(),
                        gemini_key: $('input[name="gemini_key"]').val(),
                        deepseek_key: $('input[name="deepseek_key"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            alert('Settings saved successfully!');
                        } else {
                            alert('Error: ' + response.data);
                        }
                    },
                    error: function() {
                        alert('Network error occurred');
                    }
                });
            });
        });
        </script>
        <?php
    }
}

// Initialize the plugin
new TelegramBlogPublisher();

// Activation hook
register_activation_hook(__FILE__, function() {
    if (empty(get_option('tbp_webhook_secret'))) {
        update_option('tbp_webhook_secret', wp_generate_password(32, false));
    }
});
?>