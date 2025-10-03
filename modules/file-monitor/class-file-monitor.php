<?php
/**
 * File Monitor Module for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Modules;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * File Monitor class handling file monitoring and integrity checking
 */
class FileMonitor {
    
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
     * Initialize file monitor
     */
    private function init() {
        add_action('init', array($this, 'initFileMonitor'));
        add_action('kbes_daily_security_scan', array($this, 'runDailyScan'));
    }
    
    /**
     * Initialize file monitor
     */
    public function initFileMonitor() {
        // Set up file monitor hooks
        add_action('wp_loaded', array($this, 'monitorFileChanges'));
        add_action('wp_loaded', array($this, 'monitorFileUploads'));
    }
    
    /**
     * Run daily scan
     */
    public function runDailyScan() {
        $this->scanFileIntegrity();
    }
    
    /**
     * Monitor file changes
     */
    public function monitorFileChanges() {
        // Monitor WordPress core files
        add_action('wp_loaded', array($this, 'monitorCoreFiles'));
        
        // Monitor plugin files
        add_action('wp_loaded', array($this, 'monitorPluginFiles'));
        
        // Monitor theme files
        add_action('wp_loaded', array($this, 'monitorThemeFiles'));
        
        // Monitor uploads directory
        add_action('wp_loaded', array($this, 'monitorUploadsDirectory'));
    }
    
    /**
     * Monitor file uploads
     */
    public function monitorFileUploads() {
        add_action('wp_handle_upload', array($this, 'logFileUpload'), 10, 2);
        add_filter('wp_handle_upload_prefilter', array($this, 'validateFileUpload'));
    }
    
    /**
     * Monitor core files
     */
    public function monitorCoreFiles() {
        $core_files = $this->getCoreFiles();
        
        foreach ($core_files as $file) {
            $this->checkFileIntegrity($file, 'core');
        }
    }
    
    /**
     * Monitor plugin files
     */
    public function monitorPluginFiles() {
        $plugin_files = $this->getPluginFiles();
        
        foreach ($plugin_files as $file) {
            $this->checkFileIntegrity($file, 'plugin');
        }
    }
    
    /**
     * Monitor theme files
     */
    public function monitorThemeFiles() {
        $theme_files = $this->getThemeFiles();
        
        foreach ($theme_files as $file) {
            $this->checkFileIntegrity($file, 'theme');
        }
    }
    
    /**
     * Monitor uploads directory
     */
    public function monitorUploadsDirectory() {
        $upload_dir = wp_upload_dir();
        $uploads_path = $upload_dir['basedir'];
        
        if (file_exists($uploads_path)) {
            $this->scanDirectory($uploads_path, 'upload');
        }
    }
    
