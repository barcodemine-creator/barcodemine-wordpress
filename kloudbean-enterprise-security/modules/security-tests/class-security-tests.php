<?php
/**
 * Security Tests Module for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Modules;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Security Tests class handling security testing and scoring
 */
class SecurityTests {
    
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
     * Initialize security tests
     */
    private function init() {
        add_action('init', array($this, 'initSecurityTests'));
        add_action('kbes_daily_security_scan', array($this, 'runDailyTests'));
    }
    
    /**
     * Initialize security tests
     */
    public function initSecurityTests() {
        // Set up security test hooks
        add_action('wp_loaded', array($this, 'runSecurityTests'));
    }
    
    /**
     * Run daily tests
     */
    public function runDailyTests() {
        $this->runSecurityTests();
    }
    
    /**
     * Run security tests
     */
    public function runSecurityTests() {
        $this->logTestEvent('security_tests_started', array(
            'timestamp' => current_time('mysql')
        ));
        
        // Run all security tests
        $tests = $this->getAllTests();
        $results = array();
        
        foreach ($tests as $test) {
            $result = $this->runTest($test);
            $results[] = $result;
            $this->updateTestResult($test['id'], $result);
        }
        
        // Calculate security score
        $score = $this->calculateSecurityScore($results);
        
        // Update security score
        update_option('kbes_security_score', $score);
        
        $this->logTestEvent('security_tests_completed', array(
            'score' => $score,
            'timestamp' => current_time('mysql')
        ));
        
        return $results;
    }
    
    /**
     * Run individual test
     */
    private function runTest($test) {
        $test_class = $test['class'];
        $test_method = $test['method'];
        
        if (class_exists($test_class)) {
            $test_instance = new $test_class();
            
            if (method_exists($test_instance, $test_method)) {
                try {
                    $result = $test_instance->$test_method();
                    return $result;
                } catch (Exception $e) {
                    return array(
                        'status' => 'error',
                        'message' => 'Test failed: ' . $e->getMessage(),
                        'score' => 0
                    );
                }
            }
        }
        
        return array(
            'status' => 'error',
            'message' => 'Test method not found',
            'score' => 0
        );
    }
    
