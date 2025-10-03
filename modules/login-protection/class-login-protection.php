<?php
/**
 * Login Protection Module for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Modules;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Login Protection class handling login security and protection
 */
class LoginProtection {
    
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
     * Initialize login protection
     */
    private function init() {
        add_action('init', array($this, 'initLoginProtection'));
    }
    
    /**
     * Initialize login protection
     */
    public function initLoginProtection() {
        // Set up login protection hooks
        add_action('wp_login_failed', array($this, 'handleFailedLogin'));
        add_action('wp_login', array($this, 'handleSuccessfulLogin'), 10, 2);
        add_action('wp_logout', array($this, 'handleLogout'));
        
        // Add brute force protection
        add_filter('authenticate', array($this, 'checkBruteForce'), 30, 3);
        
        // Add 2FA support
        add_action('wp_authenticate_user', array($this, 'check2FA'), 10, 2);
        
        // Add password policy
        add_action('user_profile_update_errors', array($this, 'validatePasswordPolicy'), 10, 3);
        
        // Hide login page
        if (get_option('kbes_hide_login_page', false)) {
            add_action('init', array($this, 'hideLoginPage'));
        }
        
        // Disable user enumeration
        if (get_option('kbes_disable_user_enumeration', true)) {
            add_action('init', array($this, 'disableUserEnumeration'));
        }
        
        // Add login rate limiting
        add_action('wp_login_failed', array($this, 'checkLoginRateLimit'));
    }
    
    /**
     * Handle failed login
     */
    public function handleFailedLogin($username) {
        $ip = $this->getClientIP();
        
        // Log failed login
        $this->logFailedLogin($username, $ip);
        
        // Check for brute force attack
        $this->checkBruteForceAttack($username, $ip);
        
        // Increment failed login count
        $this->incrementFailedLoginCount($username, $ip);
    }
    
    /**
     * Handle successful login
     */
    public function handleSuccessfulLogin($user_login, $user) {
        $ip = $this->getClientIP();
        
        // Log successful login
        $this->logSuccessfulLogin($user_login, $user, $ip);
        
        // Reset failed login count
        $this->resetFailedLoginCount($user_login, $ip);
        
        // Check for suspicious login
        $this->checkSuspiciousLogin($user, $ip);
    }
    
    /**
     * Handle logout
     */
    public function handleLogout() {
        $user_id = get_current_user_id();
        $ip = $this->getClientIP();
        
        // Log logout
        $this->logLogout($user_id, $ip);
    }
    
    /**
     * Check brute force attack
     */
    public function checkBruteForce($user, $username, $password) {
        $ip = $this->getClientIP();
        $max_attempts = get_option('kbes_max_login_attempts', 5);
        $lockout_time = get_option('kbes_lockout_time', 900); // 15 minutes
        
        $key = 'kbes_login_attempts_' . md5($ip . $username);
        $attempts = get_transient($key);
        
        if ($attempts === false) {
            set_transient($key, 1, $lockout_time);
            return $user;
        }
        
        if ($attempts >= $max_attempts) {
            $this->logBruteForceAttack($username, $ip, $attempts);
            $this->blockIP($ip, 'Brute force attack detected');
            
            return new \WP_Error('too_many_attempts', 'Too many failed login attempts. Please try again later.');
        }
        
        set_transient($key, $attempts + 1, $lockout_time);
        return $user;
    }
    
    /**
     * Check 2FA
     */
    public function check2FA($user, $password) {
        if (get_user_meta($user->ID, 'kbes_2fa_enabled', true)) {
            $this->require2FA($user);
        }
        
        return $user;
    }
    
