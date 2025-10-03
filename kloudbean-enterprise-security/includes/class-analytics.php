<?php
/**
 * Analytics for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Analytics class handling analytics and reporting
 */
class Analytics {
    
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
     * Initialize analytics
     */
    private function init() {
        add_action('wp_loaded', array($this, 'initAnalytics'));
        add_action('wp_loaded', array($this, 'updateStats'));
    }
    
    /**
     * Initialize analytics
     */
    public function initAnalytics() {
        // Set up analytics hooks
        add_action('wp_loaded', array($this, 'trackPageViews'));
        add_action('wp_loaded', array($this, 'trackUserActivity'));
        add_action('wp_loaded', array($this, 'trackSecurityEvents'));
    }
    
    /**
     * Update statistics
     */
    public function updateStats() {
        // Update security metrics
        $this->updateSecurityMetrics();
        
        // Update performance metrics
        $this->updatePerformanceMetrics();
        
        // Update threat metrics
        $this->updateThreatMetrics();
        
        // Update compliance metrics
        $this->updateComplianceMetrics();
    }
    
    /**
     * Track page views
     */
    public function trackPageViews() {
        if (!is_admin()) {
            $this->logMetric('page_view', 1, 'counter', array(
                'page' => $_SERVER['REQUEST_URI'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'ip' => $this->getClientIP()
            ));
        }
    }
    
    /**
     * Track user activity
     */
    public function trackUserActivity() {
        if (is_user_logged_in()) {
            $this->logMetric('user_activity', 1, 'counter', array(
                'user_id' => get_current_user_id(),
                'page' => $_SERVER['REQUEST_URI'] ?? '',
                'ip' => $this->getClientIP()
            ));
        }
    }
    
    /**
     * Track security events
     */
    public function trackSecurityEvents() {
        // Track security events
        add_action('kbes_security_event', array($this, 'logSecurityEvent'), 10, 2);
    }
    
    /**
     * Log security event
     */
    public function logSecurityEvent($event_type, $data) {
        $this->logMetric('security_event', 1, 'counter', array(
            'event_type' => $event_type,
            'severity' => $data['severity'] ?? 'medium',
            'ip' => $data['ip'] ?? '',
            'user_id' => $data['user_id'] ?? null
        ));
    }
    
    /**
     * Update security metrics
     */
    private function updateSecurityMetrics() {
        // Get security metrics from database
        $threats_blocked = $this->getThreatsBlocked();
        $vulnerabilities_found = $this->getVulnerabilitiesFound();
        $malware_detected = $this->getMalwareDetected();
        $security_score = $this->calculateSecurityScore();
        
        // Log metrics
        $this->logMetric('threats_blocked', $threats_blocked, 'gauge');
        $this->logMetric('vulnerabilities_found', $vulnerabilities_found, 'gauge');
        $this->logMetric('malware_detected', $malware_detected, 'gauge');
        $this->logMetric('security_score', $security_score, 'gauge');
    }
    
    /**
     * Update performance metrics
     */
    private function updatePerformanceMetrics() {
        // Get performance metrics
        $page_load_time = $this->getPageLoadTime();
        $memory_usage = $this->getMemoryUsage();
        $database_queries = $this->getDatabaseQueries();
        
        // Log metrics
        $this->logMetric('page_load_time', $page_load_time, 'histogram');
        $this->logMetric('memory_usage', $memory_usage, 'gauge');
        $this->logMetric('database_queries', $database_queries, 'counter');
    }
    
    /**
     * Update threat metrics
     */
    private function updateThreatMetrics() {
        // Get threat metrics
        $threats_today = $this->getThreatsToday();
        $threats_this_week = $this->getThreatsThisWeek();
        $threats_this_month = $this->getThreatsThisMonth();
        
        // Log metrics
        $this->logMetric('threats_today', $threats_today, 'gauge');
        $this->logMetric('threats_this_week', $threats_this_week, 'gauge');
        $this->logMetric('threats_this_month', $threats_this_month, 'gauge');
    }
    
    /**
     * Update compliance metrics
     */
    private function updateComplianceMetrics() {
        // Get compliance metrics
        $compliance_score = $this->getComplianceScore();
        $failed_checks = $this->getFailedChecks();
        $passed_checks = $this->getPassedChecks();
        
        // Log metrics
        $this->logMetric('compliance_score', $compliance_score, 'gauge');
        $this->logMetric('failed_checks', $failed_checks, 'gauge');
        $this->logMetric('passed_checks', $passed_checks, 'gauge');
    }
    
    /**
     * Log metric
     */
    private function logMetric($metric_name, $metric_value, $metric_type = 'counter', $dimensions = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_analytics';
        
        $wpdb->insert(
            $table_name,
            array(
                'metric_name' => $metric_name,
                'metric_value' => $metric_value,
                'metric_type' => $metric_type,
                'dimensions' => json_encode($dimensions),
                'timestamp' => current_time('mysql')
            ),
            array(
                '%s', '%f', '%s', '%s', '%s'
            )
        );
    }
    
    /**
     * Get threats blocked
     */
    private function getThreatsBlocked() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_threats';
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE blocked = 1");
    }
    
