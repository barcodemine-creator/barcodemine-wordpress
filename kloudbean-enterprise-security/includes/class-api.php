<?php
/**
 * API for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * API class handling REST API endpoints
 */
class API {
    
    private $database;
    private $logging;
    private $security_manager;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->database = new Database();
        $this->logging = new Logging();
        $this->security_manager = new SecurityManager();
        
        $this->init();
    }
    
    /**
     * Initialize API
     */
    private function init() {
        add_action('rest_api_init', array($this, 'initAPI'));
        add_action('wp_loaded', array($this, 'registerEndpoints'));
    }
    
    /**
     * Initialize API
     */
    public function initAPI() {
        // Set up API hooks
        add_action('rest_api_init', array($this, 'registerRoutes'));
        add_action('rest_api_init', array($this, 'addCorsHeaders'));
    }
    
    /**
     * Register endpoints
     */
    public function registerEndpoints() {
        // Register custom endpoints
        $this->registerCustomEndpoints();
    }
    
    /**
     * Register routes
     */
    public function registerRoutes() {
        // Security endpoints
        register_rest_route('kbes/v1', '/security/scan', array(
            'methods' => 'POST',
            'callback' => array($this, 'runSecurityScan'),
            'permission_callback' => array($this, 'checkPermissions')
        ));
        
        register_rest_route('kbes/v1', '/security/status', array(
            'methods' => 'GET',
            'callback' => array($this, 'getSecurityStatus'),
            'permission_callback' => array($this, 'checkPermissions')
        ));
        
        // Analytics endpoints
        register_rest_route('kbes/v1', '/analytics/dashboard', array(
            'methods' => 'GET',
            'callback' => array($this, 'getDashboardData'),
            'permission_callback' => array($this, 'checkPermissions')
        ));
        
        register_rest_route('kbes/v1', '/analytics/metrics', array(
            'methods' => 'GET',
            'callback' => array($this, 'getAnalyticsMetrics'),
            'permission_callback' => array($this, 'checkPermissions')
        ));
        
        // Compliance endpoints
        register_rest_route('kbes/v1', '/compliance/status', array(
            'methods' => 'GET',
            'callback' => array($this, 'getComplianceStatus'),
            'permission_callback' => array($this, 'checkPermissions')
        ));
        
        register_rest_route('kbes/v1', '/compliance/report', array(
            'methods' => 'GET',
            'callback' => array($this, 'getComplianceReport'),
            'permission_callback' => array($this, 'checkPermissions')
        ));
        
        // Firewall endpoints
        register_rest_route('kbes/v1', '/firewall/rules', array(
            'methods' => 'GET',
            'callback' => array($this, 'getFirewallRules'),
            'permission_callback' => array($this, 'checkPermissions')
        ));
        
        register_rest_route('kbes/v1', '/firewall/block-ip', array(
            'methods' => 'POST',
            'callback' => array($this, 'blockIP'),
            'permission_callback' => array($this, 'checkPermissions')
        ));
        
        register_rest_route('kbes/v1', '/firewall/unblock-ip', array(
            'methods' => 'POST',
            'callback' => array($this, 'unblockIP'),
            'permission_callback' => array($this, 'checkPermissions')
        ));
        
        // Logs endpoints
        register_rest_route('kbes/v1', '/logs/security', array(
            'methods' => 'GET',
            'callback' => array($this, 'getSecurityLogs'),
            'permission_callback' => array($this, 'checkPermissions')
        ));
        
        register_rest_route('kbes/v1', '/logs/events', array(
            'methods' => 'GET',
            'callback' => array($this, 'getEventLogs'),
            'permission_callback' => array($this, 'checkPermissions')
        ));
        
        // Settings endpoints
        register_rest_route('kbes/v1', '/settings', array(
            'methods' => 'GET',
            'callback' => array($this, 'getSettings'),
            'permission_callback' => array($this, 'checkPermissions')
        ));
        
        register_rest_route('kbes/v1', '/settings', array(
            'methods' => 'POST',
            'callback' => array($this, 'updateSettings'),
            'permission_callback' => array($this, 'checkPermissions')
        ));
    }
    
    /**
     * Add CORS headers
     */
    public function addCorsHeaders() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-WP-Nonce');
    }
    
    /**
     * Register custom endpoints
     */
    private function registerCustomEndpoints() {
        // Custom endpoints for specific functionality
    }
    
    /**
     * Check permissions
     */
    public function checkPermissions($request) {
        return current_user_can('manage_options');
    }
    
    /**
     * Run security scan
     */
    public function runSecurityScan($request) {
        $this->security_manager->runFullScan();
        
        return new \WP_REST_Response(array(
            'success' => true,
            'message' => 'Security scan completed successfully',
            'timestamp' => current_time('mysql')
        ), 200);
    }
    
    /**
     * Get security status
     */
    public function getSecurityStatus($request) {
        $status = array(
            'security_level' => $this->security_manager->getSecurityLevel(),
            'threats_blocked' => $this->security_manager->getThreatsBlocked(),
            'last_scan' => $this->security_manager->getLastScan(),
            'scan_status' => 'completed'
        );
        
        return new \WP_REST_Response($status, 200);
    }
    
    /**
     * Get dashboard data
     */
    public function getDashboardData($request) {
        $analytics = new Analytics();
        $data = $analytics->getDashboardData();
        
        return new \WP_REST_Response($data, 200);
    }
    
    /**
     * Get analytics metrics
     */
    public function getAnalyticsMetrics($request) {
        $analytics = new Analytics();
        $metric_name = $request->get_param('metric_name');
        $start_date = $request->get_param('start_date');
        $end_date = $request->get_param('end_date');
        
        $data = $analytics->getAnalyticsData($metric_name, $start_date, $end_date);
        
        return new \WP_REST_Response($data, 200);
    }
    
    /**
     * Get compliance status
     */
    public function getComplianceStatus($request) {
        $compliance = new Compliance();
        $framework = $request->get_param('framework');
        
        $status = $compliance->getComplianceStatus($framework);
        
        return new \WP_REST_Response($status, 200);
    }
    
    /**
     * Get compliance report
     */
    public function getComplianceReport($request) {
        $compliance = new Compliance();
        $framework = $request->get_param('framework');
        
        $report = $compliance->generateComplianceReport($framework);
        
        return new \WP_REST_Response($report, 200);
    }
    
    /**
     * Get firewall rules
     */
    public function getFirewallRules($request) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_firewall_rules';
        $rules = $wpdb->get_results("SELECT * FROM $table_name ORDER BY priority ASC");
        
        return new \WP_REST_Response($rules, 200);
    }
    
    /**
     * Block IP
     */
    public function blockIP($request) {
        $ip = $request->get_param('ip');
        $reason = $request->get_param('reason');
        
        if (!$ip) {
            return new \WP_Error('missing_ip', 'IP address is required', array('status' => 400));
        }
        
        $this->security_manager->addToBlacklist($ip, $reason);
        
        return new \WP_REST_Response(array(
            'success' => true,
            'message' => 'IP blocked successfully',
            'ip' => $ip
        ), 200);
    }
    
    /**
     * Unblock IP
     */
    public function unblockIP($request) {
        $ip = $request->get_param('ip');
        
        if (!$ip) {
            return new \WP_Error('missing_ip', 'IP address is required', array('status' => 400));
        }
        
        $this->security_manager->removeFromBlacklist($ip);
        
        return new \WP_REST_Response(array(
            'success' => true,
            'message' => 'IP unblocked successfully',
            'ip' => $ip
        ), 200);
    }
    
    /**
     * Get security logs
     */
    public function getSecurityLogs($request) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_logs';
        $page = $request->get_param('page') ?: 1;
        $per_page = $request->get_param('per_page') ?: 20;
        $offset = ($page - 1) * $per_page;
        
        $logs = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY timestamp DESC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ));
        
        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        return new \WP_REST_Response(array(
            'logs' => $logs,
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total / $per_page)
        ), 200);
    }
    
    /**
     * Get event logs
     */
    public function getEventLogs($request) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_events';
        $page = $request->get_param('page') ?: 1;
        $per_page = $request->get_param('per_page') ?: 20;
        $offset = ($page - 1) * $per_page;
        
        $logs = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY timestamp DESC LIMIT %d OFFSET %d",
            $per_page,
            $offset
        ));
        
        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        return new \WP_REST_Response(array(
            'logs' => $logs,
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total / $per_page)
        ), 200);
    }
    
    /**
     * Get settings
     */
    public function getSettings($request) {
        $settings = array(
            'security_level' => get_option('kbes_security_level', 'medium'),
            'threats_blocked' => get_option('kbes_threats_blocked', 0),
            'last_scan' => get_option('kbes_last_scan', 0),
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
        
        return new \WP_REST_Response($settings, 200);
    }
    
    /**
     * Update settings
     */
    public function updateSettings($request) {
        $settings = $request->get_json_params();
        
        foreach ($settings as $key => $value) {
            update_option('kbes_' . $key, $value);
        }
        
        return new \WP_REST_Response(array(
            'success' => true,
            'message' => 'Settings updated successfully'
        ), 200);
    }
    
    /**
     * Log API request
     */
    private function logApiRequest($endpoint, $method, $status_code, $response_time, $ip, $user_agent, $request_data = null, $response_data = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_api_logs';
        
        $wpdb->insert(
            $table_name,
            array(
                'endpoint' => $endpoint,
                'method' => $method,
                'status_code' => $status_code,
                'response_time' => $response_time,
                'ip_address' => $ip,
                'user_agent' => $user_agent,
                'request_data' => $request_data ? json_encode($request_data) : null,
                'response_data' => $response_data ? json_encode($response_data) : null,
                'timestamp' => current_time('mysql')
            ),
            array(
                '%s', '%s', '%d', '%f', '%s', '%s', '%s', '%s', '%s'
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
}
