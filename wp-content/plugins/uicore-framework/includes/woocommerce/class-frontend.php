<?php
namespace UiCore\WooCommerce;
use UiCore\Helper as Helper;
use UiCore\Pagination;

defined('ABSPATH') || exit();


/**
 * Frontend WooCommerce
 *
 * @author Andrei Voica <andrei@uicore.co
 * @since 2.0.2
 */
class Frontend
{

    /**
     * Construct Frontend
     *
     * @author Andrei Voica <andrei@uicore.co
     * @since 2.0.2
     */
    public function __construct()
    {

        $this->markup_filters();
        //Add Woo Support
        add_action('after_setup_theme', [$this, 'theme_support']);

        //hook on this to have all conditions
        add_action('wp', function () {

            if(self::is_woo()){
                //Add custom classes to body
                add_filter('body_class', [$this, 'add_body_class']);

                //handle products page title option
                if(\is_product() && Helper::get_option("woocommerces_title") === "simple page title"){
                    add_filter('uicore_is_pagetitle', '__return_false');
                }elseif(\is_product() && Helper::get_option("woocommerces_title") != "simple page title"){
                    //remove default woo title and  woo prod breadcrumb
                    remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_title', 5);
                    remove_action( 'woocommerce_before_main_content','woocommerce_breadcrumb', 20, 0);
                }

                //handle shop archive page title option
                if( (\is_shop() || \is_post_type_archive('product')  || is_product_taxonomy() ) && Helper::get_option("pagetitle") === "true"){
                    //remove default woo title and  woo archive breadcrumb
                    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
                    add_filter('woocommerce_show_page_title', '__return_false');
                }

            }
        });

        //Style and scripts are added in global

        //filter posts number
        add_filter('loop_shop_per_page', [$this, 'filter_posts_number']);
        //Filter Archieve columns number
        add_filter('loop_shop_columns', [$this, 'filter_col_number']);
        //Ajax Item in cart no for header icon.
        add_filter('woocommerce_add_to_cart_fragments', [$this, 'mini_cart_count']);

        add_filter('woocommerce_post_class', [$this, 'animate_class_on_prosts']);

        add_filter( 'template_include',[$this, 'tb_check'], 20, 1);

        // new Pagination;
        require_once UICORE_INCLUDES . '/templates/pagination.php';
    }

    function tb_check( $template)
    {
        //If there is a theme builder template for it replace it with the default one
        $is_theme_builder_template = apply_filters('uicore_is_template', false);
        if(\strpos($template,'archive-product.php') !== false && $is_theme_builder_template){
            $template = locate_template( array( 'index.php' ) );
        }

        return $template;
    }


    /**
     * Add Conditional body classes
     *
     * @param array $classes
     * @return array $classes
     * @author Andrei Voica <andrei@uicore.co
     * @since 1.0.0
     */
    public function add_body_class(array $classes)
    {
        $woocommerce_sidebar = Helper::get_option('woocommerce_sidebar_id', 'none');
        $woocommerce_sidebar_pos = Helper::get_option('woocommerce_sidebar', 'left');
        $woocommerce_single_sidebar = Helper::get_option('woocommerces_sidebar_id', 'none');
        $woocommerce_single_sidebar_pos = Helper::get_option('woocommerces_sidebar', 'left');

        $newclasses = [
            (is_shop() || is_product_taxonomy()) && $woocommerce_sidebar !== 'none'
                ? 'uicore-sidebar-' . $woocommerce_sidebar_pos
                : null,
            is_product() && $woocommerce_single_sidebar !== 'none'
                ? 'uicore-sidebar-' . $woocommerce_single_sidebar_pos
                : null,
            'uicore-woo-page'
        ];

        return array_merge($classes, $newclasses);
    }

    function animate_class_on_prosts($classes){
        $newclasses = ['uicore-animate'];
        return array_merge($classes, $newclasses);
    }

