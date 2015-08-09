<?php
/**
 * Scripts
 *
 * @package     Custom_Front_Page\Scripts
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Load admin scripts
 *
 * @since       1.0.0
 * @param       string $hook The page hook
 * @return      void
 */
function custom_front_page_admin_scripts( $hook ) {
    if( ! apply_filters( 'custom_front_page_load_admin_scripts', custom_front_page_is_admin_page( $hook ), $hook ) ) {
        return;
    }

    // Use minified libraries if SCRIPT_DEBUG is turned off
    $suffix     = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
    $ui_style   = ( get_user_option( 'admin_color' ) == 'classic' ) ? 'classic' : 'fresh';

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    wp_enqueue_media();
    wp_enqueue_style( 'jquery-ui-css', CUSTOM_FRONT_PAGE_URL . 'assets/css/jquery-ui-' . $ui_style . $suffix . '.css' );
    wp_enqueue_script( 'media-upload' );
    wp_enqueue_style( 'thickbox' );
    wp_enqueue_script( 'thickbox' );

    wp_enqueue_style( 'custom-front-page-fa', CUSTOM_FRONT_PAGE_URL . 'assets/css/font-awesome.min.css', array(), '4.3.0' );
    wp_enqueue_style( 'custom-front-page', CUSTOM_FRONT_PAGE_URL . 'assets/css/admin' . $suffix . '.css', array(), CUSTOM_FRONT_PAGE_VER );
    wp_enqueue_script( 'custom-front-page', CUSTOM_FRONT_PAGE_URL . 'assets/js/admin' . $suffix . '.js', array( 'jquery' ), CUSTOM_FRONT_PAGE_VER );
    wp_localize_script( 'custom-front-page', 'custom_front_page_vars', array(
        'image_media_button'    => __( 'Insert Image', 'custom-front-page' ),
        'image_media_title'     => __( 'Select Image', 'custom-front-page' )
    ) );
}
add_action( 'admin_enqueue_scripts', 'custom_front_page_admin_scripts', 100 );
