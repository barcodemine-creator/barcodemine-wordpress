<?php
/**
 * WooCommerce Compatibility
 */
/**
 * WooCommerce setup
 *
 * @link https://docs.woocommerce.com/document/third-party-custom-theme-compatibility/
 * @link https://github.com/woocommerce/woocommerce/wiki/Enabling-product-gallery-features-(zoom,-swipe,-lightbox)-in-3.0.0
 *
 * @return void
 */
function _s_woocommerce_setup()
{
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', '_s_woocommerce_setup');

//remove woo default sidebar
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
//remove woo default breadcrumb
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
//remove woo default page title
add_filter('woocommerce_show_page_title', '__return_false');

// new Pagination;
require_once UICORE_INCLUDES . '/templates/pagination.php';
remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
add_action(
    'woocommerce_after_shop_loop',
    function () {
        new Uicore\Pagination();
    },
    10
);

// //Zoom wrapper
// add_action(
//     'woocommerce_before_shop_loop_item',
//     function () {
//         echo '<div class="uicore-zoom-wrapper">';
//     },
//     10
// );
// add_action(
//     'woocommerce_before_shop_loop_item_title',
//     function () {
//         echo '</div>';
//     },
//     10
// );

//Add to cart&price hover effect
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
add_action(
    'woocommerce_after_shop_loop_item',
    function () {
        echo '<div class="uicore-reveal-wrapper"><div class="uicore-reveal">';
    },
    6
);
add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_price', 8);
add_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 9);
add_action(
    'woocommerce_after_shop_loop_item',
    function () {
        echo '</div></div>';
    },
    11
);

//Wrap the woo pages
remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
if (!function_exists('uicore_wrapper_before')) {
    function uicore_wrapper_before()
    {
        ?>
        <main id="main" class="site-main elementor-section elementor-section-boxed uicore">
        <div class="uicore elementor-container uicore-content-wrapper uicore-woo">
            <div class="uicore-archive uicore-post-content">
			<?php
    }
}
add_action('woocommerce_before_main_content', 'uicore_wrapper_before');
if (!function_exists('uicore_wrapper_after')) {
    function uicore_wrapper_after()
    {
        ?>
            </div>
        <?php do_action('uicore_sidebar'); ?>
        </div>
    </main>
    <?php
    }
}
add_action('woocommerce_after_main_content', 'uicore_wrapper_after');

/**
 * Enqueue Custom js in footer
 *
 * @param $classes
 * @return BodyClassList
 */
function uicore_add_body_class__for_woo($classes)
{
    $woocommerce_sidebar = get_option('uicore_woocommerce_sidebar_id', 'none');
    $woocommerce_sidebar_pos = get_option('uicore_woocommerce_sidebar', 'left');
    $woocommerce_single_sidebar = get_option('uicore_woocommerces_sidebar_id', 'none');
    $woocommerce_single_sidebar_pos = get_option('uicore_woocommerces_sidebar', 'left');

    $newclasses = [
        (is_shop() || is_product_taxonomy()) && $woocommerce_sidebar !== 'none'
            ? 'uicore-sidebar-' . $woocommerce_sidebar_pos
            : null,
        is_product() && $woocommerce_single_sidebar !== 'none'
            ? 'uicore-sidebar-' . $woocommerce_single_sidebar_pos
            : null,
        is_shop() ||
        is_product_taxonomy() ||
        is_product() ||
        is_cart() ||
        is_account_page() ||
        is_checkout() ||
        is_wc_endpoint_url()
            ? 'uicore-woo-page'
            : null,
    ];

    return array_merge($classes, $newclasses);
}
add_filter('body_class', 'uicore_add_body_class__for_woo');

function uicore_add_woo_class_on_post($classes){
    $newclasses = ['uicore-animate'];
    return array_merge($classes, $newclasses);
}
add_filter('woocommerce_post_class', 'uicore_add_woo_class_on_post');

function uicore_add_animate_class_before(){
    echo '<div class="uicore-animate">';
}
add_filter('woocommerce_before_shop_loop', 'uicore_add_animate_class_before', -999);

function uicore_add_animate_class_after(){
    echo '</div>';
}
add_filter('woocommerce_before_shop_loop', 'uicore_add_animate_class_after', 999);

function uicore_get_related_out_of_animate(){
    echo '</div><div>';
}
add_filter('woocommerce_output_related_products', 'uicore_get_related_out_of_animate', 11); //woocommerce_output_product_data_tabs - 10


/**
 * Default loop columns on product archives.
 *
 * @return integer products per row.
 */
function uicore_woocommerce_loop_columns()
{
    return get_option('uicore_woocommerce_col', '3');
}
add_filter('loop_shop_columns', 'uicore_woocommerce_loop_columns');

/**
 * Products per page.
 *
 * @return integer number of products.
 */
function uicore_woocommerce_products_per_page()
{
    return get_option('uicore_woocommerce_posts_number', '12');
}
add_filter('loop_shop_per_page', 'uicore_woocommerce_products_per_page');

/**
 * Ajax Item in cart no for header icon.
 *
 * @param  mixed $fragments
 *
 * @return void
 */
function uicore_refresh_mini_cart_count($fragments)
{
    ob_start(); ?>
    <span  id="uicore-count-update">
        <?php echo WC()->cart->get_cart_contents_count(); ?>
    </span>
    <?php
    $fragments['#uicore-count-update'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'uicore_refresh_mini_cart_count');

/**
 *  specific styles
 */
function uicore_specific_woo_style() {
    $upload_dir = wp_upload_dir(); 
    $file = $upload_dir['basedir']."/uicore-blog.css";
    if(\file_exists($file) && (is_shop() || is_product_taxonomy() || is_product() || is_cart() || is_account_page() || is_checkout() || is_wc_endpoint_url()) ){
        $url = $upload_dir['baseurl']."/uicore-woo.css";
        $version = get_option('ui_style_version', UICORE_VERSION);
        wp_enqueue_style('uicore-woo-st',$url,[], $version);
    }
}
add_action( 'wp_enqueue_scripts', 'uicore_specific_woo_style' );