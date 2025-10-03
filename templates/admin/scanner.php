<?php
/**
 * Scanner template for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}
?>

<div class="wrap kbes-scanner">
    <h1><?php _e('Security Scanner', 'kloudbean-enterprise-security'); ?></h1>
    
    <!-- Scanner Status -->
    <div class="kbes-scanner-status">
        <div class="kbes-status-card">
            <div class="kbes-status-icon">
                <span class="dashicons dashicons-shield-alt"></span>
            </div>
            <div class="kbes-status-content">
                <div class="kbes-status-title"><?php _e('Scanner Status', 'kloudbean-enterprise-security'); ?></div>
                <div class="kbes-status-value"><?php echo esc_html(ucfirst($scanner_data['scan_status'])); ?></div>
            </div>
        </div>
        
        <div class="kbes-status-card">
            <div class="kbes-status-icon">
                <span class="dashicons dashicons-clock"></span>
            </div>
            <div class="kbes-status-content">
                <div class="kbes-status-title"><?php _e('Last Scan', 'kloudbean-enterprise-security'); ?></div>
                <div class="kbes-status-value"><?php echo esc_html(date('M j, Y g:i A', $scanner_data['last_scan'])); ?></div>
            </div>
        </div>
        
        <div class="kbes-status-card">
            <div class="kbes-status-icon">
                <span class="dashicons dashicons-warning"></span>
            </div>
            <div class="kbes-status-content">
                <div class="kbes-status-title"><?php _e('Threats Found', 'kloudbean-enterprise-security'); ?></div>
                <div class="kbes-status-value"><?php echo esc_html($scanner_data['threats_found']); ?></div>
            </div>
        </div>
    </div>
    
    <!-- Scan Controls -->
    <div class="kbes-scan-controls">
        <button id="kbes-run-scan" class="button button-primary">
            <span class="dashicons dashicons-search"></span>
            <?php _e('Run Security Scan', 'kloudbean-enterprise-security'); ?>
        </button>
        
        <button id="kbes-schedule-scan" class="button">
            <span class="dashicons dashicons-calendar-alt"></span>
            <?php _e('Schedule Scan', 'kloudbean-enterprise-security'); ?>
        </button>
        
        <button id="kbes-scan-settings" class="button">
            <span class="dashicons dashicons-admin-settings"></span>
            <?php _e('Scan Settings', 'kloudbean-enterprise-security'); ?>
        </button>
    </div>
    
    <!-- Scan Results -->
    <div class="kbes-scan-results">
        <h2><?php _e('Scan Results', 'kloudbean-enterprise-security'); ?></h2>
        
        <div class="kbes-results-grid">
            <!-- Malware Detection -->
            <div class="kbes-result-card">
                <div class="kbes-result-header">
                    <h3><?php _e('Malware Detection', 'kloudbean-enterprise-security'); ?></h3>
                    <span class="kbes-result-status kbes-status-<?php echo $scanner_data['malware_detected'] > 0 ? 'danger' : 'good'; ?>">
                        <?php echo $scanner_data['malware_detected'] > 0 ? __('Issues Found', 'kloudbean-enterprise-security') : __('Clean', 'kloudbean-enterprise-security'); ?>
                    </span>
                </div>
                <div class="kbes-result-content">
                    <div class="kbes-result-value"><?php echo esc_html($scanner_data['malware_detected']); ?></div>
                    <div class="kbes-result-label"><?php _e('Malware Files', 'kloudbean-enterprise-security'); ?></div>
                </div>
                <div class="kbes-result-actions">
                    <a href="#" class="button button-small"><?php _e('View Details', 'kloudbean-enterprise-security'); ?></a>
                </div>
            </div>
            
            <!-- Vulnerability Scan -->
            <div class="kbes-result-card">
                <div class="kbes-result-header">
                    <h3><?php _e('Vulnerability Scan', 'kloudbean-enterprise-security'); ?></h3>
                    <span class="kbes-result-status kbes-status-<?php echo $scanner_data['vulnerabilities_found'] > 0 ? 'warning' : 'good'; ?>">
                        <?php echo $scanner_data['vulnerabilities_found'] > 0 ? __('Issues Found', 'kloudbean-enterprise-security') : __('Clean', 'kloudbean-enterprise-security'); ?>
                    </span>
                </div>
                <div class="kbes-result-content">
                    <div class="kbes-result-value"><?php echo esc_html($scanner_data['vulnerabilities_found']); ?></div>
                    <div class="kbes-result-label"><?php _e('Vulnerabilities', 'kloudbean-enterprise-security'); ?></div>
                </div>
                <div class="kbes-result-actions">
                    <a href="#" class="button button-small"><?php _e('View Details', 'kloudbean-enterprise-security'); ?></a>
                </div>
            </div>
            
            <!-- File Integrity -->
            <div class="kbes-result-card">
                <div class="kbes-result-header">
                    <h3><?php _e('File Integrity', 'kloudbean-enterprise-security'); ?></h3>
                    <span class="kbes-result-status kbes-status-<?php echo $scanner_data['file_integrity'] < 100 ? 'warning' : 'good'; ?>">
                        <?php echo $scanner_data['file_integrity'] < 100 ? __('Issues Found', 'kloudbean-enterprise-security') : __('Clean', 'kloudbean-enterprise-security'); ?>
                    </span>
                </div>
                <div class="kbes-result-content">
                    <div class="kbes-result-value"><?php echo esc_html($scanner_data['file_integrity']); ?>%</div>
                    <div class="kbes-result-label"><?php _e('Integrity Score', 'kloudbean-enterprise-security'); ?></div>
                </div>
                <div class="kbes-result-actions">
                    <a href="#" class="button button-small"><?php _e('View Details', 'kloudbean-enterprise-security'); ?></a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scan History -->
    <div class="kbes-scan-history">
        <h2><?php _e('Scan History', 'kloudbean-enterprise-security'); ?></h2>
        
        <div class="kbes-history-table">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Date', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Type', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Status', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Threats Found', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Duration', 'kloudbean-enterprise-security'); ?></th>
                        <th><?php _e('Actions', 'kloudbean-enterprise-security'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo esc_html(date('M j, Y g:i A', $scanner_data['last_scan'])); ?></td>
                        <td><?php _e('Full Scan', 'kloudbean-enterprise-security'); ?></td>
                        <td><span class="kbes-status-good"><?php _e('Completed', 'kloudbean-enterprise-security'); ?></span></td>
                        <td><?php echo esc_html($scanner_data['threats_found']); ?></td>
                        <td><?php _e('2m 30s', 'kloudbean-enterprise-security'); ?></td>
                        <td>
                            <a href="#" class="button button-small"><?php _e('View Report', 'kloudbean-enterprise-security'); ?></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Scan Progress Modal -->
    <div id="kbes-scan-progress" class="kbes-modal" style="display: none;">
        <div class="kbes-modal-content">
            <div class="kbes-modal-header">
                <h3><?php _e('Security Scan in Progress', 'kloudbean-enterprise-security'); ?></h3>
                <button class="kbes-modal-close">&times;</button>
            </div>
            <div class="kbes-modal-body">
                <div class="kbes-progress-bar">
                    <div class="kbes-progress-fill" style="width: 0%;"></div>
                </div>
                <div class="kbes-progress-text"><?php _e('Initializing scan...', 'kloudbean-enterprise-security'); ?></div>
                <div class="kbes-progress-details">
                    <div class="kbes-progress-item">
                        <span class="dashicons dashicons-yes"></span>
                        <?php _e('Scanning core files...', 'kloudbean-enterprise-security'); ?>
                    </div>
                    <div class="kbes-progress-item">
                        <span class="dashicons dashicons-clock"></span>
                        <?php _e('Scanning plugins...', 'kloudbean-enterprise-security'); ?>
                    </div>
                    <div class="kbes-progress-item">
                        <span class="dashicons dashicons-clock"></span>
                        <?php _e('Scanning themes...', 'kloudbean-enterprise-security'); ?>
                    </div>
                    <div class="kbes-progress-item">
                        <span class="dashicons dashicons-clock"></span>
                        <?php _e('Scanning uploads...', 'kloudbean-enterprise-security'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kbes-scanner {
    max-width: 1200px;
}

.kbes-scanner-status {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.kbes-status-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.kbes-status-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #f0f0f1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.kbes-status-icon .dashicons {
    font-size: 24px;
    color: #646970;
}

.kbes-status-content {
    flex: 1;
}

.kbes-status-title {
    font-size: 14px;
    color: #646970;
    margin-bottom: 5px;
}

.kbes-status-value {
    font-size: 18px;
    font-weight: 600;
    color: #1d2327;
}

.kbes-scan-controls {
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
}

.kbes-scan-controls .button {
    display: flex;
    align-items: center;
    gap: 8px;
}

.kbes-scan-results {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 30px;
}

.kbes-scan-results h2 {
    margin: 0 0 20px 0;
    font-size: 18px;
    font-weight: 600;
}

.kbes-results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.kbes-result-card {
    background: #f9f9f9;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
}

.kbes-result-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.kbes-result-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.kbes-result-status {
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: 600;
}

.kbes-status-good {
    background: #d1e7dd;
    color: #0f5132;
}

.kbes-status-warning {
    background: #fff3cd;
    color: #664d03;
}

.kbes-status-danger {
    background: #f8d7da;
    color: #721c24;
}

.kbes-result-content {
    text-align: center;
    margin-bottom: 15px;
}

.kbes-result-value {
    font-size: 32px;
    font-weight: bold;
    color: #1d2327;
    margin-bottom: 5px;
}

.kbes-result-label {
    font-size: 14px;
    color: #646970;
}

.kbes-result-actions {
    text-align: center;
}

.kbes-scan-history {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
}

.kbes-scan-history h2 {
    margin: 0 0 20px 0;
    font-size: 18px;
    font-weight: 600;
}

.kbes-history-table {
    overflow-x: auto;
}

.kbes-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
}

.kbes-modal-content {
    background: #fff;
    border-radius: 4px;
    max-width: 500px;
    width: 90%;
    max-height: 80%;
    overflow-y: auto;
}

.kbes-modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #ccd0d4;
}

.kbes-modal-header h3 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
}

.kbes-modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #646970;
}

.kbes-modal-body {
    padding: 20px;
}

.kbes-progress-bar {
    width: 100%;
    height: 20px;
    background: #f0f0f1;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 15px;
}

.kbes-progress-fill {
    height: 100%;
    background: #2271b1;
    transition: width 0.3s ease;
}

.kbes-progress-text {
    text-align: center;
    font-weight: 600;
    margin-bottom: 20px;
}

.kbes-progress-details {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.kbes-progress-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    background: #f9f9f9;
    border-radius: 4px;
}

.kbes-progress-item .dashicons {
    font-size: 16px;
}

.kbes-progress-item .dashicons-yes {
    color: #00a32a;
}

.kbes-progress-item .dashicons-clock {
    color: #646970;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Run scan button
    $('#kbes-run-scan').on('click', function() {
        $('#kbes-scan-progress').show();
        
        // Simulate scan progress
        let progress = 0;
        const interval = setInterval(function() {
            progress += 10;
            $('.kbes-progress-fill').css('width', progress + '%');
            
            if (progress >= 100) {
                clearInterval(interval);
                setTimeout(function() {
                    $('#kbes-scan-progress').hide();
                    location.reload();
                }, 1000);
            }
        }, 500);
    });
    
    // Close modal
    $('.kbes-modal-close').on('click', function() {
        $('#kbes-scan-progress').hide();
    });
    
    // Close modal on background click
    $('#kbes-scan-progress').on('click', function(e) {
        if (e.target === this) {
            $(this).hide();
        }
    });
});
</script>
