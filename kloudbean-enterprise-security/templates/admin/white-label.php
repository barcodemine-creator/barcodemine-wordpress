<?php
/**
 * White Label Settings Template
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

$white_label_settings = get_option('kbes_white_label_settings', array());
?>

<div class="wrap">
    <h1><?php esc_html_e('White Label Settings', 'kloudbean-enterprise-security'); ?></h1>
    <p><?php esc_html_e('Customize the appearance and branding of the security plugin to match your brand.', 'kloudbean-enterprise-security'); ?></p>
    
    <form method="post" action="options.php" id="kbes-white-label-form">
        <?php settings_fields('kbes_white_label_settings'); ?>
        
        <div class="kbes-white-label-settings">
            <div class="kbes-settings-section">
                <h2><?php esc_html_e('Branding', 'kloudbean-enterprise-security'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="plugin_name"><?php esc_html_e('Plugin Name', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="kbes_white_label_settings[plugin_name]" id="plugin_name" 
                                   value="<?php echo esc_attr($white_label_settings['plugin_name'] ?? 'Kloudbean Enterprise Security'); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Custom name for the plugin in admin menus and pages.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="plugin_description"><?php esc_html_e('Plugin Description', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <textarea name="kbes_white_label_settings[plugin_description]" id="plugin_description" 
                                      rows="3" cols="50" class="large-text"><?php echo esc_textarea($white_label_settings['plugin_description'] ?? 'Advanced WordPress security plugin with comprehensive protection features.'); ?></textarea>
                            <p class="description"><?php esc_html_e('Description shown in plugin details and admin pages.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="company_name"><?php esc_html_e('Company Name', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="kbes_white_label_settings[company_name]" id="company_name" 
                                   value="<?php echo esc_attr($white_label_settings['company_name'] ?? ''); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Your company name for branding purposes.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="company_url"><?php esc_html_e('Company URL', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <input type="url" name="kbes_white_label_settings[company_url]" id="company_url" 
                                   value="<?php echo esc_attr($white_label_settings['company_url'] ?? ''); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Your company website URL.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="kbes-settings-section">
                <h2><?php esc_html_e('Logo & Branding', 'kloudbean-enterprise-security'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="logo_url"><?php esc_html_e('Logo URL', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <input type="url" name="kbes_white_label_settings[logo_url]" id="logo_url" 
                                   value="<?php echo esc_attr($white_label_settings['logo_url'] ?? ''); ?>" 
                                   class="regular-text">
                            <button type="button" class="button kbes-upload-logo"><?php esc_html_e('Upload Logo', 'kloudbean-enterprise-security'); ?></button>
                            <p class="description"><?php esc_html_e('URL to your company logo. Recommended size: 200x50px.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="favicon_url"><?php esc_html_e('Favicon URL', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <input type="url" name="kbes_white_label_settings[favicon_url]" id="favicon_url" 
                                   value="<?php echo esc_attr($white_label_settings['favicon_url'] ?? ''); ?>" 
                                   class="regular-text">
                            <button type="button" class="button kbes-upload-favicon"><?php esc_html_e('Upload Favicon', 'kloudbean-enterprise-security'); ?></button>
                            <p class="description"><?php esc_html_e('URL to your favicon. Recommended size: 32x32px.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="primary_color"><?php esc_html_e('Primary Color', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <input type="color" name="kbes_white_label_settings[primary_color]" id="primary_color" 
                                   value="<?php echo esc_attr($white_label_settings['primary_color'] ?? '#00a32a'); ?>" 
                                   class="kbes-color-picker">
                            <p class="description"><?php esc_html_e('Primary color for the admin interface.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="secondary_color"><?php esc_html_e('Secondary Color', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <input type="color" name="kbes_white_label_settings[secondary_color]" id="secondary_color" 
                                   value="<?php echo esc_attr($white_label_settings['secondary_color'] ?? '#2271b1'); ?>" 
                                   class="kbes-color-picker">
                            <p class="description"><?php esc_html_e('Secondary color for the admin interface.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="kbes-settings-section">
                <h2><?php esc_html_e('Admin Interface', 'kloudbean-enterprise-security'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="hide_kloudbean_branding"><?php esc_html_e('Hide Kloudbean Branding', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="kbes_white_label_settings[hide_kloudbean_branding]" id="hide_kloudbean_branding" 
                                       value="1" <?php checked($white_label_settings['hide_kloudbean_branding'] ?? false); ?>>
                                <?php esc_html_e('Hide Kloudbean branding from admin interface', 'kloudbean-enterprise-security'); ?>
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="custom_admin_css"><?php esc_html_e('Custom Admin CSS', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <textarea name="kbes_white_label_settings[custom_admin_css]" id="custom_admin_css" 
                                      rows="10" cols="50" class="large-text code"><?php echo esc_textarea($white_label_settings['custom_admin_css'] ?? ''); ?></textarea>
                            <p class="description"><?php esc_html_e('Custom CSS to override admin interface styles.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="custom_footer_text"><?php esc_html_e('Custom Footer Text', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="kbes_white_label_settings[custom_footer_text]" id="custom_footer_text" 
                                   value="<?php echo esc_attr($white_label_settings['custom_footer_text'] ?? ''); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Custom footer text for admin pages.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="kbes-settings-section">
                <h2><?php esc_html_e('Email Templates', 'kloudbean-enterprise-security'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="email_from_name"><?php esc_html_e('Email From Name', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="kbes_white_label_settings[email_from_name]" id="email_from_name" 
                                   value="<?php echo esc_attr($white_label_settings['email_from_name'] ?? get_bloginfo('name')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Name used in email notifications.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="email_from_email"><?php esc_html_e('Email From Address', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <input type="email" name="kbes_white_label_settings[email_from_email]" id="email_from_email" 
                                   value="<?php echo esc_attr($white_label_settings['email_from_email'] ?? get_option('admin_email')); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Email address used in notifications.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="email_template_header"><?php esc_html_e('Email Template Header', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <textarea name="kbes_white_label_settings[email_template_header]" id="email_template_header" 
                                      rows="5" cols="50" class="large-text"><?php echo esc_textarea($white_label_settings['email_template_header'] ?? ''); ?></textarea>
                            <p class="description"><?php esc_html_e('HTML header for email templates. Use {logo_url} for logo placeholder.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="email_template_footer"><?php esc_html_e('Email Template Footer', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <textarea name="kbes_white_label_settings[email_template_footer]" id="email_template_footer" 
                                      rows="5" cols="50" class="large-text"><?php echo esc_textarea($white_label_settings['email_template_footer'] ?? ''); ?></textarea>
                            <p class="description"><?php esc_html_e('HTML footer for email templates.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="kbes-settings-section">
                <h2><?php esc_html_e('Reports & Exports', 'kloudbean-enterprise-security'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="report_company_name"><?php esc_html_e('Report Company Name', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="kbes_white_label_settings[report_company_name]" id="report_company_name" 
                                   value="<?php echo esc_attr($white_label_settings['report_company_name'] ?? $white_label_settings['company_name'] ?? ''); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Company name shown in security reports.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="report_company_address"><?php esc_html_e('Report Company Address', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <textarea name="kbes_white_label_settings[report_company_address]" id="report_company_address" 
                                      rows="3" cols="50" class="large-text"><?php echo esc_textarea($white_label_settings['report_company_address'] ?? ''); ?></textarea>
                            <p class="description"><?php esc_html_e('Company address shown in security reports.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="report_company_phone"><?php esc_html_e('Report Company Phone', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <input type="text" name="kbes_white_label_settings[report_company_phone]" id="report_company_phone" 
                                   value="<?php echo esc_attr($white_label_settings['report_company_phone'] ?? ''); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Company phone number shown in security reports.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="report_company_email"><?php esc_html_e('Report Company Email', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <input type="email" name="kbes_white_label_settings[report_company_email]" id="report_company_email" 
                                   value="<?php echo esc_attr($white_label_settings['report_company_email'] ?? ''); ?>" 
                                   class="regular-text">
                            <p class="description"><?php esc_html_e('Company email shown in security reports.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="kbes-settings-section">
                <h2><?php esc_html_e('Advanced Settings', 'kloudbean-enterprise-security'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="custom_js"><?php esc_html_e('Custom JavaScript', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <textarea name="kbes_white_label_settings[custom_js]" id="custom_js" 
                                      rows="10" cols="50" class="large-text code"><?php echo esc_textarea($white_label_settings['custom_js'] ?? ''); ?></textarea>
                            <p class="description"><?php esc_html_e('Custom JavaScript for admin interface enhancements.', 'kloudbean-enterprise-security'); ?></p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="hide_version_info"><?php esc_html_e('Hide Version Information', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="kbes_white_label_settings[hide_version_info]" id="hide_version_info" 
                                       value="1" <?php checked($white_label_settings['hide_version_info'] ?? false); ?>>
                                <?php esc_html_e('Hide version information from admin interface', 'kloudbean-enterprise-security'); ?>
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="hide_help_links"><?php esc_html_e('Hide Help Links', 'kloudbean-enterprise-security'); ?></label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" name="kbes_white_label_settings[hide_help_links]" id="hide_help_links" 
                                       value="1" <?php checked($white_label_settings['hide_help_links'] ?? false); ?>>
                                <?php esc_html_e('Hide help and documentation links', 'kloudbean-enterprise-security'); ?>
                            </label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="kbes-settings-actions">
            <?php submit_button(__('Save White Label Settings', 'kloudbean-enterprise-security'), 'primary', 'submit', false); ?>
            <button type="button" class="button kbes-reset-white-label"><?php esc_html_e('Reset to Defaults', 'kloudbean-enterprise-security'); ?></button>
            <button type="button" class="button kbes-preview-white-label"><?php esc_html_e('Preview Changes', 'kloudbean-enterprise-security'); ?></button>
        </div>
    </form>
</div>

<style>
.kbes-white-label-settings {
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    margin: 20px 0;
}

.kbes-settings-section {
    padding: 20px;
    border-bottom: 1px solid #f0f0f0;
}

.kbes-settings-section:last-child {
    border-bottom: none;
}

.kbes-settings-section h2 {
    margin-top: 0;
    color: #1d2327;
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 10px;
}

.kbes-color-picker {
    width: 60px;
    height: 40px;
    border: 1px solid #c3c4c7;
    border-radius: 3px;
    cursor: pointer;
}

.kbes-settings-actions {
    margin-top: 20px;
    padding: 20px;
    background: #f9f9f9;
    border: 1px solid #c3c4c7;
    text-align: right;
}

.kbes-settings-actions .button {
    margin-left: 10px;
}

.kbes-upload-logo,
.kbes-upload-favicon {
    margin-left: 10px;
}

.kbes-preview-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 9999;
    display: none;
}

.kbes-preview-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    padding: 30px;
    border-radius: 5px;
    max-width: 80%;
    max-height: 80%;
    overflow: auto;
}

.kbes-preview-close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.kbes-preview-close:hover {
    color: #000;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Handle logo upload
    $('.kbes-upload-logo').on('click', function() {
        var frame = wp.media({
            title: 'Select Logo',
            button: {
                text: 'Use Logo'
            },
            multiple: false
        });
        
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#logo_url').val(attachment.url);
        });
        
        frame.open();
    });
    
    // Handle favicon upload
    $('.kbes-upload-favicon').on('click', function() {
        var frame = wp.media({
            title: 'Select Favicon',
            button: {
                text: 'Use Favicon'
            },
            multiple: false
        });
        
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#favicon_url').val(attachment.url);
        });
        
        frame.open();
    });
    
    // Handle reset to defaults
    $('.kbes-reset-white-label').on('click', function() {
        if (confirm('Are you sure you want to reset all white label settings to defaults?')) {
            $('#kbes-white-label-form')[0].reset();
            $('#primary_color').val('#00a32a');
            $('#secondary_color').val('#2271b1');
        }
    });
    
    // Handle preview
    $('.kbes-preview-white-label').on('click', function() {
        var formData = $('#kbes-white-label-form').serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'kbes_preview_white_label',
                nonce: '<?php echo wp_create_nonce('kbes_preview_white_label'); ?>',
                settings: formData
            },
            success: function(response) {
                if (response.success) {
                    $('body').append('<div class="kbes-preview-overlay"><div class="kbes-preview-content"><span class="kbes-preview-close">&times;</span>' + response.data + '</div></div>');
                    $('.kbes-preview-overlay').show();
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred while generating preview.');
            }
        });
    });
    
    // Close preview
    $(document).on('click', '.kbes-preview-close', function() {
        $('.kbes-preview-overlay').remove();
    });
    
    // Close preview on overlay click
    $(document).on('click', '.kbes-preview-overlay', function(e) {
        if (e.target === this) {
            $('.kbes-preview-overlay').remove();
        }
    });
});
</script>
