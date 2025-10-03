<?php
namespace UiCore\Elementor\ThemeBuilder;


defined('ABSPATH') || exit();

/**
 * Theme Builder Frontend functions
 *
 * @author Andrei Voica <andrei@uicore.co
 * @since 2.0.0
 */
class Frontend
{

    private static $instance;
    private static $settings;

    /**
	 * Init
	 *
	 * @return mixexd
	 * @author Andrei Voica <andrei@uicore.co>
	 * @since 4.0.0
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


    /**
     * Construct Theme Builder Frontend functions
     *
     * @author Andrei Voica <andrei@uicore.co
     * @since 2.0.0
     */
    public function __construct()
    {
        if(defined('UICORE_LIBRARY_MODE') && \UICORE_LIBRARY_MODE){
            //stop theme builder from being loaded on the frontend
        }else{
            add_action('wp', function () {
                $positions = [
                    'header' => [
                        'action'    =>'uicore_page',
                        'prio'      => 5
                    ],
                    'footer' => [
                        'action'    =>'uicore_content_end',
                        'prio'      => 8
                    ],
                    'pagetitle' => [
                        'action'    =>'uicore_before_content',
                        'prio'      => 12
                    ],
                    'popup' => [
                        'action'    =>'uicore_content_end',
                        'prio'      => 999
                    ],
                    'archive' => [
                        'action'    =>'uicore_do_template',
                        'prio'      => 10
                    ],
                    'single' => [
                        'action'    =>'uicore_do_template',
                        'prio'      => 10
                    ],
                ];

                //Colect data based on current page
                $this->init();
            
                if(self::$settings){
                    //Add Hoocks and filters in order to display the TB content;
                    foreach(self::$settings as $location=>$list){
                        // \error_log($location);
                        // \error_log(print_r($list,true));
                        if(is_array($list) && count($list) && $location != 'block'){
                            $id = $list[0];
                            if(\in_array($location,['archive','single'])){
                                add_filter('uicore_is_template', [$this,'true']);
                            }else{
                                if(in_array($location,['footer','header','pagetitle'])){
                                    $post_settings = get_post_meta($id, 'tb_settings', true);
                                    if($post_settings['keep_default'] === 'false'){
                                        add_filter('uicore_is_'.$location, [$this,'false']);
                                    }
                                }else{
                                    add_filter('uicore_is_'.$location, [$this,'true']);
                                }
                            }
                            if($location == 'popup'){
                                $content_list = $list;
                            }else{
                                $content_list = [$id];
                            }
                            foreach($content_list as $id){
                                add_action($positions[$location]['action'],function() use ($location,$id){
                                    $this->display($location,$id);
                                },$positions[$location]['prio']);
                                add_action('wp_enqueue_scripts', function() use ($id){
                                    $css_file = new \Elementor\Core\Files\CSS\Post($id);
                                    $css_file->enqueue();
                                });
                            }
                        }
                        if($location == 'block'){
                            foreach($list as $id){
                                $this->inject_block($id);
                                add_action('wp_enqueue_scripts', function() use ($id){
                                    $css_file = new \Elementor\Core\Files\CSS\Post($id);
                                    $css_file->enqueue();
                                });
                            }
                        }
                    }
                }
            });

            //Change the menu walker so we can inject the megamenu
            add_filter('walker_nav_menu_start_el',[$this, 'megamenu_content_in_nav'],10,4);
            add_filter('nav_menu_css_class',[$this, 'megamenu_item_class'],10,2);

            // Prevent Google from indexing the custom URL
            add_filter('wp_robots', [$this,'prevent_custom_url_indexing']);

            $this->define_extra_hooks();
        }

    }
    
    function false(){
        return false;
    }
    function true(){
        return true;
    }

    /**
     * Get Frontend Themebuilder elements to display
     *
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 2.0.0
     */
    function init()
    {
        if(!is_array(self::$settings)){
            self::$settings = $this->get_settings();
		}
    }

    /**
     * Get frontend elments
     *
     * @param [type] $type
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 2.0.0
     */
    function get_settings()
    {
        $templates = \UiCore\Elementor\ThemeBuilder\Rule::get_instance()->get_posts_by_conditions();
        return apply_filters( "uicore_tb_get_settings", $templates );
    }

