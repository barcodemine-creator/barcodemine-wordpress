<?php
/**
 * Notifications for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Notifications class handling all notification operations
 */
class Notifications {
    
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
     * Initialize notifications
     */
    private function init() {
        add_action('init', array($this, 'initNotifications'));
        add_action('wp_loaded', array($this, 'setupNotifications'));
    }
    
    /**
     * Initialize notifications
     */
    public function initNotifications() {
        // Set up notification hooks
        add_action('kbes_threat_detected', array($this, 'sendThreatNotification'));
        add_action('kbes_security_alert', array($this, 'sendSecurityAlert'));
        add_action('kbes_scan_completed', array($this, 'sendScanCompleteNotification'));
    }
    
    /**
     * Setup notifications
     */
    public function setupNotifications() {
        // Setup notification channels
        $this->setupEmailNotifications();
        $this->setupSlackNotifications();
        $this->setupWebhookNotifications();
    }
    
    /**
     * Setup email notifications
     */
    private function setupEmailNotifications() {
        if (get_option('kbes_email_notifications_enabled', false)) {
            add_action('kbes_notification_email', array($this, 'sendEmailNotification'), 10, 2);
        }
    }
    
    /**
     * Setup Slack notifications
     */
    private function setupSlackNotifications() {
        if (get_option('kbes_slack_notifications_enabled', false)) {
            add_action('kbes_notification_slack', array($this, 'sendSlackNotification'), 10, 2);
        }
    }
    
    /**
     * Setup webhook notifications
     */
    private function setupWebhookNotifications() {
        if (get_option('kbes_webhook_notifications_enabled', false)) {
            add_action('kbes_notification_webhook', array($this, 'sendWebhookNotification'), 10, 2);
        }
    }
    
    /**
     * Send threat notification
     */
    public function sendThreatNotification($data) {
        $this->sendNotification('threat_detected', $data);
    }
    
    /**
     * Send security alert
     */
    public function sendSecurityAlert($data) {
        $this->sendNotification('security_alert', $data);
    }
    
    /**
     * Send scan complete notification
     */
    public function sendScanCompleteNotification($data) {
        $this->sendNotification('scan_completed', $data);
    }
    
    /**
     * Send notification
     */
    private function sendNotification($type, $data) {
        // Send email notification
        if (get_option('kbes_email_notifications_enabled', false)) {
            $this->sendEmailNotification($type, $data);
        }
        
        // Send Slack notification
        if (get_option('kbes_slack_notifications_enabled', false)) {
            $this->sendSlackNotification($type, $data);
        }
        
        // Send webhook notification
        if (get_option('kbes_webhook_notifications_enabled', false)) {
            $this->sendWebhookNotification($type, $data);
        }
        
        // Log notification
        $this->logNotification($type, $data);
    }
    
    /**
     * Send email notification
     */
    public function sendEmailNotification($type, $data) {
        $email_address = get_option('kbes_notification_email');
        
        if (!$email_address) {
            return;
        }
        
        $subject = $this->getEmailSubject($type, $data);
        $message = $this->getEmailMessage($type, $data);
        
        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>'
        );
        
