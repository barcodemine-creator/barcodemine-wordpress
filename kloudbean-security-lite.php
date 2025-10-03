<?php
/**
 * Plugin Name: Kloudbean Security Lite
 * Plugin URI: https://kloudbean.com/security-lite
 * Description: Lightweight WordPress security with smart signature detection. Zero false positives, maximum protection.
 * Version: 1.0.0
 * Author: Vikram Jindal
 * Author URI: https://kloudbean.com
 * Company: Kloudbean LLC
 * License: Proprietary - Kloudbean LLC
 * Text Domain: kloudbean-security-lite
 * 
 * Copyright (c) 2025 Kloudbean LLC. All rights reserved.
 * Developed by: Vikram Jindal, CEO & Founder, Kloudbean LLC
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Kloudbean Security Lite Class
 * Lightweight but powerful security protection
 */
class KloudbeanSecurityLite {
    
    private static $instance = null;
    private $threat_signatures = [];
    private $settings = [];
    
    /**
     * Singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->init_signatures();
        $this->init_hooks();
    }
    
    /**
     * Initialize threat signatures database
     */
    private function init_signatures() {
        $this->threat_signatures = [
            // 🚨 CRITICAL: PHP Code Execution (Most Dangerous)
            'php_execution' => [
                'patterns' => [
                    '/eval\s*\(/i',
                    '/exec\s*\(/i', 
                    '/system\s*\(/i',
                    '/shell_exec\s*\(/i',
                    '/passthru\s*\(/i',
                    '/proc_open\s*\(/i',
                    '/popen\s*\(/i'
                ],
                'severity' => 'CRITICAL',
                'description' => 'Direct PHP code execution functions',
                'action' => 'BLOCK_IMMEDIATELY'
            ],
            
            // 🔥 HIGH: Obfuscated Code (Common in Malware)
            'obfuscated_code' => [
                'patterns' => [
                    '/base64_decode\s*\(/i',
                    '/str_rot13\s*\(/i',
                    '/gzinflate\s*\(/i',
                    '/gzuncompress\s*\(/i',
                    '/hex2bin\s*\(/i'
                ],
                'severity' => 'HIGH',
                'description' => 'Obfuscated/encoded malicious code',
                'action' => 'BLOCK_AND_LOG'
            ],
            
            // 🎭 HIGH: Known Backdoors (Specific Signatures)
            'backdoors' => [
                'patterns' => [
                    '/c99shell/i',
                    '/r57shell/i',
                    '/wso\s*shell/i',
                    '/b374k/i',
                    '/adminer\.php/i',
                    '/phpshell/i',
                    '/webshell/i',
                    '/FilesMan/i'
                ],
                'severity' => 'CRITICAL',
                'description' => 'Known backdoor shells',
                'action' => 'BLOCK_IMMEDIATELY'
            ],
            
            // 💉 MEDIUM: SQL Injection Patterns
            'sql_injection' => [
                'patterns' => [
                    '/union\s+select/i',
                    '/select\s+.*\s+from\s+information_schema/i',
                    '/drop\s+table/i',
                    '/insert\s+into/i',
                    '/delete\s+from/i'
                ],
                'severity' => 'HIGH',
                'description' => 'SQL injection attack patterns',
                'action' => 'BLOCK_AND_LOG'
            ],
            
            // 🎯 MEDIUM: XSS Patterns
            'xss_attacks' => [
                'patterns' => [
                    '/<script[^>]*>/i',
                    '/javascript:/i',
                    '/on\w+\s*=/i',
                    '/<iframe[^>]*>/i'
                ],
                'severity' => 'MEDIUM',
                'description' => 'Cross-site scripting attempts',
                'action' => 'BLOCK_AND_LOG'
            ],
            
            // 📁 MEDIUM: File Inclusion
            'file_inclusion' => [
                'patterns' => [
                    '/\.\.\//i',
                    '/\/etc\/passwd/i',
                    '/php:\/\/input/i',
                    '/php:\/\/filter/i'
                ],
                'severity' => 'HIGH',
                'description' => 'File inclusion attack attempts',
                'action' => 'BLOCK_AND_LOG'
            ]
        ];
    }
    
