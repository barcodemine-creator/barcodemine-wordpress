<?php
/**
 * Settings for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Settings class handling all settings operations
 */
class Settings {
    
    private $database;
    private $logging;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->database = new Database();
        $this->logging = new Logging();
        
        $this->init();
    }
    
    /**
     * Initialize settings
     */
    private function init() {
        add_action('init', array($this, 'initSettings'));
    }
    
    /**
     * Initialize settings
     */
    public function initSettings() {
        // Set up settings hooks
        add_action('admin_init', array($this, 'registerSettings'));
        add_action('wp_ajax_kbes_save_settings', array($this, 'ajaxSaveSettings'));
        add_action('wp_ajax_kbes_reset_settings', array($this, 'ajaxResetSettings'));
    }
    
    /**
     * Register settings
     */
    public function registerSettings() {
        // Register security settings
        register_setting('kbes_security_settings', 'kbes_security_level');
        register_setting('kbes_security_settings', 'kbes_rate_limit');
        register_setting('kbes_security_settings', 'kbes_max_login_attempts');
        register_setting('kbes_security_settings', 'kbes_lockout_time');
        register_setting('kbes_security_settings', 'kbes_blocked_countries');
        register_setting('kbes_security_settings', 'kbes_allowed_file_types');
        register_setting('kbes_security_settings', 'kbes_max_file_size');
        register_setting('kbes_security_settings', 'kbes_encryption_enabled');
        register_setting('kbes_security_settings', 'kbes_backup_enabled');
        register_setting('kbes_security_settings', 'kbes_security_enabled');
        
        // Register notification settings
        register_setting('kbes_notification_settings', 'kbes_email_notifications_enabled');
        register_setting('kbes_notification_settings', 'kbes_notification_email');
        register_setting('kbes_notification_settings', 'kbes_slack_notifications_enabled');
        register_setting('kbes_notification_settings', 'kbes_slack_webhook_url');
        register_setting('kbes_notification_settings', 'kbes_webhook_notifications_enabled');
        register_setting('kbes_notification_settings', 'kbes_webhook_url');
        
        // Register integration settings
        register_setting('kbes_integration_settings', 'kbes_cloudflare_enabled');
        register_setting('kbes_integration_settings', 'kbes_cloudflare_api_key');
        register_setting('kbes_integration_settings', 'kbes_cloudflare_zone_id');
    }
    
    /**
     * Get settings
     */
    public function getSettings($group = null) {
        $settings = array();
        
        if ($group === null || $group === 'security') {
            $settings['security'] = array(
                'security_level' => get_option('kbes_security_level', 'medium'),
                'rate_limit' => get_option('kbes_rate_limit', 100),
                'max_login_attempts' => get_option('kbes_max_login_attempts', 5),
                'lockout_time' => get_option('kbes_lockout_time', 900),
                'blocked_countries' => get_option('kbes_blocked_countries', array()),
                'allowed_file_types' => get_option('kbes_allowed_file_types', array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt')),
                'max_file_size' => get_option('kbes_max_file_size', 10485760),
                'encryption_enabled' => get_option('kbes_encryption_enabled', false),
                'backup_enabled' => get_option('kbes_backup_enabled', false),
                'security_enabled' => get_option('kbes_security_enabled', true)
            );
        }
        
        if ($group === null || $group === 'notifications') {
            $settings['notifications'] = array(
                'email_notifications_enabled' => get_option('kbes_email_notifications_enabled', false),
                'notification_email' => get_option('kbes_notification_email', get_option('admin_email')),
                'slack_notifications_enabled' => get_option('kbes_slack_notifications_enabled', false),
                'slack_webhook_url' => get_option('kbes_slack_webhook_url', ''),
                'webhook_notifications_enabled' => get_option('kbes_webhook_notifications_enabled', false),
                'webhook_url' => get_option('kbes_webhook_url', '')
            );
        }
        
        if ($group === null || $group === 'integrations') {
            $settings['integrations'] = array(
                'cloudflare_enabled' => get_option('kbes_cloudflare_enabled', false),
                'cloudflare_api_key' => get_option('kbes_cloudflare_api_key', ''),
                'cloudflare_zone_id' => get_option('kbes_cloudflare_zone_id', '')
            );
        }
        
        return $settings;
    }
    
    /**
     * Update settings
     */
    public function updateSettings($group, $settings) {
        switch ($group) {
            case 'security':
                $this->updateSecuritySettings($settings);
                break;
            case 'notifications':
                $this->updateNotificationSettings($settings);
                break;
            case 'integrations':
                $this->updateIntegrationSettings($settings);
                break;
        }
    }
    
    /**
     * Update security settings
     */
    private function updateSecuritySettings($settings) {
        if (isset($settings['security_level'])) {
            update_option('kbes_security_level', sanitize_text_field($settings['security_level']));
        }
        
        if (isset($settings['rate_limit'])) {
            update_option('kbes_rate_limit', intval($settings['rate_limit']));
        }
        
        if (isset($settings['max_login_attempts'])) {
            update_option('kbes_max_login_attempts', intval($settings['max_login_attempts']));
        }
        
        if (isset($settings['lockout_time'])) {
            update_option('kbes_lockout_time', intval($settings['lockout_time']));
        }
        
        if (isset($settings['blocked_countries'])) {
            update_option('kbes_blocked_countries', array_map('sanitize_text_field', $settings['blocked_countries']));
        }
        
        if (isset($settings['allowed_file_types'])) {
            update_option('kbes_allowed_file_types', array_map('sanitize_text_field', $settings['allowed_file_types']));
        }
        
        if (isset($settings['max_file_size'])) {
            update_option('kbes_max_file_size', intval($settings['max_file_size']));
        }
        
        if (isset($settings['encryption_enabled'])) {
            update_option('kbes_encryption_enabled', (bool) $settings['encryption_enabled']);
        }
        
        if (isset($settings['backup_enabled'])) {
            update_option('kbes_backup_enabled', (bool) $settings['backup_enabled']);
        }
        
        if (isset($settings['security_enabled'])) {
            update_option('kbes_security_enabled', (bool) $settings['security_enabled']);
        }
    }
    
    /**
     * Update notification settings
     */
    private function updateNotificationSettings($settings) {
        if (isset($settings['email_notifications_enabled'])) {
            update_option('kbes_email_notifications_enabled', (bool) $settings['email_notifications_enabled']);
        }
        
        if (isset($settings['notification_email'])) {
            update_option('kbes_notification_email', sanitize_email($settings['notification_email']));
        }
        
        if (isset($settings['slack_notifications_enabled'])) {
            update_option('kbes_slack_notifications_enabled', (bool) $settings['slack_notifications_enabled']);
        }
        
        if (isset($settings['slack_webhook_url'])) {
            update_option('kbes_slack_webhook_url', esc_url_raw($settings['slack_webhook_url']));
        }
        
        if (isset($settings['webhook_notifications_enabled'])) {
            update_option('kbes_webhook_notifications_enabled', (bool) $settings['webhook_notifications_enabled']);
        }
        
        if (isset($settings['webhook_url'])) {
            update_option('kbes_webhook_url', esc_url_raw($settings['webhook_url']));
        }
    }
    
    /**
     * Update integration settings
     */
    private function updateIntegrationSettings($settings) {
        if (isset($settings['cloudflare_enabled'])) {
            update_option('kbes_cloudflare_enabled', (bool) $settings['cloudflare_enabled']);
        }
        
        if (isset($settings['cloudflare_api_key'])) {
            update_option('kbes_cloudflare_api_key', sanitize_text_field($settings['cloudflare_api_key']));
        }
        
        if (isset($settings['cloudflare_zone_id'])) {
            update_option('kbes_cloudflare_zone_id', sanitize_text_field($settings['cloudflare_zone_id']));
        }
    }
    
    /**
     * Set default settings
     */
    public function setDefaults() {
        // Set default security settings
        update_option('kbes_security_level', 'medium');
        update_option('kbes_rate_limit', 100);
        update_option('kbes_max_login_attempts', 5);
        update_option('kbes_lockout_time', 900);
        update_option('kbes_blocked_countries', array());
        update_option('kbes_allowed_file_types', array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'txt'));
        update_option('kbes_max_file_size', 10485760);
        update_option('kbes_encryption_enabled', false);
        update_option('kbes_backup_enabled', false);
        update_option('kbes_security_enabled', true);
        
        // Set default notification settings
        update_option('kbes_email_notifications_enabled', false);
        update_option('kbes_notification_email', get_option('admin_email'));
        update_option('kbes_slack_notifications_enabled', false);
        update_option('kbes_slack_webhook_url', '');
        update_option('kbes_webhook_notifications_enabled', false);
        update_option('kbes_webhook_url', '');
        
        // Set default integration settings
        update_option('kbes_cloudflare_enabled', false);
        update_option('kbes_cloudflare_api_key', '');
        update_option('kbes_cloudflare_zone_id', '');
    }
    
    /**
     * Reset settings
     */
    public function resetSettings() {
        // Reset all settings to defaults
        $this->setDefaults();
        
        // Log settings reset
        $this->logging->logSystemEvent('settings_reset', array(
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id()
        ));
    }
    
    /**
     * Export settings
     */
    public function exportSettings() {
        $settings = $this->getSettings();
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="kbes-settings-' . date('Y-m-d-H-i-s') . '.json"');
        
        echo json_encode($settings, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Import settings
     */
    public function importSettings($settings_file) {
        if (!file_exists($settings_file)) {
            return false;
        }
        
        $settings = json_decode(file_get_contents($settings_file), true);
        
        if (!$settings) {
            return false;
        }
        
        // Import security settings
        if (isset($settings['security'])) {
            $this->updateSecuritySettings($settings['security']);
        }
        
        // Import notification settings
        if (isset($settings['notifications'])) {
            $this->updateNotificationSettings($settings['notifications']);
        }
        
        // Import integration settings
        if (isset($settings['integrations'])) {
            $this->updateIntegrationSettings($settings['integrations']);
        }
        
        // Log settings import
        $this->logging->logSystemEvent('settings_imported', array(
            'timestamp' => current_time('mysql'),
            'user_id' => get_current_user_id()
        ));
        
        return true;
    }
    
    /**
     * AJAX: Save settings
     */
    public function ajaxSaveSettings() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $group = sanitize_text_field($_POST['group']);
        $settings = $_POST['settings'];
        
        $this->updateSettings($group, $settings);
        
        wp_send_json_success(array(
            'message' => 'Settings saved successfully'
        ));
    }
    
    /**
     * AJAX: Reset settings
     */
    public function ajaxResetSettings() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $this->resetSettings();
        
        wp_send_json_success(array(
            'message' => 'Settings reset successfully'
        ));
    }
    
    /**
     * Validate settings
     */
    public function validateSettings($group, $settings) {
        $errors = array();
        
        switch ($group) {
            case 'security':
                $errors = $this->validateSecuritySettings($settings);
                break;
            case 'notifications':
                $errors = $this->validateNotificationSettings($settings);
                break;
            case 'integrations':
                $errors = $this->validateIntegrationSettings($settings);
                break;
        }
        
        return $errors;
    }
    
    /**
     * Validate security settings
     */
    private function validateSecuritySettings($settings) {
        $errors = array();
        
        if (isset($settings['security_level']) && !in_array($settings['security_level'], array('low', 'medium', 'high', 'critical'))) {
            $errors[] = 'Invalid security level';
        }
        
        if (isset($settings['rate_limit']) && (!is_numeric($settings['rate_limit']) || $settings['rate_limit'] < 1)) {
            $errors[] = 'Rate limit must be a positive number';
        }
        
        if (isset($settings['max_login_attempts']) && (!is_numeric($settings['max_login_attempts']) || $settings['max_login_attempts'] < 1)) {
            $errors[] = 'Max login attempts must be a positive number';
        }
        
        if (isset($settings['lockout_time']) && (!is_numeric($settings['lockout_time']) || $settings['lockout_time'] < 1)) {
            $errors[] = 'Lockout time must be a positive number';
        }
        
        if (isset($settings['max_file_size']) && (!is_numeric($settings['max_file_size']) || $settings['max_file_size'] < 1)) {
            $errors[] = 'Max file size must be a positive number';
        }
        
        return $errors;
    }
    
    /**
     * Validate notification settings
     */
    private function validateNotificationSettings($settings) {
        $errors = array();
        
        if (isset($settings['notification_email']) && !is_email($settings['notification_email'])) {
            $errors[] = 'Invalid email address';
        }
        
        if (isset($settings['slack_webhook_url']) && !empty($settings['slack_webhook_url']) && !filter_var($settings['slack_webhook_url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Invalid Slack webhook URL';
        }
        
        if (isset($settings['webhook_url']) && !empty($settings['webhook_url']) && !filter_var($settings['webhook_url'], FILTER_VALIDATE_URL)) {
            $errors[] = 'Invalid webhook URL';
        }
        
        return $errors;
    }
    
    /**
     * Validate integration settings
     */
    private function validateIntegrationSettings($settings) {
        $errors = array();
        
        if (isset($settings['cloudflare_api_key']) && empty($settings['cloudflare_api_key'])) {
            $errors[] = 'Cloudflare API key is required';
        }
        
        if (isset($settings['cloudflare_zone_id']) && empty($settings['cloudflare_zone_id'])) {
            $errors[] = 'Cloudflare zone ID is required';
        }
        
        return $errors;
    }
}