        wp_mail($email_address, $subject, $message, $headers);
    }
    
    /**
     * Send Slack notification
     */
    public function sendSlackNotification($type, $data) {
        $webhook_url = get_option('kbes_slack_webhook_url');
        
        if (!$webhook_url) {
            return;
        }
        
        $message = $this->getSlackMessage($type, $data);
        
        wp_remote_post($webhook_url, array(
            'body' => json_encode($message),
            'headers' => array('Content-Type' => 'application/json')
        ));
    }
    
    /**
     * Send webhook notification
     */
    public function sendWebhookNotification($type, $data) {
        $webhook_url = get_option('kbes_webhook_url');
        
        if (!$webhook_url) {
            return;
        }
        
        $payload = array(
            'type' => $type,
            'data' => $data,
            'timestamp' => current_time('mysql'),
            'site_url' => get_site_url()
        );
        
        wp_remote_post($webhook_url, array(
            'body' => json_encode($payload),
            'headers' => array('Content-Type' => 'application/json')
        ));
    }
    
    /**
     * Get email subject
     */
    private function getEmailSubject($type, $data) {
        $subjects = array(
            'threat_detected' => 'Security Threat Detected - ' . get_bloginfo('name'),
            'security_alert' => 'Security Alert - ' . get_bloginfo('name'),
            'scan_completed' => 'Security Scan Completed - ' . get_bloginfo('name')
        );
        
        return $subjects[$type] ?? 'Security Notification - ' . get_bloginfo('name');
    }
    
    /**
     * Get email message
     */
    private function getEmailMessage($type, $data) {
        $message = '<html><body>';
        $message .= '<h2>Security Notification</h2>';
        $message .= '<p><strong>Type:</strong> ' . ucfirst(str_replace('_', ' ', $type)) . '</p>';
        $message .= '<p><strong>Time:</strong> ' . current_time('mysql') . '</p>';
        $message .= '<p><strong>Site:</strong> ' . get_site_url() . '</p>';
        
        if (!empty($data['threat_type'])) {
            $message .= '<p><strong>Threat Type:</strong> ' . $data['threat_type'] . '</p>';
        }
        
        if (!empty($data['ip'])) {
            $message .= '<p><strong>IP Address:</strong> ' . $data['ip'] . '</p>';
        }
        
        if (!empty($data['description'])) {
            $message .= '<p><strong>Description:</strong> ' . $data['description'] . '</p>';
        }
        
        if (!empty($data['severity'])) {
            $message .= '<p><strong>Severity:</strong> ' . ucfirst($data['severity']) . '</p>';
        }
        
        $message .= '<p>Please review this notification and take appropriate action if necessary.</p>';
        $message .= '<p>Best regards,<br>Kloudbean Enterprise Security Suite</p>';
        $message .= '</body></html>';
        
        return $message;
    }
    
    /**
     * Get Slack message
     */
    private function getSlackMessage($type, $data) {
        $color = $this->getSlackColor($data['severity'] ?? 'medium');
        
        $message = array(
            'text' => 'Security Notification',
            'attachments' => array(
                array(
                    'color' => $color,
                    'fields' => array(
                        array(
                            'title' => 'Type',
                            'value' => ucfirst(str_replace('_', ' ', $type)),
                            'short' => true
                        ),
                        array(
                            'title' => 'Time',
                            'value' => current_time('mysql'),
                            'short' => true
                        ),
                        array(
                            'title' => 'Site',
                            'value' => get_site_url(),
                            'short' => false
                        )
                    )
                )
            )
        );
        
        if (!empty($data['threat_type'])) {
            $message['attachments'][0]['fields'][] = array(
                'title' => 'Threat Type',
                'value' => $data['threat_type'],
                'short' => true
            );
        }
        
        if (!empty($data['ip'])) {
            $message['attachments'][0]['fields'][] = array(
                'title' => 'IP Address',
                'value' => $data['ip'],
                'short' => true
            );
        }
        
        if (!empty($data['description'])) {
            $message['attachments'][0]['fields'][] = array(
                'title' => 'Description',
                'value' => $data['description'],
                'short' => false
            );
        }
        
        if (!empty($data['severity'])) {
            $message['attachments'][0]['fields'][] = array(
                'title' => 'Severity',
                'value' => ucfirst($data['severity']),
                'short' => true
            );
        }
        
        return $message;
    }
    
    /**
     * Get Slack color
     */
    private function getSlackColor($severity) {
        $colors = array(
            'low' => 'good',
            'medium' => 'warning',
            'high' => 'danger',
            'critical' => 'danger'
        );
        
        return $colors[$severity] ?? 'warning';
    }
    
    /**
     * Log notification
     */
    private function logNotification($type, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_notifications';
        
        $wpdb->insert(
            $table_name,
            array(
                'notification_type' => $type,
                'title' => $this->getEmailSubject($type, $data),
                'message' => $this->getEmailMessage($type, $data),
                'severity' => $data['severity'] ?? 'medium',
                'status' => 'sent',
                'recipient_email' => get_option('kbes_notification_email'),
                'recipient_user_id' => get_current_user_id(),
                'sent_at' => current_time('mysql'),
                'created_at' => current_time('mysql')
            ),
            array(
                '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s'
            )
        );
    }
    
    /**
     * Get notifications
     */
    public function getNotifications($filters = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_notifications';
        
        $where_clause = '';
        $params = array();
        
        if (!empty($filters['notification_type'])) {
            $where_clause .= ' AND notification_type = %s';
            $params[] = $filters['notification_type'];
        }
        
        if (!empty($filters['severity'])) {
            $where_clause .= ' AND severity = %s';
            $params[] = $filters['severity'];
        }
        
        if (!empty($filters['status'])) {
            $where_clause .= ' AND status = %s';
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['start_date'])) {
            $where_clause .= ' AND created_at >= %s';
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $where_clause .= ' AND created_at <= %s';
            $params[] = $filters['end_date'];
        }
        
        $limit = $filters['limit'] ?? 20;
        $offset = $filters['offset'] ?? 0;
        
        $query = "SELECT * FROM $table_name WHERE 1=1 $where_clause ORDER BY created_at DESC LIMIT %d OFFSET %d";
        $params[] = $limit;
        $params[] = $offset;
        
        return $wpdb->get_results($wpdb->prepare($query, $params));
    }
    
    /**
     * Get notification
     */
    public function getNotification($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_notifications';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $id
        ));
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_notifications';
        
        $wpdb->update(
            $table_name,
            array('status' => 'read'),
            array('id' => $id),
            array('%s'),
            array('%d')
        );
    }
    
    /**
     * Delete notification
     */
    public function deleteNotification($id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_notifications';
        
        $wpdb->delete(
            $table_name,
            array('id' => $id),
            array('%d')
        );
    }
    
    /**
     * Clear old notifications
     */
    public function clearOldNotifications($older_than_days = 30) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_notifications';
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$older_than_days} days"));
        
        $wpdb->query($wpdb->prepare(
            "DELETE FROM $table_name WHERE created_at < %s",
            $cutoff_date
        ));
    }
}
