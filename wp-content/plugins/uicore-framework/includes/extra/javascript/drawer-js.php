<?php
defined('ABSPATH') || exit();
//INCLUDED IN CLASS JS

$toggle = $settings['header_sd_toggle'];

$js .= '
jQuery(document).on("click", ".uicore-drawer-toggle, .ui-sd-backdrop", ui_show_sd);
';

if($toggle === 'hover'){
    $js .= '
    jQuery(document).on("mouseenter", ".uicore-drawer-toggle", ui_show_sd);
    jQuery(document).on("mouseleave", ".ui-drawer-content", ui_show_sd);
 ';
}
$js .= '
function ui_show_sd(){
    jQuery(".ui-drawer").toggleClass("ui-active");
}
';