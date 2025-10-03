<?php
/**
 * Dashboard for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Dashboard class handling dashboard operations
 */
class Dashboard {
    
    private $database;
    private $analytics;
    private $security_manager;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->database = new Database();
        $this->analytics = new Analytics();
        $this->security_manager = new SecurityManager();
        
        $this->init();
    }
    
    /**
     * Initialize dashboard
     */
    private function init() {
        add_action('init', array($this, 'initDashboard'));
    }
    
    /**
     * Initialize dashboard
     */
    public function initDashboard() {
        // Set up dashboard hooks
        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
    }
    
    /**
     * Add admin menu
     */
    public function addAdminMenu() {
        add_menu_page(
            'Kloudbean Enterprise Security',
            'Security Suite',
            'manage_options',
            'kloudbean-enterprise-security',
            array($this, 'renderDashboard'),
            'dashicons-shield-alt',
            30
        );
        
        add_submenu_page(
            'kloudbean-enterprise-security',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'kloudbean-enterprise-security',
            array($this, 'renderDashboard')
        );
        
        add_submenu_page(
            'kloudbean-enterprise-security',
            'Security Scanner',
            'Scanner',
            'manage_options',
            'kloudbean-enterprise-security-scanner',
            array($this, 'renderScanner')
        );
        
        add_submenu_page(
            'kloudbean-enterprise-security',
            'Firewall',
            'Firewall',
            'manage_options',
            'kloudbean-enterprise-security-firewall',
            array($this, 'renderFirewall')
        );
        
        add_submenu_page(
            'kloudbean-enterprise-security',
            'Security Logs',
            'Logs',
            'manage_options',
            'kloudbean-enterprise-security-logs',
            array($this, 'renderLogs')
        );
        
        add_submenu_page(
            'kloudbean-enterprise-security',
            'Analytics',
            'Analytics',
            'manage_options',
            'kloudbean-enterprise-security-analytics',
            array($this, 'renderAnalytics')
        );
        
        add_submenu_page(
            'kloudbean-enterprise-security',
            'Compliance',
            'Compliance',
            'manage_options',
            'kloudbean-enterprise-security-compliance',
            array($this, 'renderCompliance')
        );
        
        add_submenu_page(
            'kloudbean-enterprise-security',
            'Settings',
            'Settings',
            'manage_options',
            'kloudbean-enterprise-security-settings',
            array($this, 'renderSettings')
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueueAdminScripts($hook) {
        if (strpos($hook, 'kloudbean-enterprise-security') !== false) {
            wp_enqueue_script('kbes-admin', KBES_ASSETS_URL . 'js/admin.js', array('jquery'), KBES_VERSION, true);
            wp_enqueue_style('kbes-admin', KBES_ASSETS_URL . 'css/admin.css', array(), KBES_VERSION);
            
            wp_localize_script('kbes-admin', 'kbes_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('kbes_nonce')
            ));
        }
    }
    
    /**
     * Render dashboard
     */
    public function renderDashboard() {
        $dashboard_data = $this->getDashboardData();
        
        include KBES_TEMPLATES_DIR . 'dashboard.php';
    }
    
    /**
     * Render scanner
     */
    public function renderScanner() {
        $scanner_data = $this->getScannerData();
        
        include KBES_TEMPLATES_DIR . 'scanner.php';
    }
    
    /**
     * Render firewall
     */
    public function renderFirewall() {
        $firewall_data = $this->getFirewallData();
        
        include KBES_TEMPLATES_DIR . 'firewall.php';
    }
    
    /**
     * Render logs
     */
    public function renderLogs() {
        $logs_data = $this->getLogsData();
        
        include KBES_TEMPLATES_DIR . 'logs.php';
    }
    
    /**
     * Render analytics
     */
    public function renderAnalytics() {
        $analytics_data = $this->getAnalyticsData();
        
        include KBES_TEMPLATES_DIR . 'analytics.php';
    }
    
    /**
     * Render compliance
     */
    public function renderCompliance() {
        $compliance_data = $this->getComplianceData();
        
        include KBES_TEMPLATES_DIR . 'compliance.php';
    }
    
    /**
     * Render settings
     */
    public function renderSettings() {
        $settings_data = $this->getSettingsData();
        
        include KBES_TEMPLATES_DIR . 'settings.php';
    }
    
    /**
     * Get dashboard data
     */
    public function getDashboardData() {
        return array(
            'security_score' => $this->analytics->calculateSecurityScore(),
            'threats_blocked' => $this->security_manager->getThreatsBlocked(),
            'vulnerabilities_found' => $this->getVulnerabilitiesFound(),
            'malware_detected' => $this->getMalwareDetected(),
            'threats_today' => $this->getThreatsToday(),
            'threats_this_week' => $this->getThreatsThisWeek(),
            'threats_this_month' => $this->getThreatsThisMonth(),
            'compliance_score' => $this->getComplianceScore(),
            'failed_checks' => $this->getFailedChecks(),
            'passed_checks' => $this->getPassedChecks(),
            'last_scan' => $this->security_manager->getLastScan(),
            'security_level' => $this->security_manager->getSecurityLevel()
        );
    }
    
    /**
     * Get scanner data
     */
    public function getScannerData() {
        return array(
            'scan_status' => 'completed',
            'last_scan' => $this->security_manager->getLastScan(),
            'threats_found' => $this->getThreatsFound(),
            'vulnerabilities_found' => $this->getVulnerabilitiesFound(),
            'malware_detected' => $this->getMalwareDetected(),
            'file_integrity' => $this->getFileIntegrityStatus()
        );
    }
    
    /**
     * Get firewall data
     */
    public function getFirewallData() {
        return array(
            'rules_count' => $this->getFirewallRulesCount(),
            'blocked_ips' => $this->getBlockedIPsCount(),
            'whitelisted_ips' => $this->getWhitelistedIPsCount(),
            'recent_blocks' => $this->getRecentBlocks(),
            'top_attacking_ips' => $this->getTopAttackingIPs()
        );
    }
    
    /**
     * Get logs data
     */
    public function getLogsData() {
        $logging = new Logging();
        
        return array(
            'security_logs' => $logging->getSecurityLogs(array('limit' => 50)),
            'user_activities' => $logging->getUserActivities(array('limit' => 50)),
            'system_events' => $logging->getSystemEvents(array('limit' => 50))
        );
    }
    
    /**
     * Get analytics data
     */
    public function getAnalyticsData() {
        return $this->analytics->getDashboardData();
    }
    
    /**
     * Get compliance data
     */
    public function getComplianceData() {
        $compliance = new Compliance();
        
        return array(
            'gdpr_score' => $compliance->getComplianceScore('GDPR'),
            'hipaa_score' => $compliance->getComplianceScore('HIPAA'),
            'sox_score' => $compliance->getComplianceScore('SOX'),
            'compliance_status' => $compliance->getComplianceStatus()
        );
    }
    
    /**
     * Get settings data
     */
    public function getSettingsData() {
        return array(
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
        $compliance = new Compliance();
        
        return $compliance->getComplianceScore();
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
     * Get threats found
     */
    private function getThreatsFound() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_threats';
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    }
    
    /**
     * Get file integrity status
     */
    private function getFileIntegrityStatus() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_file_integrity';
        
        $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        $clean = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'clean'");
        
        if ($total > 0) {
            return ($clean / $total) * 100;
        }
        
        return 100;
    }
    
    /**
     * Get firewall rules count
     */
    private function getFirewallRulesCount() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_firewall_rules';
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE enabled = 1");
    }
    
    /**
     * Get blocked IPs count
     */
    private function getBlockedIPsCount() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_blacklist';
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE expires_at IS NULL OR expires_at > NOW()");
    }
    
    /**
     * Get whitelisted IPs count
     */
    private function getWhitelistedIPsCount() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_whitelist';
        
        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE expires_at IS NULL OR expires_at > NOW()");
    }
    
    /**
     * Get recent blocks
     */
    private function getRecentBlocks() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_blocked_requests';
        
        return $wpdb->get_results("SELECT * FROM $table_name ORDER BY timestamp DESC LIMIT 10");
    }
    
    /**
     * Get top attacking IPs
     */
    private function getTopAttackingIPs() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_blocked_requests';
        
        return $wpdb->get_results("SELECT ip, COUNT(*) as count FROM $table_name GROUP BY ip ORDER BY count DESC LIMIT 10");
    }
}
