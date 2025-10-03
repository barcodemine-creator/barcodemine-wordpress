<?php
/**
 * Logging for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Logging class handling all logging operations
 */
class Logging {
    
    private $database;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->database = new Database();
        
        $this->init();
    }
    
    /**
     * Initialize logging
     */
    private function init() {
        add_action('init', array($this, 'initLogging'));
        add_action('wp_loaded', array($this, 'startLogging'));
    }
    
    /**
     * Initialize logging
     */
    public function initLogging() {
        // Set up logging hooks
        add_action('wp_loaded', array($this, 'logSecurityEvents'));
        add_action('wp_loaded', array($this, 'logUserActivities'));
        add_action('wp_loaded', array($this, 'logSystemEvents'));
    }
    
    /**
     * Start logging
     */
    public function startLogging() {
        // Start logging operations
        $this->logSystemEvent('logging_started', array(
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log security events
     */
    public function logSecurityEvents() {
        // Log security events
        add_action('kbes_security_event', array($this, 'logSecurityEvent'), 10, 2);
    }
    
    /**
     * Log user activities
     */
    public function logUserActivities() {
        // Log user activities
        add_action('kbes_user_activity', array($this, 'logUserActivity'), 10, 2);
    }
    
    /**
     * Log system events
     */
    public function logSystemEvents() {
        // Log system events
        add_action('kbes_system_event', array($this, 'logSystemEvent'), 10, 2);
    }
    
    /**
     * Log security event
     */
    public function logSecurityEvent($event_type, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_logs';
        
        $wpdb->insert(
            $table_name,
            array(
                'event_type' => $event_type,
                'severity' => $data['severity'] ?? 'medium',
                'message' => $data['message'] ?? '',
                'data' => json_encode($data),
                'ip_address' => $data['ip'] ?? '',
                'user_id' => $data['user_id'] ?? null,
                'user_agent' => $data['user_agent'] ?? '',
                'request_uri' => $data['request_uri'] ?? '',
                'request_method' => $data['request_method'] ?? '',
                'country' => $data['country'] ?? '',
                'timestamp' => $data['timestamp'] ?? current_time('mysql'),
                'resolved' => $data['resolved'] ?? 0,
                'resolved_at' => $data['resolved_at'] ?? null,
                'resolved_by' => $data['resolved_by'] ?? null
            ),
            array(
                '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%d'
            )
        );
    }
    
    /**
     * Log user activity
     */
    public function logUserActivity($activity_type, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_user_activity';
        
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $data['user_id'] ?? get_current_user_id(),
                'activity_type' => $activity_type,
                'activity_description' => $data['description'] ?? '',
                'ip_address' => $data['ip'] ?? $this->getClientIP(),
                'user_agent' => $data['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? '',
                'page_url' => $data['page_url'] ?? $_SERVER['REQUEST_URI'] ?? '',
                'data' => json_encode($data),
                'timestamp' => $data['timestamp'] ?? current_time('mysql')
            ),
            array(
                '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s'
            )
        );
    }
    
    /**
     * Log system event
     */
    public function logSystemEvent($event_type, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_events';
        
        $wpdb->insert(
            $table_name,
            array(
                'event_type' => $event_type,
                'data' => json_encode($data),
                'timestamp' => $data['timestamp'] ?? current_time('mysql'),
                'ip' => $data['ip'] ?? $this->getClientIP(),
                'user_id' => $data['user_id'] ?? get_current_user_id()
            ),
            array(
                '%s', '%s', '%s', '%s', '%d'
            )
        );
    }
    
    /**
     * Get security logs
     */
    public function getSecurityLogs($filters = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_logs';
        
        $where_clause = '';
        $params = array();
        
        if (!empty($filters['event_type'])) {
            $where_clause .= ' AND event_type = %s';
            $params[] = $filters['event_type'];
        }
        
        if (!empty($filters['severity'])) {
            $where_clause .= ' AND severity = %s';
            $params[] = $filters['severity'];
        }
        
        if (!empty($filters['user_id'])) {
            $where_clause .= ' AND user_id = %d';
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['ip_address'])) {
            $where_clause .= ' AND ip_address = %s';
            $params[] = $filters['ip_address'];
        }
        
        if (!empty($filters['start_date'])) {
            $where_clause .= ' AND timestamp >= %s';
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $where_clause .= ' AND timestamp <= %s';
            $params[] = $filters['end_date'];
        }
        
        $limit = $filters['limit'] ?? 20;
        $offset = $filters['offset'] ?? 0;
        
        $query = "SELECT * FROM $table_name WHERE 1=1 $where_clause ORDER BY timestamp DESC LIMIT %d OFFSET %d";
        $params[] = $limit;
        $params[] = $offset;
        
        return $wpdb->get_results($wpdb->prepare($query, $params));
    }
    
    /**
     * Get user activities
     */
    public function getUserActivities($filters = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_user_activity';
        
        $where_clause = '';
        $params = array();
        
        if (!empty($filters['user_id'])) {
            $where_clause .= ' AND user_id = %d';
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['activity_type'])) {
            $where_clause .= ' AND activity_type = %s';
            $params[] = $filters['activity_type'];
        }
        
        if (!empty($filters['start_date'])) {
            $where_clause .= ' AND timestamp >= %s';
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $where_clause .= ' AND timestamp <= %s';
            $params[] = $filters['end_date'];
        }
        
        $limit = $filters['limit'] ?? 20;
        $offset = $filters['offset'] ?? 0;
        
        $query = "SELECT * FROM $table_name WHERE 1=1 $where_clause ORDER BY timestamp DESC LIMIT %d OFFSET %d";
        $params[] = $limit;
        $params[] = $offset;
        
        return $wpdb->get_results($wpdb->prepare($query, $params));
    }
    
    /**
     * Get system events
     */
    public function getSystemEvents($filters = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_events';
        
        $where_clause = '';
        $params = array();
        
        if (!empty($filters['event_type'])) {
            $where_clause .= ' AND event_type = %s';
            $params[] = $filters['event_type'];
        }
        
        if (!empty($filters['user_id'])) {
            $where_clause .= ' AND user_id = %d';
            $params[] = $filters['user_id'];
        }
        
        if (!empty($filters['start_date'])) {
            $where_clause .= ' AND timestamp >= %s';
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $where_clause .= ' AND timestamp <= %s';
            $params[] = $filters['end_date'];
        }
        
        $limit = $filters['limit'] ?? 20;
        $offset = $filters['offset'] ?? 0;
        
        $query = "SELECT * FROM $table_name WHERE 1=1 $where_clause ORDER BY timestamp DESC LIMIT %d OFFSET %d";
        $params[] = $limit;
        $params[] = $offset;
        
        return $wpdb->get_results($wpdb->prepare($query, $params));
    }
    
    /**
     * Clear logs
     */
    public function clearLogs($log_type = 'all', $older_than_days = 30) {
        global $wpdb;
        
        $cutoff_date = date('Y-m-d H:i:s', strtotime("-{$older_than_days} days"));
        
        switch ($log_type) {
            case 'security':
                $table_name = $wpdb->prefix . 'kbes_security_logs';
                $wpdb->query($wpdb->prepare(
                    "DELETE FROM $table_name WHERE timestamp < %s",
                    $cutoff_date
                ));
                break;
            case 'user_activity':
                $table_name = $wpdb->prefix . 'kbes_user_activity';
                $wpdb->query($wpdb->prepare(
                    "DELETE FROM $table_name WHERE timestamp < %s",
                    $cutoff_date
                ));
                break;
            case 'system_events':
                $table_name = $wpdb->prefix . 'kbes_security_events';
                $wpdb->query($wpdb->prepare(
                    "DELETE FROM $table_name WHERE timestamp < %s",
                    $cutoff_date
                ));
                break;
            case 'all':
                $this->clearLogs('security', $older_than_days);
                $this->clearLogs('user_activity', $older_than_days);
                $this->clearLogs('system_events', $older_than_days);
                break;
        }
    }
    
    /**
     * Export logs
     */
    public function exportLogs($log_type = 'all', $format = 'json', $filters = array()) {
        $logs = array();
        
        switch ($log_type) {
            case 'security':
                $logs = $this->getSecurityLogs($filters);
                break;
            case 'user_activity':
                $logs = $this->getUserActivities($filters);
                break;
            case 'system_events':
                $logs = $this->getSystemEvents($filters);
                break;
            case 'all':
                $logs = array(
                    'security' => $this->getSecurityLogs($filters),
                    'user_activity' => $this->getUserActivities($filters),
                    'system_events' => $this->getSystemEvents($filters)
                );
                break;
        }
        
        switch ($format) {
            case 'json':
                return json_encode($logs, JSON_PRETTY_PRINT);
            case 'csv':
                return $this->convertToCSV($logs);
            case 'xml':
                return $this->convertToXML($logs);
            default:
                return $logs;
        }
    }
    
    /**
     * Convert to CSV
     */
    private function convertToCSV($logs) {
        if (empty($logs)) {
            return '';
        }
        
        $csv = '';
        
        // Get headers
        $headers = array_keys((array) $logs[0]);
        $csv .= implode(',', $headers) . "\n";
        
        // Add data
        foreach ($logs as $log) {
            $row = array();
            foreach ($headers as $header) {
                $row[] = is_array($log->$header) ? json_encode($log->$header) : $log->$header;
            }
            $csv .= implode(',', $row) . "\n";
        }
        
        return $csv;
    }
    
    /**
     * Convert to XML
     */
    private function convertToXML($logs) {
        $xml = new \SimpleXMLElement('<logs></logs>');
        
        foreach ($logs as $log) {
            $log_element = $xml->addChild('log');
            foreach ($log as $key => $value) {
                $log_element->addChild($key, htmlspecialchars($value));
            }
        }
        
        return $xml->asXML();
    }
    
    /**
     * Get client IP address
     */
    private function getClientIP() {
        $ip_keys = array(
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        );
        
        foreach ($ip_keys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
