<?php
defined('ABSPATH') || exit();
//INCLUDED IN CLASS CSS

$topbanner_animation = str_replace(' ','-',$settings['animations_topbanner']);
if($topbanner_animation != 'none'){
    $multiplier = $settings['animations_topbanner_delay_child'];
    $js .=  '
    var topbarItems = document.querySelectorAll(".uicore-top-bar .uicore-animate");
    if(topbarItems.length){
        [].forEach.call(topbarItems, function(div, index) {
            setTimeout(function(){
                div.style.animationPlayState="running";
            }, ('.$multiplier.' * (index + 1)) - '.$multiplier.');
        });
    }
    ';
}

$menu_animation = str_replace(' ','-',$settings['animations_menu']);
if($menu_animation != 'none'){
    $multiplier = $settings['animations_menu_delay_child'];
    $js .=  '
    var logo = document.querySelector(".uicore-header-wrapper .uicore-branding");
    var mq = window.matchMedia( "(max-width: 1025px)" );
    if (mq.matches) {
        var ham = document.querySelectorAll("nav .uicore-ham");
        menuItems = logo ? [logo, ...ham] : [...ham];
    }
    else {
        ';
        if($settings['header_layout'] === 'center_creative'){
            $js .= '
            var menuItems = document.querySelectorAll(".ui-header-row1 > *");
            var extraItems = document.querySelectorAll(".ui-header-row2 > *");
            menuItems = [...menuItems, ...extraItems];
            ';
        }else{
            $js .= '
            var menuItems = document.querySelectorAll(".uicore-header-wrapper ul.uicore-menu > .menu-item > a");
            var extraItems = document.querySelectorAll("#wrapper-navbar .uicore-extra > *");
            menuItems = logo ? [logo, ...menuItems, ...extraItems] : [...menuItems, ...extraItems];
            ';
        }
        $js .='
        var ham = document.querySelectorAll("#wrapper-navbar nav .uicore-ham:not(.uicore-drawer-toggle)")[0];
        if (ham) {
            ham.style.animationPlayState="running";
        }
    }
    if(menuItems.length){
        [].forEach.call(menuItems, function(div, index) {
            setTimeout(function(){
                div.style.animationPlayState="running";
            }, ('.$multiplier.' * (index + 1)) - '.$multiplier.');
        });
    }
    ';

}


$title_animation = str_replace(' ','-',$settings['animations_title']);
if($title_animation != 'none'){
    $js .=  '
    var pagetitle = document.querySelectorAll(".uicore-page-title");
    if(pagetitle.length){
        pagetitle[0].classList.add("ui-a-in-view");
    }
    ';
}

//footer
$footer_animation = str_replace(' ','-',$settings['animations_footer']);
if($footer_animation != 'none'){
    $multiplier = $settings['animations_footer_delay_child'] + 100;
    $js .=  '
    var extra = document.querySelector(".uicore-footer-column");
    var animOff = extra ? "94%" : "99%";
    var footerDiv = document.querySelector(".uicore-footer-wrapper");
    function addFooterAnimation() {
        var footerItems = document.querySelectorAll(".uicore-footer-wrapper .uicore-animate");
        [].forEach.call(footerItems, function(div, index) {
            setTimeout(function(){
                div.style.animationPlayState="running";
            }, ('.$multiplier.' * (index + 1)) - '.$multiplier.');
        });
    }
    if(footerDiv){
        if(typeof Waypoint !== "undefined"){
            var waypoint = new Waypoint({
                element: footerDiv,
                handler: function () {addFooterAnimation()},
                offset: animOff,
            });
        }else{
            addFooterAnimation();
        }
    }
    ';
}

//blog
$blog_animation = str_replace(' ','-',$settings['animations_blog']);
if($blog_animation != 'none'){
    $multiplier = $settings['animations_blog_delay_child'];
    $js .=  '
    var blogItems = document.querySelectorAll(".uicore-blog-animation .uicore-animate");
    var blogTitleItems = document.querySelectorAll(".ui-simple-creative .uicore-animate");
	blogItems = [...blogItems, ...blogTitleItems];
    if(blogItems.length && typeof Waypoint !== "undefined"){
        [].forEach.call(blogItems, function(div, index) {
            new Waypoint({
                element: div,
                handler: function(direction) {
                div.style.animationPlayState="running";
                },
                offset: "90%",
            });
        });
    }
    ';
}

//portfolio
$portfolio_animation = str_replace(' ','-',$settings['animations_portfolio']);
if($portfolio_animation != 'none'){
    $multiplier = $settings['animations_portfolio_delay_child'];
    $js .=  '
    var portfolioItems = document.querySelectorAll(".uicore-portfolio-animation .uicore-animate");
    if(typeof Waypoint !== "undefined"){
        [].forEach.call(portfolioItems, function(div, index) {
            new Waypoint({
                element: div,
                handler: function(direction) {
                    div.style.animationPlayState="running";
                },
                offset: "90%",
            });
        });
    }
    ';
}
//shop
$shop_animation = str_replace(' ','-',$settings['animations_shop']);
if($shop_animation != 'none'){
    $multiplier = $settings['animations_shop_delay_child'];
    $js .=  '
    var shopItems = document.querySelectorAll(".uicore-woo .uicore-animate");
    if(typeof Waypoint !== "undefined"){
        [].forEach.call(shopItems, function(div, index) {
            new Waypoint({
                element: div,
                handler: function(direction) {
                    div.style.animationPlayState="running";
                },
                offset: "90%",
            });
        });
    }
    ';
}
