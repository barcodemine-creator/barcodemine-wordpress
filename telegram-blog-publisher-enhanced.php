<?php
/**
 * Plugin Name: Telegram Blog Publisher Enhanced
 * Plugin URI: https://kloudbean.com/telegram-blog-publisher
 * Description: Publish blog posts from Telegram via n8n webhooks with AI content generation. Enhanced with KloudBean branding and modern UI.
 * Version: 3.1.0
 * Author: KloudBean
 * Author URI: https://kloudbean.com
 * License: GPL v2 or later
 * Text Domain: telegram-blog-publisher
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TBP_VERSION', '3.1.0');
define('TBP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TBP_PLUGIN_PATH', plugin_dir_path(__FILE__));

class TelegramBlogPublisherEnhanced {
    
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
            '📱 Telegram Blog',
            'manage_options',
            'telegram-blog-publisher',
            [$this, 'renderDashboard'],
            'dashicons-format-chat',
            30
        );
        
        add_submenu_page(
            'telegram-blog-publisher',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'telegram-blog-publisher',
            [$this, 'renderDashboard']
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
            'Logs',
            'Logs',
            'manage_options',
            'telegram-blog-publisher-logs',
            [$this, 'renderLogs']
        );
        
        add_submenu_page(
            'telegram-blog-publisher',
            'KloudBean Hosting',
            '☁️ KloudBean Hosting',
            'manage_options',
            'telegram-blog-publisher-hosting',
            [$this, 'renderHosting']
        );
    }
    
    public function enqueueAdminScripts($hook) {
        if (strpos($hook, 'telegram-blog-publisher') === false) {
            return;
        }
        
        wp_enqueue_script('tbp-admin', TBP_PLUGIN_URL . 'assets/admin.js', ['jquery'], TBP_VERSION, true);
        wp_enqueue_style('tbp-admin', TBP_PLUGIN_URL . 'assets/admin.css', [], TBP_VERSION);
        
        wp_localize_script('tbp-admin', 'tbp_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tbp_nonce')
        ]);
    }
    
    public function registerRestRoutes() {
        register_rest_route('telegram-blog-publisher/v1', '/webhook', [
            'methods' => 'POST',
            'callback' => [$this, 'handleWebhook'],
            'permission_callback' => [$this, 'checkWebhookPermission']
        ]);
    }
    
    public function checkWebhookPermission($request) {
        $secret = $request->get_header('X-Webhook-Secret');
        $stored_secret = get_option('tbp_webhook_secret', '');
        
        return !empty($secret) && $secret === $stored_secret;
    }
    
    public function handleWebhook($request) {
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
            'post_type' => 'post'
        ];
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        
        // Add metadata
        update_post_meta($post_id, '_tbp_telegram_generated', true);
        update_post_meta($post_id, '_tbp_original_data', $data);
        
        // Log the creation
        $this->logActivity('Post created', [
            'post_id' => $post_id,
            'topic' => $data['topic'],
            'title' => $post_data['post_title']
        ]);
        
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
        
        // Fallback: Generate basic content if no AI service is available
        return $this->generateFallbackContent($topic, $word_count, $tone);
    }
    
    private function generateFallbackContent($topic, $word_count, $tone) {
        // Generate a basic blog post structure without AI
        $intro = "Welcome to our comprehensive guide on {$topic}. In this article, we'll explore the key aspects and provide valuable insights.";
        
        $main_content = "Understanding {$topic} is crucial for success. Let's dive into the details and discover what makes this topic important and relevant in today's context.";
        
        $subheading1 = "Key Benefits of {$topic}";
        $content1 = "One of the primary advantages of {$topic} is its versatility and practical applications. Many professionals and enthusiasts find it to be an essential tool in their toolkit.";
        
        $subheading2 = "Best Practices";
        $content2 = "When working with {$topic}, it's important to follow established best practices. This ensures optimal results and helps avoid common pitfalls.";
        
        $conclusion = "In conclusion, {$topic} offers numerous opportunities for growth and development. By understanding its core principles and applying them effectively, you can achieve significant results.";
        
        $content = "<h2>Introduction</h2>\n<p>{$intro}</p>\n\n";
        $content .= "<h2>{$subheading1}</h2>\n<p>{$content1}</p>\n\n";
        $content .= "<h2>{$subheading2}</h2>\n<p>{$content2}</p>\n\n";
        $content .= "<h2>Conclusion</h2>\n<p>{$conclusion}</p>";
        
        return $content;
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
    
    private function logActivity($action, $data = []) {
        $logs = get_option('tbp_logs', []);
        $logs[] = [
            'timestamp' => current_time('mysql'),
            'action' => $action,
            'data' => $data
        ];
        
        // Keep only last 100 logs
        if (count($logs) > 100) {
            $logs = array_slice($logs, -100);
        }
        
        update_option('tbp_logs', $logs);
    }
    
    public function renderDashboard() {
        $webhook_url = get_rest_url() . 'telegram-blog-publisher/v1/webhook';
        $webhook_secret = get_option('tbp_webhook_secret', '');
        $gemini_key = get_option('tbp_gemini_key', '');
        $deepseek_key = get_option('tbp_deepseek_key', '');
        $recent_posts = get_posts([
            'meta_key' => '_tbp_telegram_generated',
            'meta_value' => true,
            'numberposts' => 5,
            'post_status' => 'any',
        ]);
        ?>
        <div class="wrap tbp-dashboard">
            <div class="tbp-header">
                <h1>📱 Telegram Blog Publisher</h1>
                <p>Publish blog posts from Telegram with AI-powered content generation</p>
                <div class="tbp-brand">
                    <span>Powered by</span>
                    <a href="https://kloudbean.com" target="_blank" class="tbp-kloudbean-link">☁️ KloudBean</a>
                </div>
            </div>
            
            <!-- KloudBean Hosting Promotion -->
            <div class="tbp-hosting-promo">
                <div class="tbp-hosting-content">
                    <div class="tbp-hosting-text">
                        <h2>☁️ Host Your WordPress Sites on KloudBean</h2>
                        <p>Get enterprise-grade hosting with this plugin included. Perfect for WordPress, n8n workflows, Lovable apps, and Cursor projects.</p>
                        <div class="tbp-hosting-features">
                            <span class="tbp-feature-tag">🚀 Lightning Fast</span>
                            <span class="tbp-feature-tag">🛡️ Enterprise Security</span>
                            <span class="tbp-feature-tag">⚡ Auto-scaling</span>
                            <span class="tbp-feature-tag">🔧 Developer Tools</span>
                        </div>
                    </div>
                    <div class="tbp-hosting-actions">
                        <a href="https://kloudbean.com/hosting" target="_blank" class="tbp-btn tbp-btn-primary">
                            <span class="dashicons dashicons-cloud"></span>
                            Get Started on KloudBean
                        </a>
                        <a href="<?php echo admin_url('admin.php?page=telegram-blog-publisher-hosting'); ?>" class="tbp-btn tbp-btn-secondary">
                            <span class="dashicons dashicons-info"></span>
                            View Features
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="tbp-dashboard-grid">
                <div class="tbp-card tbp-card-large">
                    <h2>🚀 Quick Test</h2>
                    <form id="quick-test-form">
                        <table class="form-table">
                            <tr>
                                <th>Topic:</th>
                                <td><input type="text" id="test-topic" name="topic" class="regular-text" placeholder="Enter topic here..." value="r3e" /></td>
                            </tr>
                            <tr>
                                <th>Details:</th>
                                <td><textarea id="test-details" name="details" rows="3" cols="50" placeholder="Enter additional details...">3f</textarea></td>
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
                
                <div class="tbp-card">
                    <h2>🔗 Webhook Information</h2>
                    <p><strong>Webhook URL:</strong> <code><?php echo esc_html($webhook_url); ?></code></p>
                    <p><strong>Webhook Secret:</strong> <code><?php echo esc_html($webhook_secret); ?></code></p>
                    <div class="tbp-button-group">
                        <button onclick="navigator.clipboard.writeText('<?php echo esc_js($webhook_url); ?>')" class="button">Copy URL</button>
                        <button onclick="navigator.clipboard.writeText('<?php echo esc_js($webhook_secret); ?>')" class="button">Copy Secret</button>
                    </div>
                </div>
                
                <div class="tbp-card">
                    <h2>🤖 AI Services Status</h2>
                    <div class="tbp-ai-status">
                        <div class="tbp-service-status">
                            <span class="tbp-service-name">Gemini API:</span>
                            <span class="tbp-status <?php echo !empty($gemini_key) ? 'active' : 'inactive'; ?>">
                                <?php echo !empty($gemini_key) ? '✅ Configured' : '❌ Not Set'; ?>
                            </span>
                        </div>
                        <div class="tbp-service-status">
                            <span class="tbp-service-name">DeepSeek API:</span>
                            <span class="tbp-status <?php echo !empty($deepseek_key) ? 'active' : 'inactive'; ?>">
                                <?php echo !empty($deepseek_key) ? '✅ Configured' : '❌ Not Set'; ?>
                            </span>
                        </div>
                    </div>
                    <p><a href="<?php echo admin_url('admin.php?page=telegram-blog-publisher-settings'); ?>" class="button">Configure API Keys</a></p>
                </div>
                
                <div class="tbp-card">
                    <h2>📊 Recent Posts</h2>
                    <?php if (!empty($recent_posts)): ?>
                        <ul class="tbp-recent-posts">
                            <?php foreach ($recent_posts as $post): ?>
                                <li>
                                    <a href="<?php echo get_edit_post_link($post->ID); ?>"><?php echo esc_html($post->post_title); ?></a>
                                    <span class="tbp-post-date"><?php echo get_the_date('M j, Y', $post->ID); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No posts generated yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <style>
        .tbp-dashboard {
            max-width: 1200px;
        }
        
        .tbp-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 12px;
            margin-bottom: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .tbp-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: tbp-float 6s ease-in-out infinite;
        }
        
        @keyframes tbp-float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-20px, -20px) rotate(180deg); }
        }
        
        .tbp-header h1 {
            font-size: 2.5rem;
            margin: 0 0 10px 0;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }
        
        .tbp-header p {
            font-size: 1.2rem;
            margin: 0 0 20px 0;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .tbp-brand {
            position: relative;
            z-index: 1;
        }
        
        .tbp-brand span {
            margin-right: 10px;
            opacity: 0.8;
        }
        
        .tbp-kloudbean-link {
            color: #ffd700;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .tbp-kloudbean-link:hover {
            color: #ffed4e;
        }
        
        .tbp-hosting-promo {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        
        .tbp-hosting-content {
            display: flex;
            align-items: center;
            gap: 40px;
        }
        
        .tbp-hosting-text {
            flex: 1;
        }
        
        .tbp-hosting-text h2 {
            font-size: 1.8rem;
            margin: 0 0 15px 0;
        }
        
        .tbp-hosting-text p {
            font-size: 1.1rem;
            margin: 0 0 20px 0;
            opacity: 0.9;
        }
        
        .tbp-hosting-features {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .tbp-feature-tag {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }
        
        .tbp-hosting-actions {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .tbp-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .tbp-btn-primary {
            background: #ffd700;
            color: #333;
        }
        
        .tbp-btn-primary:hover {
            background: #ffed4e;
            transform: translateY(-2px);
            color: #333;
        }
        
        .tbp-btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        
        .tbp-btn-secondary:hover {
            background: white;
            color: #333;
        }
        
        .tbp-dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .tbp-card {
            background: white;
            border: 1px solid #e1e5e9;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        
        .tbp-card:hover {
            transform: translateY(-2px);
        }
        
        .tbp-card-large {
            grid-column: span 2;
        }
        
        .tbp-card h2 {
            margin: 0 0 20px 0;
            color: #333;
        }
        
        .tbp-button-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .tbp-ai-status {
            margin: 15px 0;
        }
        
        .tbp-service-status {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .tbp-service-status:last-child {
            border-bottom: none;
        }
        
        .tbp-service-name {
            font-weight: 600;
        }
        
        .tbp-status.active {
            color: #28a745;
            font-weight: 600;
        }
        
        .tbp-status.inactive {
            color: #dc3545;
            font-weight: 600;
        }
        
        .tbp-recent-posts {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .tbp-recent-posts li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .tbp-recent-posts li:last-child {
            border-bottom: none;
        }
        
        .tbp-post-date {
            color: #666;
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .tbp-hosting-content {
                flex-direction: column;
                text-align: center;
            }
            
            .tbp-dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .tbp-card-large {
                grid-column: span 1;
            }
        }
        </style>
        
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
                        topic: topic,
                        details: details,
                        nonce: tbp_ajax.nonce
                    },
                    beforeSend: function() {
                        $('#content-result').html('<div class="tbp-loading">Generating content...</div>');
                        $('#generated-content').show();
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#content-result').html(response.data.content);
                        } else {
                            $('#content-result').html('<div class="tbp-error">Error: ' + response.data + '</div>');
                        }
                    },
                    error: function() {
                        $('#content-result').html('<div class="tbp-error">An error occurred. Please try again.</div>');
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
            <h1>Settings</h1>
            <form id="tbp-settings-form">
                <table class="form-table">
                    <tr>
                        <th>Webhook Secret</th>
                        <td><input type="text" name="webhook_secret" value="<?php echo esc_attr($webhook_secret); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th>Gemini API Key</th>
                        <td>
                            <input type="password" name="gemini_key" value="<?php echo esc_attr($gemini_key); ?>" class="regular-text" />
                            <button type="button" class="button" onclick="testAPI('gemini')">Test</button>
                        </td>
                    </tr>
                    <tr>
                        <th>DeepSeek API Key</th>
                        <td>
                            <input type="password" name="deepseek_key" value="<?php echo esc_attr($deepseek_key); ?>" class="regular-text" />
                            <button type="button" class="button" onclick="testAPI('deepseek')">Test</button>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary">Save Settings</button>
                </p>
            </form>
        </div>
        
        <script>
        function testAPI(service) {
            var apiKey = document.querySelector('input[name="' + service + '_key"]').value;
            if (!apiKey) {
                alert('Please enter an API key first');
                return;
            }
            
            jQuery.post(tbp_ajax.ajax_url, {
                action: 'tbp_test_api',
                service: service,
                api_key: apiKey,
                nonce: tbp_ajax.nonce
            }, function(response) {
                if (response.success) {
                    alert('API key is working!');
                } else {
                    alert('Error: ' + response.data);
                }
            });
        }
        
        jQuery(document).ready(function($) {
            $('#tbp-settings-form').on('submit', function(e) {
                e.preventDefault();
                
                $.ajax({
                    url: tbp_ajax.ajax_url,
                    type: 'POST',
                    data: $(this).serialize() + '&action=tbp_save_settings&nonce=' + tbp_ajax.nonce,
                    success: function(response) {
                        if (response.success) {
                            alert('Settings saved successfully!');
                        } else {
                            alert('Error: ' + response.data);
                        }
                    }
                });
            });
        });
        </script>
        <?php
    }
    
    public function renderLogs() {
        $logs = get_option('tbp_logs', []);
        $recent_logs = array_reverse($logs);
        ?>
        <div class="wrap">
            <h1>Activity Logs</h1>
            <div class="tbp-logs">
                <?php if (!empty($recent_logs)): ?>
                    <?php foreach ($recent_logs as $log): ?>
                        <div class="tbp-log-entry">
                            <div class="tbp-log-time"><?php echo esc_html($log['timestamp']); ?></div>
                            <div class="tbp-log-action"><?php echo esc_html($log['action']); ?></div>
                            <?php if (!empty($log['data'])): ?>
                                <div class="tbp-log-data"><?php echo esc_html(json_encode($log['data'], JSON_PRETTY_PRINT)); ?></div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No logs available.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <style>
        .tbp-logs {
            max-height: 500px;
            overflow-y: auto;
        }
        
        .tbp-log-entry {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 10px;
        }
        
        .tbp-log-time {
            font-weight: bold;
            color: #666;
        }
        
        .tbp-log-action {
            margin: 5px 0;
            color: #333;
        }
        
        .tbp-log-data {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 10px;
            font-family: monospace;
            font-size: 12px;
            white-space: pre-wrap;
        }
        </style>
        <?php
    }
    
    public function renderHosting() {
        ?>
        <div class="wrap tbp-hosting-page">
            <div class="tbp-hosting-hero">
                <h1>☁️ KloudBean Hosting</h1>
                <p>Enterprise-grade hosting for WordPress, n8n workflows, Lovable apps, and Cursor projects</p>
            </div>
            
            <div class="tbp-hosting-features">
                <div class="tbp-feature-card">
                    <h3>🚀 Lightning Fast Performance</h3>
                    <p>SSD storage, CDN integration, and optimized server configurations ensure your WordPress sites load in milliseconds.</p>
                </div>
                
                <div class="tbp-feature-card">
                    <h3>🛡️ Enterprise Security</h3>
                    <p>Advanced security measures including DDoS protection, SSL certificates, and automated backups keep your sites safe.</p>
                </div>
                
                <div class="tbp-feature-card">
                    <h3>🔧 Developer-Friendly</h3>
                    <p>Built for developers with Git integration, staging environments, and support for modern development tools.</p>
                </div>
            </div>
            
            <div class="tbp-hosting-cta">
                <h2>Ready to Get Started?</h2>
                <p>Join thousands of developers who trust KloudBean for their hosting needs.</p>
                <a href="https://kloudbean.com/hosting" target="_blank" class="button button-primary button-large">Get Started on KloudBean</a>
            </div>
        </div>
        
        <style>
        .tbp-hosting-page {
            max-width: 1200px;
        }
        
        .tbp-hosting-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 40px;
            border-radius: 12px;
            margin-bottom: 40px;
            text-align: center;
        }
        
        .tbp-hosting-hero h1 {
            font-size: 3rem;
            margin: 0 0 20px 0;
            font-weight: 700;
        }
        
        .tbp-hosting-hero p {
            font-size: 1.2rem;
            margin: 0;
            opacity: 0.9;
        }
        
        .tbp-hosting-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        
        .tbp-feature-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .tbp-feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #333;
        }
        
        .tbp-hosting-cta {
            background: #f8f9fa;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
        }
        
        .tbp-hosting-cta h2 {
            font-size: 2rem;
            margin-bottom: 15px;
        }
        
        .tbp-hosting-cta p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            color: #666;
        }
        </style>
        <?php
    }
}

// Initialize the plugin
new TelegramBlogPublisherEnhanced();
?>