    /**
     * Validate password policy
     */
    public function validatePasswordPolicy($errors, $update, $user) {
        $password = $_POST['pass1'] ?? '';
        
        if (empty($password)) {
            return;
        }
        
        $min_length = get_option('kbes_password_min_length', 8);
        $require_uppercase = get_option('kbes_password_require_uppercase', true);
        $require_lowercase = get_option('kbes_password_require_lowercase', true);
        $require_numbers = get_option('kbes_password_require_numbers', true);
        $require_symbols = get_option('kbes_password_require_symbols', true);
        
        if (strlen($password) < $min_length) {
            $errors->add('password_too_short', sprintf('Password must be at least %d characters long.', $min_length));
        }
        
        if ($require_uppercase && !preg_match('/[A-Z]/', $password)) {
            $errors->add('password_no_uppercase', 'Password must contain at least one uppercase letter.');
        }
        
        if ($require_lowercase && !preg_match('/[a-z]/', $password)) {
            $errors->add('password_no_lowercase', 'Password must contain at least one lowercase letter.');
        }
        
        if ($require_numbers && !preg_match('/[0-9]/', $password)) {
            $errors->add('password_no_numbers', 'Password must contain at least one number.');
        }
        
        if ($require_symbols && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors->add('password_no_symbols', 'Password must contain at least one special character.');
        }
    }
    
    /**
     * Hide login page
     */
    public function hideLoginPage() {
        if (is_admin() || is_user_logged_in()) {
            return;
        }
        
        $login_url = get_option('kbes_custom_login_url', '');
        
        if (empty($login_url)) {
            $login_url = $this->generateCustomLoginUrl();
            update_option('kbes_custom_login_url', $login_url);
        }
        
        if ($_SERVER['REQUEST_URI'] === '/wp-login.php' || $_SERVER['REQUEST_URI'] === '/wp-admin/') {
            wp_redirect(home_url());
            exit;
        }
    }
    
    /**
     * Disable user enumeration
     */
    public function disableUserEnumeration() {
        if (isset($_GET['author'])) {
            wp_redirect(home_url());
            exit;
        }
    }
    
    /**
     * Check login rate limit
     */
    public function checkLoginRateLimit() {
        $ip = $this->getClientIP();
        $rate_limit = get_option('kbes_login_rate_limit', 10);
        $time_window = 60; // 1 minute
        
        $key = 'kbes_login_rate_' . md5($ip);
        $attempts = get_transient($key);
        
        if ($attempts === false) {
            set_transient($key, 1, $time_window);
            return;
        }
        
        if ($attempts >= $rate_limit) {
            $this->blockIP($ip, 'Login rate limit exceeded');
        }
        
        set_transient($key, $attempts + 1, $time_window);
    }
    
    /**
     * Check brute force attack
     */
    private function checkBruteForceAttack($username, $ip) {
        $max_attempts = get_option('kbes_max_login_attempts', 5);
        $lockout_time = get_option('kbes_lockout_time', 900);
        
        $key = 'kbes_login_attempts_' . md5($ip . $username);
        $attempts = get_transient($key);
        
        if ($attempts && $attempts >= $max_attempts) {
            $this->logBruteForceAttack($username, $ip, $attempts);
            $this->blockIP($ip, 'Brute force attack detected');
        }
    }
    
    /**
     * Check suspicious login
     */
    private function checkSuspiciousLogin($user, $ip) {
        $last_login_ip = get_user_meta($user->ID, 'kbes_last_login_ip', true);
        $last_login_time = get_user_meta($user->ID, 'kbes_last_login_time', true);
        
        // Check for IP change
        if ($last_login_ip && $last_login_ip !== $ip) {
            $this->logSuspiciousLogin($user, $ip, $last_login_ip, 'IP change');
        }
        
        // Check for time-based suspicious activity
        if ($last_login_time && (time() - $last_login_time) < 300) { // 5 minutes
            $this->logSuspiciousLogin($user, $ip, $last_login_ip, 'Rapid login');
        }
        
        // Update last login info
        update_user_meta($user->ID, 'kbes_last_login_ip', $ip);
        update_user_meta($user->ID, 'kbes_last_login_time', time());
    }
    
    /**
     * Require 2FA
     */
    private function require2FA($user) {
        // Implementation for 2FA requirement
        // This would typically involve redirecting to a 2FA verification page
    }
    
    /**
     * Generate custom login URL
     */
    private function generateCustomLoginUrl() {
        $random_string = $this->utilities->generateRandomString(32);
        return home_url('/' . $random_string . '/');
    }
    
