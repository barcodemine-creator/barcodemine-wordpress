<?php
/**
 * AJAX Handlers for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Admin;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * AJAX handlers class
 */
class AjaxHandlers {
    
    private $security_tests;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->security_tests = new \KloudbeanEnterpriseSecurity\Modules\SecurityTests();
        
        $this->init();
    }
    
    /**
     * Initialize AJAX handlers
     */
    private function init() {
        // Security Tests AJAX
        add_action('wp_ajax_kbes_run_security_tests', array($this, 'runSecurityTests'));
        add_action('wp_ajax_kbes_get_test_results', array($this, 'getTestResults'));
        add_action('wp_ajax_kbes_fix_test_issue', array($this, 'fixTestIssue'));
        add_action('wp_ajax_kbes_fix_all_issues', array($this, 'fixAllIssues'));
        add_action('wp_ajax_kbes_export_test_report', array($this, 'exportTestReport'));
        
        // Integrity Scanner AJAX
        add_action('wp_ajax_kbes_scan_files', array($this, 'scanFiles'));
        add_action('wp_ajax_kbes_get_file_results', array($this, 'getFileResults'));
        add_action('wp_ajax_kbes_create_baseline', array($this, 'createBaseline'));
        add_action('wp_ajax_kbes_restore_file', array($this, 'restoreFile'));
        add_action('wp_ajax_kbes_restore_all_files', array($this, 'restoreAllFiles'));
        
        // Scanner AJAX
        add_action('wp_ajax_kbes_run_scanner', array($this, 'runScanner'));
        add_action('wp_ajax_kbes_get_scanner_results', array($this, 'getScannerResults'));
        
        // Firewall AJAX
        add_action('wp_ajax_kbes_get_firewall_rules', array($this, 'getFirewallRules'));
        add_action('wp_ajax_kbes_add_firewall_rule', array($this, 'addFirewallRule'));
        add_action('wp_ajax_kbes_delete_firewall_rule', array($this, 'deleteFirewallRule'));
        add_action('wp_ajax_kbes_block_ip', array($this, 'blockIP'));
        add_action('wp_ajax_kbes_unblock_ip', array($this, 'unblockIP'));
        
        // Logs AJAX
        add_action('wp_ajax_kbes_get_security_logs', array($this, 'getSecurityLogs'));
        add_action('wp_ajax_kbes_clear_logs', array($this, 'clearLogs'));
        
        // Settings AJAX
        add_action('wp_ajax_kbes_save_settings', array($this, 'saveSettings'));
        add_action('wp_ajax_kbes_reset_settings', array($this, 'resetSettings'));
    }
    
    /**
     * Run security tests
     */
    public function runSecurityTests() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        try {
            $results = $this->security_tests->runSecurityTests();
            $score = $this->security_tests->getSecurityScore();
            
            wp_send_json_success(array(
                'results' => $results,
                'score' => $score,
                'message' => 'Security tests completed successfully'
            ));
        } catch (Exception $e) {
            wp_send_json_error('Error running security tests: ' . $e->getMessage());
        }
    }
    
    /**
     * Get test results
     */
    public function getTestResults() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $filters = array(
            'category' => sanitize_text_field($_POST['category'] ?? ''),
            'status' => sanitize_text_field($_POST['status'] ?? ''),
            'limit' => intval($_POST['limit'] ?? 20),
            'offset' => intval($_POST['offset'] ?? 0)
        );
        
        $results = $this->security_tests->getTestResults($filters);
        
        wp_send_json_success($results);
    }
    
    /**
     * Fix test issue
     */
    public function fixTestIssue() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $test_id = sanitize_text_field($_POST['test_id']);
        
        if (empty($test_id)) {
            wp_send_json_error('Test ID is required');
        }
        
        $result = $this->security_tests->fixTestIssue($test_id);
        
        if ($result) {
            wp_send_json_success('Test issue fixed successfully');
        } else {
            wp_send_json_error('Failed to fix test issue');
        }
    }
    
    /**
     * Fix all issues
     */
    public function fixAllIssues() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $results = $this->security_tests->getTestResults(array('status' => 'fail'));
        $fixed_count = 0;
        
        foreach ($results as $test) {
            if ($test->auto_fixable) {
                if ($this->security_tests->fixTestIssue($test->test_name)) {
                    $fixed_count++;
                }
            }
        }
        
        wp_send_json_success(array(
            'fixed_count' => $fixed_count,
            'message' => "Fixed $fixed_count issues"
        ));
    }
    
    /**
     * Export test report
     */
    public function exportTestReport() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $report = $this->security_tests->getTestReport();
        
        $filename = 'security-test-report-' . date('Y-m-d-H-i-s') . '.json';
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        echo json_encode($report, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Scan files
     */
    public function scanFiles() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $integrity_scanner = new \KloudbeanEnterpriseSecurity\Modules\IntegrityScanner();
        $results = $integrity_scanner->scanCoreFiles();
        
        wp_send_json_success($results);
    }
    
    /**
     * Get file results
     */
    public function getFileResults() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $integrity_scanner = new \KloudbeanEnterpriseSecurity\Modules\IntegrityScanner();
        $results = $integrity_scanner->scanCoreFiles();
        
        wp_send_json_success($results);
    }
    
    /**
     * Create baseline
     */
    public function createBaseline() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $integrity_scanner = new \KloudbeanEnterpriseSecurity\Modules\IntegrityScanner();
        $count = $integrity_scanner->createBaselineHashes();
        
        wp_send_json_success(array(
            'message' => "Baseline created for $count files",
            'count' => $count
        ));
    }
    
    /**
     * Restore file
     */
    public function restoreFile() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $file_path = sanitize_text_field($_POST['file_path']);
        
        if (empty($file_path)) {
            wp_send_json_error('File path is required');
        }
        
        $integrity_scanner = new \KloudbeanEnterpriseSecurity\Modules\IntegrityScanner();
        $result = $integrity_scanner->restoreFile($file_path);
        
        if ($result) {
            wp_send_json_success('File restored successfully');
        } else {
            wp_send_json_error('Failed to restore file');
        }
    }
    
    /**
     * Restore all files
     */
    public function restoreAllFiles() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $integrity_scanner = new \KloudbeanEnterpriseSecurity\Modules\IntegrityScanner();
        $modified_files = $integrity_scanner->scanCoreFiles();
        
        $restored_count = 0;
        
        foreach ($modified_files as $file) {
            if ($integrity_scanner->restoreFile($file['path'])) {
                $restored_count++;
            }
        }
        
        wp_send_json_success(array(
            'message' => "Restored $restored_count files",
            'count' => $restored_count
        ));
    }
    
    /**
     * Run scanner
     */
    public function runScanner() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        // This would integrate with the actual scanner module
        wp_send_json_success(array(
            'message' => 'Scanner functionality not yet implemented'
        ));
    }
    
    /**
     * Get scanner results
     */
    public function getScannerResults() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        // This would integrate with the actual scanner module
        wp_send_json_success(array(
            'results' => array(),
            'message' => 'Scanner results not yet implemented'
        ));
    }
    
    /**
     * Get firewall rules
     */
    public function getFirewallRules() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        // This would integrate with the actual firewall module
        wp_send_json_success(array(
            'rules' => array(),
            'message' => 'Firewall rules not yet implemented'
        ));
    }
    
    /**
     * Add firewall rule
     */
    public function addFirewallRule() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        // This would integrate with the actual firewall module
        wp_send_json_success(array(
            'message' => 'Firewall rule functionality not yet implemented'
        ));
    }
    
    /**
     * Delete firewall rule
     */
    public function deleteFirewallRule() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        // This would integrate with the actual firewall module
        wp_send_json_success(array(
            'message' => 'Firewall rule deletion not yet implemented'
        ));
    }
    
    /**
     * Block IP
     */
    public function blockIP() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $ip = sanitize_text_field($_POST['ip']);
        
        if (empty($ip) || !filter_var($ip, FILTER_VALIDATE_IP)) {
            wp_send_json_error('Invalid IP address');
        }
        
        // This would integrate with the actual firewall module
        wp_send_json_success(array(
            'message' => 'IP blocking functionality not yet implemented'
        ));
    }
    
    /**
     * Unblock IP
     */
    public function unblockIP() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $ip = sanitize_text_field($_POST['ip']);
        
        if (empty($ip) || !filter_var($ip, FILTER_VALIDATE_IP)) {
            wp_send_json_error('Invalid IP address');
        }
        
        // This would integrate with the actual firewall module
        wp_send_json_success(array(
            'message' => 'IP unblocking functionality not yet implemented'
        ));
    }
    
    /**
     * Get security logs
     */
    public function getSecurityLogs() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        // This would integrate with the actual logging module
        wp_send_json_success(array(
            'logs' => array(),
            'message' => 'Security logs not yet implemented'
        ));
    }
    
    /**
     * Clear logs
     */
    public function clearLogs() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        // This would integrate with the actual logging module
        wp_send_json_success(array(
            'message' => 'Log clearing functionality not yet implemented'
        ));
    }
    
    /**
     * Save settings
     */
    public function saveSettings() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        $settings = $_POST['settings'] ?? array();
        
        foreach ($settings as $key => $value) {
            $sanitized_key = sanitize_key($key);
            $sanitized_value = sanitize_text_field($value);
            
            update_option('kbes_' . $sanitized_key, $sanitized_value);
        }
        
        wp_send_json_success(array(
            'message' => 'Settings saved successfully'
        ));
    }
    
    /**
     * Reset settings
     */
    public function resetSettings() {
        check_ajax_referer('kbes_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Insufficient permissions');
        }
        
        // Reset all settings to defaults
        $default_settings = array(
            'security_level' => 'medium',
            'rate_limit' => 100,
            'max_login_attempts' => 5,
            'lockout_time' => 300,
            'blocked_countries' => array(),
            'allowed_file_types' => array('jpg', 'jpeg', 'png', 'gif', 'pdf'),
            'max_file_size' => 10485760, // 10MB
            'encryption_enabled' => false,
            'backup_enabled' => false,
            'security_enabled' => true
        );
        
        foreach ($default_settings as $key => $value) {
            update_option('kbes_' . $key, $value);
        }
        
        wp_send_json_success(array(
            'message' => 'Settings reset to defaults'
        ));
    }
}
