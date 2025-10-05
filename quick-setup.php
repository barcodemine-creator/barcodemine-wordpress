<?php
/**
 * Quick Setup Script for Telegram Blog Publisher
 * 
 * This script helps you quickly set up the plugin with default settings
 * Run this after installing the plugin
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Check if we're in WordPress admin
if (!is_admin()) {
    wp_die('This script must be run from WordPress admin.');
}

// Check if plugin is active
if (!class_exists('TelegramBlogPublisher')) {
    wp_die('Telegram Blog Publisher plugin is not active. Please activate it first.');
}

echo '<div class="wrap">';
echo '<h1>🚀 Quick Setup - Telegram Blog Publisher</h1>';

// Handle form submission
if (isset($_POST['setup_plugin'])) {
    $webhook_secret = sanitize_text_field($_POST['webhook_secret']);
    $ai_service = sanitize_text_field($_POST['ai_service']);
    $ai_key = sanitize_text_field($_POST['ai_key']);
    
    if (empty($webhook_secret) || empty($ai_key)) {
        echo '<div class="notice notice-error"><p>Please fill in all required fields.</p></div>';
    } else {
        // Save settings
        update_option('tbp_webhook_secret', $webhook_secret);
        
        // Save AI key based on service
        switch ($ai_service) {
            case 'gemini':
                update_option('tbp_gemini_key', $ai_key);
                break;
            case 'openai':
                update_option('tbp_openai_key', $ai_key);
                break;
            case 'claude':
                update_option('tbp_claude_key', $ai_key);
                break;
        }
        
        // Set default content settings
        update_option('tbp_content_quality', 'premium');
        update_option('tbp_include_images', 1);
        update_option('tbp_seo_optimized', 1);
        
        echo '<div class="notice notice-success"><p>✅ Plugin configured successfully!</p></div>';
        echo '<p><strong>Next steps:</strong></p>';
        echo '<ol>';
        echo '<li>Go to <a href="' . admin_url('admin.php?page=telegram-blog-publisher') . '">Telegram Blog → Dashboard</a> to see your webhook URL</li>';
        echo '<li>Test your API key to make sure it\'s working</li>';
        echo '<li>Set up n8n workflow using the webhook URL</li>';
        echo '</ol>';
    }
}

// Get current settings
$webhook_secret = get_option('tbp_webhook_secret', '');
$gemini_key = get_option('tbp_gemini_key', '');
$openai_key = get_option('tbp_openai_key', '');
$claude_key = get_option('tbp_claude_key', '');

// Determine which AI service is configured
$ai_service = '';
$ai_key = '';
if (!empty($gemini_key)) {
    $ai_service = 'gemini';
    $ai_key = $gemini_key;
} elseif (!empty($openai_key)) {
    $ai_service = 'openai';
    $ai_key = $openai_key;
} elseif (!empty($claude_key)) {
    $ai_service = 'claude';
    $ai_key = $claude_key;
}

?>

<div class="card">
    <h2>⚙️ Quick Configuration</h2>
    <p>Fill in the details below to quickly configure your plugin:</p>
    
    <form method="post" action="">
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="webhook_secret">Webhook Secret *</label>
                </th>
                <td>
                    <input type="text" id="webhook_secret" name="webhook_secret" 
                           value="<?php echo esc_attr($webhook_secret ?: wp_generate_password(32, false)); ?>" 
                           class="regular-text" required />
                    <p class="description">A secret key for webhook authentication (auto-generated if empty)</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="ai_service">AI Service *</label>
                </th>
                <td>
                    <select id="ai_service" name="ai_service" required>
                        <option value="">Select AI Service</option>
                        <option value="gemini" <?php selected($ai_service, 'gemini'); ?>>Google Gemini (Free)</option>
                        <option value="openai" <?php selected($ai_service, 'openai'); ?>>OpenAI (Paid)</option>
                        <option value="claude" <?php selected($ai_service, 'claude'); ?>>Claude (Paid)</option>
                    </select>
                    <p class="description">Choose your preferred AI service for content generation</p>
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="ai_key">API Key *</label>
                </th>
                <td>
                    <input type="password" id="ai_key" name="ai_key" 
                           value="<?php echo esc_attr($ai_key); ?>" 
                           class="regular-text" required />
                    <p class="description">
                        Get your API key from:
                        <br>• <a href="https://aistudio.google.com/" target="_blank">Google AI Studio</a> (Gemini)
                        <br>• <a href="https://platform.openai.com/" target="_blank">OpenAI Platform</a> (OpenAI)
                        <br>• <a href="https://console.anthropic.com/" target="_blank">Anthropic Console</a> (Claude)
                    </p>
                </td>
            </tr>
        </table>
        
        <?php wp_nonce_field('tbp_quick_setup', 'tbp_nonce'); ?>
        <p class="submit">
            <input type="submit" name="setup_plugin" class="button-primary" value="🚀 Configure Plugin" />
        </p>
    </form>
</div>

<div class="card">
    <h2>📋 Current Status</h2>
    
    <h3>Plugin Status</h3>
    <p>
        <?php if (class_exists('TelegramBlogPublisher')): ?>
            ✅ <strong>Active</strong> - Plugin is installed and active
        <?php else: ?>
            ❌ <strong>Inactive</strong> - Plugin is not active
        <?php endif; ?>
    </p>
    
    <h3>Webhook Configuration</h3>
    <p>
        <?php if (!empty($webhook_secret)): ?>
            ✅ <strong>Configured</strong> - Webhook secret is set
        <?php else: ?>
            ❌ <strong>Not Set</strong> - Webhook secret needs to be configured
        <?php endif; ?>
    </p>
    
    <h3>AI Service</h3>
    <p>
        <?php if (!empty($ai_key)): ?>
            ✅ <strong>Configured</strong> - <?php echo ucfirst($ai_service); ?> API key is set
        <?php else: ?>
            ❌ <strong>Not Set</strong> - No AI service configured
        <?php endif; ?>
    </p>
    
    <h3>Webhook URL</h3>
    <p>
        <code><?php echo esc_html(get_rest_url() . 'telegram-blog-publisher/v1/webhook'); ?></code>
        <button onclick="navigator.clipboard.writeText('<?php echo esc_js(get_rest_url() . 'telegram-blog-publisher/v1/webhook'); ?>')" class="button">Copy</button>
    </p>
</div>

<div class="card">
    <h2>🎯 Next Steps</h2>
    <ol>
        <li><strong>Configure the plugin</strong> using the form above</li>
        <li><strong>Test your API key</strong> in the plugin settings</li>
        <li><strong>Set up n8n workflow</strong> using the webhook URL</li>
        <li><strong>Create a Telegram bot</strong> for automation</li>
        <li><strong>Test the complete flow</strong> end-to-end</li>
    </ol>
    
    <p>
        <a href="<?php echo admin_url('admin.php?page=telegram-blog-publisher'); ?>" class="button button-primary">
            Go to Plugin Dashboard
        </a>
        <a href="<?php echo admin_url('admin.php?page=telegram-blog-publisher-settings'); ?>" class="button">
            Advanced Settings
        </a>
    </p>
</div>

<style>
.card {
    background: #fff;
    border: 1px solid #ccd0d4;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    margin: 20px 0;
    padding: 20px;
}

.card h2 {
    margin-top: 0;
    color: #23282d;
}

.form-table th {
    width: 200px;
    padding: 20px 10px 20px 0;
}

.form-table td {
    padding: 15px 10px;
}

.description {
    font-style: italic;
    color: #666;
}

.notice {
    padding: 12px;
    margin: 15px 0;
    border-left: 4px solid #00a0d2;
    background: #f7fcfe;
}

.notice.notice-success {
    border-left-color: #46b450;
    background: #f7fcf7;
}

.notice.notice-error {
    border-left-color: #dc3232;
    background: #fcf7f7;
}
</style>

<?php
echo '</div>';
?>