    /**
     * Get all tests
     */
    private function getAllTests() {
        return array(
            // WordPress Core Tests
            array(
                'id' => 'wp_version_check',
                'name' => 'WordPress Version Check',
                'description' => 'Check if WordPress is up to date',
                'category' => 'core',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\CoreTests',
                'method' => 'checkWordPressVersion',
                'weight' => 10
            ),
            array(
                'id' => 'wp_debug_check',
                'name' => 'Debug Mode Check',
                'description' => 'Check if debug mode is disabled',
                'category' => 'core',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\CoreTests',
                'method' => 'checkDebugMode',
                'weight' => 5
            ),
            array(
                'id' => 'wp_file_editor_check',
                'name' => 'File Editor Check',
                'description' => 'Check if file editor is disabled',
                'category' => 'core',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\CoreTests',
                'method' => 'checkFileEditor',
                'weight' => 5
            ),
            array(
                'id' => 'wp_directory_listing_check',
                'name' => 'Directory Listing Check',
                'description' => 'Check if directory listing is disabled',
                'category' => 'core',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\CoreTests',
                'method' => 'checkDirectoryListing',
                'weight' => 5
            ),
            array(
                'id' => 'wp_xmlrpc_check',
                'name' => 'XML-RPC Check',
                'description' => 'Check if XML-RPC is properly configured',
                'category' => 'core',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\CoreTests',
                'method' => 'checkXMLRPC',
                'weight' => 5
            ),
            
            // Plugin Tests
            array(
                'id' => 'plugin_updates_check',
                'name' => 'Plugin Updates Check',
                'description' => 'Check if plugins are up to date',
                'category' => 'plugins',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\PluginTests',
                'method' => 'checkPluginUpdates',
                'weight' => 8
            ),
            array(
                'id' => 'plugin_vulnerabilities_check',
                'name' => 'Plugin Vulnerabilities Check',
                'description' => 'Check for known plugin vulnerabilities',
                'category' => 'plugins',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\PluginTests',
                'method' => 'checkPluginVulnerabilities',
                'weight' => 10
            ),
            array(
                'id' => 'inactive_plugins_check',
                'name' => 'Inactive Plugins Check',
                'description' => 'Check for inactive plugins that should be removed',
                'category' => 'plugins',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\PluginTests',
                'method' => 'checkInactivePlugins',
                'weight' => 3
            ),
            
            // Theme Tests
            array(
                'id' => 'theme_updates_check',
                'name' => 'Theme Updates Check',
                'description' => 'Check if themes are up to date',
                'category' => 'themes',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\ThemeTests',
                'method' => 'checkThemeUpdates',
                'weight' => 8
            ),
            array(
                'id' => 'theme_vulnerabilities_check',
                'name' => 'Theme Vulnerabilities Check',
                'description' => 'Check for known theme vulnerabilities',
                'category' => 'themes',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\ThemeTests',
                'method' => 'checkThemeVulnerabilities',
                'weight' => 10
            ),
            
            // User Tests
            array(
                'id' => 'admin_users_check',
                'name' => 'Admin Users Check',
                'description' => 'Check for admin users with weak passwords',
                'category' => 'users',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\UserTests',
                'method' => 'checkAdminUsers',
                'weight' => 8
            ),
            array(
                'id' => 'user_enumeration_check',
                'name' => 'User Enumeration Check',
                'description' => 'Check if user enumeration is disabled',
                'category' => 'users',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\UserTests',
                'method' => 'checkUserEnumeration',
                'weight' => 5
            ),
            array(
                'id' => 'login_protection_check',
                'name' => 'Login Protection Check',
                'description' => 'Check if login protection is enabled',
                'category' => 'users',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\UserTests',
                'method' => 'checkLoginProtection',
                'weight' => 8
            ),
            
            // File Tests
            array(
                'id' => 'file_permissions_check',
                'name' => 'File Permissions Check',
                'description' => 'Check if file permissions are secure',
                'category' => 'files',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FileTests',
                'method' => 'checkFilePermissions',
                'weight' => 7
            ),
            array(
                'id' => 'upload_security_check',
                'name' => 'Upload Security Check',
                'description' => 'Check if upload security is properly configured',
                'category' => 'files',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FileTests',
                'method' => 'checkUploadSecurity',
                'weight' => 8
            ),
            array(
                'id' => 'file_integrity_check',
                'name' => 'File Integrity Check',
                'description' => 'Check for modified core files',
                'category' => 'files',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FileTests',
                'method' => 'checkFileIntegrity',
                'weight' => 10
            ),
            
            // Database Tests
            array(
                'id' => 'database_security_check',
                'name' => 'Database Security Check',
                'description' => 'Check database security configuration',
                'category' => 'database',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\DatabaseTests',
                'method' => 'checkDatabaseSecurity',
                'weight' => 8
            ),
            array(
                'id' => 'database_prefix_check',
                'name' => 'Database Prefix Check',
                'description' => 'Check if database prefix is changed from default',
                'category' => 'database',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\DatabaseTests',
                'method' => 'checkDatabasePrefix',
                'weight' => 5
            ),
            
            // Server Tests
            array(
                'id' => 'php_version_check',
                'name' => 'PHP Version Check',
                'description' => 'Check if PHP version is up to date',
                'category' => 'server',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\ServerTests',
                'method' => 'checkPHPVersion',
                'weight' => 10
            ),
            array(
                'id' => 'mysql_version_check',
                'name' => 'MySQL Version Check',
                'description' => 'Check if MySQL version is up to date',
                'category' => 'server',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\ServerTests',
                'method' => 'checkMySQLVersion',
                'weight' => 8
            ),
            array(
                'id' => 'ssl_check',
                'name' => 'SSL Check',
                'description' => 'Check if SSL is properly configured',
                'category' => 'server',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\ServerTests',
                'method' => 'checkSSL',
                'weight' => 10
            ),
            array(
                'id' => 'security_headers_check',
                'name' => 'Security Headers Check',
                'description' => 'Check if security headers are present',
                'category' => 'server',
                'class' => 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\ServerTests',
                'method' => 'checkSecurityHeaders',
                'weight' => 8
            )
        );
    }
    
