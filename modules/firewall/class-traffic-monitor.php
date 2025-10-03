<?php
/**
 * Traffic Monitor for Kloudbean Enterprise Security Suite Firewall
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Modules\Firewall;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Traffic Monitor class for real-time traffic monitoring
 */
class TrafficMonitor {
    
    private $database;
    private $logging;
    private $utilities;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->database = new \KloudbeanEnterpriseSecurity\Database();
        $this->logging = new \KloudbeanEnterpriseSecurity\Logging();
        $this->utilities = new \KloudbeanEnterpriseSecurity\Utilities();
        
        $this->init();
    }
    
    /**
     * Initialize traffic monitor
     */
    private function init() {
        add_action('init', array($this, 'startMonitoring'));
        add_action('wp_loaded', array($this, 'logRequest'));
    }
    
    /**
     * Start monitoring
     */
    public function startMonitoring() {
        // Set up monitoring hooks
        add_action('wp_loaded', array($this, 'monitorRequest'));
        add_action('wp_footer', array($this, 'monitorPageLoad'));
        add_action('wp_ajax_kbes_get_traffic_data', array($this, 'getTrafficData'));
        add_action('wp_ajax_nopriv_kbes_get_traffic_data', array($this, 'getTrafficData'));
    }
    
    /**
     * Monitor request
     */
    public function monitorRequest() {
        $request_data = array(
            'ip' => $this->utilities->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'uri' => $_SERVER['REQUEST_URI'] ?? '',
            'method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'referer' => $_SERVER['HTTP_REFERER'] ?? '',
            'headers' => getallheaders(),
            'timestamp' => current_time('mysql'),
            'country' => $this->getCountryByIP($this->utilities->getClientIP()),
            'is_ajax' => wp_doing_ajax(),
            'is_admin' => is_admin(),
            'is_login' => is_user_logged_in(),
            'user_id' => get_current_user_id()
        );
        
        $this->logRequest($request_data);
        $this->updateStatistics($request_data);
    }
    
    /**
     * Log request
     */
    public function logRequest($request_data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_traffic_log';
        
        $wpdb->insert(
            $table_name,
            array(
                'ip_address' => $request_data['ip'],
                'user_agent' => $request_data['user_agent'],
                'request_uri' => $request_data['uri'],
                'request_method' => $request_data['method'],
                'referer' => $request_data['referer'],
                'headers' => json_encode($request_data['headers']),
                'country' => $request_data['country'],
                'is_ajax' => $request_data['is_ajax'] ? 1 : 0,
                'is_admin' => $request_data['is_admin'] ? 1 : 0,
                'is_login' => $request_data['is_login'] ? 1 : 0,
                'user_id' => $request_data['user_id'],
                'timestamp' => $request_data['timestamp']
            ),
            array(
                '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%d', '%s'
            )
        );
    }
    
    /**
     * Monitor page load
     */
    public function monitorPageLoad() {
        $load_time = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
        $memory_usage = memory_get_usage(true);
        
        $this->logPageLoad($load_time, $memory_usage);
    }
    
    /**
     * Log page load
     */
    private function logPageLoad($load_time, $memory_usage) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_page_load_log';
        
        $wpdb->insert(
            $table_name,
            array(
                'ip_address' => $this->utilities->getClientIP(),
                'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
                'load_time' => $load_time,
                'memory_usage' => $memory_usage,
                'timestamp' => current_time('mysql')
            ),
            array(
                '%s', '%s', '%f', '%d', '%s'
            )
        );
    }
    
    /**
     * Update statistics
     */
    private function updateStatistics($request_data) {
        $stats = get_option('kbes_traffic_stats', array());
        
        // Update hourly stats
        $hour_key = date('Y-m-d-H');
        if (!isset($stats['hourly'][$hour_key])) {
            $stats['hourly'][$hour_key] = array(
                'requests' => 0,
                'unique_ips' => array(),
                'countries' => array(),
                'user_agents' => array()
            );
        }
        
        $stats['hourly'][$hour_key]['requests']++;
        $stats['hourly'][$hour_key]['unique_ips'][$request_data['ip']] = true;
        $stats['hourly'][$hour_key]['countries'][$request_data['country']] = true;
        $stats['hourly'][$hour_key]['user_agents'][$request_data['user_agent']] = true;
        
        // Update daily stats
        $day_key = date('Y-m-d');
        if (!isset($stats['daily'][$day_key])) {
            $stats['daily'][$day_key] = array(
                'requests' => 0,
                'unique_ips' => array(),
                'countries' => array(),
                'user_agents' => array()
            );
        }
        
        $stats['daily'][$day_key]['requests']++;
        $stats['daily'][$day_key]['unique_ips'][$request_data['ip']] = true;
        $stats['daily'][$day_key]['countries'][$request_data['country']] = true;
        $stats['daily'][$day_key]['user_agents'][$request_data['user_agent']] = true;
        
        // Clean up old stats (keep last 30 days)
        $cutoff_date = date('Y-m-d', strtotime('-30 days'));
        foreach ($stats['daily'] as $date => $data) {
            if ($date < $cutoff_date) {
                unset($stats['daily'][$date]);
            }
        }
        
        update_option('kbes_traffic_stats', $stats);
    }
    
    /**
     * Get traffic data
     */
    public function getTrafficData() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $timeframe = sanitize_text_field($_POST['timeframe'] ?? 'hour');
        $data = $this->getTrafficStatistics($timeframe);
        
        wp_send_json_success($data);
    }
    
    /**
     * Get traffic statistics
     */
    public function getTrafficStatistics($timeframe = 'hour') {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_traffic_log';
        
        $where_clause = '';
        $params = array();
        
        switch ($timeframe) {
            case 'hour':
                $where_clause = 'AND timestamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR)';
                break;
            case 'day':
                $where_clause = 'AND timestamp >= DATE_SUB(NOW(), INTERVAL 1 DAY)';
                break;
            case 'week':
                $where_clause = 'AND timestamp >= DATE_SUB(NOW(), INTERVAL 1 WEEK)';
                break;
            case 'month':
                $where_clause = 'AND timestamp >= DATE_SUB(NOW(), INTERVAL 1 MONTH)';
                break;
        }
        
        $stats = array();
        
        // Total requests
        $stats['total_requests'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE 1=1 $where_clause");
        
        // Unique IPs
        $stats['unique_ips'] = $wpdb->get_var("SELECT COUNT(DISTINCT ip_address) FROM $table_name WHERE 1=1 $where_clause");
        
        // Top IPs
        $stats['top_ips'] = $wpdb->get_results("SELECT ip_address, COUNT(*) as count FROM $table_name WHERE 1=1 $where_clause GROUP BY ip_address ORDER BY count DESC LIMIT 10");
        
        // Top countries
        $stats['top_countries'] = $wpdb->get_results("SELECT country, COUNT(*) as count FROM $table_name WHERE 1=1 $where_clause GROUP BY country ORDER BY count DESC LIMIT 10");
        
        // Top user agents
        $stats['top_user_agents'] = $wpdb->get_results("SELECT user_agent, COUNT(*) as count FROM $table_name WHERE 1=1 $where_clause GROUP BY user_agent ORDER BY count DESC LIMIT 10");
        
        // Top URIs
        $stats['top_uris'] = $wpdb->get_results("SELECT request_uri, COUNT(*) as count FROM $table_name WHERE 1=1 $where_clause GROUP BY request_uri ORDER BY count DESC LIMIT 10");
        
        // Request methods
        $stats['request_methods'] = $wpdb->get_results("SELECT request_method, COUNT(*) as count FROM $table_name WHERE 1=1 $where_clause GROUP BY request_method ORDER BY count DESC");
        
        // Hourly distribution
        $stats['hourly_distribution'] = $wpdb->get_results("SELECT HOUR(timestamp) as hour, COUNT(*) as count FROM $table_name WHERE 1=1 $where_clause GROUP BY HOUR(timestamp) ORDER BY hour");
        
        return $stats;
    }
    
    /**
     * Get real-time traffic
     */
    public function getRealTimeTraffic() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_traffic_log';
        
        $recent_requests = $wpdb->get_results("
            SELECT 
                ip_address,
                user_agent,
                request_uri,
                request_method,
                country,
                timestamp
            FROM $table_name 
            WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
            ORDER BY timestamp DESC
            LIMIT 50
        ");
        
        return $recent_requests;
    }
    
    /**
     * Get country by IP
     */
    private function getCountryByIP($ip) {
        $response = wp_remote_get('http://ip-api.com/json/' . $ip . '?fields=countryCode');
        
        if (is_wp_error($response)) {
            return 'Unknown';
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        return $data['countryCode'] ?? 'Unknown';
    }
    
    /**
     * Get traffic alerts
     */
    public function getTrafficAlerts() {
        $alerts = array();
        
        // Check for unusual traffic patterns
        $stats = $this->getTrafficStatistics('hour');
        
        // Alert if requests per hour exceed threshold
        $threshold = get_option('kbes_traffic_threshold', 1000);
        if ($stats['total_requests'] > $threshold) {
            $alerts[] = array(
                'type' => 'high_traffic',
                'message' => 'High traffic detected: ' . $stats['total_requests'] . ' requests in the last hour',
                'severity' => 'warning'
            );
        }
        
        // Alert if too many requests from single IP
        if (!empty($stats['top_ips'])) {
            $top_ip = $stats['top_ips'][0];
            if ($top_ip->count > 100) {
                $alerts[] = array(
                    'type' => 'suspicious_ip',
                    'message' => 'Suspicious activity from IP: ' . $top_ip->ip_address . ' (' . $top_ip->count . ' requests)',
                    'severity' => 'danger'
                );
            }
        }
        
        return $alerts;
    }
    
    /**
     * Clean old traffic logs
     */
    public function cleanOldLogs() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_traffic_log';
        $cutoff_date = date('Y-m-d H:i:s', strtotime('-30 days'));
        
        $deleted = $wpdb->query($wpdb->prepare(
            "DELETE FROM $table_name WHERE timestamp < %s",
            $cutoff_date
        ));
        
        return $deleted;
    }
}
