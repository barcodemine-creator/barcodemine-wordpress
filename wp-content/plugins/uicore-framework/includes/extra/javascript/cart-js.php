<?php
defined("ABSPATH") || exit();
//INCLUDED IN CLASS JS

$js .= "
jQuery(function ($) {
    $('.uicore-cart-icon.uicore-link').click(function () {
        //continue only if the parent of this element is not a link
        if ($(this).parent().is('a')) {
            return;
        }
        $('body').addClass('uicore-cart-active');
    });
    $('#cart-wrapper').click(function () {
        $('body').removeClass('uicore-cart-active');
    });

    $('#uicore-cart-close').click(function () {
        $('body').removeClass('uicore-cart-active');
    }); 
});
";

