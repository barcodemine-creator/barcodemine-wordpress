<?php
/**
 * Setup Wizard for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Admin;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Setup Wizard class handling initial plugin setup
 */
class SetupWizard {
    
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
     * Initialize setup wizard
     */
    private function init() {
        add_action('admin_menu', array($this, 'addSetupWizardMenu'));
        add_action('admin_init', array($this, 'handleSetupWizard'));
        add_action('wp_ajax_kbes_setup_wizard', array($this, 'handleAjaxSetup'));
    }
    
    /**
     * Add setup wizard menu
     */
    public function addSetupWizardMenu() {
        add_dashboard_page(
            'Setup Wizard',
            'Setup Wizard',
            'manage_options',
            'kbes-setup-wizard',
            array($this, 'renderSetupWizard')
        );
    }
    
    /**
     * Handle setup wizard
     */
    public function handleSetupWizard() {
        if (isset($_GET['page']) && $_GET['page'] === 'kbes-setup-wizard') {
            $this->enqueueSetupWizardAssets();
        }
    }
    
    /**
     * Enqueue setup wizard assets
     */
    private function enqueueSetupWizardAssets() {
        wp_enqueue_script('kbes-setup-wizard', KBES_ASSETS_URL . 'js/setup-wizard.js', array('jquery'), KBES_VERSION, true);
        wp_enqueue_style('kbes-setup-wizard', KBES_ASSETS_URL . 'css/setup-wizard.css', array(), KBES_VERSION);
        
        wp_localize_script('kbes-setup-wizard', 'kbesSetupWizard', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('kbes_setup_wizard_nonce')
        ));
    }
    
    /**
     * Render setup wizard
     */
    public function renderSetupWizard() {
        $current_step = $this->getCurrentStep();
        
        include KBES_TEMPLATES_DIR . 'admin/setup-wizard.php';
    }
    
    /**
     * Get current step
     */
    private function getCurrentStep() {
        return isset($_GET['step']) ? intval($_GET['step']) : 1;
    }
    
    /**
     * Handle AJAX setup
     */
    public function handleAjaxSetup() {
        check_ajax_referer('kbes_setup_wizard_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $step = intval($_POST['step']);
        $data = $_POST['data'] ?? array();
        
        switch ($step) {
            case 1:
                $this->handleStep1($data);
                break;
            case 2:
                $this->handleStep2($data);
                break;
            case 3:
                $this->handleStep3($data);
                break;
            case 4:
                $this->handleStep4($data);
                break;
            case 5:
                $this->handleStep5($data);
                break;
            default:
                wp_send_json_error('Invalid step');
        }
    }
    
    /**
     * Handle step 1 - Welcome
     */
    private function handleStep1($data) {
        wp_send_json_success(array(
            'message' => 'Welcome step completed',
            'next_step' => 2
        ));
    }
    
    /**
     * Handle step 2 - Basic Settings
     */
    private function handleStep2($data) {
        $settings = array(
            'security_level' => sanitize_text_field($data['security_level'] ?? 'medium'),
            'rate_limit' => intval($data['rate_limit'] ?? 100),
            'max_login_attempts' => intval($data['max_login_attempts'] ?? 5),
            'lockout_time' => intval($data['lockout_time'] ?? 300),
            'auto_quarantine' => isset($data['auto_quarantine']),
            'email_notifications' => isset($data['email_notifications']),
            'slack_notifications' => isset($data['slack_notifications'])
        );
        
        foreach ($settings as $key => $value) {
            update_option('kbes_' . $key, $value);
        }
        
        wp_send_json_success(array(
            'message' => 'Basic settings saved',
            'next_step' => 3
        ));
    }
    
    /**
     * Handle step 3 - Security Tests
     */
    private function handleStep3($data) {
        $security_tests = new \KloudbeanEnterpriseSecurity\Modules\SecurityTests();
        $results = $security_tests->runSecurityTests();
        
        wp_send_json_success(array(
            'message' => 'Security tests completed',
            'results' => $results,
            'next_step' => 4
        ));
    }
    
    /**
     * Handle step 4 - Firewall Rules
     */
    private function handleStep4($data) {
        $firewall = new \KloudbeanEnterpriseSecurity\Modules\Firewall();
        
        // Add default firewall rules
        $default_rules = array(
            array(
                'rule_name' => 'Block SQL Injection',
                'rule_type' => 'uri',
                'rule_pattern' => '/(\%27)|(\')|(\-\-)|(\%23)|(#)/ix',
                'action' => 'block',
                'priority' => 100
            ),
            array(
                'rule_name' => 'Block XSS',
                'rule_type' => 'uri',
                'rule_pattern' => '/(\%3C)|(<)|(\%3E)|(>)/ix',
                'action' => 'block',
                'priority' => 100
            ),
            array(
                'rule_name' => 'Block Path Traversal',
                'rule_type' => 'uri',
                'rule_pattern' => '/(\%2E)|(\.)|(\%2F)|(\/)/ix',
                'action' => 'block',
                'priority' => 100
            )
        );
        
        foreach ($default_rules as $rule) {
            $firewall->addRule($rule['rule_name'], $rule['rule_type'], $rule['rule_pattern'], $rule['action'], $rule['priority']);
        }
        
        wp_send_json_success(array(
            'message' => 'Firewall rules configured',
            'next_step' => 5
        ));
    }
    
    /**
     * Handle step 5 - Complete
     */
    private function handleStep5($data) {
        // Mark setup as completed
        update_option('kbes_setup_completed', true);
        update_option('kbes_setup_completed_at', current_time('mysql'));
        
        // Create initial baseline
        $integrity_scanner = new \KloudbeanEnterpriseSecurity\Modules\IntegrityScanner();
        $integrity_scanner->createBaselineHashes();
        
        // Schedule daily scans
        if (!wp_next_scheduled('kbes_daily_security_scan')) {
            wp_schedule_event(time(), 'daily', 'kbes_daily_security_scan');
        }
        
        if (!wp_next_scheduled('kbes_daily_malware_scan')) {
            wp_schedule_event(time(), 'daily', 'kbes_daily_malware_scan');
        }
        
        if (!wp_next_scheduled('kbes_daily_vulnerability_scan')) {
            wp_schedule_event(time(), 'daily', 'kbes_daily_vulnerability_scan');
        }
        
        wp_send_json_success(array(
            'message' => 'Setup completed successfully',
            'next_step' => 'complete'
        ));
    }
    
    /**
     * Check if setup is completed
     */
    public function isSetupCompleted() {
        return get_option('kbes_setup_completed', false);
    }
    
    /**
     * Get setup progress
     */
    public function getSetupProgress() {
        $progress = 0;
        
        if (get_option('kbes_security_level')) {
            $progress += 20;
        }
        
        if (get_option('kbes_rate_limit')) {
            $progress += 20;
        }
        
        if (get_option('kbes_max_login_attempts')) {
            $progress += 20;
        }
        
        if (get_option('kbes_auto_quarantine')) {
            $progress += 20;
        }
        
        if (get_option('kbes_setup_completed')) {
            $progress += 20;
        }
        
        return $progress;
    }
    
    /**
     * Reset setup
     */
    public function resetSetup() {
        delete_option('kbes_setup_completed');
        delete_option('kbes_setup_completed_at');
        
        // Clear scheduled events
        wp_clear_scheduled_hook('kbes_daily_security_scan');
        wp_clear_scheduled_hook('kbes_daily_malware_scan');
        wp_clear_scheduled_hook('kbes_daily_vulnerability_scan');
        
        wp_send_json_success(array(
            'message' => 'Setup reset successfully'
        ));
    }
}
