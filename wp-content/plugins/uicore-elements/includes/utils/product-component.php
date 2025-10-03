<?php
namespace UiCoreElements\Utils;

use UiCoreElements\Helper;
use UiCoreElements\Controls\Query;

use UiCoreElements\Utils\Meta_Trait;
use UiCoreElements\Utils\Post_Trait;

defined('ABSPATH') || exit();

/**
 * Product Component
 *
 * @since 1.0.11
 */

trait Product_Trait {

    use Post_Trait,
        Meta_Trait;

    /**
     * Returns the proper query args for the product loop.
     *
     * @param array $settings Elementor controls settings.
     * @param array|WP_Query $default_query The default query variables.
     *
     * @return array|stdClass Return the products data, prepared for loop.
    */
    function TRAIT_query_products($settings, $default_query)
    {
        $post_type  = $settings['product-filter_post_type'];

        switch ($post_type){
            case 'current' :
                // Makes sure widget will render some products at editor screen
                if( $this->is_edit_mode() ){
                    $query_args['post_type'] = 'product';

                } else {
                    $query_args = $default_query;

                    // Set pagination
                    $query_args['paged'] = Query::get_queried_page($settings);

                    // Set tax filters for filter component, if enabled
                    $queried_filters = Query::get_queried_filters($settings, 'product', 'product-filter');
                    $query_args['tax_query'] = empty($queried_filters['tax_query']) ? [] : $queried_filters['tax_query'];
                }
                break;

            case 'related' :
                return Helper::get_product_related($settings['item_limit']['size']);
                break;

            default :
                $query_args = Query::get_query_args('product-filter', $settings, true);
                break;
        }

        // Set total pages
        $query_args['total_pages'] = Query::get_total_pages($query_args);

        // Hide out of stock products
        if( $settings['hide_out_of_stock'] === 'yes' ){
            $query_args['meta_query'] = [
                [
                    'key' => '_stock_status',
                    'value' => 'instock',
                    'compare' => '=',
                ]
            ];
        }

        $this->_query = $query_args;

        return wc_get_products($query_args);
    }


    // Render functions
    function get_product_image(object $product)
    {
        if ($this->get_settings_for_display('image') !== 'yes') {
            return;
        }

        // Get product image ID
        $pic_id = $product->get_image_id();
        if (!$pic_id) {
            return;
        }

        // Get image size
        $size = $this->get_settings_for_display('image_size_select') ?? 'uicore-medium';
        ?>
            <a class="ui-e-post-img-wrapp"
               href="<?php echo esc_url($product->get_permalink()); ?>"
               title="<?php echo esc_attr__('View Product:', 'uicore-elements') . ' ' . esc_attr($product->get_name()); ?>">

                <?php if ($this->get_settings_for_display('masonry') === 'ui-e-maso') { ?>
                    <?php
                        // Get secondary image if available
                        $sec_img = $this->get_product_secondary_image( $product );
                        $classes = $sec_img ? 'ui-e-post-img ui-e-hover-hide' : 'ui-e-post-img';

                        // Print secondary image markup if exists
                        echo $sec_img ? wp_kses_post($sec_img) : '';

                        // Print main product image
                        echo wp_get_attachment_image($pic_id, $size, false, ['class' => $classes]);
                    ?>
                <?php } else { ?>
                    <?php
                        // Get secondary image with size
                        $sec_img = $this->get_product_secondary_image( $product, $size, true );
                        echo $sec_img ? wp_kses_post($sec_img) : '';
                    ?>
                    <div class="ui-e-post-img <?php echo $sec_img ? 'ui-e-hover-hide' : ''; ?>"
                        style="background-image:url(<?php echo wp_get_attachment_image_url($pic_id, $size); ?>)">
                    </div>
                <?php } ?>
            </a>
        <?php
    }
    function get_product_secondary_image($product, $size = 'woocommerce_thumbnail', $bg_output = false)
    {
        // Requested only for products with change-image animation
        if( 'ui-e-img-anim-change' !== $this->get_settings_for_display('anim_image') ) {
            return false;
        }

        // Get product gallery image IDs
        $gallery_image_ids = $product->get_gallery_image_ids();

        // Check if there is at least one gallery image
        if (empty($gallery_image_ids)) {
            return false;
        }

        // Get the first image from the gallery
        $secondary_image_id = $gallery_image_ids[0];
        $secondary_image_url = wp_get_attachment_image_url($secondary_image_id, $size);

        // Output secondary image as <img> or background <div>
        if ($bg_output) {
            return sprintf(
                '<div class="ui-e-post-img ui-e-post-img-secondary" style="background-image: url(%s);"></div>',
                esc_url($secondary_image_url)
            );
        } else {
            return wp_get_attachment_image($secondary_image_id, $size, false, [
                'class' => 'ui-e-post-img ui-e-post-img-secondary'
            ]);
        }
    }
    function get_product_title($product)
    {
        if ($this->get_settings_for_display('title') !== 'yes') {
            return;
        }

        ?>
            <a href="<?php echo esc_url($product->get_permalink()); ?>"
               title="<?php echo esc_attr__('View Product:', 'uicore-elements') . ' ' . esc_html($product->get_name()); ?>">
                <h4 class="ui-e-post-title"><span><?php echo esc_html($product->get_name()); ?></span></h4>
            </a>
        <?php
    }

