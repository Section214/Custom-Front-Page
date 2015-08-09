<?php
/**
 * Register settings
 *
 * @package     Custom_Front_Page\Admin\Settings\Register
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Retrieve the settings tabs
 *
 * @since       1.0.0
 * @return      array $tabs The registered settings tabs
 */
function custom_front_page_get_settings_tabs() {
    $settings = custom_front_page_get_registered_settings();

    $tabs               = array();
    $tabs['login-status-pages'] = __( 'Login Status Pages', 'custom-front-page' );
    
    return apply_filters( 'custom_front_page_settings_tabs', $tabs );
}


/**
 * Retrieve the array of plugin settings
 *
 * @since       1.0.0
 * @return      array $custom_front_page_settings The registered settings
 */
function custom_front_page_get_registered_settings() {
    $custom_front_page_settings = array(
        // Page Settings
        'login-status-pages' => apply_filters( 'custom_front_page_settings_login_status_pages', array(
            array(
                'id'        => 'login_status_pages_header',
                'name'      => __( 'Page Settings', 'custom-front-page' ),
                'desc'      => '',
                'type'      => 'header'
            ),
            array(
                'id'        => 'logged_in_front_page',
                'name'      => __( 'Logged-In Front Page', 'custom-front-page' ),
                'desc'      => '',
                'type'      => 'pages'
            ),
            array(
                'id'        => 'logged_out_front_page',
                'name'      => __( 'Logged-Out Front Page', 'custom-front-page' ),
                'desc'      => '',
                'type'      => 'pages'
            )
        ) )
    );

    return apply_filters( 'custom_front_page_registered_settings', $custom_front_page_settings );
}


/**
 * Retrieve an option
 *
 * @since       1.0.0
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      mixed
 */
function custom_front_page_get_option( $key = '', $default = false ) {
    global $custom_front_page_options;

    $value = ! empty( $custom_front_page_options[$key] ) ? $custom_front_page_options[$key] : $default;
    $value = apply_filters( 'custom_front_page_get_option', $value, $key, $default );

    return apply_filters( 'custom_front_page_get_option_' . $key, $value, $key, $default );
}


/**
 * Retrieve all options
 *
 * @since       1.0.0
 * @return      array $custom_front_page_options The Custom_Front_Page options
 */
function custom_front_page_get_settings() {
    $custom_front_page_settings = get_option( 'custom_front_page_settings' );

    if( empty( $custom_front_page_settings ) ) {
        $custom_front_page_settings = array();

        update_option( 'custom_front_page_settings', $custom_front_page_settings );
    }

    return apply_filters( 'custom_front_page_get_settings', $custom_front_page_settings );
}


/**
 * Add settings sections and fields
 *
 * @since       1.0.0
 * @return      void
 */
function custom_front_page_register_settings() {
    if( get_option( 'custom_front_page_settings' ) == false ) {
        add_option( 'custom_front_page_settings' );
    }

    foreach( custom_front_page_get_registered_settings() as $tab => $settings ) {
        add_settings_section(
            'custom_front_page_settings_' . $tab,
            __return_null(),
            '__return_false',
            'custom_front_page_settings_' . $tab
        );

        foreach( $settings as $option ) {
            $name = isset( $option['name'] ) ? $option['name'] : '';

            add_settings_field(
                'custom_front_page_settings[' . $option['id'] . ']',
                $name,
                function_exists( 'custom_front_page_' . $option['type'] . '_callback' ) ? 'custom_front_page_' . $option['type'] . '_callback' : 'custom_front_page_missing_callback',
                'custom_front_page_settings_' . $tab,
                'custom_front_page_settings_' . $tab,
                array(
                    'section'       => $tab,
                    'id'            => isset( $option['id'] )           ? $option['id']             : null,
                    'desc'          => ! empty( $option['desc'] )       ? $option['desc']           : '',
                    'name'          => isset( $option['name'] )         ? $option['name']           : null,
                    'size'          => isset( $option['size'] )         ? $option['size']           : null,
                    'options'       => isset( $option['options'] )      ? $option['options']        : '',
                    'std'           => isset( $option['std'] )          ? $option['std']            : '',
                    'min'           => isset( $option['min'] )          ? $option['min']            : null,
                    'max'           => isset( $option['max'] )          ? $option['max']            : null,
                    'step'          => isset( $option['step'] )         ? $option['step']           : null,
                    'placeholder'   => isset( $option['placeholder'] )  ? $option['placeholder']    : null,
                    'rows'          => isset( $option['rows'] )         ? $option['rows']           : null,
                    'buttons'       => isset( $option['buttons'] )      ? $option['buttons']        : null,
                    'wpautop'       => isset( $option['wpautop'] )      ? $option['wpautop']        : null,
                    'teeny'         => isset( $option['teeny'] )        ? $option['teeny']          : null,
                    'notice'        => isset( $option['notice'] )       ? $option['notice']         : false,
                    'style'         => isset( $option['style'] )        ? $option['style']          : null,
                    'header'        => isset( $option['header'] )       ? $option['header']         : null,
                    'icon'          => isset( $option['icon'] )         ? $option['icon']           : null,
                    'class'         => isset( $option['class'] )        ? $option['class']          : null
                )
            );
        }
    }

    register_setting( 'custom_front_page_settings', 'custom_front_page_settings', 'custom_front_page_settings_sanitize' );
}
add_action( 'admin_init', 'custom_front_page_register_settings' );


