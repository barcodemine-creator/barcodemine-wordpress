<?php
/**
 * Integrity Scanner Module for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

namespace KloudbeanEnterpriseSecurity\Modules;

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

/**
 * Integrity Scanner class handling core file integrity checks
 */
class IntegrityScanner {
    
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
     * Initialize integrity scanner
     */
    private function init() {
        add_action('init', array($this, 'initIntegrityScanner'));
        add_action('kbes_daily_integrity_scan', array($this, 'runDailyIntegrityScan'));
    }
    
    /**
     * Initialize integrity scanner
     */
    public function initIntegrityScanner() {
        // Set up integrity scanner hooks
    }
    
    /**
     * Run daily integrity scan
     */
    public function runDailyIntegrityScan() {
        $this->scanCoreFiles();
    }
    
    /**
     * Scan core files for integrity
     */
    public function scanCoreFiles() {
        $this->logScanEvent('integrity_scan_started', array(
            'timestamp' => current_time('mysql')
        ));
        
        $core_files = $this->getCoreFiles();
        $modified_files = array();
        
        foreach ($core_files as $file_path) {
            if (file_exists($file_path)) {
                $current_hash = md5_file($file_path);
                $stored_hash = get_option('kbes_file_hash_' . md5($file_path), '');
                
                if (!empty($stored_hash) && $current_hash !== $stored_hash) {
                    $modified_files[] = array(
                        'path' => $file_path,
                        'current_hash' => $current_hash,
                        'stored_hash' => $stored_hash,
                        'modified_at' => current_time('mysql')
                    );
                }
            }
        }
        
        if (!empty($modified_files)) {
            $this->logScanEvent('integrity_scan_modified_files', array(
                'modified_files' => $modified_files,
                'count' => count($modified_files)
            ));
        }
        
        $this->logScanEvent('integrity_scan_completed', array(
            'modified_files_count' => count($modified_files),
            'timestamp' => current_time('mysql')
        ));
        
        return $modified_files;
    }
    
    /**
     * Get core files list
     */
    private function getCoreFiles() {
        $core_files = array(
            ABSPATH . 'index.php',
            ABSPATH . 'wp-config.php',
            ABSPATH . 'wp-blog-header.php',
            ABSPATH . 'wp-load.php',
            ABSPATH . 'wp-settings.php',
            ABSPATH . 'wp-cron.php',
            ABSPATH . 'wp-links-opml.php',
            ABSPATH . 'wp-mail.php',
            ABSPATH . 'wp-signup.php',
            ABSPATH . 'wp-trackback.php',
            ABSPATH . 'xmlrpc.php'
        );
        
        return $core_files;
    }
    
    /**
     * Create baseline hashes
     */
    public function createBaselineHashes() {
        $core_files = $this->getCoreFiles();
        $created_count = 0;
        
        foreach ($core_files as $file_path) {
            if (file_exists($file_path)) {
                $hash = md5_file($file_path);
                update_option('kbes_file_hash_' . md5($file_path), $hash);
                $created_count++;
            }
        }
        
        $this->logScanEvent('baseline_hashes_created', array(
            'files_hashed' => $created_count,
            'timestamp' => current_time('mysql')
        ));
        
        return $created_count;
    }
    
    /**
     * Restore file from backup
     */
    public function restoreFile($file_path) {
        if (!file_exists($file_path)) {
            return false;
        }
        
        $backup_path = $this->getBackupPath($file_path);
        
        if (!file_exists($backup_path)) {
            return false;
        }
        
        if (copy($backup_path, $file_path)) {
            $this->logScanEvent('file_restored', array(
                'file_path' => $file_path,
                'backup_path' => $backup_path,
                'timestamp' => current_time('mysql')
            ));
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get backup path
     */
    private function getBackupPath($file_path) {
        $upload_dir = wp_upload_dir();
        $backup_dir = $upload_dir['basedir'] . '/kloudbean-enterprise-security/backups/core-files/';
        
        if (!file_exists($backup_dir)) {
            wp_mkdir_p($backup_dir);
        }
        
        $relative_path = str_replace(ABSPATH, '', $file_path);
        return $backup_dir . $relative_path;
    }
    
    /**
     * Log scan event
     */
    private function logScanEvent($event_type, $data) {
        $this->logging->logSystemEvent($event_type, $data);
    }
}