    /**
     * Renders a product item with various settings and options.
     * Important: any changes here should also be considered to `TRAIT_render_item()` from Post Component.
     *
     * @param WC_Product $product The product object.
     * @param bool $carousel Indicates if the item needs carousel classnames.
     * @param bool $legacy Indicates if the item is using legacy classnames.
     * @param bool $is_ajax Indicates if the item is being rendered through REST API.
     *
     * @return void
     */
    function TRAIT_render_product($product, $carousel = false, $legacy = false, $is_ajax = false)
    {
        $settings       = $this->get_settings_for_display();
        $excerpt_length = $settings['excerpt_trim'];

        // Classnames but checking if we the widget is APG (legacy version)
        $item_classes     = $legacy ? ['ui-e-post-item', 'ui-e-item'] : ['ui-e-item'] ; // Single item lower wrap class receptor
        $hover_item_class = $legacy ? 'anim_item' : 'item_hover_animation'; // item hover animation class

        // If widget is not carousel type, we set animations classes directly on item selector
        if(!$carousel) {
            $item_classes[] = 'ui-e-animations-wrp';
            $item_classes[] = $settings['animate_items'] === 'ui-e-grid-animate' ? 'elementor-invisible' : '';
            $item_classes[] = $settings[$hover_item_class] !== '' ? $settings[$hover_item_class] : '';

        } else {
            // Get entrance and item hover animation classes
            $entrance   = (isset($settings['animate_items']) &&  $settings['animate_items'] == 'ui-e-grid-animate') ? 'elementor-invisible' : '';
            $hover      = isset($settings[$hover_item_class]) ? $settings[$hover_item_class] : ''; //$settings[$hover_item_class] : null;
            $animations = sprintf('%s %s', $entrance, $hover);

            // Check if entrance or hover animation are set
            $has_animation = !empty($entrance) || !empty($hover)

            // Prints extra wrappers required by the carousel script
            ?>
            <div class="ui-e-wrp swiper-slide">
            <?php if($has_animation) : ?>
                <div class="ui-e-animations-wrp <?php echo esc_attr($animations);?>">
            <?php endif; ?>

        <?php } ?>

            <div class="<?php echo esc_attr( implode(' ', $item_classes));?>">
                <article <?php post_class('product'); ?>>
                    <div class="ui-e-post-top">
                        <?php $this->get_product_image($product); ?>
                        <?php $this->get_post_meta('top'); ?>
                        <?php
                            if($settings['button_position'] === 'ui-e-button-placement-image') {
                                $this->TRAIT_get_button(true);
                            }
                        ?>
                    </div>
                    <div class="ui-e-post-content">
                        <?php $this->get_post_meta('before_title'); ?>
                        <?php
                        if ($this->get_settings_for_display('title') === 'yes')
                            $this->get_product_title($product);
                        ?>
                        <?php $this->get_post_meta('after_title'); ?>
                        <?php
                        if ($this->get_settings_for_display('excerpt'))
                            echo wp_kses_post('<div class="ui-e-post-text">' . wp_trim_words(get_the_excerpt(), $excerpt_length) . '</div>');
                        ?>
                        <?php $this->get_post_meta('bottom');  // button
                        ?>
                        <?php
                        if( defined('UICORE_VERSION') && version_compare(UICORE_VERSION, '6.0.1', '>=') && $this->get_settings_for_display('show_swatches') === 'yes' ){
                            // ajax requests works under minimal conditions, wich means Swatches class is not present
                            if ( $is_ajax ) {
                                require_once UICORE_INCLUDES . '/woocommerce/components/class-swatches.php';
                            }

                            \UiCore\WooCommerce\Swatches::print_swatches($product); // from Uicore Framework
                        }
                        if ( $this->get_settings_for_display('show_button') === 'yes' ) {
                            // Add match height spacing element if carousel. May look intrusive, but is the simplest method compared
                            // to absolute positioning or catching last content element to apply margin.
                            if($carousel && $settings['match_height'] === 'yes'){
                                ?>
                                    <span class="ui-e-match-height"></span>
                                <?php
                            }

                            if( !isset($settings['button_position']) || empty($settings['button_position']) ) {
                                $this->TRAIT_get_button(true);
                            }
                        }
                        ?>
                    </div>
                </article>
            </div>

        <?php if($carousel) { ?>
            </div>
            <?php if($has_animation) : ?>
                </div>
            <?php endif; ?>

        <?php }
    }

    // TODO: discover why using Post_Trait here forces us to have
    // this public content_template() to avoid fatal error
    public function content_template() {}
}
