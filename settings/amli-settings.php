<?php

/**
 * WordPress settings API demo class
 *
 * @author Tareq Hasan
 */
if ( !class_exists('WeDevs_Settings' ) ):
class WeDevs_Settings {

    private $settings_api;

    function __construct() {
        $this->settings_api = new WeDevs_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_options_page( 'Webqnna Settings', 'Webqnna Settings', 'delete_posts', 'Store-Webqnna-Settings', array($this, 'plugin_page') );
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'qs_sms_settings',
                'title' => __( 'الرسائل النصية', 'wedevs' )
            ),
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            //////////////////////////////////////////////////  qs_sms_settings
            'qs_sms_settings' => array(
                
                array(
                    'name' => 'sms_provider',
                    'label' => __( 'Select Provider', 'wedevs' ),
                    'desc' => __( 'Select one provider', 'wedevs' ),
                    'type' => 'select',
                    'options' => array(
                        'vip1sms' => 'VIP1SMS',
                        'oursms' => 'OurSMS'
                    )
                ),

                array(
                    'name'    => 'hr1',
                    'label'   => __( '<hr />', 'wedevs' ),
                    'desc'    => __( '<hr />', 'wedevs' ),
                    'type'    => 'html',
                    'default' => ''
                ),

                array(
                    'name'    => 'vip1sms_username',
                    'label'   => __( 'vip1sms username', 'wedevs' ),
                    'desc'    => __( 'insert vip1sms', 'wedevs' ),
                    'type'    => 'text',
                    'default' => ''
                ),
                array(
                    'name'    => 'vip1sms_password',
                    'label'   => __( 'vip1sms Password', 'wedevs' ),
                    'desc'    => __( 'insert vip1sms Password', 'wedevs' ),
                    'type'    => 'password',
                    'default' => ''
                ),
                array(
                    'name'    => 'vip1sms_sender',
                    'label'   => __( 'vip1sms sender name', 'wedevs' ),
                    'desc'    => __( 'insert vip1sms sender name', 'wedevs' ),
                    'type'    => 'text',
                    'default' => ''
                ),


                array(
                    'name'    => 'hr2',
                    'label'   => __( '<hr />', 'wedevs' ),
                    'desc'    => __( '<hr />', 'wedevs' ),
                    'type'    => 'html',
                    'default' => ''
                ),
                
                array(
                    'name'    => 'oursms_username',
                    'label'   => __( 'Oursms Username', 'wedevs' ),
                    'desc'    => __( 'insert Username', 'wedevs' ),
                    'type'    => 'text',
                    'default' => ''
                ),
                array(
                    'name'    => 'oursms_api_token',
                    'label'   => __( 'Oursms API Token', 'wedevs' ),
                    'desc'    => __( 'insert Oursms API Token', 'wedevs' ),
                    'type'    => 'text',
                    'default' => ''
                ),
                array(
                    'name'    => 'oursms_sender',
                    'label'   => __( 'OurSMS sender name', 'wedevs' ),
                    'desc'    => __( 'insert OurSMS sender name', 'wedevs' ),
                    'type'    => 'text',
                    'default' => ''
                ),
            ),
            //////////////////////////////////////////////////
        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}
endif;