    /**
     * Display item makrup
     *
     * @param string $type
     * @param int|array $id
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 2.0.0
     */
    function display($type, $id=null)
    {
        $attr = '';
        $settings = get_post_meta($id, 'tb_settings', true);
        $is_fixed = isset($settings['fixed']) ? $settings['fixed'] : 'false';

        if($type === 'header'){
            if($is_fixed != 'false'){
                $attr = 'style="position:sticky;top:0;z-index:99;"';
            }
            ?>
            <header id="uicore-tb-header" itemscope="itemscope" itemtype="https://schema.org/WPHeader"<?php echo $attr; ?>>
			    <?php echo Common::get_elementor_content($id); ?>
		    </header>
            <?php
        }else if($type === 'footer'){
            if($is_fixed != 'false'){
                $attr = 'style="position:sticky;bottom:0;z-index:-2;max-height: 100vh;"';
            }
            ?>
            <footer id="uicore-tb-footer" itemscope="itemscope" itemtype="https://schema.org/WPFooter" <?php echo $attr; ?>>
			    <?php echo Common::get_elementor_content($id); ?>
		    </footer>
            <?php
        }else if($type === 'pagetitle'){
            if($is_fixed != 'false'){
                $attr = 'style="position:sticky;top:0;z-index:-1;"';
            }
            ?>
            <header id="uicore-tb-pagetitle" <?php echo $attr; ?>>
			    <?php echo Common::get_elementor_content($id); ?>
		    </header>
            <?php
        }else if($type === 'popup'){
				$content = Common::get_elementor_content($id);
				Common::popup_markup($content,$id);
        } else{
            echo Common::get_elementor_content($id);
        }


    }

    /**
     * Inject Megamenu in navbar
     *
     * @param [type] $item_output
     * @param [type] $item
     * @param [type] $depth
     * @param [type] $args
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 2.0.0
     */
    function megamenu_content_in_nav($item_output, $item, $depth, $args )
    {
        if($item->object === 'uicore-tb'){
            $atts = null;
            $settings = get_post_meta($item->object_id, 'tb_settings', true);
            if(isset($settings['width']) && $settings['width'] === 'custom'){
                $atts = 'style="--uicore-max-width:' . $settings['widthCustom'] . 'px"';
            }
            //add bdt-navbar-dropdown for bdt navbar element
            $extra_class = 'uicore-megamenu bdt-navbar-dropdown';
            $item_output .= '<ul class="sub-menu '.$extra_class.'" ' . $atts . '>'; 
            $item_output .= Common::get_elementor_content($item->object_id, true); 
            $item_output .= '</ul>'; 
        }
        return $item_output;
    }

    /**
     * Add Magamenu item class in navbar
     *
     * @param [type] $classes
     * @param [type] $item
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 2.0.0
     */
    function megamenu_item_class($classes, $item)
    {
        if($item->object === 'uicore-tb'){
            $url = get_post_meta( $item->ID, '_menu_item_url', true );
            $url = $url ? $url : '#';
            $item->url = apply_filters( "uicore_tb_megamenu_url_{$item->object_id}", $url );
            $classes[] = "menu-item-has-children";
            $classes[] = "menu-item-has-megamenu";
            $settings = get_post_meta($item->object_id, 'tb_settings', true);
            if(isset($settings['width']) && $settings['width'] === 'custom') {
                $classes[] = 'custom-width';
            }
            if(isset($settings['width']) && $settings['width'] === 'container') {
                $classes[] = 'container-width';
            }
        }
        return $classes;
    }

    /**
     * Add no index to Theme Builder custom URL
     *
     * @param string $robots
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 4.1.8
     */
    function prevent_custom_url_indexing($robots) {
        // Check if the current URL matches the custom URL
        if (isset($_GET['uicore-tb']) && isset($_GET['post-name'])) {
          // If the URL matches, add a "noindex" meta tag to the page header
          $robots .= "\n<meta name=\"robots\" content=\"noindex,follow\" />";
        }
        return $robots;
      }

    
    function inject_block($id) {
        $settings = get_post_meta($id, 'tb_settings', true);
        $hook = isset($settings['blockHook']) ? $settings['blockHook'] : ['name'=>'custom', 'value'=>'custom'];

        
        if($hook['name'] === 'custom'){
            return;
        }
        $hook = $hook['value'];
        // \error_log('injecting block with id '.$id.' on '.$hook.' hook');
        if($hook === 'uicore__mobile_menu'){
           \add_filter('uicore-mobile-menu-content', '__return_true');
        }
        add_action($hook, function() use ($id){
            echo Common::get_elementor_content($id);
        });
    }

    function define_extra_hooks(){
        add_action('uicore_before_content', function () {
            /**
             * Fires inside mobile menu content.
             *
             * @since 5.0.6
             *
             */
            do_action('uicore__before_content');
        },11);

        add_action('uicore_content_end', function () {
            /**
             * Fires inside mobile menu content.
             *
             * @since 5.0.6
             *
             */
            do_action('uicore__after_content');
            do_action('uicore__before_footer');
        },7);

        add_action('wp_footer', function () {
            /**
             * Fires inside mobile menu content.
             *
             * @since 5.0.6
             *
             */
            do_action('uicore__after_footer');
        },1000);


    }
}
Frontend::get_instance(); 