<?php
/**
 * Integrations for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Integrations class handling third-party integrations
 */
class Integrations {
    
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
     * Initialize integrations
     */
    private function init() {
        add_action('init', array($this, 'initIntegrations'));
        add_action('wp_loaded', array($this, 'loadIntegrations'));
    }
    
    /**
     * Initialize integrations
     */
    public function initIntegrations() {
        // Set up integration hooks
        add_action('wp_loaded', array($this, 'loadCloudflareIntegration'));
        add_action('wp_loaded', array($this, 'loadSlackIntegration'));
        add_action('wp_loaded', array($this, 'loadEmailIntegration'));
        add_action('wp_loaded', array($this, 'loadWebhookIntegration'));
    }
    
    /**
     * Load integrations
     */
    public function loadIntegrations() {
        // Load active integrations
        $this->loadActiveIntegrations();
    }
    
    /**
     * Load Cloudflare integration
     */
    public function loadCloudflareIntegration() {
        if (get_option('kbes_cloudflare_enabled', false)) {
            $this->initCloudflareIntegration();
        }
    }
    
    /**
     * Load Slack integration
     */
    public function loadSlackIntegration() {
        if (get_option('kbes_slack_enabled', false)) {
            $this->initSlackIntegration();
        }
    }
    
    /**
     * Load email integration
     */
    public function loadEmailIntegration() {
        if (get_option('kbes_email_enabled', false)) {
            $this->initEmailIntegration();
        }
    }
    
    /**
     * Load webhook integration
     */
    public function loadWebhookIntegration() {
        if (get_option('kbes_webhook_enabled', false)) {
            $this->initWebhookIntegration();
        }
    }
    
    /**
     * Load active integrations
     */
    private function loadActiveIntegrations() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_integrations';
        $integrations = $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'active'");
        
