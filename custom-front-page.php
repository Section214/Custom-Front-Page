<?php
/**
 * Plugin Name:     Custom Front Page
 * Plugin URI:      http://section214.com
 * Description:     Allows fine-grained control over the display of the front page
 * Version:         1.0.0
 * Author:          Daniel J Griffiths
 * Author URI:      http://section214.com
 * Text Domain:     custom-front-page
 *
 * @package         Custom_Front_Page
 * @author          Daniel J Griffiths <dgriffiths@section214.com>
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) {
    exit;
}


if( ! class_exists( 'Custom_Front_Page' ) ) {


    /**
     * Main Custom_Front_Page class
     *
     * @since       1.0.0
     */
    class Custom_Front_Page {


        /**
         * @access      private
         * @since       1.0.0
         * @var         Custom_Front_Page $instance The one true Custom_Front_Page
         */
        private static $instance;


        /**
         * Get active instance
         *
         * @access      public
         * @since       1.0.0
         * @return      self::$instance The one true Custom_Front_Page
         */
        public static function instance() {
            if( ! self::$instance ) {
                self::$instance = new Custom_Front_Page();
                self::$instance->setup_constants();

                add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );

                self::$instance->includes();
                self::$instance->hooks();
            }

            return self::$instance;
        }


        /**
         * Setup plugin constants
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function setup_constants() {
            // Plugin version
            define( 'CUSTOM_FRONT_PAGE_VER', '1.0.0' );

            // Plugin path
            define( 'CUSTOM_FRONT_PAGE_DIR', plugin_dir_path( __FILE__ ) );

            // Plugin URL
            define( 'CUSTOM_FRONT_PAGE_URL', plugin_dir_url( __FILE__ ) );
        }


        /**
         * Include required files
         *
         * @access      private
         * @since       1.0.0
         * @global      array $custom_front_page_options The Custom Front Page options array
         * @return      void
         */
        private function includes() {
            global $custom_front_page_options;

            require_once CUSTOM_FRONT_PAGE_DIR . 'includes/admin/settings/register.php';
            $custom_front_page_options = custom_front_page_get_settings();

            require_once CUSTOM_FRONT_PAGE_DIR . 'includes/scripts.php';
            require_once CUSTOM_FRONT_PAGE_DIR . 'includes/functions.php';

            if( is_admin() ) {
                require_once CUSTOM_FRONT_PAGE_DIR . 'includes/admin/actions.php';
                require_once CUSTOM_FRONT_PAGE_DIR . 'includes/admin/pages.php';
                require_once CUSTOM_FRONT_PAGE_DIR . 'includes/admin/settings/display.php';
            }
        }


        /**
         * Run action and filter hooks
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function hooks() {
            // Override show_on_front option
            add_filter( 'option_show_on_front', array( $this, 'option_show_on_front' ) );

            // Override page_on_front_option
            add_filter( 'option_page_on_front', array( $this, 'option_page_on_front' ) );
        }


        /**
         * Internationalization
         *
         * @access      public
         * @since       1.0.0
         * @return      void
         */
        public function load_textdomain() {
            // Set filter for language directory
            $lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
            $lang_dir = apply_filters( 'custom_front_page_language_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), '' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'custom-front-page', $locale );

            // Setup paths to current locale file
            $mofile_local   = $lang_dir . $mofile;
            $mofile_global  = WP_LANG_DIR . '/custom-front-page/' . $mofile;

            if( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/custom-front-page/ folder
                load_textdomain( 'custom-front-page', $mofile_global );
            } elseif( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/custom-front-page/languages/ folder
                load_textdomain( 'custom-front-page', $mofile_local );
            } else {
                load_plugin_textdomain( 'custom-front-page', false, $lang_dir );
            }
        }


        /**
         * Override the show_on_front option
         *
         * @access      public
         * @since       1.0.0
         * @param       string $show The current setting for the show_on_front option
         * @return      string $show The updated setting for the show_on_front option
         */
        public function option_show_on_front( $show ) {
            $logged_in_page     = custom_front_page_get_option( 'logged_in_front_page', '--default--' );
            $logged_out_page    = custom_front_page_get_option( 'logged_out_front_page', '--default--' );

            if( $logged_in_page !== '--default--' || $logged_out_page !== '--default--' ) {
                if( $show == 'posts' ) {
                    if( is_user_logged_in() && $logged_in_page !== '--default--' ) {
                        $show = 'page';
                    } elseif( ! is_user_logged_in() && $logged_out_page !== '--default--' ) {
                        $show = 'page';
                    }
                }
            }

            return $show;
        }


        /**
         * Override the page_on_front option
         *
         * @access      public
         * @since       1.0.0
         * @param       int $page The current setting for the page_on_front option
         * @return      int $page The updated setting for the page_on_front option
         */
        public function option_page_on_front( $page ) {
            $logged_in_page     = custom_front_page_get_option( 'logged_in_front_page', '--default--' );
            $logged_out_page    = custom_front_page_get_option( 'logged_out_front_page', '--default--' );

            if( $logged_in_page !== '--default--' || $logged_out_page !== '--default--' ) {
                if( is_user_logged_in() && $logged_in_page !== '--default--' ) {
                    $page = $logged_in_page;
                } elseif( ! is_user_logged_in() && $logged_out_page !== '--default--' ) {
                    $page = $logged_out_page;
                }
            }

            return intval( $page );
        }
    }
}


/**
 * The main function responsible for returning the one true Custom_Front_Page
 * instance to functions everywhere
 *
 * @since       1.0.0
 * @return      Custom_Front_Page The one true Custom_Front_Page
 */
function custom_front_page() {
    return Custom_Front_Page::instance();
}
custom_front_page();
