<?php
/**
 * Plugin Name: Telegram Blog Publisher
 * Plugin URI: https://barcodemine.com
 * Description: Publish blogs from Telegram via n8n webhooks. Send a topic and details from Telegram, and automatically create and publish blog posts on your WordPress site.
 * Version: 1.0.0
 * Author: Vikram Jindal
 * Author URI: https://barcodemine.com
 * License: GPL v2 or later
 * Text Domain: telegram-blog-publisher
 * Requires at least: 5.0
 * Tested up to: 6.7
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

// Define plugin constants
define('TBP_VERSION', '1.0.0');
define('TBP_PLUGIN_FILE', __FILE__);
define('TBP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TBP_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Main Telegram Blog Publisher Class
 */
class TelegramBlogPublisher {
    
    private static $instance = null;
    private $webhook_secret;
    private $ai_service;
    
    /**
     * Get instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init();
    }
    
    /**
     * Initialize plugin
     */
    private function init() {
        // Load settings
        $this->webhook_secret = get_option('tbp_webhook_secret', '');
        $this->ai_service = get_option('tbp_ai_service', 'openai');
        
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // WordPress hooks
        add_action('init', array($this, 'initPlugin'));
        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_action('admin_enqueue_scripts', array($this, 'adminEnqueueScripts'));
        
        // Webhook endpoint
        add_action('rest_api_init', array($this, 'registerWebhookEndpoint'));
        
        // AJAX handlers
        add_action('wp_ajax_tbp_test_webhook', array($this, 'testWebhook'));
        add_action('wp_ajax_tbp_generate_content', array($this, 'generateContent'));
        add_action('wp_ajax_tbp_save_settings', array($this, 'saveSettings'));
        add_action('wp_ajax_tbp_reactivate_license', array($this, 'reactivateLicense'));
        add_action('wp_ajax_tbp_test_api_key', array($this, 'testApiKey'));
    }
    
