<?php
/**
 * Dashboard template for Kloudbean Enterprise Security Suite
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}
?>

<div class="wrap kbes-dashboard">
    <h1><?php _e('Security Dashboard', 'kloudbean-enterprise-security'); ?></h1>
    
    <div class="kbes-dashboard-grid">
        <!-- Security Score Card -->
        <div class="kbes-card kbes-security-score">
            <div class="kbes-card-header">
                <h3><?php _e('Security Score', 'kloudbean-enterprise-security'); ?></h3>
            </div>
            <div class="kbes-card-content">
                <div class="kbes-score-circle">
                    <div class="kbes-score-value"><?php echo esc_html($dashboard_data['security_score']); ?></div>
                    <div class="kbes-score-label"><?php _e('Score', 'kloudbean-enterprise-security'); ?></div>
                </div>
                <div class="kbes-score-status">
                    <?php if ($dashboard_data['security_score'] >= 80): ?>
                        <span class="kbes-status-good"><?php _e('Good', 'kloudbean-enterprise-security'); ?></span>
                    <?php elseif ($dashboard_data['security_score'] >= 60): ?>
                        <span class="kbes-status-warning"><?php _e('Warning', 'kloudbean-enterprise-security'); ?></span>
                    <?php else: ?>
                        <span class="kbes-status-danger"><?php _e('Danger', 'kloudbean-enterprise-security'); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Threats Blocked Card -->
        <div class="kbes-card kbes-threats-blocked">
            <div class="kbes-card-header">
                <h3><?php _e('Threats Blocked', 'kloudbean-enterprise-security'); ?></h3>
            </div>
            <div class="kbes-card-content">
                <div class="kbes-stat-value"><?php echo esc_html($dashboard_data['threats_blocked']); ?></div>
                <div class="kbes-stat-label"><?php _e('Total', 'kloudbean-enterprise-security'); ?></div>
            </div>
        </div>
        
        <!-- Vulnerabilities Found Card -->
        <div class="kbes-card kbes-vulnerabilities">
            <div class="kbes-card-header">
                <h3><?php _e('Vulnerabilities', 'kloudbean-enterprise-security'); ?></h3>
            </div>
            <div class="kbes-card-content">
                <div class="kbes-stat-value"><?php echo esc_html($dashboard_data['vulnerabilities_found']); ?></div>
                <div class="kbes-stat-label"><?php _e('Found', 'kloudbean-enterprise-security'); ?></div>
            </div>
        </div>
        
        <!-- Malware Detected Card -->
        <div class="kbes-card kbes-malware">
            <div class="kbes-card-header">
                <h3><?php _e('Malware', 'kloudbean-enterprise-security'); ?></h3>
            </div>
            <div class="kbes-card-content">
                <div class="kbes-stat-value"><?php echo esc_html($dashboard_data['malware_detected']); ?></div>
                <div class="kbes-stat-label"><?php _e('Detected', 'kloudbean-enterprise-security'); ?></div>
            </div>
        </div>
        
        <!-- Threats Today Card -->
        <div class="kbes-card kbes-threats-today">
            <div class="kbes-card-header">
                <h3><?php _e('Threats Today', 'kloudbean-enterprise-security'); ?></h3>
            </div>
            <div class="kbes-card-content">
                <div class="kbes-stat-value"><?php echo esc_html($dashboard_data['threats_today']); ?></div>
                <div class="kbes-stat-label"><?php _e('Today', 'kloudbean-enterprise-security'); ?></div>
            </div>
        </div>
        
        <!-- Compliance Score Card -->
        <div class="kbes-card kbes-compliance">
            <div class="kbes-card-header">
                <h3><?php _e('Compliance', 'kloudbean-enterprise-security'); ?></h3>
            </div>
            <div class="kbes-card-content">
                <div class="kbes-stat-value"><?php echo esc_html($dashboard_data['compliance_score']); ?>%</div>
                <div class="kbes-stat-label"><?php _e('Score', 'kloudbean-enterprise-security'); ?></div>
            </div>
        </div>
    </div>
    
    <!-- Security Tests Section -->
    <div class="kbes-section">
        <h2><?php _e('Security Tests', 'kloudbean-enterprise-security'); ?></h2>
        <div class="kbes-tests-summary">
            <div class="kbes-test-item">
                <span class="kbes-test-label"><?php _e('Passed', 'kloudbean-enterprise-security'); ?></span>
                <span class="kbes-test-value kbes-test-passed"><?php echo esc_html($dashboard_data['passed_checks']); ?></span>
            </div>
            <div class="kbes-test-item">
                <span class="kbes-test-label"><?php _e('Failed', 'kloudbean-enterprise-security'); ?></span>
                <span class="kbes-test-value kbes-test-failed"><?php echo esc_html($dashboard_data['failed_checks']); ?></span>
            </div>
            <div class="kbes-test-item">
                <span class="kbes-test-label"><?php _e('Warnings', 'kloudbean-enterprise-security'); ?></span>
                <span class="kbes-test-value kbes-test-warning"><?php echo esc_html($dashboard_data['failed_checks']); ?></span>
            </div>
        </div>
        <div class="kbes-tests-actions">
            <a href="<?php echo admin_url('admin.php?page=kloudbean-enterprise-security-scanner'); ?>" class="button button-primary">
                <?php _e('Run Security Scan', 'kloudbean-enterprise-security'); ?>
            </a>
        </div>
    </div>
    
    <!-- Recent Activity Section -->
    <div class="kbes-section">
        <h2><?php _e('Recent Activity', 'kloudbean-enterprise-security'); ?></h2>
        <div class="kbes-activity-list">
            <div class="kbes-activity-item">
                <div class="kbes-activity-icon">
                    <span class="dashicons dashicons-shield-alt"></span>
                </div>
                <div class="kbes-activity-content">
                    <div class="kbes-activity-title"><?php _e('Security scan completed', 'kloudbean-enterprise-security'); ?></div>
                    <div class="kbes-activity-time"><?php echo esc_html(date('M j, Y g:i A', $dashboard_data['last_scan'])); ?></div>
                </div>
            </div>
            <div class="kbes-activity-item">
                <div class="kbes-activity-icon">
                    <span class="dashicons dashicons-warning"></span>
                </div>
                <div class="kbes-activity-content">
                    <div class="kbes-activity-title"><?php _e('Threats blocked today', 'kloudbean-enterprise-security'); ?></div>
                    <div class="kbes-activity-time"><?php echo esc_html($dashboard_data['threats_today']); ?> <?php _e('threats', 'kloudbean-enterprise-security'); ?></div>
                </div>
            </div>
            <div class="kbes-activity-item">
                <div class="kbes-activity-icon">
                    <span class="dashicons dashicons-admin-tools"></span>
                </div>
                <div class="kbes-activity-content">
                    <div class="kbes-activity-title"><?php _e('Security level', 'kloudbean-enterprise-security'); ?></div>
                    <div class="kbes-activity-time"><?php echo esc_html(ucfirst($dashboard_data['security_level'])); ?></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions Section -->
    <div class="kbes-section">
        <h2><?php _e('Quick Actions', 'kloudbean-enterprise-security'); ?></h2>
        <div class="kbes-quick-actions">
            <a href="<?php echo admin_url('admin.php?page=kloudbean-enterprise-security-scanner'); ?>" class="kbes-action-button">
                <span class="dashicons dashicons-search"></span>
                <span><?php _e('Run Scan', 'kloudbean-enterprise-security'); ?></span>
            </a>
            <a href="<?php echo admin_url('admin.php?page=kloudbean-enterprise-security-firewall'); ?>" class="kbes-action-button">
                <span class="dashicons dashicons-shield"></span>
                <span><?php _e('Firewall', 'kloudbean-enterprise-security'); ?></span>
            </a>
            <a href="<?php echo admin_url('admin.php?page=kloudbean-enterprise-security-logs'); ?>" class="kbes-action-button">
                <span class="dashicons dashicons-list-view"></span>
                <span><?php _e('View Logs', 'kloudbean-enterprise-security'); ?></span>
            </a>
            <a href="<?php echo admin_url('admin.php?page=kloudbean-enterprise-security-settings'); ?>" class="kbes-action-button">
                <span class="dashicons dashicons-admin-settings"></span>
                <span><?php _e('Settings', 'kloudbean-enterprise-security'); ?></span>
            </a>
        </div>
    </div>
</div>

<style>
.kbes-dashboard {
    max-width: 1200px;
}

.kbes-dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.kbes-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.kbes-card-header h3 {
    margin: 0 0 15px 0;
    font-size: 16px;
    font-weight: 600;
}

.kbes-card-content {
    text-align: center;
}

.kbes-score-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #f0f0f1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    margin: 0 auto 15px;
}

.kbes-score-value {
    font-size: 24px;
    font-weight: bold;
    color: #1d2327;
}

.kbes-score-label {
    font-size: 12px;
    color: #646970;
}

.kbes-score-status {
    margin-top: 10px;
}

.kbes-status-good {
    color: #00a32a;
    font-weight: 600;
}

.kbes-status-warning {
    color: #dba617;
    font-weight: 600;
}

.kbes-status-danger {
    color: #d63638;
    font-weight: 600;
}

.kbes-stat-value {
    font-size: 32px;
    font-weight: bold;
    color: #1d2327;
    margin-bottom: 5px;
}

.kbes-stat-label {
    font-size: 14px;
    color: #646970;
}

.kbes-section {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 20px;
}

.kbes-section h2 {
    margin: 0 0 20px 0;
    font-size: 18px;
    font-weight: 600;
}

.kbes-tests-summary {
    display: flex;
    gap: 30px;
    margin-bottom: 20px;
}

.kbes-test-item {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.kbes-test-label {
    font-size: 14px;
    color: #646970;
    margin-bottom: 5px;
}

.kbes-test-value {
    font-size: 24px;
    font-weight: bold;
}

.kbes-test-passed {
    color: #00a32a;
}

.kbes-test-failed {
    color: #d63638;
}

.kbes-test-warning {
    color: #dba617;
}

.kbes-tests-actions {
    margin-top: 20px;
}

.kbes-activity-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.kbes-activity-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 4px;
}

.kbes-activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f0f0f1;
    display: flex;
    align-items: center;
    justify-content: center;
}

.kbes-activity-icon .dashicons {
    font-size: 20px;
    color: #646970;
}

.kbes-activity-content {
    flex: 1;
}

.kbes-activity-title {
    font-weight: 600;
    margin-bottom: 5px;
}

.kbes-activity-time {
    font-size: 14px;
    color: #646970;
}

.kbes-quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 15px;
}

.kbes-action-button {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    background: #f9f9f9;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    text-decoration: none;
    color: #1d2327;
    transition: all 0.2s ease;
}

.kbes-action-button:hover {
    background: #f0f0f1;
    border-color: #8c8f94;
}

.kbes-action-button .dashicons {
    font-size: 24px;
    margin-bottom: 10px;
    color: #646970;
}

.kbes-action-button span:last-child {
    font-weight: 600;
}
</style>
