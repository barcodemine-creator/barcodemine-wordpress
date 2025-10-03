<?php
/**
 * Integrity Scanner Admin Template
 * 
 * @package KloudbeanEnterpriseSecurity
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit('Direct access denied.');
}
?>

<div class="wrap">
    <h1><?php esc_html_e('Core File Integrity Scanner', 'kloudbean-enterprise-security'); ?></h1>
    
    <div class="kbes-integrity-scanner">
        <div class="kbes-scanner-header">
            <div class="kbes-scanner-info">
                <h2><?php esc_html_e('File Integrity Status', 'kloudbean-enterprise-security'); ?></h2>
                <p><?php esc_html_e('Monitor core WordPress files for unauthorized modifications', 'kloudbean-enterprise-security'); ?></p>
            </div>
            
            <div class="kbes-scanner-actions">
                <button type="button" class="button button-primary kbes-scan-files">
                    <?php esc_html_e('Scan Files', 'kloudbean-enterprise-security'); ?>
                </button>
                <button type="button" class="button kbes-create-baseline">
                    <?php esc_html_e('Create Baseline', 'kloudbean-enterprise-security'); ?>
                </button>
                <button type="button" class="button kbes-restore-all">
                    <?php esc_html_e('Restore All', 'kloudbean-enterprise-security'); ?>
                </button>
            </div>
        </div>
        
        <div class="kbes-scanner-content">
            <div class="kbes-scanner-filters">
                <select id="kbes-file-status-filter">
                    <option value=""><?php esc_html_e('All Files', 'kloudbean-enterprise-security'); ?></option>
                    <option value="modified"><?php esc_html_e('Modified', 'kloudbean-enterprise-security'); ?></option>
                    <option value="unchanged"><?php esc_html_e('Unchanged', 'kloudbean-enterprise-security'); ?></option>
                    <option value="missing"><?php esc_html_e('Missing', 'kloudbean-enterprise-security'); ?></option>
                </select>
                
                <input type="text" id="kbes-file-search" placeholder="<?php esc_html_e('Search files...', 'kloudbean-enterprise-security'); ?>">
            </div>
            
            <div class="kbes-scanner-results">
                <div class="kbes-scanner-loading" style="display: none;">
                    <p><?php esc_html_e('Scanning files...', 'kloudbean-enterprise-security'); ?></p>
                </div>
                
                <div class="kbes-files-list">
                    <!-- File results will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.kbes-integrity-scanner {
    max-width: 1200px;
}

.kbes-scanner-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 20px;
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.kbes-scanner-info h2 {
    margin: 0 0 10px 0;
}

.kbes-scanner-info p {
    margin: 0;
    color: #666;
}

.kbes-scanner-actions {
    display: flex;
    gap: 10px;
}

.kbes-scanner-content {
    background: #fff;
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
}

.kbes-scanner-filters {
    padding: 20px;
    border-bottom: 1px solid #c3c4c7;
    display: flex;
    gap: 15px;
}

.kbes-scanner-filters select,
.kbes-scanner-filters input {
    min-width: 150px;
}

.kbes-scanner-results {
    padding: 20px;
}

.kbes-scanner-loading {
    text-align: center;
    padding: 40px;
    color: #666;
}

.kbes-file-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border: 1px solid #e1e1e1;
    margin-bottom: 10px;
    border-radius: 4px;
}

.kbes-file-item.modified {
    border-left: 4px solid #d63638;
    background: #fdf0f0;
}

.kbes-file-item.unchanged {
    border-left: 4px solid #00a32a;
    background: #f0f8f0;
}

.kbes-file-item.missing {
    border-left: 4px solid #dba617;
    background: #fdf9f0;
}

.kbes-file-info {
    flex: 1;
}

.kbes-file-path {
    font-weight: bold;
    margin-bottom: 5px;
}

.kbes-file-status {
    color: #666;
    font-size: 14px;
}

.kbes-file-actions {
    display: flex;
    gap: 5px;
}

.kbes-restore-button {
    background: #00a32a;
    color: #fff;
    border: none;
    padding: 5px 10px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 12px;
}

.kbes-restore-button:hover {
    background: #008a20;
}

.kbes-restore-button:disabled {
    background: #ccc;
    cursor: not-allowed;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Load initial file results
    loadFileResults();
    
    // Scan files button
    $('.kbes-scan-files').on('click', function() {
        scanFiles();
    });
    
    // Create baseline button
    $('.kbes-create-baseline').on('click', function() {
        createBaseline();
    });
    
    // Restore all button
    $('.kbes-restore-all').on('click', function() {
        restoreAllFiles();
    });
    
    // Filter change
    $('#kbes-file-status-filter, #kbes-file-search').on('change keyup', function() {
        loadFileResults();
    });
    
    // Restore individual file
    $(document).on('click', '.kbes-restore-button', function() {
        var filePath = $(this).data('file-path');
        restoreFile(filePath);
    });
    
    function loadFileResults() {
        var status = $('#kbes-file-status-filter').val();
        var search = $('#kbes-file-search').val();
        
        $.ajax({
            url: kbesAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'kbes_get_file_results',
                nonce: kbesAdmin.nonce,
                status: status,
                search: search
            },
            success: function(response) {
                if (response.success) {
                    displayFileResults(response.data);
                }
            },
            error: function() {
                $('.kbes-files-list').html('<p>Error loading file results.</p>');
            }
        });
    }
    
    function scanFiles() {
        $('.kbes-scanner-loading').show();
        $('.kbes-files-list').empty();
        
        $.ajax({
            url: kbesAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'kbes_scan_files',
                nonce: kbesAdmin.nonce
            },
            success: function(response) {
                $('.kbes-scanner-loading').hide();
                
                if (response.success) {
                    displayFileResults(response.data);
                } else {
                    $('.kbes-files-list').html('<p>Error scanning files: ' + response.data + '</p>');
                }
            },
            error: function() {
                $('.kbes-scanner-loading').hide();
                $('.kbes-files-list').html('<p>Error scanning files.</p>');
            }
        });
    }
    
    function createBaseline() {
        if (!confirm('Are you sure you want to create a new baseline? This will overwrite existing hashes.')) {
            return;
        }
        
        $.ajax({
            url: kbesAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'kbes_create_baseline',
                nonce: kbesAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Baseline created successfully!');
                    loadFileResults();
                } else {
                    alert('Error creating baseline: ' + response.data);
                }
            },
            error: function() {
                alert('Error creating baseline.');
            }
        });
    }
    
    function displayFileResults(results) {
        var html = '';
        
        if (results.length === 0) {
            html = '<p>No file results found.</p>';
        } else {
            results.forEach(function(file) {
                var statusClass = file.status || 'unknown';
                var statusText = file.status_text || 'Unknown';
                
                html += '<div class="kbes-file-item ' + statusClass + '">';
                html += '<div class="kbes-file-info">';
                html += '<div class="kbes-file-path">' + file.path + '</div>';
                html += '<div class="kbes-file-status">' + statusText + '</div>';
                html += '</div>';
                html += '<div class="kbes-file-actions">';
                if (file.status === 'modified' && file.can_restore) {
                    html += '<button class="kbes-restore-button" data-file-path="' + file.path + '">Restore</button>';
                }
                html += '</div>';
                html += '</div>';
            });
        }
        
        $('.kbes-files-list').html(html);
    }
    
    function restoreFile(filePath) {
        if (!confirm('Are you sure you want to restore this file? This will overwrite the current file.')) {
            return;
        }
        
        $.ajax({
            url: kbesAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'kbes_restore_file',
                nonce: kbesAdmin.nonce,
                file_path: filePath
            },
            success: function(response) {
                if (response.success) {
                    alert('File restored successfully!');
                    loadFileResults();
                } else {
                    alert('Error restoring file: ' + response.data);
                }
            },
            error: function() {
                alert('Error restoring file.');
            }
        });
    }
    
    function restoreAllFiles() {
        if (!confirm('Are you sure you want to restore all modified files? This will overwrite current files.')) {
            return;
        }
        
        $.ajax({
            url: kbesAdmin.ajax_url,
            type: 'POST',
            data: {
                action: 'kbes_restore_all_files',
                nonce: kbesAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('All files restored successfully!');
                    loadFileResults();
                } else {
                    alert('Error restoring files: ' + response.data);
                }
            },
            error: function() {
                alert('Error restoring files.');
            }
        });
    }
});
</script>
