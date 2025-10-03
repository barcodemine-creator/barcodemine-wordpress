<?php
/**
 * Setup Wizard Template
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}

$current_step = $this->getCurrentStep();
?>

<div class="wrap">
    <div class="kbes-setup-wizard">
        <div class="kbes-wizard-header">
            <h1><?php esc_html_e('Kloudbean Enterprise Security Setup Wizard', 'kloudbean-enterprise-security'); ?></h1>
            <p><?php esc_html_e('Welcome to the setup wizard. This will help you configure your security settings.', 'kloudbean-enterprise-security'); ?></p>
        </div>
        
        <div class="kbes-wizard-progress">
            <div class="kbes-progress-bar">
                <div class="kbes-progress-fill" style="width: <?php echo esc_attr(($current_step / 5) * 100); ?>%"></div>
            </div>
            <div class="kbes-progress-text">
                <?php printf(esc_html__('Step %d of 5', 'kloudbean-enterprise-security'), $current_step); ?>
            </div>
        </div>
        
        <div class="kbes-wizard-content">
            <?php if ($current_step === 1): ?>
                <div class="kbes-wizard-step">
                    <h2><?php esc_html_e('Welcome to Kloudbean Enterprise Security', 'kloudbean-enterprise-security'); ?></h2>
                    <p><?php esc_html_e('This setup wizard will help you configure your security settings and get started with protecting your WordPress site.', 'kloudbean-enterprise-security'); ?></p>
                    
                    <div class="kbes-features-list">
                        <h3><?php esc_html_e('Features included:', 'kloudbean-enterprise-security'); ?></h3>
                        <ul>
                            <li><?php esc_html_e('Advanced Firewall with WAF Rules', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Malware Scanner with Quarantine', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Vulnerability Scanner with CVE Database', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('File Integrity Monitoring', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Security Tests and Scoring', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Comprehensive Event Logging', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Real-time Traffic Monitoring', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Email and Slack Notifications', 'kloudbean-enterprise-security'); ?></li>
                        </ul>
                    </div>
                    
                    <div class="kbes-wizard-actions">
                        <button type="button" class="button button-primary kbes-next-step" data-step="1">
                            <?php esc_html_e('Get Started', 'kloudbean-enterprise-security'); ?>
                        </button>
                    </div>
                </div>
            <?php elseif ($current_step === 2): ?>
                <div class="kbes-wizard-step">
                    <h2><?php esc_html_e('Basic Security Settings', 'kloudbean-enterprise-security'); ?></h2>
                    <p><?php esc_html_e('Configure your basic security settings to get started.', 'kloudbean-enterprise-security'); ?></p>
                    
                    <form id="kbes-step2-form">
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <label for="security_level"><?php esc_html_e('Security Level', 'kloudbean-enterprise-security'); ?></label>
                                </th>
                                <td>
                                    <select name="security_level" id="security_level">
                                        <option value="low"><?php esc_html_e('Low', 'kloudbean-enterprise-security'); ?></option>
                                        <option value="medium" selected><?php esc_html_e('Medium', 'kloudbean-enterprise-security'); ?></option>
                                        <option value="high"><?php esc_html_e('High', 'kloudbean-enterprise-security'); ?></option>
                                        <option value="critical"><?php esc_html_e('Critical', 'kloudbean-enterprise-security'); ?></option>
                                    </select>
                                    <p class="description"><?php esc_html_e('Choose the security level for your site.', 'kloudbean-enterprise-security'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="rate_limit"><?php esc_html_e('Rate Limit (requests per hour)', 'kloudbean-enterprise-security'); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="rate_limit" id="rate_limit" value="100" min="10" max="10000" class="regular-text">
                                    <p class="description"><?php esc_html_e('Maximum requests per hour per IP address.', 'kloudbean-enterprise-security'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="max_login_attempts"><?php esc_html_e('Max Login Attempts', 'kloudbean-enterprise-security'); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="max_login_attempts" id="max_login_attempts" value="5" min="1" max="20" class="regular-text">
                                    <p class="description"><?php esc_html_e('Maximum failed login attempts before lockout.', 'kloudbean-enterprise-security'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="lockout_time"><?php esc_html_e('Lockout Time (seconds)', 'kloudbean-enterprise-security'); ?></label>
                                </th>
                                <td>
                                    <input type="number" name="lockout_time" id="lockout_time" value="300" min="60" max="3600" class="regular-text">
                                    <p class="description"><?php esc_html_e('Time to lockout IP after max attempts reached.', 'kloudbean-enterprise-security'); ?></p>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="auto_quarantine"><?php esc_html_e('Auto Quarantine', 'kloudbean-enterprise-security'); ?></label>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="auto_quarantine" id="auto_quarantine" value="1">
                                        <?php esc_html_e('Automatically quarantine malicious files', 'kloudbean-enterprise-security'); ?>
                                    </label>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="email_notifications"><?php esc_html_e('Email Notifications', 'kloudbean-enterprise-security'); ?></label>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="email_notifications" id="email_notifications" value="1" checked>
                                        <?php esc_html_e('Enable email notifications for security events', 'kloudbean-enterprise-security'); ?>
                                    </label>
                                </td>
                            </tr>
                            
                            <tr>
                                <th scope="row">
                                    <label for="slack_notifications"><?php esc_html_e('Slack Notifications', 'kloudbean-enterprise-security'); ?></label>
                                </th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="slack_notifications" id="slack_notifications" value="1">
                                        <?php esc_html_e('Enable Slack notifications for security events', 'kloudbean-enterprise-security'); ?>
                                    </label>
                                </td>
                            </tr>
                        </table>
                        
                        <div class="kbes-wizard-actions">
                            <button type="button" class="button kbes-prev-step" data-step="2">
                                <?php esc_html_e('Previous', 'kloudbean-enterprise-security'); ?>
                            </button>
                            <button type="button" class="button button-primary kbes-next-step" data-step="2">
                                <?php esc_html_e('Next', 'kloudbean-enterprise-security'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            <?php elseif ($current_step === 3): ?>
                <div class="kbes-wizard-step">
                    <h2><?php esc_html_e('Security Tests', 'kloudbean-enterprise-security'); ?></h2>
                    <p><?php esc_html_e('Running initial security tests to check your site configuration.', 'kloudbean-enterprise-security'); ?></p>
                    
                    <div class="kbes-tests-progress">
                        <div class="kbes-tests-loading">
                            <div class="kbes-spinner"></div>
                            <p><?php esc_html_e('Running security tests...', 'kloudbean-enterprise-security'); ?></p>
                        </div>
                        
                        <div class="kbes-tests-results" style="display: none;">
                            <h3><?php esc_html_e('Test Results', 'kloudbean-enterprise-security'); ?></h3>
                            <div class="kbes-test-summary">
                                <div class="kbes-test-score">
                                    <span class="kbes-score-value">0</span>
                                    <span class="kbes-score-label"><?php esc_html_e('Security Score', 'kloudbean-enterprise-security'); ?></span>
                                </div>
                            </div>
                            <div class="kbes-test-details">
                                <!-- Test results will be loaded here -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="kbes-wizard-actions">
                        <button type="button" class="button kbes-prev-step" data-step="3">
                            <?php esc_html_e('Previous', 'kloudbean-enterprise-security'); ?>
                        </button>
                        <button type="button" class="button button-primary kbes-next-step" data-step="3" style="display: none;">
                            <?php esc_html_e('Next', 'kloudbean-enterprise-security'); ?>
                        </button>
                    </div>
                </div>
            <?php elseif ($current_step === 4): ?>
                <div class="kbes-wizard-step">
                    <h2><?php esc_html_e('Firewall Configuration', 'kloudbean-enterprise-security'); ?></h2>
                    <p><?php esc_html_e('Setting up basic firewall rules to protect your site.', 'kloudbean-enterprise-security'); ?></p>
                    
                    <div class="kbes-firewall-setup">
                        <h3><?php esc_html_e('Default Firewall Rules', 'kloudbean-enterprise-security'); ?></h3>
                        <ul class="kbes-rules-list">
                            <li><?php esc_html_e('Block SQL Injection attempts', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Block XSS attacks', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Block Path Traversal attempts', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Block suspicious user agents', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Rate limiting protection', 'kloudbean-enterprise-security'); ?></li>
                        </ul>
                        
                        <div class="kbes-firewall-status">
                            <div class="kbes-status-indicator">
                                <span class="kbes-status-icon">✓</span>
                                <span class="kbes-status-text"><?php esc_html_e('Firewall rules configured', 'kloudbean-enterprise-security'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="kbes-wizard-actions">
                        <button type="button" class="button kbes-prev-step" data-step="4">
                            <?php esc_html_e('Previous', 'kloudbean-enterprise-security'); ?>
                        </button>
                        <button type="button" class="button button-primary kbes-next-step" data-step="4">
                            <?php esc_html_e('Next', 'kloudbean-enterprise-security'); ?>
                        </button>
                    </div>
                </div>
            <?php elseif ($current_step === 5): ?>
                <div class="kbes-wizard-step">
                    <h2><?php esc_html_e('Setup Complete!', 'kloudbean-enterprise-security'); ?></h2>
                    <p><?php esc_html_e('Congratulations! Your Kloudbean Enterprise Security Suite has been configured and is ready to protect your site.', 'kloudbean-enterprise-security'); ?></p>
                    
                    <div class="kbes-completion-summary">
                        <h3><?php esc_html_e('What\'s been configured:', 'kloudbean-enterprise-security'); ?></h3>
                        <ul>
                            <li><?php esc_html_e('Security settings applied', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Firewall rules activated', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('File integrity baseline created', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Daily security scans scheduled', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Notifications configured', 'kloudbean-enterprise-security'); ?></li>
                        </ul>
                    </div>
                    
                    <div class="kbes-next-steps">
                        <h3><?php esc_html_e('Next Steps:', 'kloudbean-enterprise-security'); ?></h3>
                        <ul>
                            <li><?php esc_html_e('Review your security dashboard', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Configure additional firewall rules if needed', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Set up email notifications', 'kloudbean-enterprise-security'); ?></li>
                            <li><?php esc_html_e('Run a full security scan', 'kloudbean-enterprise-security'); ?></li>
                        </ul>
                    </div>
                    
                    <div class="kbes-wizard-actions">
                        <a href="<?php echo esc_url(admin_url('admin.php?page=kloudbean-enterprise-security')); ?>" class="button button-primary">
                            <?php esc_html_e('Go to Dashboard', 'kloudbean-enterprise-security'); ?>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.kbes-setup-wizard {
    max-width: 800px;
    margin: 20px auto;
}

.kbes-wizard-header {
    text-align: center;
    margin-bottom: 30px;
    padding: 20px;
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.kbes-wizard-progress {
    margin-bottom: 30px;
}

.kbes-progress-bar {
    width: 100%;
    height: 20px;
    background: #f0f0f0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 10px;
}

.kbes-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #00a32a, #00a32a);
    transition: width 0.3s ease;
}

.kbes-progress-text {
    text-align: center;
    font-weight: bold;
    color: #666;
}

.kbes-wizard-content {
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
    padding: 30px;
}

.kbes-wizard-step h2 {
    margin-top: 0;
    color: #1d2327;
}

.kbes-features-list ul {
    list-style: none;
    padding: 0;
}

.kbes-features-list li {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.kbes-features-list li:before {
    content: "✓";
    color: #00a32a;
    font-weight: bold;
    margin-right: 10px;
}

.kbes-wizard-actions {
    margin-top: 30px;
    text-align: right;
}

.kbes-wizard-actions .button {
    margin-left: 10px;
}

.kbes-tests-progress {
    text-align: center;
    padding: 40px;
}

.kbes-tests-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 20px;
}

.kbes-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #00a32a;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.kbes-test-summary {
    display: flex;
    justify-content: center;
    margin-bottom: 30px;
}

.kbes-test-score {
    text-align: center;
    padding: 20px;
    background: #f0f8f0;
    border-radius: 10px;
    border: 2px solid #00a32a;
}

.kbes-score-value {
    display: block;
    font-size: 48px;
    font-weight: bold;
    color: #00a32a;
}

.kbes-score-label {
    display: block;
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}

.kbes-rules-list {
    list-style: none;
    padding: 0;
}

.kbes-rules-list li {
    padding: 10px 0;
    border-bottom: 1px solid #f0f0f0;
}

.kbes-rules-list li:before {
    content: "✓";
    color: #00a32a;
    font-weight: bold;
    margin-right: 10px;
}

.kbes-firewall-status {
    margin-top: 20px;
}

.kbes-status-indicator {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 15px;
    background: #f0f8f0;
    border-radius: 5px;
    border: 1px solid #00a32a;
}

.kbes-status-icon {
    color: #00a32a;
    font-size: 20px;
}

.kbes-completion-summary,
.kbes-next-steps {
    margin: 20px 0;
}

.kbes-completion-summary ul,
.kbes-next-steps ul {
    list-style: none;
    padding: 0;
}

.kbes-completion-summary li,
.kbes-next-steps li {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.kbes-completion-summary li:before {
    content: "✓";
    color: #00a32a;
    font-weight: bold;
    margin-right: 10px;
}

.kbes-next-steps li:before {
    content: "→";
    color: #2271b1;
    font-weight: bold;
    margin-right: 10px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Handle next step
    $('.kbes-next-step').on('click', function() {
        var step = $(this).data('step');
        var formData = {};
        
        if (step === 2) {
            formData = $('#kbes-step2-form').serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
        }
        
        $.ajax({
            url: kbesSetupWizard.ajax_url,
            type: 'POST',
            data: {
                action: 'kbes_setup_wizard',
                nonce: kbesSetupWizard.nonce,
                step: step,
                data: formData
            },
            success: function(response) {
                if (response.success) {
                    if (response.data.next_step === 'complete') {
                        window.location.href = '<?php echo esc_url(admin_url('admin.php?page=kloudbean-enterprise-security')); ?>';
                    } else {
                        window.location.href = '<?php echo esc_url(admin_url('admin.php?page=kbes-setup-wizard')); ?>&step=' + response.data.next_step;
                    }
                } else {
                    alert('Error: ' + response.data);
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
    
    // Handle previous step
    $('.kbes-prev-step').on('click', function() {
        var step = $(this).data('step');
        var prevStep = step - 1;
        
        if (prevStep > 0) {
            window.location.href = '<?php echo esc_url(admin_url('admin.php?page=kbes-setup-wizard')); ?>&step=' + prevStep;
        }
    });
    
    // Auto-run step 3
    if (<?php echo $current_step; ?> === 3) {
        setTimeout(function() {
            $('.kbes-next-step').click();
        }, 2000);
    }
});
</script>