    /**
     * Scan file integrity
     */
    public function scanFileIntegrity() {
        $this->logScanEvent('file_integrity_scan_started', array(
            'timestamp' => current_time('mysql')
        ));
        
        // Scan core files
        $this->scanCoreFiles();
        
        // Scan plugin files
        $this->scanPluginFiles();
        
        // Scan theme files
        $this->scanThemeFiles();
        
        // Scan uploads directory
        $this->scanUploadsDirectory();
        
        $this->logScanEvent('file_integrity_scan_completed', array(
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Scan core files
     */
    private function scanCoreFiles() {
        $core_files = $this->getCoreFiles();
        
        foreach ($core_files as $file) {
            $this->checkFileIntegrity($file, 'core');
        }
    }
    
    /**
     * Scan plugin files
     */
    private function scanPluginFiles() {
        $plugin_files = $this->getPluginFiles();
        
        foreach ($plugin_files as $file) {
            $this->checkFileIntegrity($file, 'plugin');
        }
    }
    
    /**
     * Scan theme files
     */
    private function scanThemeFiles() {
        $theme_files = $this->getThemeFiles();
        
        foreach ($theme_files as $file) {
            $this->checkFileIntegrity($file, 'theme');
        }
    }
    
    /**
     * Scan uploads directory
     */
    private function scanUploadsDirectory() {
        $upload_dir = wp_upload_dir();
        $uploads_path = $upload_dir['basedir'];
        
        if (file_exists($uploads_path)) {
            $this->scanDirectory($uploads_path, 'upload');
        }
    }
    
    /**
     * Check file integrity
     */
    private function checkFileIntegrity($file_path, $file_type) {
        if (!file_exists($file_path) || !is_file($file_path)) {
            return;
        }
        
        $file_hash = $this->utilities->getFileHash($file_path);
        $file_size = filesize($file_path);
        $last_modified = filemtime($file_path);
        
        // Check if file exists in database
        $existing_file = $this->getFileRecord($file_path);
        
        if ($existing_file) {
            // Check if file has changed
            if ($existing_file->file_hash !== $file_hash) {
                $this->logFileChange($file_path, $file_type, $existing_file->file_hash, $file_hash);
                $this->updateFileRecord($file_path, $file_hash, $file_size, $last_modified, 'modified');
            } else {
                $this->updateFileRecord($file_path, $file_hash, $file_size, $last_modified, 'clean');
            }
        } else {
            // New file
            $this->addFileRecord($file_path, $file_hash, $file_size, $last_modified, 'added');
        }
    }
    
    /**
     * Scan directory
     */
    private function scanDirectory($directory_path, $file_type) {
        if (!is_dir($directory_path)) {
            return;
        }
        
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory_path));
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $this->checkFileIntegrity($file->getPathname(), $file_type);
            }
        }
    }
    
    /**
     * Log file upload
     */
    public function logFileUpload($file, $filename) {
        $this->logging->logSecurityEvent('file_upload', array(
            'filename' => $filename,
            'file_type' => $file['type'],
            'file_size' => $file['size'],
            'ip' => $this->getClientIP(),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql')
        ));
        
        return $file;
    }
    
    /**
     * Validate file upload
     */
    public function validateFileUpload($file) {
        // Check file type
        if ($this->isSuspiciousFileType($file['name'])) {
            $file['error'] = 'File type not allowed';
            return $file;
        }
        
        // Check file size
        if ($file['size'] > get_option('kbes_max_file_size', 10485760)) {
            $file['error'] = 'File size too large';
            return $file;
        }
        
        return $file;
    }
    
    /**
     * Log file change
     */
    private function logFileChange($file_path, $file_type, $old_hash, $new_hash) {
        $this->logging->logSecurityEvent('file_changed', array(
            'file_path' => $file_path,
            'file_type' => $file_type,
            'old_hash' => $old_hash,
            'new_hash' => $new_hash,
            'ip' => $this->getClientIP(),
            'user_id' => get_current_user_id(),
            'timestamp' => current_time('mysql')
        ));
    }
    
    /**
     * Log scan event
     */
    private function logScanEvent($event_type, $data) {
        $this->logging->logSystemEvent($event_type, $data);
    }
    
    /**
     * Check if file type is suspicious
     */
    private function isSuspiciousFileType($filename) {
        $suspicious_extensions = array(
            'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'pht', 'phtm',
            'shtml', 'shtm', 'htaccess', 'htpasswd', 'js', 'vbs', 'bat',
            'cmd', 'com', 'exe', 'scr', 'pif', 'sh', 'cgi', 'pl', 'py'
        );
        
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        return in_array($extension, $suspicious_extensions);
    }
    
    /**
     * Get core files
     */
    private function getCoreFiles() {
        $core_files = array();
        
        $wp_files = array(
            'wp-config.php',
            'wp-salt.php',
            'wp-activate.php',
            'wp-blog-header.php',
            'wp-comments-post.php',
            'wp-cron.php',
            'wp-links-opml.php',
            'wp-load.php',
            'wp-login.php',
            'wp-mail.php',
            'wp-settings.php',
            'wp-signup.php',
            'wp-trackback.php',
            'xmlrpc.php'
        );
        
        foreach ($wp_files as $file) {
            $file_path = ABSPATH . $file;
            if (file_exists($file_path)) {
                $core_files[] = $file_path;
            }
        }
        
        return $core_files;
    }
    
    /**
     * Get plugin files
     */
    private function getPluginFiles() {
        $plugin_files = array();
        
        if (file_exists(WP_PLUGIN_DIR)) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(WP_PLUGIN_DIR));
            
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $plugin_files[] = $file->getPathname();
                }
            }
        }
        
        return $plugin_files;
    }
    
    /**
     * Get theme files
     */
    private function getThemeFiles() {
        $theme_files = array();
        
        if (file_exists(get_theme_root())) {
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(get_theme_root()));
            
            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $theme_files[] = $file->getPathname();
                }
            }
        }
        
        return $theme_files;
    }
    
    /**
     * Get file record
     */
    private function getFileRecord($file_path) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_file_integrity';
        
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE file_path = %s",
            $file_path
        ));
    }
    
    /**
     * Add file record
     */
    private function addFileRecord($file_path, $file_hash, $file_size, $last_modified, $status) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_file_integrity';
        
        $wpdb->insert(
            $table_name,
            array(
                'file_path' => $file_path,
                'file_hash' => $file_hash,
                'file_size' => $file_size,
                'file_type' => $this->getFileType($file_path),
                'status' => $status,
                'last_checked' => current_time('mysql'),
                'last_modified' => date('Y-m-d H:i:s', $last_modified)
            ),
            array(
                '%s', '%s', '%d', '%s', '%s', '%s', '%s'
            )
        );
    }
    
    /**
     * Update file record
     */
    private function updateFileRecord($file_path, $file_hash, $file_size, $last_modified, $status) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_file_integrity';
        
        $wpdb->update(
            $table_name,
            array(
                'file_hash' => $file_hash,
                'file_size' => $file_size,
                'status' => $status,
                'last_checked' => current_time('mysql'),
                'last_modified' => date('Y-m-d H:i:s', $last_modified)
            ),
            array('file_path' => $file_path),
            array('%s', '%d', '%s', '%s', '%s'),
            array('%s')
        );
    }
    
    /**
     * Get file type
     */
    private function getFileType($file_path) {
        if (strpos($file_path, ABSPATH) === 0) {
            $relative_path = str_replace(ABSPATH, '', $file_path);
            
            if (strpos($relative_path, 'wp-content/plugins/') === 0) {
                return 'plugin';
            } elseif (strpos($relative_path, 'wp-content/themes/') === 0) {
                return 'theme';
            } elseif (strpos($relative_path, 'wp-content/uploads/') === 0) {
                return 'upload';
            } else {
                return 'core';
            }
        }
        
        return 'custom';
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
     * Get file integrity status
     */
    public function getFileIntegrityStatus() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_file_integrity';
        
        $status = array();
        
        // Total files
        $status['total_files'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
        
        // Clean files
        $status['clean_files'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'clean'");
        
        // Modified files
        $status['modified_files'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'modified'");
        
        // Added files
        $status['added_files'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'added'");
        
        // Missing files
        $status['missing_files'] = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE status = 'missing'");
        
        // Files by type
        $status['by_type'] = $wpdb->get_results("SELECT file_type, COUNT(*) as count FROM $table_name GROUP BY file_type");
        
        // Recent changes
        $status['recent_changes'] = $wpdb->get_results("SELECT * FROM $table_name WHERE status != 'clean' ORDER BY last_checked DESC LIMIT 10");
        
        return $status;
    }
    
    /**
     * Get file changes
     */
    public function getFileChanges($filters = array()) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'kbes_file_integrity';
        
        $where_clause = '';
        $params = array();
        
        if (!empty($filters['file_type'])) {
            $where_clause .= ' AND file_type = %s';
            $params[] = $filters['file_type'];
        }
        
        if (!empty($filters['status'])) {
            $where_clause .= ' AND status = %s';
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['start_date'])) {
            $where_clause .= ' AND last_checked >= %s';
            $params[] = $filters['start_date'];
        }
        
        if (!empty($filters['end_date'])) {
            $where_clause .= ' AND last_checked <= %s';
            $params[] = $filters['end_date'];
        }
        
        $limit = $filters['limit'] ?? 20;
        $offset = $filters['offset'] ?? 0;
        
        $query = "SELECT * FROM $table_name WHERE 1=1 $where_clause ORDER BY last_checked DESC LIMIT %d OFFSET %d";
        $params[] = $limit;
        $params[] = $offset;
        
        return $wpdb->get_results($wpdb->prepare($query, $params));
    }
    
    /**
     * Restore file
     */
    public function restoreFile($file_path) {
        // Implementation for restoring file from backup
        // This would typically involve restoring from a clean backup
    }
    
    /**
     * Get file report
     */
    public function getFileReport() {
        $status = $this->getFileIntegrityStatus();
        $changes = $this->getFileChanges();
        
        return array(
            'status' => $status,
            'changes' => $changes,
            'generated_at' => current_time('mysql')
        );
    }
}