        foreach ($integrations as $integration) {
            $this->loadIntegration($integration);
        }
    }
    
    /**
     * Load integration
     */
    private function loadIntegration($integration) {
        switch ($integration->integration_type) {
            case 'cloudflare':
                $this->initCloudflareIntegration($integration);
                break;
            case 'slack':
                $this->initSlackIntegration($integration);
                break;
            case 'email':
                $this->initEmailIntegration($integration);
                break;
            case 'webhook':
                $this->initWebhookIntegration($integration);
                break;
        }
    }
    
    /**
     * Initialize Cloudflare integration
     */
    private function initCloudflareIntegration($integration = null) {
        // Cloudflare integration logic
        add_action('kbes_threat_detected', array($this, 'sendToCloudflare'));
        add_action('kbes_ip_blocked', array($this, 'sendToCloudflare'));
    }
    
    /**
     * Initialize Slack integration
     */
    private function initSlackIntegration($integration = null) {
        // Slack integration logic
        add_action('kbes_threat_detected', array($this, 'sendToSlack'));
        add_action('kbes_security_alert', array($this, 'sendToSlack'));
    }
    
    /**
     * Initialize email integration
     */
    private function initEmailIntegration($integration = null) {
        // Email integration logic
        add_action('kbes_threat_detected', array($this, 'sendEmail'));
        add_action('kbes_security_alert', array($this, 'sendEmail'));
    }
    
    /**
     * Initialize webhook integration
     */
    private function initWebhookIntegration($integration = null) {
        // Webhook integration logic
        add_action('kbes_threat_detected', array($this, 'sendWebhook'));
        add_action('kbes_security_alert', array($this, 'sendWebhook'));
    }
    
    /**
     * Send to Cloudflare
     */
    public function sendToCloudflare($data) {
        $api_key = get_option('kbes_cloudflare_api_key');
        $zone_id = get_option('kbes_cloudflare_zone_id');
        
        if (!$api_key || !$zone_id) {
            return;
        }
        
        // Send threat data to Cloudflare
        $this->sendCloudflareRequest($api_key, $zone_id, $data);
    }
    
    /**
     * Send to Slack
     */
    public function sendToSlack($data) {
        $webhook_url = get_option('kbes_slack_webhook_url');
        
        if (!$webhook_url) {
            return;
        }
        
        // Send alert to Slack
        $this->sendSlackMessage($webhook_url, $data);
    }
    
    /**
     * Send email
     */
    public function sendEmail($data) {
        $email_address = get_option('kbes_email_address');
        
        if (!$email_address) {
            return;
        }
        
        // Send email alert
        $this->sendEmailAlert($email_address, $data);
    }
    
    /**
     * Send webhook
     */
    public function sendWebhook($data) {
        $webhook_url = get_option('kbes_webhook_url');
        
        if (!$webhook_url) {
            return;
        }
        
        // Send webhook request
        $this->sendWebhookRequest($webhook_url, $data);
    }
    
    /**
     * Send Cloudflare request
     */
    private function sendCloudflareRequest($api_key, $zone_id, $data) {
        $url = "https://api.cloudflare.com/client/v4/zones/{$zone_id}/firewall/access_rules/rules";
        
        $headers = array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json'
        );
        
        $body = array(
            'mode' => 'block',
            'configuration' => array(
                'target' => 'ip',
                'value' => $data['ip'] ?? ''
            ),
            'notes' => 'Blocked by Kloudbean Enterprise Security: ' . ($data['reason'] ?? 'Threat detected')
        );
        
        wp_remote_post($url, array(
            'headers' => $headers,
            'body' => json_encode($body)
        ));
    }
    
    /**
     * Send Slack message
     */
    private function sendSlackMessage($webhook_url, $data) {
        $message = array(
            'text' => 'Security Alert',
            'attachments' => array(
                array(
                    'color' => 'danger',
                    'fields' => array(
                        array(
                            'title' => 'Threat Type',
                            'value' => $data['threat_type'] ?? 'Unknown',
                            'short' => true
                        ),
                        array(
                            'title' => 'IP Address',
                            'value' => $data['ip'] ?? 'Unknown',
                            'short' => true
                        ),
                        array(
                            'title' => 'Description',
                            'value' => $data['description'] ?? 'No description available',
                            'short' => false
                        ),
                        array(
                            'title' => 'Timestamp',
                            'value' => $data['timestamp'] ?? current_time('mysql'),
                            'short' => true
                        )
                    )
                )
            )
        );
        
        wp_remote_post($webhook_url, array(
            'body' => json_encode($message),
            'headers' => array('Content-Type' => 'application/json')
        ));
    }
    
    /**
     * Send email alert
     */
    private function sendEmailAlert($email_address, $data) {
        $subject = 'Security Alert - ' . get_bloginfo('name');
        $message = $this->buildEmailMessage($data);
        
        wp_mail($email_address, $subject, $message);
    }
    
    /**
     * Build email message
     */
    private function buildEmailMessage($data) {
        $message = "Security Alert\n\n";
        $message .= "Threat Type: " . ($data['threat_type'] ?? 'Unknown') . "\n";
        $message .= "IP Address: " . ($data['ip'] ?? 'Unknown') . "\n";
        $message .= "Description: " . ($data['description'] ?? 'No description available') . "\n";
        $message .= "Timestamp: " . ($data['timestamp'] ?? current_time('mysql')) . "\n";
        $message .= "\nPlease review this alert and take appropriate action.\n";
        $message .= "\nBest regards,\nKloudbean Enterprise Security Suite";
        
        return $message;
    }
    
    /**
     * Send webhook request
     */
    private function sendWebhookRequest($webhook_url, $data) {
        wp_remote_post($webhook_url, array(
            'body' => json_encode($data),
            'headers' => array('Content-Type' => 'application/json')
        ));
    }
    
    /**
     * Add integration
     */
    public function addIntegration($name, $type, $config) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_integrations';
        
        $wpdb->insert(
            $table_name,
            array(
                'integration_name' => $name,
                'integration_type' => $type,
                'status' => 'active',
                'config' => json_encode($config),
                'created_at' => current_time('mysql')
            ),
            array(
                '%s', '%s', '%s', '%s', '%s'
            )
        );
    }
    
    /**
     * Update integration
     */
    public function updateIntegration($id, $config) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_integrations';
        
        $wpdb->update(
            $table_name,
            array(
                'config' => json_encode($config),
                'updated_at' => current_time('mysql')
            ),
            array('id' => $id),
            array('%s', '%s'),
            array('%d')
        );
    }
    
    /**
     * Remove integration
     */
    public function removeIntegration($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_integrations';
        
        $wpdb->update(
            $table_name,
            array(
                'status' => 'inactive',
                'updated_at' => current_time('mysql')
            ),
            array('id' => $id),
            array('%s', '%s'),
            array('%d')
        );
    }
    
    /**
     * Get integrations
     */
    public function getIntegrations() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_integrations';
        
        return $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
    }
    
    /**
     * Get integration
     */
    public function getIntegration($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_integrations';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Test integration
     */
    public function testIntegration($id) {
        $integration = $this->getIntegration($id);
        
        if (!$integration) {
            return false;
        }
        
        $test_data = array(
            'threat_type' => 'test',
            'ip' => '127.0.0.1',
            'description' => 'Integration test',
            'timestamp' => current_time('mysql')
        );
        
        switch ($integration->integration_type) {
            case 'cloudflare':
                $this->sendToCloudflare($test_data);
                break;
            case 'slack':
                $this->sendToSlack($test_data);
                break;
            case 'email':
                $this->sendEmail($test_data);
                break;
            case 'webhook':
                $this->sendWebhook($test_data);
                break;
        }
        
        return true;
    }
}
