<?php
/**
 * Security Tests Admin Template
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}
?>

<div class="wrap">
    <h1><?php esc_html_e('Security Tests', 'kloudbean-enterprise-security'); ?></h1>
    
    <div class="kbes-security-tests">
        <div class="kbes-tests-header">
            <div class="kbes-score-card">
                <h2><?php esc_html_e('Security Score', 'kloudbean-enterprise-security'); ?></h2>
                <div class="kbes-score-circle">
                    <span class="kbes-score-value"><?php echo esc_html(get_option('kbes_security_score', 0)); ?>%</span>
                </div>
                <p class="kbes-score-description">
                    <?php esc_html_e('Based on comprehensive security tests', 'kloudbean-enterprise-security'); ?>
                </p>
            </div>
            
            <div class="kbes-tests-actions">
                <button type="button" class="button button-primary kbes-run-tests">
                    <?php esc_html_e('Run Tests', 'kloudbean-enterprise-security'); ?>
                </button>
                <button type="button" class="button kbes-fix-all">
                    <?php esc_html_e('Fix All Issues', 'kloudbean-enterprise-security'); ?>
                </button>
                <button type="button" class="button kbes-export-report">
                    <?php esc_html_e('Export Report', 'kloudbean-enterprise-security'); ?>
                </button>
            </div>
        </div>
        
        <div class="kbes-tests-content">
            <div class="kbes-tests-filters">
                <select id="kbes-category-filter">
                    <option value=""><?php esc_html_e('All Categories', 'kloudbean-enterprise-security'); ?></option>
                    <option value="core"><?php esc_html_e('WordPress Core', 'kloudbean-enterprise-security'); ?></option>
                    <option value="plugins"><?php esc_html_e('Plugins', 'kloudbean-enterprise-security'); ?></option>
                    <option value="themes"><?php esc_html_e('Themes', 'kloudbean-enterprise-security'); ?></option>
                    <option value="users"><?php esc_html_e('Users', 'kloudbean-enterprise-security'); ?></option>
                    <option value="files"><?php esc_html_e('Files', 'kloudbean-enterprise-security'); ?></option>
                    <option value="database"><?php esc_html_e('Database', 'kloudbean-enterprise-security'); ?></option>
                    <option value="server"><?php esc_html_e('Server', 'kloudbean-enterprise-security'); ?></option>
                </select>
                
                <select id="kbes-status-filter">
                    <option value=""><?php esc_html_e('All Status', 'kloudbean-enterprise-security'); ?></option>
                    <option value="pass"><?php esc_html_e('Passed', 'kloudbean-enterprise-security'); ?></option>
                    <option value="fail"><?php esc_html_e('Failed', 'kloudbean-enterprise-security'); ?></option>
                    <option value="warn"><?php esc_html_e('Warning', 'kloudbean-enterprise-security'); ?></option>
                    <option value="error"><?php esc_html_e('Error', 'kloudbean-enterprise-security'); ?></option>
                </select>
            </div>
            
            <div class="kbes-tests-list">
                <div class="kbes-tests-loading" style="display: none;">
                    <p><?php esc_html_e('Running security tests...', 'kloudbean-enterprise-security'); ?></p>
                </div>
                
                <div class="kbes-tests-results">
                    <!-- Test results will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kbes-security-tests {
    max-width: 1200px;
}

.kbes-tests-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px;
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.kbes-score-card {
    text-align: center;
}

.kbes-score-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 10px auto;
    position: relative;
}

.kbes-score-value {
    font-size: 32px;
    font-weight: bold;
    color: #fff;
}

.kbes-score-description {
    color: #666;
    margin-top: 10px;
}

.kbes-tests-actions {
    display: flex;
    gap: 10px;
}

.kbes-tests-content {
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.kbes-tests-filters {
    padding: 20px;
    border-bottom: 1px solid #c3c4c7;
    display: flex;
    gap: 15px;
}

.kbes-tests-filters select {
    min-width: 150px;
}

.kbes-tests-list {
    padding: 20px;
}

.kbes-tests-loading {
    text-align: center;
    padding: 40px;
    color: #666;
}

.kbes-test-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border: 1px solid #e1e1e1;
    margin-bottom: 10px;
    border-radius: 4px;
}

.kbes-test-item.pass {
    border-left: 4px solid #00a32a;
    background: #f0f8f0;
}

.kbes-test-item.fail {
    border-left: 4px solid #d63638;
    background: #fdf0f0;
}

.kbes-test-item.warn {
    border-left: 4px solid #dba617;
    background: #fdf9f0;
}

.kbes-test-item.error {
    border-left: 4px solid #d63638;
    background: #fdf0f0;
}

.kbes-test-info {
    flex: 1;
}

.kbes-test-name {
    font-weight: bold;
    margin-bottom: 5px;
}

.kbes-test-description {
    color: #666;
    font-size: 14px;
}

.kbes-test-status {
    display: flex;
    align-items: center;
    gap: 10px;
}

.kbes-test-score {
    font-weight: bold;
    padding: 4px 8px;
    border-radius: 4px;
    background: #f0f0f0;
}

.kbes-test-actions {
    display: flex;
    gap: 5px;
}

.kbes-fix-button {
    background: #00a32a;
    color: #fff;
    border: none;
    padding: 5px 10px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 12px;
}

.kbes-fix-button:hover {
    background: #008a20;
}

.kbes-fix-button:disabled {
    background: #ccc;
    cursor: not-allowed;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Load initial test results
    loadTestResults();
    
    // Run tests button
    $('.kbes-run-tests').on('click', function() {
        runSecurityTests();
    });
    
    // Fix all issues button
    $('.kbes-fix-all').on('click', function() {
        fixAllIssues();
    });
    
    // Export report button
    $('.kbes-export-report').on('click', function() {
        exportReport();
    });
    
    // Filter change
    $('#kbes-category-filter, #kbes-status-filter').on('change', function() {
        loadTestResults();
    });
    
    // Fix individual test
    $(document).on('click', '.kbes-fix-button', function() {
        var testId = $(this).data('test-id');
        fixTestIssue(testId);
    });
    
    function loadTestResults() {
        var category = $('#kbes-category-filter').val();
        var status = $('#kbes-status-filter').val();
        
        $.ajax({
            url: kbesAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'kbes_get_test_results',
                nonce: kbesAdmin.nonce,
                category: category,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    displayTestResults(response.data);
                }
            },
            error: function() {
                $('.kbes-tests-results').html('<p>Error loading test results.</p>');
            }
        });
    }
    
    function runSecurityTests() {
        $('.kbes-tests-loading').show();
        $('.kbes-tests-results').empty();
        
        $.ajax({
            url: kbesAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'kbes_run_security_tests',
                nonce: kbesAdmin.nonce
            },
            success: function(response) {
                $('.kbes-tests-loading').hide();
                
                if (response.success) {
                    displayTestResults(response.data);
                    updateSecurityScore(response.data.score);
                } else {
                    $('.kbes-tests-results').html('<p>Error running tests: ' + response.data + '</p>');
                }
            },
            error: function() {
                $('.kbes-tests-loading').hide();
                $('.kbes-tests-results').html('<p>Error running tests.</p>');
            }
        });
    }
    
    function displayTestResults(results) {
        var html = '';
        
        if (results.length === 0) {
            html = '<p>No test results found.</p>';
        } else {
            results.forEach(function(test) {
                var statusClass = test.status || 'unknown';
                var score = test.score || 0;
                var autoFixable = test.auto_fixable || false;
                
                html += '<div class="kbes-test-item ' + statusClass + '">';
                html += '<div class="kbes-test-info">';
                html += '<div class="kbes-test-name">' + test.test_name + '</div>';
                html += '<div class="kbes-test-description">' + test.test_description + '</div>';
                html += '</div>';
                html += '<div class="kbes-test-status">';
                html += '<span class="kbes-test-score">' + score + '%</span>';
                if (autoFixable) {
                    html += '<button class="kbes-fix-button" data-test-id="' + test.test_name + '">Fix</button>';
                }
                html += '</div>';
                html += '</div>';
            });
        }
        
        $('.kbes-tests-results').html(html);
    }
    
    function updateSecurityScore(score) {
        $('.kbes-score-value').text(score + '%');
    }
    
    function fixTestIssue(testId) {
        $.ajax({
            url: kbesAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'kbes_fix_test_issue',
                nonce: kbesAdmin.nonce,
                test_id: testId
            },
            success: function(response) {
                if (response.success) {
                    loadTestResults();
                } else {
                    alert('Error fixing issue: ' + response.data);
                }
            },
            error: function() {
                alert('Error fixing issue.');
            }
        });
    }
    
    function fixAllIssues() {
        if (!confirm('Are you sure you want to fix all auto-fixable issues?')) {
            return;
        }
        
        $.ajax({
            url: kbesAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'kbes_fix_all_issues',
                nonce: kbesAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    loadTestResults();
                } else {
                    alert('Error fixing issues: ' + response.data);
                }
            },
            error: function() {
                alert('Error fixing issues.');
            }
        });
    }
    
    function exportReport() {
        window.open(kbesAdmin.ajax_url + '?action=kbes_export_test_report&nonce=' + kbesAdmin.nonce);
    }
});
</script>
