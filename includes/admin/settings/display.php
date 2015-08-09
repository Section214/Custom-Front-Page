<?php
/**
 * Settings page
 *
 * @package     Custom_Front_Page\Admin\Settings\Display
 * @since       1.0.0
 */


// Exit if accessed directly
if( ! defined( 'ABSPATH' ) ) exit;


/**
 * Render the settings page
 *
 * @since       1.0.0
 * @global      array $custom_front_page_options The Custom_Front_Page options
 * @return      void
 */
function custom_front_page_render_settings_page() {
    global $custom_front_page_options;

    $active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], custom_front_page_get_settings_tabs() ) ? $_GET['tab'] : 'login-status-pages';

    ob_start();
    ?>
    <div class="wrap">
        <h2 class="nav-tab-wrapper">
            <?php
            foreach( custom_front_page_get_settings_tabs() as $tab_id => $tab_name ) {
                $tab_url = add_query_arg( array(
                    'settings-updated'  => false,
                    'tab'               => $tab_id
                ) );

                $active = $active_tab == $tab_id ? ' nav-tab-active' : '';

                echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name )  . '" class="nav-tab' . $active . '">' . esc_html( $tab_name ) . '</a>';
            }
            ?>
        </h2>
        <div id="tab_container">
            <form method="post" action="options.php">
                <table class="form-table">
                    <?php
                    settings_fields( 'custom_front_page_settings' );
                    do_settings_fields( 'custom_front_page_settings_' . $active_tab, 'custom_front_page_settings_' . $active_tab );
                    ?>
                </table>
                <?php if( ! isset( $_GET['tab'] ) || $_GET['tab'] !== 'help' ) { submit_button(); } ?>
            </form>
        </div>
    </div>
    <?php
    echo ob_get_clean();
}
