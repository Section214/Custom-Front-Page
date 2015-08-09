<?php
/**
 * Admin actions
 *
 * @package     Custom_Front_Page\Admin\Actions
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Process all actions sent via POST and GET by looking for the 'custom-front-page-action'
 * request and running do_action() to call the function
 *
 * @since       1.0.0
 * @return      void
 */
function custom_front_page_process_actions() {
    if( isset( $_POST['custom-front-page-action'] ) ) {
        do_action( 'custom_front_page_' . $_POST['custom-front-page-action'], $_POST );
    }

    if( isset( $_GET['custom-front-page-action'] ) ) {
        do_action( 'custom_front_page_' . $_GET['custom-front-page-action'], $_GET );
    }
}
add_action( 'admin_init', 'custom_front_page_process_actions' );