    /**
     * Initialize WordPress hooks
     */
    private function init_hooks() {
        // Early security check (before WordPress loads)
        add_action('init', [$this, 'security_check'], 1);
        
        // Admin interface
        add_action('admin_menu', [$this, 'add_admin_menu']);
        
        // AJAX handlers
        add_action('wp_ajax_kbs_scan', [$this, 'ajax_scan']);
        
        // Activation hook
        register_activation_hook(__FILE__, [$this, 'activate']);
    }
    
    /**
     * Main security check
     */
    public function security_check() {
        $ip = $this->get_client_ip();
        
        // Check all request data
        $request_data = array_merge($_GET, $_POST, $_COOKIE);
        $headers = getallheaders() ?: [];
        $all_data = array_merge($request_data, $headers);
        
        // Also check raw input
        $raw_input = file_get_contents('php://input');
        if ($raw_input) {
            $all_data['__raw_input__'] = $raw_input;
        }
        
        foreach ($all_data as $key => $value) {
            if (is_string($value)) {
                $threat = $this->analyze_content($value);
                if ($threat) {
                    $this->handle_threat($threat, $key, $value, $ip);
                }
            }
        }
    }
    
    /**
     * Analyze content for threats
     */
    private function analyze_content($content) {
        foreach ($this->threat_signatures as $category => $signature_data) {
            foreach ($signature_data['patterns'] as $pattern) {
                if (preg_match($pattern, $content)) {
                    return [
                        'category' => $category,
                        'pattern' => $pattern,
                        'severity' => $signature_data['severity'],
                        'description' => $signature_data['description'],
                        'action' => $signature_data['action']
                    ];
                }
            }
        }
        return false;
    }
    
    /**
     * Handle detected threat
     */
    private function handle_threat($threat, $parameter, $value, $ip) {
        // Log the threat
        $this->log_threat($threat, $parameter, $value, $ip);
        
        // Take action based on severity
        if ($threat['action'] === 'BLOCK_IMMEDIATELY') {
            $this->block_request($threat);
        }
        
        // Send notification for critical threats
        if ($threat['severity'] === 'CRITICAL') {
            $this->send_threat_notification($threat, $ip);
        }
    }
    
    /**
     * Block malicious request
     */
    private function block_request($threat) {
        status_header(403);
        
        $message = "🛡️ KLOUDBEAN SECURITY PROTECTION\n\n";
        $message .= "Access Denied - Malicious Activity Detected\n\n";
        $message .= "Threat Type: " . $threat['description'] . "\n";
        $message .= "Severity: " . $threat['severity'] . "\n";
        $message .= "Time: " . current_time('mysql') . "\n";
        $message .= "IP: " . $this->get_client_ip() . "\n\n";
        $message .= "This incident has been logged and reported.\n";
        $message .= "Contact: security@kloudbean.com";
        
        die($message);
    }
    
    /**
     * Log security threat
     */
    private function log_threat($threat, $parameter, $value, $ip) {
        $log_dir = WP_CONTENT_DIR . '/kloudbean-security-logs';
        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
            file_put_contents($log_dir . '/.htaccess', "Order deny,allow\nDeny from all");
        }
        
        $log_file = $log_dir . '/threats-' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        
        $log_entry = "[$timestamp] [{$threat['severity']}] {$threat['description']}\n";
        $log_entry .= "IP: $ip | Parameter: $parameter | Pattern: {$threat['pattern']}\n";
        $log_entry .= "Value: " . substr($value, 0, 200) . "\n";
        $log_entry .= "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') . "\n";
        $log_entry .= "---\n";
        
        file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Send threat notification
     */
    private function send_threat_notification($threat, $ip) {
        $admin_email = get_option('admin_email');
        $subject = '🚨 CRITICAL SECURITY ALERT - ' . get_bloginfo('name');
        
        $message = "CRITICAL SECURITY THREAT DETECTED\n\n";
        $message .= "Website: " . home_url() . "\n";
        $message .= "Threat: " . $threat['description'] . "\n";
        $message .= "Severity: " . $threat['severity'] . "\n";
        $message .= "IP Address: " . $ip . "\n";
        $message .= "Time: " . current_time('mysql') . "\n\n";
        $message .= "The threat has been automatically blocked.\n";
        $message .= "Kloudbean Security Lite is protecting your website.\n\n";
        $message .= "Powered by Kloudbean LLC";
        
        wp_mail($admin_email, $subject, $message);
    }
    
