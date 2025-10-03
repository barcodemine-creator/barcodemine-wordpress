( function( $ ) {
    "use strict";
    
    function getUrlVars(){
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for(var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    }

    function validateBarcodeInput(input) {
        var value = input.replace(/[^0-9]/g, ''); // Remove non-numeric characters
        var isValid = value.length >= 8 && value.length <= 13; // Allow 8-13 digits
        return {
            isValid: isValid,
            cleanValue: value,
            message: isValid ? '' : 'Please enter a valid 8-13 digit barcode number'
        };
    }

    function showValidationMessage(form, message, isError) {
        var messageDiv = form.find('.validation-message');
        if (messageDiv.length === 0) {
            messageDiv = $('<div class="validation-message"></div>');
            form.find('.search-help').after(messageDiv);
        }
        
        messageDiv.removeClass('error success').addClass(isError ? 'error' : 'success');
        messageDiv.html(message);
        
        if (message) {
            messageDiv.show();
        } else {
            messageDiv.hide();
        }
    }

    function search_form_submit( formData ){
        var form = formData,
            formSelector = $(form)[0],
            input = form.find('input[name="geiper_name"]'),
            submitBtn = form.find('.barcode-search-btn'),
            searchIcon = form.find('.search-icon'),
            loader = form.find('.search-loader');
            
        // Validate input
        var validation = validateBarcodeInput(input.val());
        if (!validation.isValid) {
            showValidationMessage(form, validation.message, true);
            return;
        }
        
        // Clear validation message
        showValidationMessage(form, '', false);
        
        // Update input with clean value
        input.val(validation.cleanValue);
        
        var fd = new FormData( formSelector );
        // Our AJAX identifier
        fd.append('action', 'barcode_search');

        $.ajax({
            type: 'POST',
            url: barcodemain.ajaxurl,
            contentType: false,
            processData: false,
            data : fd,
            timeout: 30000, // 30 second timeout
            beforeSend: function () {
                submitBtn.prop('disabled', true);
                searchIcon.hide();
                loader.show();
                form.parent().find('.search-result').html('<div class="searching-message">Searching for barcode...</div>');
            },
            success: function (response) {
                form.parent().find('.search-result').html(response);
                
                // Scroll to results if found
                if (response.indexOf('bct-gepir-results__table') > -1) {
                    $('html, body').animate({
                        scrollTop: form.parent().find('.search-result').offset().top - 50
                    }, 500);
                }
            },
            error: function(xhr, status, error) {
                var errorMessage = '<div class="barcode-not-found"><p><strong>Search Error</strong></p><p>There was an error processing your search. Please try again.</p></div>';
                if (status === 'timeout') {
                    errorMessage = '<div class="barcode-not-found"><p><strong>Search Timeout</strong></p><p>The search is taking longer than expected. Please try again.</p></div>';
                }
                form.parent().find('.search-result').html(errorMessage);
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                searchIcon.show();
                loader.hide();
            }
        });
    }
    
    function bulk_search_form_submit( formData ){
        var form = formData,
            formSelector = $(form)[0],
            textarea = form.find('textarea[name="bulk_barcodes"]'),
            submitBtn = form.find('.bulk-search-btn'),
            searchIcon = form.find('.search-icon'),
            loader = form.find('.search-loader');
            
        // Validate input
        var barcodes = textarea.val().trim();
        if (!barcodes) {
            alert('Please enter at least one barcode.');
            return;
        }
        
        var fd = new FormData( formSelector );
        fd.append('action', 'bulk_barcode_search');

        $.ajax({
            type: 'POST',
            url: barcodemain.ajaxurl,
            contentType: false,
            processData: false,
            data : fd,
            timeout: 60000, // 60 second timeout for bulk search
            beforeSend: function () {
                submitBtn.prop('disabled', true);
                searchIcon.hide();
                loader.show();
                form.parent().find('.bulk-search-results').html('<div class="searching-message">Processing bulk search...</div>');
            },
            success: function (response) {
                try {
                    var data = JSON.parse(response);
                    if (data.error) {
                        form.parent().find('.bulk-search-results').html('<div class="bulk-result-item bulk-result-not-found">' + data.error + '</div>');
                        return;
                    }
                    
                    var html = '<div class="bulk-summary">';
                    html += '<h4>Bulk Search Results</h4>';
                    html += '<p><strong>Total Searched:</strong> ' + data.total + ' | ';
                    html += '<strong style="color: #28a745;">Found:</strong> ' + data.found + ' | ';
                    html += '<strong style="color: #dc3545;">Not Found:</strong> ' + data.not_found + '</p>';
                    html += '</div>';
                    
                    data.results.forEach(function(result) {
                        if (result.found) {
                            html += '<div class="bulk-result-item bulk-result-found">';
                            html += '<h5>✓ Barcode: ' + result.barcode + '</h5>';
                            html += '<p><strong>Company:</strong> ' + (result.data.company || 'N/A') + '</p>';
                            html += '<p><strong>Owner:</strong> ' + result.data.first_name + ' ' + result.data.last_name + '</p>';
                            html += '<p><strong>Range:</strong> ' + result.data.barcode_range.first + ' - ' + result.data.barcode_range.last + ' (' + result.data.barcode_range.count + ' barcodes)</p>';
                            html += '<p><strong>Contact:</strong> ' + (result.data.phone || 'N/A') + '</p>';
                            html += '</div>';
                        } else {
                            html += '<div class="bulk-result-item bulk-result-not-found">';
                            html += '<h5>✗ Barcode: ' + result.barcode + '</h5>';
                            html += '<p>' + result.message + '</p>';
                            html += '</div>';
                        }
                    });
                    
                    form.parent().find('.bulk-search-results').html(html);
                    
                    // Scroll to results
                    $('html, body').animate({
                        scrollTop: form.parent().find('.bulk-search-results').offset().top - 50
                    }, 500);
                    
                } catch (e) {
                    form.parent().find('.bulk-search-results').html('<div class="bulk-result-item bulk-result-not-found">Error processing search results.</div>');
                }
            },
            error: function(xhr, status, error) {
                var errorMessage = '<div class="bulk-result-item bulk-result-not-found"><h5>Search Error</h5><p>There was an error processing your bulk search. Please try again.</p></div>';
                if (status === 'timeout') {
                    errorMessage = '<div class="bulk-result-item bulk-result-not-found"><h5>Search Timeout</h5><p>The bulk search is taking longer than expected. Please try with fewer barcodes.</p></div>';
                }
                form.parent().find('.bulk-search-results').html(errorMessage);
            },
            complete: function() {
                submitBtn.prop('disabled', false);
                searchIcon.show();
                loader.hide();
            }
        });
    }
    
    $(document).ready(function() {
        // Auto-populate from URL parameter
        var barcode = getUrlVars()["barcode_number"];
        if( typeof barcode !== "undefined" && barcode != '' ){
            $('input[name="geiper_name"]').val(decodeURIComponent(barcode));
            search_form_submit($('.search-gepir-data'));
        }
        
        // Handle form submission
        $( document ).on( 'submit', '.search-gepir-data', function(e) {
            e.preventDefault();
            search_form_submit($(this));
        });
        
        // Handle bulk search form submission
        $( document ).on( 'submit', '.bulk-search-form', function(e) {
            e.preventDefault();
            bulk_search_form_submit($(this));
        });
        
        // Real-time input validation
        $( document ).on( 'input', '.barcode-input', function() {
            var form = $(this).closest('form');
            var validation = validateBarcodeInput($(this).val());
            
            if ($(this).val().length > 0) {
                if (validation.isValid) {
                    showValidationMessage(form, '✓ Valid barcode format', false);
                } else {
                    showValidationMessage(form, validation.message, true);
                }
            } else {
                showValidationMessage(form, '', false);
            }
        });
        
        // Only allow numeric input
        $( document ).on( 'keypress', '.barcode-input', function(e) {
            // Allow: backspace, delete, tab, escape, enter
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true)) {
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });
        
        // Add CSS for validation messages
        $('<style>')
            .prop('type', 'text/css')
            .html(`
                .validation-message {
                    padding: 8px 12px;
                    border-radius: 4px;
                    margin: 10px 0;
                    font-size: 14px;
                    display: none;
                }
                .validation-message.error {
                    background: #ffebee;
                    border: 1px solid #f44336;
                    color: #c62828;
                }
                .validation-message.success {
                    background: #e8f5e8;
                    border: 1px solid #4caf50;
                    color: #2e7d32;
                }
                .searching-message {
                    text-align: center;
                    padding: 20px;
                    color: #666;
                    font-style: italic;
                }
            `)
            .appendTo('head');
    });

})( jQuery );