    /**
     * Initialize plugin
     */
    public function initPlugin() {
        // Load text domain
        load_plugin_textdomain('telegram-blog-publisher', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Register webhook endpoint
     */
    public function registerWebhookEndpoint() {
        register_rest_route('telegram-blog-publisher/v1', '/webhook', array(
            'methods' => 'POST',
            'callback' => array($this, 'handleWebhook'),
            'permission_callback' => array($this, 'verifyWebhook'),
            'args' => array(
                'topic' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'details' => array(
                    'required' => true,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_textarea_field',
                ),
                'category' => array(
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'tags' => array(
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                ),
                'featured_image' => array(
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'esc_url_raw',
                ),
                'author_id' => array(
                    'required' => false,
                    'type' => 'integer',
                    'sanitize_callback' => 'absint',
                ),
                'status' => array(
                    'required' => false,
                    'type' => 'string',
                    'sanitize_callback' => 'sanitize_text_field',
                    'default' => 'publish',
                ),
            ),
        ));
    }
    
    /**
     * Verify webhook request
     */
    public function verifyWebhook($request) {
        // Check if webhook secret is set
        if (empty($this->webhook_secret)) {
            return new WP_Error('no_secret', 'Webhook secret not configured', array('status' => 401));
        }
        
        // Get the secret from headers (try different header formats)
        $headers = $request->get_headers();
        $provided_secret = '';
        
        // Try different header formats
        if (isset($headers['x_webhook_secret'])) {
            $provided_secret = is_array($headers['x_webhook_secret']) ? $headers['x_webhook_secret'][0] : $headers['x_webhook_secret'];
        } elseif (isset($headers['x-webhook-secret'])) {
            $provided_secret = is_array($headers['x-webhook-secret']) ? $headers['x-webhook-secret'][0] : $headers['x-webhook-secret'];
        } elseif (isset($_SERVER['HTTP_X_WEBHOOK_SECRET'])) {
            $provided_secret = $_SERVER['HTTP_X_WEBHOOK_SECRET'];
        }
        
        // Verify secret
        if (empty($provided_secret) || !hash_equals($this->webhook_secret, $provided_secret)) {
            return new WP_Error('invalid_secret', 'Invalid webhook secret', array('status' => 401));
        }
        
        return true;
    }
    
    /**
     * Handle webhook request
     */
    public function handleWebhook($request) {
        try {
            $params = $request->get_params();
            
            // Log the incoming request
            $this->logWebhookRequest($params);
            
            // Generate blog content using AI
            $content = $this->generateBlogContent($params['topic'], $params['details'], $params);
            
            if (is_wp_error($content)) {
                return $content;
            }
            
            // Create the blog post
            $post_id = $this->createBlogPost($params, $content);
            
            if (is_wp_error($post_id)) {
                return $post_id;
            }
            
            // Return success response
            return array(
                'success' => true,
                'message' => 'Blog post created successfully',
                'post_id' => $post_id,
                'post_url' => get_permalink($post_id),
                'edit_url' => get_edit_post_link($post_id),
            );
            
        } catch (Exception $e) {
            return new WP_Error('webhook_error', $e->getMessage(), array('status' => 500));
        }
    }
    
    /**
     * Generate blog content using AI
     */
    private function generateBlogContent($topic, $details, $params = array()) {
        $ai_service = get_option('tbp_ai_service', 'openai');
        $api_keys = get_option('tbp_api_keys', array());
        $api_key = get_option('tbp_ai_api_key', '');
        
        // Try to get API key from multiple keys first
        if (!empty($api_keys[$ai_service])) {
            $api_key = $api_keys[$ai_service];
        }
        
        if (empty($api_key)) {
            return new WP_Error('no_api_key', 'AI API key not configured for ' . $ai_service);
        }
        
        // Prepare the prompt
        $prompt = $this->buildAIPrompt($topic, $details, $params);
        
        // Call AI service
        switch ($ai_service) {
            case 'openai':
                return $this->callOpenAI($prompt, $api_key);
            case 'deepseek':
                return $this->callDeepSeek($prompt, $api_key);
            case 'claude':
                return $this->callClaude($prompt, $api_key);
            case 'gemini':
                return $this->callGemini($prompt, $api_key);
            default:
                return new WP_Error('invalid_ai_service', 'Invalid AI service selected');
        }
    }
    
    /**
     * Build AI prompt
     */
    private function buildAIPrompt($topic, $details, $params) {
        $category = isset($params['category']) ? $params['category'] : 'General';
        $tags = isset($params['tags']) ? $params['tags'] : '';
        
        $prompt = "Write a comprehensive blog post about: {$topic}\n\n";
        $prompt .= "Additional details: {$details}\n\n";
        $prompt .= "Category: {$category}\n";
        if (!empty($tags)) {
            $prompt .= "Tags: {$tags}\n";
        }
        $prompt .= "\n";
        $prompt .= "Requirements:\n";
        $prompt .= "- Write in a professional, engaging tone\n";
        $prompt .= "- Include an introduction, main content, and conclusion\n";
        $prompt .= "- Use proper headings (H2, H3) for structure\n";
        $prompt .= "- Include relevant examples and insights\n";
        $prompt .= "- Make it SEO-friendly\n";
        $prompt .= "- Aim for 800-1500 words\n";
        $prompt .= "- Format the content in HTML\n";
        $prompt .= "- Include a compelling meta description\n";
        
        return $prompt;
    }
    
    /**
     * Call OpenAI API
     */
    private function callOpenAI($prompt, $api_key) {
        $response = wp_remote_post('https://api.openai.com/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode(array(
                'model' => 'gpt-3.5-turbo',
                'messages' => array(
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                ),
                'max_tokens' => 2000,
                'temperature' => 0.7,
            )),
            'timeout' => 60,
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['choices'][0]['message']['content'])) {
            return $data['choices'][0]['message']['content'];
        }
        
        return new WP_Error('ai_error', 'Failed to generate content from OpenAI');
    }
    
    /**
     * Call DeepSeek API
     */
    private function callDeepSeek($prompt, $api_key) {
        $response = wp_remote_post('https://api.deepseek.com/v1/chat/completions', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode(array(
                'model' => 'deepseek-chat',
                'messages' => array(
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                ),
                'max_tokens' => 2000,
                'temperature' => 0.7,
            )),
            'timeout' => 60,
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['choices'][0]['message']['content'])) {
            return $data['choices'][0]['message']['content'];
        }
        
