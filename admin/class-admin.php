<?php
/**
 * Admin class for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Admin;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Admin class handling admin interface
 */
class Admin {
    
    private $database;
    private $logging;
    private $dashboard;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->database = new \KloudbeanEnterpriseSecurity\Database();
        $this->logging = new \KloudbeanEnterpriseSecurity\Logging();
        $this->dashboard = new \KloudbeanEnterpriseSecurity\Dashboard();
        
        $this->init();
    }
    
    /**
     * Initialize admin
     */
    private function init() {
        add_action('admin_menu', array($this, 'addAdminMenu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueAdminScripts'));
        add_action('admin_init', array($this, 'initAdmin'));
        
        // Initialize AJAX handlers
        new AjaxHandlers();
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
            'Security Tests',
            'Security Tests',
            'manage_options',
            'kloudbean-enterprise-security-tests',
            array($this, 'renderSecurityTests')
        );
        
        add_submenu_page(
            'kloudbean-enterprise-security',
            'Integrity Scanner',
            'Integrity Scanner',
            'manage_options',
            'kloudbean-enterprise-security-integrity',
            array($this, 'renderIntegrityScanner')
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
        
        add_submenu_page(
            'kloudbean-enterprise-security',
            'White Label',
            'White Label',
            'manage_options',
            'kbes-white-label',
            array($this, 'renderWhiteLabel')
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueueAdminScripts($hook) {
        if (strpos($hook, 'kloudbean-enterprise-security') !== false) {
            wp_enqueue_script('kbes-admin', KBES_ASSETS_URL . 'js/admin.js', array('jquery'), KBES_VERSION, true);
            wp_enqueue_style('kbes-admin', KBES_ASSETS_URL . 'css/admin.css', array(), KBES_VERSION);
            
            wp_localize_script('kbes-admin', 'kbesAdmin', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('kbes_nonce')
            ));
        }
    }
    
    /**
     * Initialize admin
     */
    public function initAdmin() {
        // Register settings
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
     * Render dashboard
     */
    public function renderDashboard() {
        $dashboard_data = $this->dashboard->getDashboardData();
        
        include KBES_TEMPLATES_DIR . 'admin/dashboard.php';
    }
    
    /**
     * Render scanner
     */
    public function renderScanner() {
        $scanner_data = $this->dashboard->getScannerData();
        
        include KBES_TEMPLATES_DIR . 'admin/scanner.php';
    }
    
    /**
     * Render firewall
     */
    public function renderFirewall() {
        $firewall_data = $this->dashboard->getFirewallData();
        
        include KBES_TEMPLATES_DIR . 'admin/firewall.php';
    }
    
    /**
     * Render security tests
     */
    public function renderSecurityTests() {
        $security_tests = new \KloudbeanEnterpriseSecurity\Modules\SecurityTests();
        $test_data = $security_tests->getTestReport();
        
        include KBES_TEMPLATES_DIR . 'admin/security-tests.php';
    }
    
    /**
     * Render integrity scanner
     */
    public function renderIntegrityScanner() {
        $integrity_scanner = new \KloudbeanEnterpriseSecurity\Modules\IntegrityScanner();
        
        include KBES_TEMPLATES_DIR . 'admin/integrity-scanner.php';
    }
    
    /**
     * Render logs
     */
    public function renderLogs() {
        $logs_data = $this->dashboard->getLogsData();
        
        include KBES_TEMPLATES_DIR . 'admin/logs.php';
    }
    
    /**
     * Render analytics
     */
    public function renderAnalytics() {
        $analytics_data = $this->dashboard->getAnalyticsData();
        
        include KBES_TEMPLATES_DIR . 'admin/analytics.php';
    }
    
    /**
     * Render compliance
     */
    public function renderCompliance() {
        $compliance_data = $this->dashboard->getComplianceData();
        
        include KBES_TEMPLATES_DIR . 'admin/compliance.php';
    }
    
    /**
     * Render settings
     */
    public function renderSettings() {
        $settings_data = $this->dashboard->getSettingsData();
        
        include KBES_TEMPLATES_DIR . 'admin/settings.php';
    }
    
    /**
     * Render white label page
     */
    public function renderWhiteLabel() {
        include KBES_TEMPLATES_DIR . 'admin/white-label.php';
    }
}