/**
 * Settings sanitization
 *
 * @since       1.0.0
 * @param       array $input The value entered in the field
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      string $input The sanitized value
 */
function custom_front_page_settings_sanitize( $input = array() ) {
    global $custom_front_page_options;

    if( empty( $_POST['_wp_http_referer'] ) ) {
        return $input;
    }
    
    parse_str( $_POST['_wp_http_referer'], $referrer );

    $settings   = custom_front_page_get_registered_settings();
    $tab        = isset( $referrer['tab'] ) ? $referrer['tab'] : 'settings';

    $input = $input ? $input : array();
    $input = apply_filters( 'custom_front_page_settings_' . $tab . '_sanitize', $input );

    foreach( $input as $key => $value ) {
        $type = isset( $settings[$tab][$key]['type'] ) ? $settings[$tab][$key]['type'] : false;

        if( $type ) {
            // Field type specific filter
            $input[$key] = apply_filters( 'custom_front_page_settings_sanitize_' . $type, $value, $key );
        }

        // General filter
        $input[$key] = apply_filters( 'custom_front_page_settings_sanitize', $input[$key], $key );
    }

    if( ! empty( $settings[$tab] ) ) {
        foreach( $settings[$tab] as $key => $value ) {
            if( is_numeric( $key ) ) {
                $key = $value['id'];
            }

            if( empty( $input[$key] ) || ! isset( $input[$key] ) ) {
                unset( $custom_front_page_options[$key] );
            }
        }
    }

    // Merge our new settings with the existing
    $input = array_merge( $custom_front_page_options, $input );

    add_settings_error( 'custom_front_page-notices', '', __( 'Settings updated.', 'custom-front-page' ), 'updated' );

    return $input;
}


/**
 * Sanitize text fields
 *
 * @since       1.0.0
 * @param       array $input The value entered in the field
 * @return      string $input The sanitized value
 */
function custom_front_page_sanitize_text_field( $input ) {
    return trim( $input );
}
add_filter( 'custom_front_page_settings_sanitize_text', 'custom_front_page_sanitize_text_field' );


/**
 * Header callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @return      void
 */
function custom_front_page_header_callback( $args ) {
    echo '<hr />';
}


/**
 * Checkbox callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      void
 */