    /**
     * Log failed login
     */
    private function logFailedLogin($username, $ip) {
        $this->logging->logSecurityEvent('failed_login', array(
            'username' => $username,
            'ip' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log successful login
     */
    private function logSuccessfulLogin($user_login, $user, $ip) {
        $this->logging->logSecurityEvent('successful_login', array(
            'username' => $user_login,
            'user_id' => $user->ID,
            'ip' => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log logout
     */
    private function logLogout($user_id, $ip) {
        $this->logging->logSecurityEvent('logout', array(
            'user_id' => $user_id,
            'ip' => $ip,
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log brute force attack
     */
    private function logBruteForceAttack($username, $ip, $attempts) {
        $this->logging->logSecurityEvent('brute_force_attack', array(
            'username' => $username,
            'ip' => $ip,
            'attempts' => $attempts,
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log suspicious login
     */
    private function logSuspiciousLogin($user, $ip, $last_ip, $reason) {
        $this->logging->logSecurityEvent('suspicious_login', array(
            'user_id' => $user->ID,
            'username' => $user->user_login,
            'ip' => $ip,
            'last_ip' => $last_ip,
            'reason' => $reason,
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Increment failed login count
     */
    private function incrementFailedLoginCount($username, $ip) {
        $key = 'kbes_login_attempts_' . md5($ip . $username);
        $attempts = get_transient($key);
        
        if ($attempts === false) {
            set_transient($key, 1, get_option('kbes_lockout_time', 900));
        } else {
            set_transient($key, $attempts + 1, get_option('kbes_lockout_time', 900));
        }
    }
    
    /**
     * Reset failed login count
     */
    private function resetFailedLoginCount($username, $ip) {
        $key = 'kbes_login_attempts_' . md5($ip . $username);
        delete_transient($key);
    }
    
    /**
     * Block IP
     */
    private function blockIP($ip, $reason) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_blacklist';
        
        $wpdb->insert(
            $table_name,
            array(
                'ip' => $ip,
                'reason' => $reason,
                'source' => 'automatic',
                'expires_at' => date('Y-m-d H:i:s', strtotime('+1 day')),
                'created_at' => current_time('mysql'),
                'created_by' => get_current_user_id()
            ),
            array(
                '%s', '%s', '%s', '%s', '%s', '%d'
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
     * Get login statistics
     */
    public function getLoginStatistics() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_logs';
        
        $stats = array();
        
        // Total logins
        $stats['total_logins'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE event_type = 'successful_login'");
        
        // Failed logins
        $stats['failed_logins'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE event_type = 'failed_login'");
        
        // Brute force attacks
        $stats['brute_force_attacks'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE event_type = 'brute_force_attack'");
        
        // Suspicious logins
        $stats['suspicious_logins'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE event_type = 'suspicious_login'");
        
        // Recent logins
        $stats['recent_logins'] = $wpdb->get_results("SELECT * FROM $table_name WHERE event_type IN ('successful_login', 'failed_login') ORDER BY timestamp DESC LIMIT 10");
        
        return $stats;
    }
    
    /**
     * Get user login history
     */
    public function getUserLoginHistory($user_id) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_security_logs';
        
        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE event_type IN ('successful_login', 'failed_login', 'logout') AND user_id = %d ORDER BY timestamp DESC",
            $user_id
        ));
    }
    
    /**
     * Enable 2FA for user
     */
    public function enable2FA($user_id) {
        update_user_meta($user_id, 'kbes_2fa_enabled', true);
        update_user_meta($user_id, 'kbes_2fa_secret', $this->generate2FASecret());
    }
    
    /**
     * Disable 2FA for user
     */
    public function disable2FA($user_id) {
        delete_user_meta($user_id, 'kbes_2fa_enabled');
        delete_user_meta($user_id, 'kbes_2fa_secret');
    }
    
    /**
     * Generate 2FA secret
     */
    private function generate2FASecret() {
        return $this->utilities->generateRandomString(32);
    }
    
    /**
     * Get 2FA status
     */
    public function get2FAStatus($user_id) {
        return get_user_meta($user_id, 'kbes_2fa_enabled', true);
    }
}
