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

    function search_form_submit( formData ){
        var form = formData,
                formSelector = $(form)[0],
                fd = new FormData( formSelector );
            // Our AJAX identifier
            fd.append('action', 'barcode_search');

            $.ajax({
                type: 'POST',
                url: barcodemain.ajaxurl,
                contentType: false,
                processData: false,
                data : fd,
                beforeSend: function () {
                    form.find('.search-loader').css('display', 'inline-block');
                },
                success: function (response) {
                    form.parent().find('.search-result').html(response);
                    form.trigger('reset');
                    form.find('.search-loader').css('display', 'none');
                }
            });
    }
    $(document).ready(function() {
        var barcode = getUrlVars()["barcode_number"];
        if( typeof barcode !== "undefined" && barcode != '' ){
            $('input[name="geiper_name"]').val(barcode);
            search_form_submit($('.search-gepir-data'));
        }
        $( document ).on( 'submit', '.search-gepir-data', function(e) {
            search_form_submit($(this));
            e.preventDefault();
        });
    });

  
})( jQuery );