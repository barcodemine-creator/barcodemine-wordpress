<?php
namespace UiCore;
defined('ABSPATH') || exit();

/**
 * Here we generate the page template
 */
class Pages
{
    function __construct()
    {
        //add suport for elementor pro theme builder
        if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'single' ) ) {

            //fallback to the default page template
            $this->page();
        }
    }

    /**
     * This is the default Page template
     */
    function page()
    {
        global $post;
        $is_elementor = false;

        while (have_posts()):

            //setup the post first
            the_post();

            //check if the page is built with elementor
            if(\class_exists('\Elementor\Plugin') && isset($post->ID) && $post->ID){
                $is_elementor = \Elementor\Plugin::$instance->documents->get( $post->ID )->is_built_with_elementor();
            }
            
            //if the page is built with elementor, then we don't need to add the wrapper
            if ( ($is_elementor && !\class_exists('WooCommerce')) || Helper::get_option('gen_maintenance') === 'true' || ($is_elementor && \class_exists('WooCommerce') && !\is_product()) ) {
                $this->elementor_content();
            } else {
                $this->content();
            }
        endwhile; // End of the loop.
    }

    /**
     * This is the default content function
     */
    function elementor_content()
    {
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <main class="entry-content">
                <?php the_content(); ?>
            </main>
        </article>
        <?php
    }

    /**
     * This is the content function that includes the wrapper container
     */
    function content()
    {
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="entry-content">
            <main id="main" class="site-main elementor-section elementor-section-boxed uicore">
                <div class="uicore elementor-container uicore-content-wrapper uicore-no-editor">
                    <div class="uicore-content">
                    <?php the_content(); ?>
                    </div>
                </div>
            </main>
            </div>
        </article>
        <?php
    }
}
