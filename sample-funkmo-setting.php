<?php
/**
 * @link              http://astahdziq.in/
 * @since             1.0.0
 * @package           Funktube
 *
 * @wordpress-plugin
 * Plugin Name:       Sample Funkmo Setting
 */

require_once('class.funkmo-settings.php');

if ( !class_exists('SampleFunkmoSettings' ) ):
class SampleFunkmoSettings {

    private $settings_api;

    public $plugin_name;
    
    function __construct() {
        $this->plugin_name = 'testSetting';
        $this->settings_api = new  FunkmoSettings($this->plugin_name);
        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }
    function admin_init() {
        //set the settings
        $this->settings_api->set_style( true );
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );
        //initialize settings
        $this->settings_api->admin_init();
    }
    function admin_menu() {
        add_options_page( 'Options Page', 'Options Page', 'delete_posts', $this->plugin_name, array($this, 'plugin_page') );
    }
    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'funkopt_basics',
                'title' => __( 'Basic Settings', $this->plugin_name ),
                'desc' => __( 'Ini Basic Settings', $this->plugin_name ),
            ),
            array(
                'id' => 'funkopt_advanced',
                'title' => __( 'Advanced Settings', $this->plugin_name ),
                'desc' => __( 'Ini Advanced Settings', $this->plugin_name ),
            ),
            array(
                'id' => 'funkopt_others',
                'title' => __( 'Other Settings', 'wpuf' )
            )
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
            'funkopt_basics' => array(
                array(
                    'name'              => 'text_val',
                    'label'             => __( 'Text Input', $this->plugin_name ),
                    'desc'              => __( 'Text input description', $this->plugin_name ),
                    'type'              => 'text',
                    'default'           => 'Title',
                    'sanitize_callback' => 'intval'
                ),
                array(
                    'name'              => 'number_input',
                    'label'             => __( 'Number Input', $this->plugin_name ),
                    'desc'              => __( 'Number field with validation callback `intval`', $this->plugin_name ),
                    'type'              => 'number',
                    'default'           => 'Title',
                    'sanitize_callback' => 'intval'
                ),
                array(
                    'name'  => 'textarea',
                    'label' => __( 'Textarea Input', $this->plugin_name ),
                    'desc'  => __( 'Textarea description', $this->plugin_name ),
                    'type'  => 'textarea'
                ),
                array(
                    'name'  => 'checkbox',
                    'label' => __( 'Checkbox', $this->plugin_name ),
                    'desc'  => __( 'Checkbox Label', $this->plugin_name ),
                    'type'  => 'checkbox'
                ),
                array(
                    'name'    => 'radio',
                    'label'   => __( 'Radio Button', $this->plugin_name ),
                    'desc'    => __( 'A radio button', $this->plugin_name ),
                    'type'    => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    )
                ),
                array(
                    'name'    => 'multicheck',
                    'label'   => __( 'Multile checkbox', $this->plugin_name ),
                    'desc'    => __( 'Multi checkbox description', $this->plugin_name ),
                    'type'    => 'multicheck',
                    'options' => array(
                        'one'   => 'One',
                        'two'   => 'Two',
                        'three' => 'Three',
                        'four'  => 'Four'
                    )
                ),
                array(
                    'name'    => 'selectbox',
                    'label'   => __( 'A Dropdown', $this->plugin_name ),
                    'desc'    => __( 'Dropdown description', $this->plugin_name ),
                    'type'    => 'select',
                    'default' => 'no',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    )
                ),
                array(
                    'name'    => 'password',
                    'label'   => __( 'Password', $this->plugin_name ),
                    'desc'    => __( 'Password description', $this->plugin_name ),
                    'type'    => 'password',
                    'default' => ''
                ),
                array(
                    'name'    => 'file',
                    'label'   => __( 'File', $this->plugin_name ),
                    'desc'    => __( 'File description', $this->plugin_name ),
                    'type'    => 'file',
                    'default' => '',
                    'options' => array(
                        'button_label' => 'Choose Image'
                    )
                )
            ),
            'funkopt_advanced' => array(
                array(
                    'name'    => 'color',
                    'label'   => __( 'Color', $this->plugin_name ),
                    'desc'    => __( 'Color description', $this->plugin_name ),
                    'type'    => 'color',
                    'default' => ''
                ),
                array(
                    'name'    => 'password',
                    'label'   => __( 'Password', $this->plugin_name ),
                    'desc'    => __( 'Password description', $this->plugin_name ),
                    'type'    => 'password',
                    'default' => ''
                ),
                array(
                    'name'    => 'wysiwyg',
                    'label'   => __( 'Advanced Editor', $this->plugin_name ),
                    'desc'    => __( 'WP_Editor description', $this->plugin_name ),
                    'type'    => 'wysiwyg',
                    'default' => ''
                ),
                array(
                    'name'    => 'multicheck',
                    'label'   => __( 'Multile checkbox', $this->plugin_name ),
                    'desc'    => __( 'Multi checkbox description', $this->plugin_name ),
                    'type'    => 'multicheck',
                    'default' => array('one' => 'one', 'four' => 'four'),
                    'options' => array(
                        'one'   => 'One',
                        'two'   => 'Two',
                        'three' => 'Three',
                        'four'  => 'Four'
                    )
                ),
                array(
                    'name'    => 'selectbox',
                    'label'   => __( 'A Dropdown', $this->plugin_name ),
                    'desc'    => __( 'Dropdown description', $this->plugin_name ),
                    'type'    => 'select',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    )
                ),
                array(
                    'name'    => 'password',
                    'label'   => __( 'Password', $this->plugin_name ),
                    'desc'    => __( 'Password description', $this->plugin_name ),
                    'type'    => 'password',
                    'default' => ''
                ),
                array(
                    'name'    => 'file',
                    'label'   => __( 'File', $this->plugin_name ),
                    'desc'    => __( 'File description', $this->plugin_name ),
                    'type'    => 'file',
                    'default' => ''
                )
            ),
            'funkopt_others' => array(
                array(
                    'name'    => 'text',
                    'label'   => __( 'Text Input', $this->plugin_name ),
                    'desc'    => __( 'Text input description', $this->plugin_name ),
                    'type'    => 'text',
                    'default' => 'Title'
                ),
                array(
                    'name'  => 'textarea',
                    'label' => __( 'Textarea Input', $this->plugin_name ),
                    'desc'  => __( 'Textarea description', $this->plugin_name ),
                    'type'  => 'textarea'
                ),
                array(
                    'name'  => 'checkbox',
                    'label' => __( 'Checkbox', $this->plugin_name ),
                    'desc'  => __( 'Checkbox Label', $this->plugin_name ),
                    'type'  => 'checkbox'
                ),
                array(
                    'name'    => 'radio',
                    'label'   => __( 'Radio Button', $this->plugin_name ),
                    'desc'    => __( 'A radio button', $this->plugin_name ),
                    'type'    => 'radio',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    )
                ),
                array(
                    'name'    => 'multicheck',
                    'label'   => __( 'Multile checkbox', $this->plugin_name ),
                    'desc'    => __( 'Multi checkbox description', $this->plugin_name ),
                    'type'    => 'multicheck',
                    'options' => array(
                        'one'   => 'One',
                        'two'   => 'Two',
                        'three' => 'Three',
                        'four'  => 'Four'
                    )
                ),
                array(
                    'name'    => 'selectbox',
                    'label'   => __( 'A Dropdown', $this->plugin_name ),
                    'desc'    => __( 'Dropdown description', $this->plugin_name ),
                    'type'    => 'select',
                    'options' => array(
                        'yes' => 'Yes',
                        'no'  => 'No'
                    )
                ),
                array(
                    'name'    => 'password',
                    'label'   => __( 'Password', $this->plugin_name ),
                    'desc'    => __( 'Password description', $this->plugin_name ),
                    'type'    => 'password',
                    'default' => ''
                ),
                array(
                    'name'    => 'file',
                    'label'   => __( 'File', $this->plugin_name ),
                    'desc'    => __( 'File description', $this->plugin_name ),
                    'type'    => 'file',
                    'default' => ''
                )
            )
        );
        return $settings_fields;
    }
    function plugin_page() {
        echo '<div class="wrap">';
        $this->settings_api->show_element();
        echo '</div>';
        // require 'test.php';
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

new SampleFunkmoSettings();
endif;