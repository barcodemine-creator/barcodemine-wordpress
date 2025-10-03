<?php
/**
 * Firewall Module for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Modules;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Firewall class handling firewall operations
 */
class Firewall {
    
    private $database;
    private $logging;
    private $security_manager;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->database = new \KloudbeanEnterpriseSecurity\Database();
        $this->logging = new \KloudbeanEnterpriseSecurity\Logging();
        $this->security_manager = new \KloudbeanEnterpriseSecurity\SecurityManager();
        
        $this->init();
    }
    
    /**
     * Initialize firewall
     */
    private function init() {
        add_action('init', array($this, 'initFirewall'));
        add_action('wp_loaded', array($this, 'processRequest'));
    }
    
    /**
     * Initialize firewall
     */
    public function initFirewall() {
        // Set up firewall hooks
        add_action('wp_loaded', array($this, 'checkFirewallRules'));
        add_action('wp_loaded', array($this, 'monitorTraffic'));
    }
    
    /**
     * Process request
     */
    public function processRequest() {
        // Check if request should be blocked
        if ($this->shouldBlockRequest()) {
            $this->blockRequest();
        }
    }
    
    /**
     * Check firewall rules
     */
    public function checkFirewallRules() {
        $ip = $this->getClientIP();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $request_method = $_SERVER['REQUEST_METHOD'] ?? '';
        
        // Check IP-based rules
        if ($this->checkIPRules($ip)) {
            return;
        }
        
        // Check user agent rules
        if ($this->checkUserAgentRules($user_agent)) {
            return;
        }
        
        // Check URI rules
        if ($this->checkURIRules($request_uri)) {
            return;
        }
        
        // Check method rules
        if ($this->checkMethodRules($request_method)) {
            return;
        }
        
        // Check rate limiting
        if ($this->checkRateLimiting($ip)) {
            return;
        }
    }
    
    /**
     * Monitor traffic
     */
    public function monitorTraffic() {
        $ip = $this->getClientIP();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        $request_method = $_SERVER['REQUEST_METHOD'] ?? '';
        
        // Log traffic
        $this->logTraffic($ip, $user_agent, $request_uri, $request_method);
    }
    
    /**
     * Check if request should be blocked
     */
    private function shouldBlockRequest() {
        $ip = $this->getClientIP();
        
        // Check if IP is blacklisted
        if ($this->isIPBlacklisted($ip)) {
            return true;
        }
        
        // Check if IP is whitelisted
        if ($this->isIPWhitelisted($ip)) {
            return false;
        }
        
        // Check firewall rules
        return $this->checkFirewallRules();
    }
    
    /**
     * Block request
     */
    private function blockRequest() {
        $ip = $this->getClientIP();
        $reason = 'Blocked by firewall rules';
        
        // Log blocked request
        $this->logBlockedRequest($ip, $reason);
        
        // Send 403 Forbidden response
        http_response_code(403);
        die('Access Denied');
    }
    
    /**
     * Check IP rules
     */
    private function checkIPRules($ip) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_firewall_rules';
        
        $rules = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE rule_type = 'ip' AND enabled = 1 AND rule_pattern = %s",
            $ip
        ));
        
        foreach ($rules as $rule) {
            if ($rule->action === 'block') {
                $this->logBlockedRequest($ip, 'IP blocked by rule: ' . $rule->rule_name);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check user agent rules
     */
    private function checkUserAgentRules($user_agent) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_firewall_rules';
        
        $rules = $wpdb->get_results("SELECT * FROM $table_name WHERE rule_type = 'user_agent' AND enabled = 1");
        
        foreach ($rules as $rule) {
            if (preg_match('/' . $rule->rule_pattern . '/i', $user_agent)) {
                if ($rule->action === 'block') {
                    $this->logBlockedRequest($this->getClientIP(), 'User agent blocked by rule: ' . $rule->rule_name);
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Check URI rules
     */
    private function checkURIRules($request_uri) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_firewall_rules';
        
        $rules = $wpdb->get_results("SELECT * FROM $table_name WHERE rule_type = 'uri' AND enabled = 1");
        
        foreach ($rules as $rule) {
            if (preg_match('/' . $rule->rule_pattern . '/i', $request_uri)) {
                if ($rule->action === 'block') {
                    $this->logBlockedRequest($this->getClientIP(), 'URI blocked by rule: ' . $rule->rule_name);
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Check method rules
     */
    private function checkMethodRules($request_method) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_firewall_rules';
        
        $rules = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE rule_type = 'method' AND enabled = 1 AND rule_pattern = %s",
            $request_method
        ));
        
        foreach ($rules as $rule) {
            if ($rule->action === 'block') {
                $this->logBlockedRequest($this->getClientIP(), 'Method blocked by rule: ' . $rule->rule_name);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check rate limiting
     */
    private function checkRateLimiting($ip) {
        $rate_limit = get_option('kbes_rate_limit', 100);
        $time_window = 60; // 1 minute
        
        $key = 'kbes_rate_limit_' . md5($ip);
        $requests = get_transient($key);
        
        if ($requests === false) {
            set_transient($key, 1, $time_window);
            return false;
        }
        
        if ($requests >= $rate_limit) {
            $this->logBlockedRequest($ip, 'Rate limit exceeded');
            return true;
        }
        
        set_transient($key, $requests + 1, $time_window);
        return false;
    }
    
    /**
     * Check if IP is blacklisted
     */
    private function isIPBlacklisted($ip) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_blacklist';
        
        $result = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE ip_address = %s AND (expires_at IS NULL OR expires_at > NOW())",
            $ip
        ));
        
        return $result > 0;
    }
    
    /**
     * Check if IP is whitelisted
     */
    private function isIPWhitelisted($ip) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_whitelist';
        
        $result = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE ip_address = %s AND (expires_at IS NULL OR expires_at > NOW())",
            $ip
        ));
        
        return $result > 0;
    }
    
    /**
     * Log traffic
     */
    private function logTraffic($ip, $user_agent, $request_uri, $request_method) {
        // Log traffic for monitoring purposes
        $this->logging->logSystemEvent('traffic_monitored', array(
            'ip' => $ip,
            'user_agent' => $user_agent,
            'request_uri' => $request_uri,
            'request_method' => $request_method,
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log blocked request
     */
    private function logBlockedRequest($ip, $reason) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_blocked_requests';
        
        $wpdb->insert(
            $table_name,
            array(
                'ip' => $ip,
                'reason' => $reason,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
                'request_method' => $_SERVER['REQUEST_METHOD'] ?? '',
                'timestamp' => current_time('mysql'),
                'country' => $this->getCountryByIP($ip),
                'user_id' => get_current_user_id()
            ),
            array(
                '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d'
            )
        );
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
     * Add firewall rule
     */
    public function addRule($rule_name, $rule_type, $rule_pattern, $action = 'block', $priority = 100) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_firewall_rules';
        
        $wpdb->insert(
            $table_name,
            array(
                'rule_name' => $rule_name,
                'rule_type' => $rule_type,
                'rule_pattern' => $rule_pattern,
                'action' => $action,
                'priority' => $priority,
                'enabled' => 1,
                'source' => 'manual',
                'created_at' => current_time('mysql')
            ),
            array(
                '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s'
            )
        );
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update firewall rule
     */
    public function updateRule($rule_id, $rule_name, $rule_type, $rule_pattern, $action = 'block', $priority = 100, $enabled = 1) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_firewall_rules';
        
        $wpdb->update(
            $table_name,
            array(
                'rule_name' => $rule_name,
                'rule_type' => $rule_type,
                'rule_pattern' => $rule_pattern,
                'action' => $action,
                'priority' => $priority,
                'enabled' => $enabled,
                'updated_at' => current_time('mysql')
            ),
            array('id' => $rule_id),
            array('%s', '%s', '%s', '%s', '%d', '%d', '%s'),
            array('%d')
        );
    }
    
    /**
     * Delete firewall rule
     */
    public function deleteRule($rule_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_firewall_rules';
        
        $wpdb->delete(
            $table_name,
            array('id' => $rule_id),
            array('%d')
        );
    }
    
    /**
     * Get firewall rules
     */
    public function getRules($filters = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_firewall_rules';
        
        $where_clause = '';
        $params = array();
        
        if (!empty($filters['rule_type'])) {
            $where_clause .= ' AND rule_type = %s';
            $params[] = $filters['rule_type'];
        }
        
        if (!empty($filters['action'])) {
            $where_clause .= ' AND action = %s';
            $params[] = $filters['action'];
        }
        
        if (!empty($filters['enabled'])) {
            $where_clause .= ' AND enabled = %d';
            $params[] = $filters['enabled'];
        }
        
        $limit = $filters['limit'] ?? 20;
        $offset = $filters['offset'] ?? 0;
        
        $query = "SELECT * FROM $table_name WHERE 1=1 $where_clause ORDER BY priority ASC, created_at DESC LIMIT %d OFFSET %d";
        $params[] = $limit;
        $params[] = $offset;
        
        return $wpdb->get_results($wpdb->prepare($query, $params));
    }
    
    /**
     * Get firewall rule
     */
    public function getRule($rule_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_firewall_rules';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE id = %d",
            $rule_id
        ));
    }
    
    /**
     * Get blocked requests
     */
    public function getBlockedRequests($filters = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_blocked_requests';
        
        $where_clause = '';
        $params = array();
        
        if (!empty($filters['ip'])) {
            $where_clause .= ' AND ip = %s';
            $params[] = $filters['ip'];
        }
        
        if (!empty($filters['reason'])) {
            $where_clause .= ' AND reason LIKE %s';
            $params[] = '%' . $filters['reason'] . '%';
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
     * Get firewall statistics
     */
    public function getStatistics() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_blocked_requests';
        
        $stats = array();
        
        // Total blocked requests
        $stats['total_blocked'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        // Blocked requests today
        $stats['blocked_today'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE DATE(timestamp) = CURDATE()");
        
        // Blocked requests this week
        $stats['blocked_this_week'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE YEARWEEK(timestamp) = YEARWEEK(NOW())");
        
        // Blocked requests this month
        $stats['blocked_this_month'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE YEAR(timestamp) = YEAR(NOW()) AND MONTH(timestamp) = MONTH(NOW())");
        
        // Top attacking IPs
        $stats['top_attacking_ips'] = $wpdb->get_results("SELECT ip, COUNT(*) as count FROM $table_name GROUP BY ip ORDER BY count DESC LIMIT 10");
        
        // Top blocked reasons
        $stats['top_blocked_reasons'] = $wpdb->get_results("SELECT reason, COUNT(*) as count FROM $table_name GROUP BY reason ORDER BY count DESC LIMIT 10");
        
        return $stats;
    }
}