    /**
     * Calculate security score
     */
    private function calculateSecurityScore($results) {
        $total_weight = 0;
        $weighted_score = 0;
        
        foreach ($results as $result) {
            $weight = $result['weight'] ?? 1;
            $score = $result['score'] ?? 0;
            
            $total_weight += $weight;
            $weighted_score += $score * $weight;
        }
        
        if ($total_weight > 0) {
            return round(($weighted_score / $total_weight) * 100);
        }
        
        return 0;
    }
    
    /**
     * Update test result
     */
    private function updateTestResult($test_id, $result) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_tests';
        
        $wpdb->replace(
            $table_name,
            array(
                'test_name' => $result['name'] ?? $test_id,
                'test_category' => $result['category'] ?? 'general',
                'test_description' => $result['description'] ?? '',
                'status' => $result['status'] ?? 'not_applicable',
                'result_message' => $result['message'] ?? '',
                'last_run' => current_time('mysql'),
                'last_result' => json_encode($result),
                'auto_fixable' => $result['auto_fixable'] ?? false,
                'weight' => $result['weight'] ?? 1
            ),
            array(
                '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d'
            )
        );
    }
    
    /**
     * Log test event
     */
    private function logTestEvent($event_type, $data) {
        $this->logging->logSystemEvent($event_type, $data);
    }
    
    /**
     * Get test results
     */
    public function getTestResults($filters = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_tests';
        
        $where_clause = '';
        $params = array();
        
        if (!empty($filters['category'])) {
            $where_clause .= ' AND test_category = %s';
            $params[] = $filters['category'];
        }
        
        if (!empty($filters['status'])) {
            $where_clause .= ' AND status = %s';
            $params[] = $filters['status'];
        }
        
        $limit = $filters['limit'] ?? 20;
        $offset = $filters['offset'] ?? 0;
        
        $query = "SELECT * FROM $table_name WHERE 1=1 $where_clause ORDER BY test_category, test_name LIMIT %d OFFSET %d";
        $params[] = $limit;
        $params[] = $offset;
        
        return $wpdb->get_results($wpdb->prepare($query, $params));
    }
    
    /**
     * Get test result
     */
    public function getTestResult($test_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_tests';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE test_name = %s",
            $test_id
        ));
    }
    
    /**
     * Get security score
     */
    public function getSecurityScore() {
        return get_option('kbes_security_score', 0);
    }
    
    /**
     * Get test statistics
     */
    public function getTestStatistics() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_tests';
        
        $stats = array();
        
        // Total tests
        $stats['total_tests'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        // Tests by status
        $stats['by_status'] = $wpdb->get_results("SELECT status, COUNT(*) as count FROM $table_name GROUP BY status");
        
        // Tests by category
        $stats['by_category'] = $wpdb->get_results("SELECT test_category, COUNT(*) as count FROM $table_name GROUP BY test_category");
        
        // Security score
        $stats['security_score'] = $this->getSecurityScore();
        
        return $stats;
    }
    
    /**
     * Fix test issue
     */
    public function fixTestIssue($test_id) {
        $test = $this->getTestResult($test_id);
        
        if (!$test || !$test->auto_fixable) {
            return false;
        }
        
        $result = json_decode($test->last_result, true);
        
        if (isset($result['fix_method'])) {
            $fix_class = $result['fix_class'] ?? 'KloudbeanEnterpriseSecurity\\Modules\\SecurityTests\\FixMethods';
            $fix_method = $result['fix_method'];
            
            if (class_exists($fix_class)) {
                $fix_instance = new $fix_class();
                
                if (method_exists($fix_instance, $fix_method)) {
                    try {
                        $fix_result = $fix_instance->$fix_method();
                        
                        if ($fix_result) {
                            // Re-run the test
                            $this->runTest(array(
                                'id' => $test_id,
                                'name' => $test->test_name,
                                'category' => $test->test_category,
                                'class' => $result['class'] ?? '',
                                'method' => $result['method'] ?? '',
                                'weight' => $test->weight
                            ));
                            
                            return true;
                        }
                    } catch (Exception $e) {
                        return false;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Get test report
     */
    public function getTestReport() {
        $results = $this->getTestResults();
        $statistics = $this->getTestStatistics();
        $score = $this->getSecurityScore();
        
        return array(
            'results' => $results,
            'statistics' => $statistics,
            'score' => $score,
            'generated_at' => current_time('mysql')
        );
    }
}
