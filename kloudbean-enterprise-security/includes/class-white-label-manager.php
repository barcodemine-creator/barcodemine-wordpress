<?php
/**
 * White Label Manager for Kloudbean Enterprise Security Suite
 *
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * White Label Manager class handling white-label customization
 */
class WhiteLabelManager {
    private $settings;
    private $default_settings;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->default_settings = $this->getDefaultSettings();
        $this->settings = get_option('kbes_white_label_settings', $this->default_settings);
        $this->init();
    }
    
    /**
     * Initialize white label manager
     */
    private function init() {
        add_action('init', array($this, 'applyWhiteLabelSettings'));
        add_action('admin_init', array($this, 'registerSettings'));
        add_action('wp_ajax_kbes_preview_white_label', array($this, 'previewWhiteLabel'));
        add_action('wp_ajax_kbes_reset_white_label', array($this, 'resetWhiteLabel'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueWhiteLabelAssets'));
        add_action('admin_head', array($this, 'injectCustomCSS'));
        add_action('admin_footer', array($this, 'injectCustomJS'));
        add_filter('plugin_row_meta', array($this, 'modifyPluginMeta'), 10, 2);
        add_filter('all_plugins', array($this, 'modifyPluginList'));
    }
    
    /**
     * Get default white label settings
     */
    private function getDefaultSettings() {
        return array(
            'plugin_name' => 'Kloudbean Enterprise Security',
            'plugin_description' => 'Advanced WordPress security plugin with comprehensive protection features.',
            'company_name' => '',
            'company_url' => '',
            'logo_url' => '',
            'favicon_url' => '',
            'primary_color' => '#00a32a',
            'secondary_color' => '#2271b1',
            'hide_kloudbean_branding' => false,
            'custom_admin_css' => '',
            'custom_footer_text' => '',
            'email_from_name' => get_bloginfo('name'),
            'email_from_email' => get_option('admin_email'),
            'email_template_header' => '',
            'email_template_footer' => '',
            'report_company_name' => '',
            'report_company_address' => '',
            'report_company_phone' => '',
            'report_company_email' => '',
            'custom_js' => '',
            'hide_version_info' => false,
            'hide_help_links' => false
        );
    }
    
    /**
     * Apply white label settings
     */
    public function applyWhiteLabelSettings() {
        // Apply custom CSS
        if (!empty($this->settings['custom_admin_css'])) {
            add_action('admin_head', array($this, 'injectCustomCSS'));
        }
        
        // Apply custom JS
        if (!empty($this->settings['custom_js'])) {
            add_action('admin_footer', array($this, 'injectCustomJS'));
        }
        
        // Modify admin menu
        if (!empty($this->settings['plugin_name'])) {
            add_filter('gettext', array($this, 'modifyPluginName'), 10, 3);
        }
    }
    
    /**
     * Register white label settings
     */
    public function registerSettings() {
        register_setting('kbes_white_label_settings', 'kbes_white_label_settings', array(
            'sanitize_callback' => array($this, 'sanitizeSettings')
        ));
    }
    
    /**
     * Sanitize white label settings
     */
    public function sanitizeSettings($settings) {
        $sanitized = array();
        
        foreach ($this->default_settings as $key => $default_value) {
            if (isset($settings[$key])) {
                switch ($key) {
                    case 'plugin_name':
                    case 'company_name':
                    case 'email_from_name':
                    case 'report_company_name':
                        $sanitized[$key] = sanitize_text_field($settings[$key]);
                        break;
                    case 'plugin_description':
                    case 'custom_admin_css':
                    case 'custom_js':
                    case 'email_template_header':
                    case 'email_template_footer':
                    case 'report_company_address':
                        $sanitized[$key] = wp_kses_post($settings[$key]);
                        break;
                    case 'company_url':
                    case 'logo_url':
                    case 'favicon_url':
                    case 'email_from_email':
                    case 'report_company_email':
                        $sanitized[$key] = esc_url_raw($settings[$key]);
                        break;
                    case 'primary_color':
                    case 'secondary_color':
                        $sanitized[$key] = sanitize_hex_color($settings[$key]);
                        break;
                    case 'custom_footer_text':
                    case 'report_company_phone':
                        $sanitized[$key] = sanitize_text_field($settings[$key]);
                        break;
                    case 'hide_kloudbean_branding':
                    case 'hide_version_info':
                    case 'hide_help_links':
                        $sanitized[$key] = (bool) $settings[$key];
                        break;
                    default:
                        $sanitized[$key] = sanitize_text_field($settings[$key]);
                        break;
                }
            } else {
                $sanitized[$key] = $default_value;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Enqueue white label assets
     */
    public function enqueueWhiteLabelAssets() {
        if (isset($_GET['page']) && strpos($_GET['page'], 'kloudbean-enterprise-security') !== false) {
            wp_enqueue_media();
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_style('wp-color-picker');
        }
    }
    
    /**
     * Inject custom CSS
     */
    public function injectCustomCSS() {
        if (!empty($this->settings['custom_admin_css'])) {
            echo '<style type="text/css">' . $this->settings['custom_admin_css'] . '</style>';
        }
        
        // Apply color scheme
        if (!empty($this->settings['primary_color']) || !empty($this->settings['secondary_color'])) {
            $primary_color = $this->settings['primary_color'] ?? '#00a32a';
            $secondary_color = $this->settings['secondary_color'] ?? '#2271b1';
            
            echo '<style type="text/css">
                .kbes-admin .button-primary { background-color: ' . esc_attr($primary_color) . '; border-color: ' . esc_attr($primary_color) . '; }
                .kbes-admin .button-primary:hover { background-color: ' . esc_attr($this->darkenColor($primary_color, 10)) . '; border-color: ' . esc_attr($this->darkenColor($primary_color, 10)) . '; }
                .kbes-admin .kbes-primary { color: ' . esc_attr($primary_color) . '; }
                .kbes-admin .kbes-secondary { color: ' . esc_attr($secondary_color) . '; }
                .kbes-admin .kbes-bg-primary { background-color: ' . esc_attr($primary_color) . '; }
                .kbes-admin .kbes-bg-secondary { background-color: ' . esc_attr($secondary_color) . '; }
            </style>';
        }
    }
    
    /**
     * Inject custom JavaScript
     */
    public function injectCustomJS() {
        if (!empty($this->settings['custom_js'])) {
            echo '<script type="text/javascript">' . $this->settings['custom_js'] . '</script>';
        }
    }
    
    /**
     * Modify plugin name in admin
     */
    public function modifyPluginName($translated_text, $text, $domain) {
        if ($domain === 'kloudbean-enterprise-security' && $text === 'Kloudbean Enterprise Security') {
            return $this->settings['plugin_name'];
        }
        return $translated_text;
    }
    
    /**
     * Modify plugin meta links
     */
    public function modifyPluginMeta($links, $file) {
        if ($file === 'kloudbean-enterprise-security/kloudbean-enterprise-security.php') {
            if ($this->settings['hide_help_links']) {
                $links = array();
            } else {
                if (!empty($this->settings['company_url'])) {
                    $links[] = '<a href="' . esc_url($this->settings['company_url']) . '" target="_blank">' . __('Support', 'kloudbean-enterprise-security') . '</a>';
                }
            }
        }
        return $links;
    }
    
    /**
     * Modify plugin list
     */
    public function modifyPluginList($plugins) {
        if (isset($plugins['kloudbean-enterprise-security/kloudbean-enterprise-security.php'])) {
            $plugin_file = 'kloudbean-enterprise-security/kloudbean-enterprise-security.php';
            
            if (!empty($this->settings['plugin_name'])) {
                $plugins[$plugin_file]['Name'] = $this->settings['plugin_name'];
            }
            
            if (!empty($this->settings['plugin_description'])) {
                $plugins[$plugin_file]['Description'] = $this->settings['plugin_description'];
            }
            
            if ($this->settings['hide_version_info']) {
                unset($plugins[$plugin_file]['Version']);
            }
        }
        
        return $plugins;
    }
    
    /**
     * Preview white label settings
     */
    public function previewWhiteLabel() {
        check_ajax_referer('kbes_preview_white_label', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        $settings = $_POST['settings'] ?? array();
        $preview_html = $this->generatePreviewHTML($settings);
        
        wp_send_json_success($preview_html);
    }
    
    /**
     * Reset white label settings
     */
    public function resetWhiteLabel() {
        check_ajax_referer('kbes_reset_white_label', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }
        
        update_option('kbes_white_label_settings', $this->default_settings);
        wp_send_json_success('White label settings reset to defaults');
    }
    
    /**
     * Generate preview HTML
     */
    private function generatePreviewHTML($settings) {
        $primary_color = $settings['primary_color'] ?? '#00a32a';
        $secondary_color = $settings['secondary_color'] ?? '#2271b1';
        $logo_url = $settings['logo_url'] ?? '';
        $company_name = $settings['company_name'] ?? '';
        $plugin_name = $settings['plugin_name'] ?? 'Kloudbean Enterprise Security';
        
        ob_start();
        ?>
        <div class="kbes-preview" style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;">
            <div class="kbes-preview-header" style="background: <?php echo esc_attr($primary_color); ?>; color: white; padding: 20px; text-align: center;">
                <?php if ($logo_url): ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($company_name); ?>" style="max-height: 50px; margin-bottom: 10px;">
                <?php endif; ?>
                <h1 style="margin: 0; color: white;"><?php echo esc_html($plugin_name); ?></h1>
                <?php if ($company_name): ?>
                    <p style="margin: 5px 0 0 0; opacity: 0.9;"><?php echo esc_html($company_name); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="kbes-preview-content" style="padding: 30px;">
                <h2 style="color: <?php echo esc_attr($primary_color); ?>; margin-top: 0;">Security Dashboard</h2>
                <p>This is a preview of how your white-labeled security plugin will appear to users.</p>
                
                <div class="kbes-preview-stats" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0;">
                    <div class="kbes-stat-card" style="background: #f9f9f9; padding: 20px; border-radius: 5px; text-align: center; border-left: 4px solid <?php echo esc_attr($primary_color); ?>;">
                        <h3 style="margin: 0; color: <?php echo esc_attr($primary_color); ?>; font-size: 24px;">95</h3>
                        <p style="margin: 5px 0 0 0; color: #666;">Security Score</p>
                    </div>
                    <div class="kbes-stat-card" style="background: #f9f9f9; padding: 20px; border-radius: 5px; text-align: center; border-left: 4px solid <?php echo esc_attr($secondary_color); ?>;">
                        <h3 style="margin: 0; color: <?php echo esc_attr($secondary_color); ?>; font-size: 24px;">12</h3>
                        <p style="margin: 5px 0 0 0; color: #666;">Threats Blocked</p>
                    </div>
                    <div class="kbes-stat-card" style="background: #f9f9f9; padding: 20px; border-radius: 5px; text-align: center; border-left: 4px solid <?php echo esc_attr($primary_color); ?>;">
                        <h3 style="margin: 0; color: <?php echo esc_attr($primary_color); ?>; font-size: 24px;">3</h3>
                        <p style="margin: 5px 0 0 0; color: #666;">Vulnerabilities</p>
                    </div>
                </div>
                
                <div class="kbes-preview-actions" style="margin-top: 30px;">
                    <button style="background: <?php echo esc_attr($primary_color); ?>; color: white; border: none; padding: 10px 20px; border-radius: 3px; cursor: pointer; margin-right: 10px;">Run Security Scan</button>
                    <button style="background: <?php echo esc_attr($secondary_color); ?>; color: white; border: none; padding: 10px 20px; border-radius: 3px; cursor: pointer;">View Reports</button>
                </div>
            </div>
            
            <div class="kbes-preview-footer" style="background: #f0f0f0; padding: 20px; text-align: center; color: #666;">
                <?php if (!empty($settings['custom_footer_text'])): ?>
                    <p style="margin: 0;"><?php echo esc_html($settings['custom_footer_text']); ?></p>
                <?php else: ?>
                    <p style="margin: 0;">Powered by <?php echo esc_html($plugin_name); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Darken color
     */
    private function darkenColor($color, $percent) {
        $color = ltrim($color, '#');
        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));
        
        $r = max(0, $r - ($r * $percent / 100));
        $g = max(0, $g - ($g * $percent / 100));
        $b = max(0, $b - ($b * $percent / 100));
        
        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get white label setting
     */
    public function getSetting($key, $default = null) {
        return $this->settings[$key] ?? $default;
    }
    
    /**
     * Get all white label settings
     */
    public function getAllSettings() {
        return $this->settings;
    }
    
    /**
     * Update white label setting
     */
    public function updateSetting($key, $value) {
        $this->settings[$key] = $value;
        return update_option('kbes_white_label_settings', $this->settings);
    }
    
    /**
     * Reset to defaults
     */
    public function resetToDefaults() {
        $this->settings = $this->default_settings;
        return update_option('kbes_white_label_settings', $this->settings);
    }
    
    /**
     * Get branded plugin name
     */
    public function getBrandedPluginName() {
        return $this->settings['plugin_name'] ?? 'Kloudbean Enterprise Security';
    }
    
    /**
     * Get branded company name
     */
    public function getBrandedCompanyName() {
        return $this->settings['company_name'] ?? '';
    }
    
    /**
     * Get logo URL
     */
    public function getLogoURL() {
        return $this->settings['logo_url'] ?? '';
    }
    
    /**
     * Get primary color
     */
    public function getPrimaryColor() {
        return $this->settings['primary_color'] ?? '#00a32a';
    }
    
    /**
     * Get secondary color
     */
    public function getSecondaryColor() {
        return $this->settings['secondary_color'] ?? '#2271b1';
    }
    
    /**
     * Check if Kloudbean branding should be hidden
     */
    public function shouldHideKloudbeanBranding() {
        return $this->settings['hide_kloudbean_branding'] ?? false;
    }
    
    /**
     * Check if version info should be hidden
     */
    public function shouldHideVersionInfo() {
        return $this->settings['hide_version_info'] ?? false;
    }
    
    /**
     * Check if help links should be hidden
     */
    public function shouldHideHelpLinks() {
        return $this->settings['hide_help_links'] ?? false;
    }
}