    /**
     * Get vulnerabilities found
     */
    private function getVulnerabilitiesFound() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_vulnerabilities';
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    }
    
    /**
     * Get malware detected
     */
    private function getMalwareDetected() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_threats';
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE threat_type = 'malware'");
    }
    
    /**
     * Calculate security score
     */
    private function calculateSecurityScore() {
        $score = 100;
        
        // Deduct points for threats
        $threats = $this->getThreatsBlocked();
        $score -= min($threats * 2, 50);
        
        // Deduct points for vulnerabilities
        $vulnerabilities = $this->getVulnerabilitiesFound();
        $score -= min($vulnerabilities * 5, 30);
        
        // Deduct points for malware
        $malware = $this->getMalwareDetected();
        $score -= min($malware * 10, 20);
        
        return max($score, 0);
    }
    
    /**
     * Get page load time
     */
    private function getPageLoadTime() {
        if (defined('WP_START_TIMESTAMP')) {
            return (microtime(true) - WP_START_TIMESTAMP) * 1000;
        }
        
        return 0;
    }
    
    /**
     * Get memory usage
     */
    private function getMemoryUsage() {
        return memory_get_usage(true);
    }
    
    /**
     * Get database queries
     */
    private function getDatabaseQueries() {
        global $wpdb;
        
        return $wpdb->num_queries;
    }
    
    /**
     * Get threats today
     */
    private function getThreatsToday() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_threats';
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE DATE(timestamp) = CURDATE()");
    }
    
    /**
     * Get threats this week
     */
    private function getThreatsThisWeek() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_threats';
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE YEARWEEK(timestamp) = YEARWEEK(NOW())");
    }
    
    /**
     * Get threats this month
     */
    private function getThreatsThisMonth() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_threats';
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE YEAR(timestamp) = YEAR(NOW()) AND MONTH(timestamp) = MONTH(NOW())");
    }
    
    /**
     * Get compliance score
     */
    private function getComplianceScore() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_compliance';
        
        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $compliant = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'compliant'");
        
        if ($total > 0) {
            return ($compliant / $total) * 100;
        }
        
        return 0;
    }
    
    /**
     * Get failed checks
     */
    private function getFailedChecks() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_tests';
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'fail'");
    }
    
    /**
     * Get passed checks
     */
    private function getPassedChecks() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_tests';
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'pass'");
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
     * Get analytics data
     */
    public function getAnalyticsData($metric_name, $start_date = null, $end_date = null) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_analytics';
        
        $where_clause = "WHERE metric_name = %s";
        $params = array($metric_name);
        
        if ($start_date) {
            $where_clause .= " AND timestamp >= %s";
            $params[] = $start_date;
        }
        
        if ($end_date) {
            $where_clause .= " AND timestamp <= %s";
            $params[] = $end_date;
        }
        
        $query = "SELECT * FROM $table_name $where_clause ORDER BY timestamp DESC";
        
        return $wpdb->get_results($wpdb->prepare($query, $params));
    }
    
    /**
     * Get dashboard data
     */
    public function getDashboardData() {
        return array(
            'security_score' => $this->calculateSecurityScore(),
            'threats_blocked' => $this->getThreatsBlocked(),
            'vulnerabilities_found' => $this->getVulnerabilitiesFound(),
            'malware_detected' => $this->getMalwareDetected(),
            'threats_today' => $this->getThreatsToday(),
            'threats_this_week' => $this->getThreatsThisWeek(),
            'threats_this_month' => $this->getThreatsThisMonth(),
            'compliance_score' => $this->getComplianceScore(),
            'failed_checks' => $this->getFailedChecks(),
            'passed_checks' => $this->getPassedChecks()
        );
    }
}
