<?php
/*
 * Plugin Name: Piwik Tracking
 * Plugin URI: http://binaryhideout.com
 * Description: Adds the Piwik tracking code to your WordPress blog.
 * Version: 0.2
 * Author: M. Dobersalsky
 * Author URI: http://binaryhideout.com
 * License: GPLv2 or later
 */

$piwiktracking = new PiwikTracking();

class PiwikTracking {

    function __construct() {

        add_action( 'plugins_loaded', array(
            &$this,
            'piwiktracking_initialise'
        ) );

    }

    function piwiktracking_initialise() {

        /* Plugin constants */
        define( 'PIWIKTRACKING_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
        define( 'PIWIKTRACKING_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
        define( 'PIWIKTRACKING_VERSION', '0.2' );

        /* Include plugin functions */
        require_once (PIWIKTRACKING_DIR . 'piwik-tracking_functions.php');

        /* Include settings page /*/
        if ( is_admin( ) ) {
            require_once (PIWIKTRACKING_DIR . 'piwik-tracking_settings.php');
            add_filter( 'plugin_action_links', array(
                &$this,
                'piwiktracking_plugin_action_links'
            ), 10, 2 );
            register_deactivation_hook( __FILE__, array(
                &$this,
                'piwiktracking_deactivate_plugin'
            ) );
        }

    }

    function piwiktracking_plugin_action_links($links, $file) {

        if ( $file == plugin_basename( dirname( __FILE__ ) . '/piwik-tracking.php' ) ) {
            $links[] = '<a href="options-general.php?page=piwiktracking-settings-page">' . __( 'Settings' ) . '</a>';
        }
        return $links;

    }

    function piwiktracking_deactivate_plugin() {

        delete_option( 'piwiktracking_settings' );

    }

}
?>