        return new WP_Error('ai_error', 'Failed to generate content from DeepSeek');
    }
    
    /**
     * Call Claude API
     */
    private function callClaude($prompt, $api_key) {
        $response = wp_remote_post('https://api.anthropic.com/v1/messages', array(
            'headers' => array(
                'x-api-key' => $api_key,
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01',
            ),
            'body' => json_encode(array(
                'model' => 'claude-3-sonnet-20240229',
                'max_tokens' => 2000,
                'messages' => array(
                    array(
                        'role' => 'user',
                        'content' => $prompt
                    )
                ),
            )),
            'timeout' => 60,
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['content'][0]['text'])) {
            return $data['content'][0]['text'];
        }
        
        return new WP_Error('ai_error', 'Failed to generate content from Claude');
    }
    
    /**
     * Call Gemini API
     */
    private function callGemini($prompt, $api_key) {
        $response = wp_remote_post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . $api_key, array(
            'headers' => array(
                'Content-Type' => 'application/json',
            ),
            'body' => json_encode(array(
                'contents' => array(
                    array(
                        'parts' => array(
                            array(
                                'text' => $prompt
                            )
                        )
                    )
                ),
                'generationConfig' => array(
                    'maxOutputTokens' => 2000,
                    'temperature' => 0.7,
                ),
            )),
            'timeout' => 60,
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            return $data['candidates'][0]['content']['parts'][0]['text'];
        }
        
        return new WP_Error('ai_error', 'Failed to generate content from Gemini');
    }
    
    /**
     * Create blog post
     */
    private function createBlogPost($params, $content) {
        // Extract meta description from content
        $meta_description = $this->extractMetaDescription($content);
        
        // Prepare post data
        $post_data = array(
            'post_title' => sanitize_text_field($params['topic']),
            'post_content' => wp_kses_post($content),
            'post_status' => sanitize_text_field($params['status']),
            'post_type' => 'post',
            'post_author' => isset($params['author_id']) ? absint($params['author_id']) : get_current_user_id(),
            'meta_input' => array(
                '_tbp_telegram_generated' => true,
                '_tbp_original_details' => sanitize_textarea_field($params['details']),
                '_yoast_wpseo_metadesc' => $meta_description,
            ),
        );
        
        // Create the post
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            return $post_id;
        }
        
        // Set category
        if (!empty($params['category'])) {
            $category_id = $this->getOrCreateCategory($params['category']);
            if ($category_id) {
                wp_set_post_categories($post_id, array($category_id));
            }
        }
        
        // Set tags
        if (!empty($params['tags'])) {
            $tags = array_map('trim', explode(',', $params['tags']));
            wp_set_post_tags($post_id, $tags);
        }
        
        // Set featured image
        if (!empty($params['featured_image'])) {
            $this->setFeaturedImage($post_id, $params['featured_image']);
        }
        
        // Log the creation
        $this->logPostCreation($post_id, $params);
        
        return $post_id;
    }
    
    /**
     * Extract meta description from content
     */
    private function extractMetaDescription($content) {
        // Remove HTML tags
        $text = wp_strip_all_tags($content);
        
        // Get first 160 characters
        $meta_description = substr($text, 0, 160);
        
        // Ensure it ends with a complete sentence
        $last_period = strrpos($meta_description, '.');
        if ($last_period !== false) {
            $meta_description = substr($meta_description, 0, $last_period + 1);
        }
        
        return $meta_description;
    }
    
    /**
     * Get or create category
     */
    private function getOrCreateCategory($category_name) {
        $category = get_term_by('name', $category_name, 'category');
        
        if ($category) {
            return $category->term_id;
        }
        
        // Create new category
        $result = wp_insert_term($category_name, 'category');
        
        if (is_wp_error($result)) {
            return false;
        }
        
        return $result['term_id'];
    }
    
    /**
     * Set featured image
     */
    private function setFeaturedImage($post_id, $image_url) {
        $image_id = $this->downloadImage($image_url);
        
        if ($image_id) {
            set_post_thumbnail($post_id, $image_id);
        }
    }
    
    /**
     * Download image from URL
     */
    private function downloadImage($image_url) {
        $upload_dir = wp_upload_dir();
        $image_data = wp_remote_get($image_url);
        
        if (is_wp_error($image_data)) {
            return false;
        }
        
        $image_body = wp_remote_retrieve_body($image_data);
        $image_name = basename($image_url);
        $image_path = $upload_dir['path'] . '/' . $image_name;
        
        file_put_contents($image_path, $image_body);
        
        $attachment = array(
            'post_mime_type' => wp_check_filetype($image_name)['type'],
            'post_title' => sanitize_file_name($image_name),
            'post_content' => '',
            'post_status' => 'inherit'
        );
        
        $attachment_id = wp_insert_attachment($attachment, $image_path);
        
        if (!is_wp_error($attachment_id)) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attachment_data = wp_generate_attachment_metadata($attachment_id, $image_path);
            wp_update_attachment_metadata($attachment_id, $attachment_data);
        }
        
        return $attachment_id;
    }
    
    /**
     * Log webhook request
     */
    private function logWebhookRequest($params) {
        $log_entry = array(
            'timestamp' => current_time('mysql'),
            'action' => 'webhook_received',
            'data' => $params,
        );
        
        $this->addLog($log_entry);
    }
    
    /**
     * Log post creation
     */
    private function logPostCreation($post_id, $params) {
        $log_entry = array(
            'timestamp' => current_time('mysql'),
            'action' => 'post_created',
            'post_id' => $post_id,
            'data' => $params,
        );
        
        $this->addLog($log_entry);
    }
    
    /**
     * Add log entry
     */
    private function addLog($log_entry) {
        $logs = get_option('tbp_logs', array());
        $logs[] = $log_entry;
        
        // Keep only last 100 entries
        if (count($logs) > 100) {
            $logs = array_slice($logs, -100);
        }
        
        update_option('tbp_logs', $logs);
    }
    
    /**
     * Check license status
     */
    public function checkLicense() {
        $license_key = get_option('tbp_license_key', '');
        $license_status = get_option('tbp_license_status', 'invalid');
        
        // For free version, always return valid
        if (strpos($license_key, 'free-license-') === 0) {
            return array('status' => 'valid', 'message' => 'Free license active');
        }
        
        return array('status' => $license_status, 'message' => 'License check completed');
    }
    
    /**
     * Reactivate license AJAX handler
     */
    public function reactivateLicense() {
        check_ajax_referer('tbp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        // Generate new free license
        $new_license = 'free-license-' . wp_generate_password(16, false);
        update_option('tbp_license_key', $new_license);
        update_option('tbp_license_status', 'valid');
        
        wp_send_json_success('License reactivated successfully');
    }
    
    /**
     * Add admin menu
     */
    public function addAdminMenu() {
        add_menu_page(
            'Telegram Blog Publisher',
            'Telegram Publisher',
            'manage_options',
            'telegram-blog-publisher',
            array($this, 'adminPage'),
            'dashicons-format-chat',
            30
        );
        
        add_submenu_page(
            'telegram-blog-publisher',
            'Settings',
            'Settings',
            'manage_options',
            'telegram-blog-publisher-settings',
            array($this, 'settingsPage')
        );
        
        add_submenu_page(
            'telegram-blog-publisher',
            'Logs',
            'Logs',
            'manage_options',
            'telegram-blog-publisher-logs',
            array($this, 'logsPage')
        );
    }
    
    /**
     * Admin page
     */
    public function adminPage() {
        include TBP_PLUGIN_DIR . 'templates/admin-dashboard.php';
    }
    
    /**
     * Settings page
     */
    public function settingsPage() {
        include TBP_PLUGIN_DIR . 'templates/admin-settings.php';
    }
    
    /**
     * Logs page
     */
    public function logsPage() {
        include TBP_PLUGIN_DIR . 'templates/admin-logs.php';
    }
    
    /**
     * Admin enqueue scripts
     */
    public function adminEnqueueScripts($hook) {
        if (strpos($hook, 'telegram-blog-publisher') !== false) {
            wp_enqueue_style('tbp-admin', TBP_PLUGIN_URL . 'assets/admin.css', array(), TBP_VERSION);
            wp_enqueue_script('tbp-admin', TBP_PLUGIN_URL . 'assets/admin.js', array('jquery'), TBP_VERSION, true);
            wp_localize_script('tbp-admin', 'tbp_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('tbp_nonce'),
            ));
        }
    }
    
    /**
     * Test webhook AJAX handler
     */
    public function testWebhook() {
        check_ajax_referer('tbp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $webhook_url = get_rest_url() . 'telegram-blog-publisher/v1/webhook';
        $webhook_secret = get_option('tbp_webhook_secret', '');
        
        if (empty($webhook_secret)) {
            wp_send_json_error('Webhook secret not configured');
        }
        
        $test_data = array(
            'topic' => 'Test Blog Post',
            'details' => 'This is a test blog post created via Telegram webhook.',
            'category' => 'Test',
            'tags' => 'test, webhook, telegram',
            'status' => 'draft',
        );
        
        $response = wp_remote_post($webhook_url, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'X-Webhook-Secret' => $webhook_secret,
            ),
            'body' => json_encode($test_data),
            'timeout' => 30,
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error('HTTP Error: ' . $response->get_error_message());
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if ($response_code === 200 && isset($data['success']) && $data['success']) {
            wp_send_json_success($data);
        } else {
            $error_message = 'Webhook test failed';
            if (isset($data['message'])) {
                $error_message .= ': ' . $data['message'];
            } elseif (!empty($body)) {
                $error_message .= ': ' . $body;
            } else {
                $error_message .= ': HTTP ' . $response_code;
            }
            wp_send_json_error($error_message);
        }
    }
    
    /**
     * Generate content AJAX handler
     */
    public function generateContent() {
        check_ajax_referer('tbp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $topic = sanitize_text_field($_POST['topic']);
        $details = sanitize_textarea_field($_POST['details']);
        
        $content = $this->generateBlogContent($topic, $details);
        
        if (is_wp_error($content)) {
            wp_send_json_error($content->get_error_message());
        }
        
        wp_send_json_success(array('content' => $content));
    }
    
    /**
     * Save settings AJAX handler
     */
    public function saveSettings() {
        check_ajax_referer('tbp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $settings = array(
            'tbp_webhook_secret' => sanitize_text_field($_POST['webhook_secret']),
            'tbp_ai_service' => sanitize_text_field($_POST['ai_service']),
            'tbp_ai_api_key' => sanitize_text_field($_POST['ai_api_key']),
            'tbp_default_author' => absint($_POST['default_author']),
            'tbp_default_category' => sanitize_text_field($_POST['default_category']),
            'tbp_auto_publish' => isset($_POST['auto_publish']),
        );
        
        // Save multiple API keys
        if (isset($_POST['api_keys']) && is_array($_POST['api_keys'])) {
            $api_keys = array();
            foreach ($_POST['api_keys'] as $service => $key) {
                $api_keys[sanitize_text_field($service)] = sanitize_text_field($key);
            }
            update_option('tbp_api_keys', $api_keys);
        }
        
        foreach ($settings as $key => $value) {
            update_option($key, $value);
        }
        
        wp_send_json_success('Settings saved successfully');
    }
    
    /**
     * Test API key AJAX handler
     */
    public function testApiKey() {
        check_ajax_referer('tbp_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $service = sanitize_text_field($_POST['service']);
        $api_key = sanitize_text_field($_POST['api_key']);
        
        if (empty($api_key)) {
            wp_send_json_error('API key is required');
        }
        
        // Test the API key
        $test_prompt = "Hello, this is a test message. Please respond with 'API key is working correctly.'";
        $result = $this->testAIService($service, $api_key, $test_prompt);
        
        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }
        
        wp_send_json_success(array('message' => 'API key is working correctly!'));
    }
    
    /**
     * Test AI service with given API key
     */
    private function testAIService($service, $api_key, $prompt) {
        switch ($service) {
            case 'openai':
                return $this->callOpenAI($prompt, $api_key);
            case 'deepseek':
                return $this->callDeepSeek($prompt, $api_key);
            case 'claude':
                return $this->callClaude($prompt, $api_key);
            case 'gemini':
                return $this->callGemini($prompt, $api_key);
            default:
                return new WP_Error('invalid_service', 'Invalid AI service');
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables if needed
        $this->createTables();
        
        // Set default options
        add_option('tbp_version', TBP_VERSION);
        add_option('tbp_webhook_secret', wp_generate_password(32, false));
        add_option('tbp_ai_service', 'openai');
        add_option('tbp_auto_publish', false);
        add_option('tbp_license_key', 'free-license-' . wp_generate_password(16, false));
        add_option('tbp_license_status', 'valid');
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up if needed
    }
    
    /**
     * Create database tables
     */
    private function createTables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'tbp_logs';
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            timestamp datetime DEFAULT CURRENT_TIMESTAMP,
            action varchar(255) NOT NULL,
            data longtext,
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Initialize the plugin
TelegramBlogPublisher::getInstance();
