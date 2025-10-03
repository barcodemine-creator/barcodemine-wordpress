<?php

namespace UiCoreElements;

/**
 * Admin Pages Handler
 */
class Admin
{
    /**
     * Constructor function to initialize hooks
     *
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->elementor_style();

        add_action('admin_menu', [$this, 'admin_menu']);
        add_action('admin_init', [$this, 'init_hooks']);
    }

    /**
     * Add admin menu page
     *
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 1.0.5
     */
    public function admin_menu()
    {
        // Settings page (only required if uicore framework is not active)
        // if (!\class_exists('\UiCore\Helper')) {
            $hook = add_submenu_page(
                'options-general.php',
                'UiCore Elements',
                'UiCore Elements',
                'manage_options',
                'uicore-elements',
                [$this, 'plugin_page']
            );

        // }

        // Connect handle
        // add_submenu_page(
        //     null,
        //     'UiCore Connect',
        //     'UiCore Connect',
        //     'manage_options',
        //     'uicore_connect_free',
        //     [$this, 'connect_page_callback']
        // );
    }

    /**
     * Initialize hooks for settings fields and sections
     *
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 1.0.5
     */
    public function init_hooks()
    {
        register_setting('uicore_elements_recaptcha', 'uicore_elements_recaptcha_site_key', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('uicore_elements_recaptcha', 'uicore_elements_recaptcha_secret_key', ['sanitize_callback' => 'sanitize_text_field']);
        register_setting('uicore_elements_mailchimp', 'uicore_elements_mailchimp_secret_key', ['sanitize_callback' => 'sanitize_text_field']);

        add_settings_section('uicore_elements_recaptcha_section', 'reCAPTCHA Keys', [$this, 'recaptcha_section'], 'uicore_elements_recaptcha');
        add_settings_section('uicore_elements_mailchimp_section', 'Mailchimp Key', [$this, 'mailchimp_section'], 'uicore_elements_mailchimp');

        add_settings_field('uicore_elements_recaptcha_site_key', 'Site Key', [$this, 'site_key'], 'uicore_elements_recaptcha', 'uicore_elements_recaptcha_section');
        add_settings_field('uicore_elements_recaptcha_secret_key', 'Secret Key', [$this, 'secret_key'], 'uicore_elements_recaptcha', 'uicore_elements_recaptcha_section');
        add_settings_field('uicore_elements_mailchimp_secret_key', 'API Key', [$this, 'mailchimp_key'], 'uicore_elements_mailchimp', 'uicore_elements_mailchimp_section');
    }

    /**
     * Render plugin page
     *
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 1.0.5
     */
    public function plugin_page()
    {
        // check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }


        // show error/update messages
        settings_errors('uicoreelements_messages');

        // display plugin page
        ?>
        <div class="wrap">
            <h1>UiCore Elements Settings</h1>

            <form method="post" action="options.php" style="margin-top:40px">
                <?php
                settings_fields('uicore_elements_recaptcha');
                do_settings_sections('uicore_elements_recaptcha');
                submit_button();
                ?>
            </form>

            <form method="post" action="options.php" style="margin-top:40px">
                <?php
                settings_fields('uicore_elements_mailchimp');
                do_settings_sections('uicore_elements_mailchimp');
                submit_button();
                ?>
            </form>

        </div>
        <?php
    }

    /**
     * Render reCAPTCHA section description
     *
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 1.0.5
     */
    public function recaptcha_section()
    {
        echo '<p class="description">Go to your Google <a href="https://www.google.com/recaptcha/admin/create" target="_blank">reCAPTCHA</a>, choose between V2 or V3 versions and create your API keys</p>';
    }
     /**
     * Render Mailchimp section description
     *
     * @return void
     * @author Lucas Marini Falbo <lucas@uicore.co>
     * @since 1.0.7
     */
    public function mailchimp_section()
    {
        echo "<p class='description'>If you don't have one yet, go to your <a href='https://admin.mailchimp.com/account/api/' target='_blank'>Mailchimp Dashboard</a> and create a new API key</p>";
    }

    /**
     * Render reCAPTCHA Site Key field
     *
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 1.0.5
     */
    public function site_key()
    {
        $site_key = get_option('uicore_elements_recaptcha_site_key');
        echo '<input type="text" name="uicore_elements_recaptcha_site_key" value="' . esc_attr($site_key) . '" class="regular-text" />';
    }

    /**
     * Render reCAPTCHA Secret Key field
     *
     * @return void
     * @author Andrei Voica <andrei@uicore.co>
     * @since 1.0.5
     */
    public function secret_key()
    {
        $secret_key = get_option('uicore_elements_recaptcha_secret_key');
        echo '<input type="text" name="uicore_elements_recaptcha_secret_key" value="' . esc_attr($secret_key) . '" class="regular-text" />';
    }

    /**
     * Render Mailchimp API Key
     *
     * @return void
     * @author Lucas Marini Falbo <lucas@uicore.co>
     * @since 1.0.5
     */
    public function mailchimp_key()
    {
        $secret_key = get_option('uicore_elements_mailchimp_secret_key');
        echo '<input type="text" name="uicore_elements_mailchimp_secret_key" value="' . esc_attr($secret_key) . '" class="regular-text" />';
    }


    /**
     * Elementor Editor Style, Fonts and Scripts
     *
     * @return void
     * @author Andrei Voica <andrei@uicore.co
     * @since 1.0.0
     */
    public function elementor_style()
    {
        add_action('elementor/editor/before_enqueue_scripts', function () {

            echo '<style id="uicore-csss" >
            .elementor-element .icon .ui-e-widget:after {
              height: 28px;
              width: 28px;
              margin-right: 10px;
              border-radius: 3px;
              background-color: #532df5;
              background-image: url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'16\' viewBox=\'0 0 16 16\' xml:space=\'preserve\'%3E%3Cpath d=\'M5.383 15.217c3.1 0 5.4-2.3 5.4-5.3v-7.9h-2.9v7.9c0 1.4-1.1 2.5-2.5 2.5s-2.5-1.1-2.5-2.5v-7.9h-2.9v7.9c0 3 2.3 5.3 5.4 5.3zM14.283 4.117c1 0 1.7-.7 1.7-1.7s-.7-1.7-1.7-1.7-1.7.7-1.7 1.7.7 1.7 1.7 1.7zM15.683 15.017v-9.6h-2.8v9.6z\' fill=\'%23fff\'/%3E%3C/svg%3E");
              background-size: 16px;
              background-position: center;
              background-repeat: no-repeat;
            }

            .elementor-element .icon .ui-e-widget:after {
				content: "";
			    position: absolute;
			    right: 5px;
			    top: 5px;
			    margin-right: 0;
			    width: 16px;
			    height: 16px;
			    background-size: 9px;
			    background-color: #656c7196;
				transition: background-color .3s ease-in-out;
            }
			.elementor-element:hover .icon .ui-e-widget:after {
				background-color: #532df5;
				transition: background-color .3s ease-in-out;
			}
            #elementor-panel-categories{
                display: flex;
                flex-direction: column;
            }
            #elementor-panel-category-basic,
            #elementor-panel-category-layout,
            #elementor-panel-category-favorites,
            #elementor-panel-category-uicore{
                order: -1;
            }
            </style>';
        });
    }
}