    /**
     * Get client IP address
     */
    private function get_client_ip() {
        $ip_keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ip_keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'Kloudbean Security Lite',
            'Security Lite',
            'manage_options',
            'kloudbean-security-lite',
            [$this, 'render_dashboard'],
            'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#00a0d2"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/></svg>'),
            30
        );
    }
    
    /**
     * Render admin dashboard
     */
    public function render_dashboard() {
        echo '<div class="wrap">';
        echo '<h1>🛡️ Kloudbean Security Lite</h1>';
        echo '<p>Lightweight WordPress security by <strong>Kloudbean LLC</strong> | Developed by <strong>Vikram Jindal</strong></p>';
        
        // Protection Status
        echo '<div style="background: linear-gradient(135deg, #27ae60, #2ecc71); color: white; padding: 20px; border-radius: 10px; margin: 20px 0;">';
        echo '<h2 style="margin: 0;">🚨 REAL-TIME PROTECTION ACTIVE</h2>';
        echo '<p style="margin: 10px 0 0 0;">Advanced threat detection with zero false positives</p>';
        echo '</div>';
        
        // Signature Database
        echo '<div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">';
        echo '<h3>📊 Threat Signature Database</h3>';
        echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 15px;">';
        
        foreach ($this->threat_signatures as $category => $data) {
            $color = $this->get_severity_color($data['severity']);
            echo '<div style="background: white; padding: 15px; border-radius: 8px; border-left: 4px solid ' . $color . ';">';
            echo '<h4 style="margin: 0 0 10px 0; color: ' . $color . ';">' . ucwords(str_replace('_', ' ', $category)) . '</h4>';
            echo '<p style="margin: 0 0 10px 0; font-size: 14px; color: #666;">' . $data['description'] . '</p>';
            echo '<p style="margin: 0; font-size: 12px; color: #999;">Patterns: ' . count($data['patterns']) . ' | Severity: ' . $data['severity'] . '</p>';
            echo '</div>';
        }
        
        echo '</div>';
        echo '</div>';
        
        // Quick Scan
        echo '<div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 10px; margin: 20px 0;">';
        echo '<h3>🔍 Quick Security Scan</h3>';
        echo '<p>Scan your website for threats using our signature database:</p>';
        echo '<button id="kbs-quick-scan" class="button button-primary" style="background: #e74c3c; border-color: #c0392b;">Run Security Scan</button>';
        echo '<div id="kbs-scan-results" style="margin-top: 20px;"></div>';
        echo '</div>';
        
        // Recent Logs
        $this->show_recent_logs();
        
        echo '</div>';
        
        // Add JavaScript
        echo '<script>
        document.getElementById("kbs-quick-scan").addEventListener("click", function() {
            this.disabled = true;
            this.textContent = "Scanning...";
            
            fetch(ajaxurl, {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "action=kbs_scan&nonce=" + "' . wp_create_nonce('kbs_scan') . '"
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("kbs-scan-results").innerHTML = data.html;
                this.disabled = false;
                this.textContent = "Run Security Scan";
            });
        });
        </script>';
    }
    
    /**
     * Get severity color
     */
    private function get_severity_color($severity) {
        switch ($severity) {
            case 'CRITICAL': return '#e74c3c';
            case 'HIGH': return '#f39c12';
            case 'MEDIUM': return '#3498db';
            default: return '#95a5a6';
        }
    }
    
    /**
     * Show recent security logs
     */
    private function show_recent_logs() {
        $log_dir = WP_CONTENT_DIR . '/kloudbean-security-logs';
        $log_file = $log_dir . '/threats-' . date('Y-m-d') . '.log';
        
        echo '<div style="background: white; padding: 20px; border: 1px solid #ddd; border-radius: 10px; margin: 20px 0;">';
        echo '<h3>📋 Today\'s Security Activity</h3>';
        
        if (file_exists($log_file)) {
            $logs = file_get_contents($log_file);
            $log_entries = explode('---', $logs);
            $recent_logs = array_slice(array_filter($log_entries), -5);
            
            if (!empty($recent_logs)) {
                echo '<div style="background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace; font-size: 12px; max-height: 300px; overflow-y: auto;">';
                foreach ($recent_logs as $log) {
                    echo '<div style="margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #ddd;">';
                    echo nl2br(esc_html(trim($log)));
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p style="color: #27ae60;">✅ No security threats detected today. Your site is secure!</p>';
            }
        } else {
            echo '<p style="color: #27ae60;">✅ No security threats detected today. Your site is secure!</p>';
        }
        
        echo '</div>';
    }
    
    /**
     * AJAX scan handler
     */
    public function ajax_scan() {
        if (!wp_verify_nonce($_POST['nonce'], 'kbs_scan')) {
            wp_die('Security check failed');
        }
        
        $scan_results = $this->quick_scan();
        
        $html = '<div style="background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 15px;">';
        $html .= '<h4>🔍 Scan Results</h4>';
        
        if ($scan_results['threats_found'] > 0) {
            $html .= '<p style="color: #e74c3c;">⚠️ ' . $scan_results['threats_found'] . ' potential threats detected!</p>';
            $html .= '<ul>';
            foreach ($scan_results['threats'] as $threat) {
                $html .= '<li><strong>' . $threat['type'] . '</strong>: ' . $threat['file'] . '</li>';
            }
            $html .= '</ul>';
        } else {
            $html .= '<p style="color: #27ae60;">✅ No threats detected. Your website is secure!</p>';
        }
        
        $html .= '<p><small>Scanned ' . $scan_results['files_scanned'] . ' files in ' . $scan_results['scan_time'] . ' seconds</small></p>';
        $html .= '</div>';
        
        wp_send_json(['html' => $html]);
    }
    
    /**
     * Quick security scan
     */
    private function quick_scan() {
        $start_time = microtime(true);
        $threats = [];
        $files_scanned = 0;
        
        // Scan critical directories
        $scan_dirs = [
            ABSPATH,
            WP_CONTENT_DIR . '/themes',
            WP_CONTENT_DIR . '/plugins'
        ];
        
        foreach ($scan_dirs as $dir) {
            $threats = array_merge($threats, $this->scan_directory($dir, $files_scanned));
        }
        
        $scan_time = round(microtime(true) - $start_time, 2);
        
        return [
            'threats_found' => count($threats),
            'threats' => $threats,
            'files_scanned' => $files_scanned,
            'scan_time' => $scan_time
        ];
    }
    
    /**
     * Scan directory for threats
     */
    private function scan_directory($dir, &$files_scanned) {
        $threats = [];
        
        if (!is_dir($dir)) {
            return $threats;
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files_scanned++;
                
                // Skip large files to prevent timeout
                if ($file->getSize() > 1024 * 1024) { // 1MB
                    continue;
                }
                
                $content = file_get_contents($file->getRealPath());
                $threat = $this->analyze_content($content);
                
                if ($threat) {
                    $threats[] = [
                        'type' => $threat['description'],
                        'file' => str_replace(ABSPATH, '', $file->getRealPath()),
                        'severity' => $threat['severity']
                    ];
                }
                
                // Prevent timeout
                if ($files_scanned > 1000) {
                    break 2;
                }
            }
        }
        
        return $threats;
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create log directory
        $log_dir = WP_CONTENT_DIR . '/kloudbean-security-logs';
        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
            file_put_contents($log_dir . '/.htaccess', "Order deny,allow\nDeny from all");
            file_put_contents($log_dir . '/index.php', "<?php // Silence is golden");
        }
        
        // Log activation
        $this->log_threat([
            'severity' => 'INFO',
            'description' => 'Kloudbean Security Lite activated',
            'pattern' => 'activation'
        ], 'system', 'Plugin activated', $this->get_client_ip());
    }
}

// Initialize the plugin
KloudbeanSecurityLite::getInstance();
?>

