<?php
/**
 * Option page wrapper class
 * lafif@astahdziq.in
 */
if ( !class_exists( ' FunkmoSettings' ) ):
class  FunkmoSettings {

    private $plugin_name;

    private $style = false;

    private $section_id;

    /**
     * settings sections array
     *
     * @var array
     */
    private $settings_sections = array();
    /**
     * Settings fields array
     *
     * @var array
     */
    private $settings_fields = array();
    /**
     * Singleton instance
     *
     * @var object
     */
    private static $_instance;

    public function __construct($plugin_name) {

        $this->plugin_name = $plugin_name;
        $this->section_id = $plugin_name;
        // $this->style = true;
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }
    /**
     * Set settings sections
     *
     * @param array   $sections setting sections array
     */
    function set_style( $style ) {
        $this->style = $style;
    }
    /**
     * Enqueue scripts and styles
     */
    function admin_enqueue_scripts() {
        wp_enqueue_style( 'wp-color-picker' );

        if($this->style)
            wp_enqueue_style( 'funkmo-options', plugins_url('assets/css/funkmo-options.css', __FILE__) );

        wp_enqueue_script( 'admin', plugins_url( 'assets/admin.js', __FILE__ ), array('jquery'), '', true );

        wp_enqueue_media();
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'jquery' );
    }
    /**
     * Set settings sections
     *
     * @param array   $sections setting sections array
     */
    function set_sections( $sections ) {
        // allow manipulate from filter
        $this->settings_sections = apply_filters( $this->plugin_name.'_tabs', $sections );
        return $this;
    }
    /**
     * Add a single section
     *
     * @param array   $section
     */
    function add_section( $section ) {
        $this->settings_sections[] = $section;
        return $this;
    }
    /**
     * Set settings fields
     *
     * @param array   $fields settings fields array
     */
    function set_fields( $fields ) {
        if(is_array($fields)){
            foreach ($fields as $id => $opts) {
                // allow manipulate from filter
                $this->settings_fields[$id] = apply_filters( $this->plugin_name.'_tab_'.$id, $opts );
            }
        }
        return $this;
    }
    function add_field( $section, $field ) {
        $defaults = array(
            'name' => '',
            'label' => '',
            'desc' => '',
            'type' => 'text'
        );
        $arg = wp_parse_args( $field, $defaults );
        $this->settings_fields[$section][] = $arg;
        return $this;
    }
    /**
     * Initialize and registers the settings sections and fileds to WordPress
     *
     * Usually this should be called at `admin_init` hook.
     *
     * This function gets the initiated settings sections and fields. Then
     * registers them to WordPress and ready for use.
     */
    function admin_init() {
        //register settings sections
        // foreach ( $this->settings_sections as $section ) {
        //     if ( false == get_option( $this->section_id ) ) {
        //         add_option( $this->section_id );
        //     }
        //     if ( isset($section['desc']) && !empty($section['desc']) ) {
        //         $section['desc'] = '<div class="inside">'.$section['desc'].'</div>';
        //         $callback = create_function('', 'echo "'.str_replace('"', '\"', $section['desc']).'";');
        //     } else if ( isset( $section['callback'] ) ) {
        //         $callback = $section['callback'];
        //     } else {
        //         $callback = null;
        //     }
        
        if ( false == get_option( $this->section_id ) ) {
            add_option( $this->section_id );
        }
        add_settings_section( $this->section_id, NULL, NULL , $this->section_id );
        // }
        //register settings fields
        foreach ( $this->settings_fields as $section => $field ) {
            foreach ( $field as $option ) {
                $type = isset( $option['type'] ) ? $option['type'] : 'text';
                $args = array(
                    'id' => $option['name'],
                    'label_for' => $option['name'],
                    'desc' => isset( $option['desc'] ) ? $option['desc'] : '',
                    'name' => $option['label'],
                    'section' => $this->section_id,
                    'group' => $section,
                    'size' => isset( $option['size'] ) ? $option['size'] : null,
                    'options' => isset( $option['options'] ) ? $option['options'] : '',
                    'std' => isset( $option['default'] ) ? $option['default'] : '',
                    'sanitize_callback' => isset( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : '',
                    'type' => $type,
                );
                add_settings_field( $this->section_id . '[' . $option['name'] . ']', $option['label'], array( $this, 'get_callback_field' ), $this->section_id, $this->section_id, $args );
            }
        }
        // creates our settings in the options table
        // foreach ( $this->settings_sections as $section ) {
            register_setting( $this->section_id, $this->section_id, array( $this, 'sanitize_options' ) );
        // }
    }
    /**
     * Get field description for display
     *
     * @param array   $args settings field args
     */
    public function get_field_description( $args ) {
        if ( ! empty( $args['desc'] ) ) {
            $desc = sprintf( '<p class="description">%s</p>', $args['desc'] );
        } else {
            $desc = '';
        }
        return $desc;
    }

    /**
     * Global field callback
     * @param array   $args settings field args
     */
    function get_callback_field( $args ){

        switch ($args['type']) {

            case 'multicheck':
                $value = $this->get_option( $args['id'], $args['section'], $args['std'] );
                $html = '<fieldset>';
                foreach ( $args['options'] as $key => $label ) {
                    $checked = isset( $value[$key] ) ? $value[$key] : '0';
                    // $html .= sprintf( '<label for="funk-%1$s[%2$s][%3$s]">', $args['section'], $args['id'], $key );
                    // $html .= sprintf( '<input type="checkbox" class="checkbox" id="funk-%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $checked, $key, false ) );
                    // $html .= sprintf( '%1$s</label><br>',  $label );
                    $html .= '<div class="option-container">';
                    $html .= sprintf( '<label for="funk-%1$s[%2$s][%3$s]" class="option">', $args['section'], $args['id'], $key );
                    $html .= sprintf( '<input type="checkbox" id="funk-%1$s[%2$s][%3$s]" name="%1$s[%2$s][%3$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $checked, $key, false ) );
                    $html .= '<span class="checkbox"></span>';
                    $html .= '</label>';
                    $html .= sprintf( '<span class="desc-label">%1$s</span>',  $label );
                    $html .= '</div>';
                }
                $html .= $this->get_field_description( $args );
                $html .= '</fieldset>';

                break;

            case 'checkbox':
                $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
                $html = '<fieldset>';
                // $html .= sprintf( '<label for="funk-%1$s[%2$s]">', $args['section'], $args['id'] );
                // $html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="off" />', $args['section'], $args['id'] );
                // $html .= sprintf( '<input type="checkbox" class="checkbox" id="funk-%1$s[%2$s]" name="%1$s[%2$s]" value="on" %3$s />', $args['section'], $args['id'], checked( $value, 'on', false ) );
                // $html .= sprintf( '%1$s</label>', $args['desc'] );
                $html .= '<div class="option-container">';
                $html .= sprintf( '<label for="funk-%1$s[%2$s]" class="option">', $args['section'], $args['id'] );
                $html .= sprintf( '<input type="checkbox" name="%1$s[%2$s]" value="on" %3$s>', $args['section'], $args['id'], checked( $value, 'on', false ) );
                $html .= '<span class="checkbox"></span>';
                $html .= '</label>';
                $html .= sprintf( '<span class="desc-label">%1$s</span>', $args['desc'] );
                $html .= '</div>';
                $html .= '</fieldset>';

                break;

            case 'radio':
                $value = $this->get_option( $args['id'], $args['section'], $args['std'] );
                $html = '<fieldset>';
                foreach ( $args['options'] as $key => $label ) {
                    // $html .= sprintf( '<label for="funk-%1$s[%2$s][%3$s]">',  $args['section'], $args['id'], $key );
                    // $html .= sprintf( '<input type="radio" class="radio" id="funk-%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ) );
                    // $html .= sprintf( '%1$s</label><br>', $label );
                    $html .= '<div class="option-container">';
                    $html .= sprintf( '<label for="funk-%1$s[%2$s][%3$s]" class="option">',  $args['section'], $args['id'], $key );
                    $html .= sprintf( '<input type="radio" id="funk-%1$s[%2$s][%3$s]" name="%1$s[%2$s]" value="%3$s" %4$s />', $args['section'], $args['id'], $key, checked( $value, $key, false ) );
                    $html .= '<span class="radio"></span>';
                    $html .= '</label>';
                    $html .= sprintf( '<span class="desc-label">%1$s</span><br>', $label );
                    $html .= '</div>';
                }
                $html .= $this->get_field_description( $args );
                $html .= '</fieldset>';

                break;

            case 'select':
                $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
                $size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
                $html = '<div class="select">';
                $html .= sprintf( '<select class="%1$s" name="%2$s[%3$s]" id="%3$s">', $size, $args['section'], $args['id'] );
                foreach ( $args['options'] as $key => $label ) {
                    $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $value, $key, false ), $label );
                }
                $html .= sprintf( '</select>' );
                $html .= sprintf( '</div>' );
                $html .= $this->get_field_description( $args );

                break;

            case 'textarea':
                $value = esc_textarea( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
                $size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
                $html = sprintf( '<textarea rows="5" cols="55" class="%1$s-text" id="%3$s" name="%2$s[%3$s]">%4$s</textarea>', $size, $args['section'], $args['id'], $value );
                $html .= $this->get_field_description( $args );

                break;

            case 'wysiwyg':
                $value = $this->get_option( $args['id'], $args['section'], $args['std'] );
                $size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : '500px';
                $html = '<div style="max-width: ' . $size . ';">';
                $editor_settings = array(
                    'teeny' => true,
                    'textarea_name' => $args['section'] . '[' . $args['id'] . ']',
                    'textarea_rows' => 10
                );
                if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
                    $editor_settings = array_merge( $editor_settings, $args['options'] );
                }
                wp_editor( $value, $args['section'] . '-' . $args['id'], $editor_settings );
                $html .= '</div>';
                $html .= $this->get_field_description( $args );

                break;

            case 'html':
                $html = $this->get_field_description( $args );
                break;

            case 'file':
                $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
                $size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
                $id = $args['section']  . '[' . $args['id'] . ']';
                $label = isset( $args['options']['button_label'] ) ?
                                $args['options']['button_label'] :
                                __( 'Choose File' );
                $html  = sprintf( '<input type="text" class="%1$s-text funkopt-url" id="%3$s" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
                $html .= '<input type="button" class="button funkopt-browse" value="' . $label . '" />';
                $html .= $this->get_field_description( $args );

                break;

            case 'password':
                $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
                $size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
                $html = sprintf( '<input type="password" class="%1$s-text" id="%3$s" name="%2$s[%3$s]" value="%4$s"/>', $size, $args['section'], $args['id'], $value );
                $html .= $this->get_field_description( $args );

                break;

            case 'color':
                $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
                $size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
                $html = sprintf( '<input type="text" class="%1$s-text wp-color-picker-field" id="%3$s" name="%2$s[%3$s]" value="%4$s" data-default-color="%5$s" />', $size, $args['section'], $args['id'], $value, $args['std'] );
                $html .= $this->get_field_description( $args );

                break;

            case 'variable':
                # code...
                break;
            
            default:
                $value = esc_attr( $this->get_option( $args['id'], $args['section'], $args['std'] ) );
                $size = isset( $args['size'] ) && !is_null( $args['size'] ) ? $args['size'] : 'regular';
                $type = isset( $args['type'] ) ? $args['type'] : 'text';
                $html = sprintf( '<input type="%1$s" class="%2$s-text" id="%4$s" name="%3$s[%4$s]" value="%5$s"/>', $type, $size, $args['section'], $args['id'], $value );
                $html .= $this->get_field_description( $args );

                break;
        }

        echo apply_filters( $this->plugin_name . '_callback_field', $html, $args);
    }

    /**
     * Sanitize callback for Settings API
     */
    function sanitize_options( $options ) {
        foreach( $options as $option_slug => $option_value ) {
            $sanitize_callback = $this->get_sanitize_callback( $option_slug );
            // If callback is set, call it
            if ( $sanitize_callback ) {
                $options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
                continue;
            }
        }
        return $options;
    }
    /**
     * Get sanitization callback for given option slug
     *
     * @param string $slug option slug
     *
     * @return mixed string or bool false
     */
    function get_sanitize_callback( $slug = '' ) {
        if ( empty( $slug ) ) {
            return false;
        }
        // Iterate over registered fields and see if we can find proper callback
        foreach( $this->settings_fields as $section => $options ) {
            foreach ( $options as $option ) {
                if ( $option['name'] != $slug ) {
                    continue;
                }
                // Return the callback name
                return isset( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
            }
        }
        return false;
    }

    function section_callback(){
        echo "string stringstringstring string";
    }

    function funkmo_do_settings_sections($page) {
        global $wp_settings_sections, $wp_settings_fields;

        if ( !isset($wp_settings_sections) || !isset($wp_settings_sections[$page]) )
            return;

        foreach( (array) $wp_settings_sections[$page] as $section ) {
            echo "<div class='funkopt-section-info'>";
            echo "<div class='funkopt-section-title'></div>";
            echo '<div class="funkopt-section-desc"></div>';
            echo '<div class="clear"></div>';
            echo '</div>';
            if ( !isset($wp_settings_fields) ||
                 !isset($wp_settings_fields[$page]) ||
                 !isset($wp_settings_fields[$page][$section['id']]) )
                    continue;
            echo '<div class="settings-form-wrapper">';
            $this->funkmo_do_settings_fields($page, $section['id']);
            echo '</div>';
        }
    }

    function funkmo_do_settings_fields($page, $section) {
        // global $wp_settings_fields;

        // if ( !isset($wp_settings_fields) ||
        //      !isset($wp_settings_fields[$page]) ||
        //      !isset($wp_settings_fields[$page][$section]) )
        //     return;

        // foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
        //     echo '<div class="settings-form-row '.$field['args']['group'].'">';
        //     if ( !empty($field['args']['label_for']) )
        //         echo '<p><label for="' . $field['args']['label_for'] . '">' .
        //             $field['title'] . '</label><br />';
        //     else
        //         echo '<p>' . $field['title'] . '<br />';
        //     call_user_func($field['callback'], $field['args']);
        //     echo '</p></div>';
        // }
        
        global $wp_settings_fields;
 
        if ( ! isset( $wp_settings_fields[$page][$section] ) )
            return;
    
        echo '<table class="form-table">';

        foreach ( (array) $wp_settings_fields[$page][$section] as $field ) {
            // $class = '';
     
            // if ( ! empty( $field['args']['class'] ) ) {
            //     $class = ' class="' . esc_attr( $field['args']['class'] ) . '"';
            // }
     
            echo '<tr class="settings-form-row '.$field['args']['group'].'">';
     
            if ( ! empty( $field['args']['label_for'] ) ) {
                echo '<th scope="row"><label for="' . esc_attr( $field['args']['label_for'] ) . '">' . $field['title'] . '</label></th>';
            } else {
                echo '<th scope="row">' . $field['title'] . '</th>';
            }
     
            echo '<td>';
            call_user_func($field['callback'], $field['args']);
            echo '</td>';
            echo '</tr>';
        }

        echo '</table>';
    }

    /**
     * Get the value of a settings field
     *
     * @param string  $option  settings field name
     * @param string  $section the section name this field belongs to
     * @param string  $default default text if it's not found
     * @return string
     */
    function get_option( $option, $section, $default = '' ) {
        $options = get_option( $section );
        if ( isset( $options[$option] ) ) {
            return $options[$option];
        }
        return $default;
    }

    function show_header(){

        do_action( $this->plugin_name . '_before_header' );
        
        $html = '<div class="funkopt-header">';
        $html .= '<div class="logo">Logo</div>';
        $html .= '<div class="search"><input type="text" id="funkopt-search-input" placeholder="Search..."></div>';
        $html .= '<div class="clear"></div>';
        $html .= '</div>';

        echo $html;

        do_action( $this->plugin_name . '_after_header' );
    }

    /**
     * Show navigations as tab
     *
     * Shows all the settings section labels as tab
     */
    function show_navigation() {

        do_action( $this->plugin_name . '_before_tabs' );

        $html = '<div class="funkopt-tabs nav-tab-wrapper">';
        foreach ( $this->settings_sections as $tab ) {
            $desc = (!empty($tab['desc'])) ? $tab['desc'] : '';
            $html .= sprintf( '<a href="#%1$s" class="nav-tab" id="%1$s-tab" data-group="%1$s" data-desc="%3$s">%2$s</a>', $tab['id'], $tab['title'], $desc );
        }
        $html .= '</div>';
        echo $html;

        do_action( $this->plugin_name . '_after_tabs' );
    }
    /**
     * Show the section settings forms
     *
     * This function displays every sections in a different form
     */
    function show_forms() {

        do_action( $this->plugin_name . '_before_options_form' );

        ?>
        <div class="funkopt-content">
            <form method="post" action="options.php">
                <?php
                // do_action( 'funkopt_form_top_' . $form['id'], $form );
                settings_fields( $this->section_id );
                $this->funkmo_do_settings_sections( $this->section_id );
                // do_action( 'funkopt_form_bottom_' . $form['id'], $form );
                
                submit_button();
                ?>
            </form>
        </div>
        <div class="clear"></div>
        <?php

        do_action( $this->plugin_name . '_after_options_form' );

        $this->script();
    }
    /**
     * Tabbable JavaScript codes & Initiate Color Picker
     *
     * This code uses localstorage for displaying active tabs
     */
    function script() {
        ?>

        <style type="text/css">
            .funkopt-container .is-hidden {display: none; }
            .funkopt-content {
                background-color: #fff;
                padding: 10px;
                margin-top: 10px;
                border: solid 1px #d5d5d5;
            }
            .funkopt-content .funkopt-section-info {
                background-color: #F5F5F5;
                margin: -10px;
                margin-bottom: 0px;
                padding: 10px;
            }
            .funkopt-content .funkopt-section-info .funkopt-section-title {
                font-size: 16px;
                font-weight: bold;
                float: left;
            }
            .funkopt-content .funkopt-section-info .funkopt-section-desc {
                float: right;
                font-size: 14px;
                font-weight: normal;
                font-style: italic;
            }
            .funkopt-content p.submit {
                margin: 20px -10px -10px -10px;
                padding: 10px;
                background-color: #F5F5F5;
                text-align: right;
                border-top: solid 1px #d5d5d5;
            }
            .funkopt-tabs { border-bottom: 1px solid #ccc; padding-bottom: 0; padding-left: 10px; }
            .funkopt-tabs .nav-tab { padding: 6px 10px; font-weight: 700; font-size: 15px; line-height: 24px; }
        </style>
        <?php
    }

    /**
     * Show navigations and content
     * @return [type] [description]
     */
    function show_element(){

        do_action( $this->plugin_name . '_before_options_page' );

        echo '<div id="'.$this->section_id.'" class="funkopt-container">';

        if($this->style)
            $this->show_header();
        
        $this->show_navigation();
        $this->show_forms();
        
        echo '</div>';

        do_action( $this->plugin_name . '_after_options_page' );
    }
}
endif;