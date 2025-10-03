<?php
/**
 * Admin Settings Template
 */

$settings = array(
    'webhook_secret' => get_option('tbp_webhook_secret', ''),
    'ai_service' => get_option('tbp_ai_service', 'openai'),
    'ai_api_key' => get_option('tbp_ai_api_key', ''),
    'default_author' => get_option('tbp_default_author', get_current_user_id()),
    'default_category' => get_option('tbp_default_category', ''),
    'auto_publish' => get_option('tbp_auto_publish', false),
);

$users = get_users(array('fields' => array('ID', 'display_name')));
$categories = get_categories(array('hide_empty' => false));
?>

<div class="wrap tbp-settings">
    <h1>⚙️ Telegram Blog Publisher Settings</h1>
    
    <form id="tbp-settings-form">
        <?php wp_nonce_field('tbp_nonce', 'tbp_nonce'); ?>
        
        <div class="tbp-settings-grid">
            <!-- Webhook Settings -->
            <div class="tbp-card">
                <h2>🔗 Webhook Settings</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="webhook_secret">Webhook Secret</label>
                        </th>
                        <td>
                            <input type="text" id="webhook_secret" name="webhook_secret" value="<?php echo esc_attr($settings['webhook_secret']); ?>" class="regular-text" />
                            <p class="description">Secret key for webhook authentication. Keep this secure!</p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- AI Settings -->
            <div class="tbp-card">
                <h2>🤖 AI Settings</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="ai_service">AI Service</label>
                        </th>
                        <td>
                            <select id="ai_service" name="ai_service">
                                <option value="openai" <?php selected($settings['ai_service'], 'openai'); ?>>OpenAI (GPT-3.5)</option>
                                <option value="claude" <?php selected($settings['ai_service'], 'claude'); ?>>Claude (Anthropic)</option>
                                <option value="gemini" <?php selected($settings['ai_service'], 'gemini'); ?>>Gemini (Google)</option>
                            </select>
                            <p class="description">Choose which AI service to use for content generation.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="ai_api_key">API Key</label>
                        </th>
                        <td>
                            <input type="password" id="ai_api_key" name="ai_api_key" value="<?php echo esc_attr($settings['ai_api_key']); ?>" class="regular-text" />
                            <p class="description">
                                <span id="ai-service-info">Enter your OpenAI API key</span>
                                <br>
                                <a href="#" id="api-key-help" target="_blank">How to get API key</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <!-- Default Settings -->
            <div class="tbp-card">
                <h2>📝 Default Post Settings</h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="default_author">Default Author</label>
                        </th>
                        <td>
                            <select id="default_author" name="default_author">
                                <?php foreach ($users as $user): ?>
                                    <option value="<?php echo $user->ID; ?>" <?php selected($settings['default_author'], $user->ID); ?>>
                                        <?php echo esc_html($user->display_name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">Default author for generated posts.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="default_category">Default Category</label>
                        </th>
                        <td>
                            <select id="default_category" name="default_category">
                                <option value="">Select Category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo esc_attr($category->name); ?>" <?php selected($settings['default_category'], $category->name); ?>>
                                        <?php echo esc_html($category->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="description">Default category for generated posts.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="auto_publish">Auto Publish</label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="auto_publish" name="auto_publish" value="1" <?php checked($settings['auto_publish']); ?> />
                                Automatically publish generated posts
                            </label>
                            <p class="description">If unchecked, posts will be saved as drafts for review.</p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="tbp-save-section">
            <button type="submit" class="button button-primary button-large">Save Settings</button>
            <div id="save-result" class="tbp-save-result"></div>
        </div>
    </form>
    
    <!-- API Key Help -->
    <div class="tbp-card tbp-card-full">
        <h2>🔑 API Key Help</h2>
        <div class="tbp-api-help">
            <div class="tbp-api-service" id="openai-help">
                <h3>OpenAI API Key</h3>
                <ol>
                    <li>Go to <a href="https://platform.openai.com/api-keys" target="_blank">OpenAI Platform</a></li>
                    <li>Sign in or create an account</li>
                    <li>Click "Create new secret key"</li>
                    <li>Copy the generated key and paste it above</li>
                    <li>Make sure you have credits in your OpenAI account</li>
                </ol>
            </div>
            
            <div class="tbp-api-service" id="claude-help" style="display: none;">
                <h3>Claude API Key</h3>
                <ol>
                    <li>Go to <a href="https://console.anthropic.com/" target="_blank">Anthropic Console</a></li>
                    <li>Sign in or create an account</li>
                    <li>Navigate to API Keys section</li>
                    <li>Click "Create Key"</li>
                    <li>Copy the generated key and paste it above</li>
                </ol>
            </div>
            
            <div class="tbp-api-service" id="gemini-help" style="display: none;">
                <h3>Gemini API Key</h3>
                <ol>
                    <li>Go to <a href="https://makersuite.google.com/app/apikey" target="_blank">Google AI Studio</a></li>
                    <li>Sign in with your Google account</li>
                    <li>Click "Create API Key"</li>
                    <li>Copy the generated key and paste it above</li>
                    <li>Make sure the Gemini API is enabled in your project</li>
                </ol>
            </div>
        </div>
    </div>
    
    <!-- Webhook Examples -->
    <div class="tbp-card tbp-card-full">
        <h2>📋 Webhook Examples</h2>
        <div class="tbp-webhook-examples">
            <h3>Basic Webhook Request</h3>
            <pre><code>POST <?php echo esc_url(get_rest_url() . 'telegram-blog-publisher/v1/webhook'); ?>

Headers:
Content-Type: application/json
X-Webhook-Secret: <?php echo esc_attr($settings['webhook_secret']); ?>

Body:
{
  "topic": "Benefits of Remote Work",
  "details": "Discuss the advantages of working from home, including increased productivity, better work-life balance, and cost savings for both employees and employers.",
  "category": "Business",
  "tags": "remote work, productivity, work-life balance",
  "status": "draft"
}</code></pre>
            
            <h3>Advanced Webhook Request</h3>
            <pre><code>{
  "topic": "10 Tips for Better Sleep",
  "details": "Create a comprehensive guide about improving sleep quality, including bedtime routines, sleep environment optimization, and lifestyle changes that promote better rest.",
  "category": "Health & Wellness",
  "tags": "sleep, health, wellness, tips",
  "featured_image": "https://example.com/sleep-image.jpg",
  "author_id": 1,
  "status": "publish"
}</code></pre>
            
            <h3>n8n Workflow Example</h3>
            <p>Here's how to set up the webhook in n8n:</p>
            <ol>
                <li>Add a "Telegram Trigger" node</li>
                <li>Configure your bot token</li>
                <li>Add a "Set" node to map the data</li>
                <li>Add an "HTTP Request" node</li>
                <li>Set the URL to your webhook endpoint</li>
                <li>Add the required headers</li>
            </ol>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Update API key help based on selected service
    function updateApiKeyHelp() {
        const service = $('#ai_service').val();
        const info = $('#ai-service-info');
        const help = $('#api-key-help');
        
        $('.tbp-api-service').hide();
        $('#' + service + '-help').show();
        
        switch(service) {
            case 'openai':
                info.text('Enter your OpenAI API key');
                help.attr('href', 'https://platform.openai.com/api-keys');
                break;
            case 'claude':
                info.text('Enter your Claude API key');
                help.attr('href', 'https://console.anthropic.com/');
                break;
            case 'gemini':
                info.text('Enter your Gemini API key');
                help.attr('href', 'https://makersuite.google.com/app/apikey');
                break;
        }
    }
    
    $('#ai_service').on('change', updateApiKeyHelp);
    updateApiKeyHelp();
    
    // Save settings
    $('#tbp-settings-form').on('submit', function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        const saveButton = $(this).find('button[type="submit"]');
        const resultDiv = $('#save-result');
        
        saveButton.prop('disabled', true).text('Saving...');
        resultDiv.html('<div class="tbp-loading">Saving settings...</div>');
        
        $.ajax({
            url: tbp_ajax.ajax_url,
            type: 'POST',
            data: formData + '&action=tbp_save_settings&nonce=' + tbp_ajax.nonce,
            success: function(response) {
                if (response.success) {
                    resultDiv.html('<div class="tbp-success">✅ Settings saved successfully!</div>');
                } else {
                    resultDiv.html('<div class="tbp-error">❌ Failed to save settings: ' + response.data + '</div>');
                }
            },
            error: function() {
                resultDiv.html('<div class="tbp-error">❌ Failed to save settings: Network error</div>');
            },
            complete: function() {
                saveButton.prop('disabled', false).text('Save Settings');
            }
        });
    });
});
</script>
