<?php
/**
 * Admin pages
 *
 * @package     Custom_Front_Page\Admin\Pages
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Create the settings menu pages
 *
 * @since       1.0.0
 * @global      string $custom_front_page_settings_page The Custom Front Page settings page hook
 * @return      void
 */
function custom_front_page_add_settings_pages() {
    global $custom_front_page_settings_page;

    $custom_front_page_settings_page = add_options_page( __( 'Custom Front Page Settings', 'custom-front-page' ), __( 'Custom Front Page', 'custom-front-page' ), 'manage_options', 'custom-front-page-settings', 'custom_front_page_render_settings_page' );
}
add_action( 'admin_menu', 'custom_front_page_add_settings_pages', 10 );


/**
 * Determines whether or not the current admin page is a Custom Front Page page
 *
 * @since       1.0.0
 * @param       string $hook The hook for this page
 * @global      string $typenow The post type we are viewing
 * @global      string $pagenow The page we are viewing
 * @global      string $custom_front_page_settings_page The Custom Front Page settings page hook
 * @return      bool $ret True if Custom_Front_Page page, false otherwise
 */
function custom_front_page_is_admin_page( $hook ) {
    global $typenow, $pagenow, $custom_front_page_settings_page;

    $ret    = false;
    $pages  = apply_filters( 'custom_front_page_admin_pages', array( $custom_front_page_settings_page ) );

    if( in_array( $hook, $pages ) ) {
        $ret = true;
    }

    return (bool) apply_filters( 'custom_front_page_is_admin_page', $ret );
}
