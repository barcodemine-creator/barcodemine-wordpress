<?php
/**
 * Database Security Tests for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Modules\SecurityTests;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Database security tests class
 */
class DatabaseTests {
    
    /**
     * Check database security
     */
    public function checkDatabaseSecurity() {
        global $wpdb;
        
        $issues = array();
        
        // Check database user privileges
        $user_privileges = $wpdb->get_results("SHOW GRANTS FOR CURRENT_USER()");
        
        if (!empty($user_privileges)) {
            foreach ($user_privileges as $grant) {
                if (strpos($grant->{'Grants for ' . $wpdb->dbuser . '@' . $wpdb->dbhost}, 'GRANT ALL PRIVILEGES') !== false) {
                    $issues[] = 'Database user has ALL PRIVILEGES';
                }
            }
        }
        
        // Check for weak database passwords
        $password_strength = $this->checkPasswordStrength(DB_PASSWORD);
        
        if ($password_strength < 3) {
            $issues[] = 'Database password is weak';
        }
        
        // Check database connection security
        if (strpos(DB_HOST, 'localhost') === false && strpos(DB_HOST, '127.0.0.1') === false) {
            $issues[] = 'Database is not on localhost';
        }
        
        if (!empty($issues)) {
            return array(
                'status' => 'warn',
                'message' => 'Database security issues found: ' . implode(', ', $issues),
                'score' => 50,
                'category' => 'database',
                'auto_fixable' => false,
                'details' => $issues
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'Database security is properly configured',
            'score' => 100,
            'category' => 'database',
            'auto_fixable' => false
        );
    }
    
    /**
     * Check database prefix
     */
    public function checkDatabasePrefix() {
        global $wpdb;
        
        if ($wpdb->prefix === 'wp_') {
            return array(
                'status' => 'fail',
                'message' => 'Database prefix is still default (wp_)',
                'score' => 0,
                'category' => 'database',
                'auto_fixable' => false
            );
        }
        
        return array(
            'status' => 'pass',
            'message' => 'Database prefix has been changed from default',
            'score' => 100,
            'category' => 'database',
            'auto_fixable' => false
        );
    }
    
    /**
     * Check password strength
     */
    private function checkPasswordStrength($password) {
        $score = 0;
        
        // Length check
        if (strlen($password) >= 8) {
            $score++;
        }
        
        // Uppercase check
        if (preg_match('/[A-Z]/', $password)) {
            $score++;
        }
        
        // Lowercase check
        if (preg_match('/[a-z]/', $password)) {
            $score++;
        }
        
        // Number check
        if (preg_match('/[0-9]/', $password)) {
            $score++;
        }
        
        // Special character check
        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            $score++;
        }
        
        return $score;
    }
}