    /**
     * Filter posts to return a number that fit's the grid.
     *
     * @param object $query
     * @return void
     * @author Andrei Voica <andrei@uicore.co
     * @since 2.0.2
     */
    function filter_posts_number()
    {
        return Helper::get_option('woocommerce_posts_number');
    }

    /**
     * Filter posts to return a number that fit's the grid.
     *
     * @param object $query
     * @return void
     * @author Andrei Voica <andrei@uicore.co
     * @since 2.0.2
     */
    function filter_col_number()
    {
        return Helper::get_option('woocommerce_col');
    }

    /**
     * Filter posts to return a number that fit's the grid.
     *
     * @param object $query
     * @return void
     * @author Andrei Voica <andrei@uicore.co
     * @since 5.0.6
     */
    function filter_title($title)
    {
        $is_simple =  (Helper::get_option("woocommerces_title") === "simple page title");
        if($is_simple){
            return $title;
        }else{
            return false;
        }
    }

    /**
     * Filter posts to return a number that fit's the grid.
     *
     * @param object $query
     * @return void
     * @author Andrei Voica <andrei@uicore.co
     * @since 2.0.2
     */
    function theme_support()
    {
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
    }

    /**
     * Filter posts to return a number that fit's the grid.
     *
     * @param object $query
     * @return void
     * @author Andrei Voica <andrei@uicore.co
     * @since 2.0.2
     */
    function mini_cart_count($fragments)
    {
        ob_start(); ?>
        <span  id="uicore-count-update">
            <?php echo WC()->cart->get_cart_contents_count(); ?>
        </span>
        <?php
        $fragments['#uicore-count-update'] = ob_get_clean();
        return $fragments;
    }


    /**
     * Filter posts to return a number that fit's the grid.
     *
     * @param object $query
     * @return void
     * @author Andrei Voica <andrei@uicore.co
     * @since 2.0.2
     */
    static function markup_filters()
    {

        //remove woo default sidebar
        remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
        //remove woo default breadcrumb
        // remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
        //remove woo default page title
        // add_filter('woocommerce_show_page_title', ['UiCore\WooCommerce\Frontend', 'filter_title']);


        remove_action('woocommerce_after_shop_loop', 'woocommerce_pagination', 10);
        add_action(
            'woocommerce_after_shop_loop',
            function () {
                new Pagination();
            },
            10
        );

        //Zoom wrapper
        add_action(
            'woocommerce_before_shop_loop_item',
            function () {
                echo '<div class="uicore-zoom-wrapper">';
            },
            10
        );
        add_action(
            'woocommerce_before_shop_loop_item_title',
            function () {
                echo '</div>';
            },
            10
        );

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

        add_action('woocommerce_before_main_content', function ()
            {
                ?>
                <main id="main" class="site-main elementor-section elementor-section-boxed uicore">
                <div class="uicore elementor-container uicore-content-wrapper uicore-woo">
                    <div class="uicore-archive uicore-post-content">
                    <?php
            }
        );

        add_action('woocommerce_after_main_content', function()
            {
                ?>
                    </div>
                <?php do_action('uicore_sidebar'); ?>
                </div>
            </main>
            <?php
            }
        );

        add_filter('woocommerce_before_shop_loop',
            function(){
                echo '<div class="uicore-animate">';
            },
         -999);

        add_filter('woocommerce_before_shop_loop',
            function (){
                echo '</div>';
            },
         999);


        add_filter('woocommerce_output_related_products',
            function (){
                echo '</div><div>';
            },
         11); //woocommerce_output_product_data_tabs - 10




         //wrap product page content in one section
         add_action ('woocommerce_before_single_product_summary', function(){
            echo '<div class="uicore-summary-wrapp">';
         }, 1);
         \add_action('woocommerce_after_single_product_summary', function(){
            echo '</div>';
         }, 1);
    }

    static function is_woo()
    {
        return (is_post_type_archive('product')  || is_product_taxonomy() || is_product() || is_woocommerce() || is_shop() || is_cart() ||
        is_account_page() ||
        is_checkout() ||
        is_wc_endpoint_url());
    }
}
new Frontend();
