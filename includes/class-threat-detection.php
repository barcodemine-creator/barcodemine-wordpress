<?php
/**
 * Threat Detection for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Threat Detection class handling threat detection and analysis
 */
class ThreatDetection {
    
    private $database;
    private $analytics;
    private $logging;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->database = new Database();
        $this->analytics = new Analytics();
        $this->logging = new Logging();
        
        $this->init();
    }
    
    /**
     * Initialize threat detection
     */
    private function init() {
        add_action('init', array($this, 'initThreatDetection'));
        add_action('wp_loaded', array($this, 'scanForThreats'));
    }
    
    /**
     * Initialize threat detection
     */
    public function initThreatDetection() {
        // Set up threat detection hooks
        add_action('wp_loaded', array($this, 'monitorTraffic'));
        add_action('wp_loaded', array($this, 'monitorFileChanges'));
        add_action('wp_loaded', array($this, 'monitorDatabaseChanges'));
    }
    
    /**
     * Scan for threats
     */
    public function scanForThreats() {
        // Scan for malware
        $this->scanForMalware();
        
        // Scan for vulnerabilities
        $this->scanForVulnerabilities();
        
        // Scan for suspicious activity
        $this->scanForSuspiciousActivity();
        
        // Scan for brute force attacks
        $this->scanForBruteForceAttacks();
        
        // Scan for DDoS attacks
        $this->scanForDDoSAttacks();
    }
    
    /**
     * Monitor traffic
     */
    public function monitorTraffic() {
        $ip = $this->getClientIP();
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $request_uri = $_SERVER['REQUEST_URI'] ?? '';
        
        // Check for suspicious patterns
        if ($this->isSuspiciousTraffic($ip, $user_agent, $request_uri)) {
            $this->logThreat('suspicious_traffic', array(
                'ip' => $ip,
                'user_agent' => $user_agent,
                'request_uri' => $request_uri,
                'timestamp' => current_time('mysql')
            ));
        }
    }
    
    /**
     * Monitor file changes
     */
    public function monitorFileChanges() {
        // Monitor core file changes
        $this->monitorCoreFileChanges();
        
        // Monitor plugin file changes
        $this->monitorPluginFileChanges();
        
        // Monitor theme file changes
        $this->monitorThemeFileChanges();
    }
    
    /**
     * Monitor database changes
     */
    public function monitorDatabaseChanges() {
        // Monitor user changes
        add_action('user_register', array($this, 'logUserChange'));
        add_action('delete_user', array($this, 'logUserChange'));
        add_action('profile_update', array($this, 'logUserChange'));
        
        // Monitor option changes
        add_action('updated_option', array($this, 'logOptionChange'), 10, 3);
        add_action('added_option', array($this, 'logOptionChange'), 10, 2);
        add_action('deleted_option', array($this, 'logOptionChange'), 10, 1);
    }
    
    /**
     * Scan for malware
     */
    private function scanForMalware() {
        // Scan for common malware patterns
        $this->scanForMalwarePatterns();
        
        // Scan for suspicious files
        $this->scanForSuspiciousFiles();
        
        // Scan for backdoors
        $this->scanForBackdoors();
    }
    
    /**
     * Scan for vulnerabilities
     */
    private function scanForVulnerabilities() {
        // Scan WordPress core vulnerabilities
        $this->scanCoreVulnerabilities();
        
        // Scan plugin vulnerabilities
        $this->scanPluginVulnerabilities();
        
        // Scan theme vulnerabilities
        $this->scanThemeVulnerabilities();
    }
    
    /**
     * Scan for suspicious activity
     */
    private function scanForSuspiciousActivity() {
        // Scan for unusual login patterns
        $this->scanForUnusualLoginPatterns();
        
        // Scan for unusual admin activity
        $this->scanForUnusualAdminActivity();
        
        // Scan for unusual file access
        $this->scanForUnusualFileAccess();
    }
    
    /**
     * Scan for brute force attacks
     */
    private function scanForBruteForceAttacks() {
        $ip = $this->getClientIP();
        $max_attempts = get_option('kbes_max_login_attempts', 5);
        $time_window = get_option('kbes_brute_force_window', 900); // 15 minutes
        
        $key = 'kbes_brute_force_' . md5($ip);
        $attempts = get_transient($key);
        
        if ($attempts && $attempts >= $max_attempts) {
            $this->logThreat('brute_force_attack', array(
                'ip' => $ip,
                'attempts' => $attempts,
                'timestamp' => current_time('mysql')
            ));
        }
    }
    
    /**
     * Scan for DDoS attacks
     */
    private function scanForDDoSAttacks() {
        $ip = $this->getClientIP();
        $max_requests = get_option('kbes_max_requests_per_minute', 100);
        $time_window = 60; // 1 minute
        
        $key = 'kbes_ddos_' . md5($ip);
        $requests = get_transient($key);
        
        if ($requests && $requests >= $max_requests) {
            $this->logThreat('ddos_attack', array(
                'ip' => $ip,
                'requests' => $requests,
                'timestamp' => current_time('mysql')
            ));
        }
    }
    
    /**
     * Check if traffic is suspicious
     */
    private function isSuspiciousTraffic($ip, $user_agent, $request_uri) {
        // Check for suspicious patterns
        $suspicious_patterns = array(
            '/\.\.\//',
            '/\.\.\\\\/',
            '/eval\s*\(/',
            '/base64_decode/',
            '/system\s*\(/',
            '/exec\s*\(/',
            '/shell_exec/',
            '/passthru/',
            '/proc_open/',
            '/popen/',
            '/file_get_contents\s*\(\s*["\']?http/',
            '/curl_exec/',
            '/fsockopen/',
            '/socket_create/',
            '/gzinflate/',
            '/str_rot13/',
            '/create_function/',
            '/assert\s*\(/',
            '/preg_replace\s*\([^,]+,\s*["\']?\/e/',
            '/<script[^>]*>.*?<\/script>/i',
            '/<iframe[^>]*>.*?<\/iframe>/i',
            '/javascript:/i',
            '/vbscript:/i'
        );
        
        foreach ($suspicious_patterns as $pattern) {
            if (preg_match($pattern, $request_uri) || preg_match($pattern, $user_agent)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Monitor core file changes
     */
    private function monitorCoreFileChanges() {
        // Implementation for monitoring core file changes
    }
    
    /**
     * Monitor plugin file changes
     */
    private function monitorPluginFileChanges() {
        // Implementation for monitoring plugin file changes
    }
    
    /**
     * Monitor theme file changes
     */
    private function monitorThemeFileChanges() {
        // Implementation for monitoring theme file changes
    }
    
    /**
     * Log user change
     */
    public function logUserChange($user_id) {
        $this->logThreat('user_change', array(
            'user_id' => $user_id,
            'ip' => $this->getClientIP(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log option change
     */
    public function logOptionChange($option_name, $old_value = null, $value = null) {
        $this->logThreat('option_change', array(
            'option_name' => $option_name,
            'old_value' => $old_value,
            'new_value' => $value,
            'ip' => $this->getClientIP(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Scan for malware patterns
     */
    private function scanForMalwarePatterns() {
        // Implementation for scanning malware patterns
    }
    
    /**
     * Scan for suspicious files
     */
    private function scanForSuspiciousFiles() {
        // Implementation for scanning suspicious files
    }
    
    /**
     * Scan for backdoors
     */
    private function scanForBackdoors() {
        // Implementation for scanning backdoors
    }
    
    /**
     * Scan core vulnerabilities
     */
    private function scanCoreVulnerabilities() {
        // Implementation for scanning core vulnerabilities
    }
    
    /**
     * Scan plugin vulnerabilities
     */
    private function scanPluginVulnerabilities() {
        // Implementation for scanning plugin vulnerabilities
    }
    
    /**
     * Scan theme vulnerabilities
     */
    private function scanThemeVulnerabilities() {
        // Implementation for scanning theme vulnerabilities
    }
    
    /**
     * Scan for unusual login patterns
     */
    private function scanForUnusualLoginPatterns() {
        // Implementation for scanning unusual login patterns
    }
    
    /**
     * Scan for unusual admin activity
     */
    private function scanForUnusualAdminActivity() {
        // Implementation for scanning unusual admin activity
    }
    
    /**
     * Scan for unusual file access
     */
    private function scanForUnusualFileAccess() {
        // Implementation for scanning unusual file access
    }
    
    /**
     * Log threat
     */
    private function logThreat($threat_type, $data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_threats';
        
        $wpdb->insert(
            $table_name,
            array(
                'threat_type' => $threat_type,
                'threat_name' => $data['threat_name'] ?? $threat_type,
                'description' => $data['description'] ?? '',
                'severity' => $data['severity'] ?? 'medium',
                'source' => $data['source'] ?? 'automatic',
                'ip_address' => $data['ip'] ?? '',
                'user_id' => $data['user_id'] ?? null,
                'file_path' => $data['file_path'] ?? null,
                'file_hash' => $data['file_hash'] ?? null,
                'payload' => $data['payload'] ?? null,
                'signature_id' => $data['signature_id'] ?? null,
                'blocked' => $data['blocked'] ?? 1,
                'quarantined' => $data['quarantined'] ?? 0,
                'resolved' => $data['resolved'] ?? 0,
                'timestamp' => $data['timestamp'] ?? current_time('mysql')
            ),
            array(
                '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%d', '%d', '%s'
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