function custom_front_page_checkbox_callback( $args ) {
    global $custom_front_page_options;

    $checked = isset( $custom_front_page_options[$args['id']] ) ? checked( 1, $custom_front_page_options[$args['id']], false ) : '';

    $html  = '<input type="checkbox" id="custom_front_page_settings[' . $args['id'] . ']" name="custom_front_page_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>&nbsp;';
    $html .= '<label for="custom_front_page_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Color callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the settings
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      void
 */
function custom_front_page_color_callback( $args ) {
    global $custom_front_page_options;

    if( isset( $custom_front_page_options[$args['id']] ) ) {
        $value = $custom_front_page_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $default = isset( $args['std'] ) ? $args['std'] : '';
    $size    = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="text" class="custom_front_page-color-picker" id="custom_front_page_settings[' . $args['id'] . ']" name="custom_front_page_settings[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />&nbsp;';
    $html .= '<span class="custom_front_page-color-picker-label"><label for="custom_front_page_settings[' . $args['id'] . ']">' . $args['desc'] . '</label></span>';

    echo $html;
}


/**
 * Editor callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      void
 */
function custom_front_page_editor_callback( $args ) {
    global $custom_front_page_options;

    if( isset( $custom_front_page_options[$args['id']] ) ) {
        $value = $custom_front_page_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $rows       = ( isset( $args['rows'] ) && ! is_numeric( $args['rows'] ) ) ? $args['rows'] : '10';
    $wpautop    = isset( $args['wpautop'] ) ? $args['wpautop'] : true;
    $buttons    = isset( $args['buttons'] ) ? $args['buttons'] : true;
    $teeny      = isset( $args['teeny'] ) ? $args['teeny'] : false;

    wp_editor(
        $value,
        'custom_front_page_settings_' . $args['id'],
        array(
            'wpautop'       => $wpautop,
            'media_buttons' => $buttons,
            'textarea_name' => 'custom_front_page_settings[' . $args['id'] . ']',
            'textarea_rows' => $rows,
            'teeny'         => $teeny
        )
    );
    echo '<br /><label for="custom_front_page_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';
}


/**
 * Info callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      void
 */
function custom_front_page_info_callback( $args ) {
    global $custom_front_page_options;

    $notice = ( $args['notice'] == true ? '-notice' : '' );
    $class  = ( isset( $args['class'] ) ? $args['class'] : '' );
    $style  = ( isset( $args['style'] ) ? $args['style'] : 'normal' );
    $header = '';

    if( isset( $args['header'] ) ) {
        $header = '<b>' . $args['header'] . '</b><br />';
    }

    echo '<div id="custom_front_page_settings[' . $args['id'] . ']" name="custom_front_page_settings[' . $args['id'] . ']" class="custom_front_page-info' . $notice . ' custom_front_page-info-' . $style . '">';

    if( isset( $args['icon'] ) ) {
        echo '<p class="custom_front_page-info-icon">';
        echo '<i class="fa fa-' . $args['icon'] . ' ' . $class . '"></i>';
        echo '</p>';
    }

    echo '<p class="custom_front_page-info-desc">' . $header . $args['desc'] . '</p>';
    echo '</div>';
}


/**
 * Multicheck callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      void
 */
function custom_front_page_multicheck_callback( $args ) {
    global $custom_front_page_options;

    if( ! empty( $args['options'] ) ) {
        foreach( $args['options'] as $key => $option ) {
            $enabled = ( isset( $custom_front_page_options[$args['id']][$key] ) ? $option : NULL );

            echo '<input name="custom_front_page_settings[' . $args['id'] . '][' . $key . ']" id="custom_front_page_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked( $option, $enabled, false ) . ' />&nbsp;';
            echo '<label for="custom_front_page_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br />';
        }
        echo '<p class="description">' . $args['desc'] . '</p>';
    }
}


/**
 * Number callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      void
 */
function custom_front_page_number_callback( $args ) {
    global $custom_front_page_options;

    if( isset( $custom_front_page_options[$args['id']] ) ) {
        $value = $custom_front_page_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $max    = isset( $args['max'] ) ? $args['max'] : 999999;
    $min    = isset( $args['min'] ) ? $args['min'] : 0;
    $step   = isset( $args['step'] ) ? $args['step'] : 1;
    $size   = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="custom_front_page_settings[' . $args['id'] . ']" name="custom_front_page_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '" />&nbsp;';
    $html .= '<label for="custom_front_page_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Page callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      void
 */
function custom_front_page_pages_callback( $args ) {
    global $custom_front_page_options;

    if( isset( $custom_front_page_options[$args['id']] ) ) {
        $value = $custom_front_page_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';

    $all_pages  = get_pages();
    $pages      = array();
    $pages['--default--']   = __( '--Default--', 'custom-front-page' );

    foreach( $all_pages as $page ) {
        $pages[$page->ID] = $page->post_title;
    }

    $html = '<select id="custom_front_page_settings[' . $args['id'] . ']" name="custom_front_page_settings[' . $args['id'] . ']" placeholder="' . $placeholder . '" />';

    foreach( $pages as $option => $name ) {
        $selected = selected( $option, $value, false );

        $html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
    }

    $html .= '</select>&nbsp;';
    $html .= '<label for="custom_front_page_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}
/**
 * Password callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the settings
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      void
 */
function custom_front_page_password_callback( $args ) {
    global $custom_front_page_options;

    if( isset( $custom_front_page_options[$args['id']] ) ) {
        $value = $custom_front_page_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="password" class="' . $size . '-text" id="custom_front_page_settings[' . $args['id'] . ']" name="custom_front_page_settings[' . $args['id'] . ']" value="' . esc_attr( $value )  . '" />&nbsp;';
    $html .= '<label for="custom_front_page_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Radio callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      void
 */
function custom_front_page_radio_callback( $args ) {
    global $custom_front_page_options;

    if( ! empty( $args['options'] ) ) {
        foreach( $args['options'] as $key => $option ) {
            $checked = false;

            if( isset( $custom_front_page_options[$args['id']] ) && $custom_front_page_options[$args['id']] == $key ) {
                $checked = true;
            } elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $custom_front_page_options[$args['id']] ) ) {
                $checked = true;
            }

            echo '<input name="custom_front_page_settings[' . $args['id'] . ']" id="custom_front_page_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( true, $checked, false ) . '/>&nbsp;';
            echo '<label for="custom_front_page_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br />';
        }

        echo '<p class="description">' . $args['desc'] . '</p>';
    }
}


/**
 * Select callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      void
 */
function custom_front_page_select_callback( $args ) {
    global $custom_front_page_options;

    if( isset( $custom_front_page_options[$args['id']] ) ) {
        $value = $custom_front_page_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';

    $html = '<select id="custom_front_page_settings[' . $args['id'] . ']" name="custom_front_page_settings[' . $args['id'] . ']" placeholder="' . $placeholder . '" />';

    foreach( $args['options'] as $option => $name ) {
        $selected = selected( $option, $value, false );

        $html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
    }

    $html .= '</select>&nbsp;';
    $html .= '<label for="custom_front_page_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Text callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      void
 */
function custom_front_page_text_callback( $args ) {
    global $custom_front_page_options;

    if( isset( $custom_front_page_options[$args['id']] ) ) {
        $value = $custom_front_page_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="text" class="' . $size . '-text" id="custom_front_page_settings[' . $args['id'] . ']" name="custom_front_page_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) )  . '" />&nbsp;';
    $html .= '<label for="custom_front_page_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Textarea callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      void
 */
function custom_front_page_textarea_callback( $args ) {
    global $custom_front_page_options;

    if( isset( $custom_front_page_options[$args['id']] ) ) {
        $value = $custom_front_page_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $html  = '<textarea class="large-text" cols="50" rows="5" id="custom_front_page_settings[' . $args['id'] . ']" name="custom_front_page_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>&nbsp;';
    $html .= '<label for="custom_front_page_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Upload callback
 * 
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      void
 */
function custom_front_page_upload_callback( $args ) {
    global $custom_front_page_options;

    if( isset( $custom_front_page_options[$args['id']] ) ) {
        $value = $custom_front_page_options[$args['id']];
    } else {
        $value = isset( $args['std'] ) ? $args['std'] : '';
    }

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

    $html  = '<input type="text" class="' . $size . '-text" id="custom_front_page_settings[' . $args['id'] . ']" name="custom_front_page_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '" />&nbsp;';
    $html .= '<span><input type="button" class="custom_front_page_settings_upload_button button-secondary" value="' . __( 'Upload File', 'custom-front-page' ) . '" /></span>&nbsp;';
    $html .= '<label for="custom_front_page_settings[' . $args['id'] . ']">' . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Hook callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @return      void
 */
function custom_front_page_hook_callback( $args ) {
    do_action( 'custom_front_page_' . $args['id'] );
}


/**
 * Missing callback
 *
 * @since       1.0.0
 * @param       array $args Arguments passed by the setting
 * @return      void
 */
function custom_front_page_missing_callback( $args ) {
    printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'custom-front-page' ), $args['id'] );
}
