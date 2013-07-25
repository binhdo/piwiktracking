<?php

/*
 * Plugin Name: Piwiktracking
 * Plugin URI: http://binaryhideout.com/piwiktracking-wordpress-plugin/
 * Description: Adds the Piwik tracking code to your WordPress blog.
 * Version: 1.2.1
 * Author: binaryhideout
 * Author URI: http://binaryhideout.com
 * License: GPLv2 or later
 */

$piwiktracking = new PiwikTracking();

class PiwikTracking {

	function __construct() {

		add_action( 'plugins_loaded', array(&$this, 'piwiktracking_initialise') );
	}

	function piwiktracking_initialise() {

		/* Plugin constants */
		define( 'PIWIKTRACKING_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'PIWIKTRACKING_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );
		define( 'PIWIKTRACKING_VERSION', '1.2.1' );

		/* Include plugin functions */
		require_once (PIWIKTRACKING_DIR . 'piwiktracking_functions.php');

		/* Include settings page / */
		if ( is_admin() ) {
			require_once (PIWIKTRACKING_DIR . 'piwiktracking_settings.php');

			add_filter( 'plugin_action_links', array(&$this, 'piwiktracking_plugin_action_links'), 10, 2 );

			register_deactivation_hook( __FILE__, array(&$this, 'piwiktracking_deactivate_plugin') );

			if ( current_user_can( 'manage_options' ) && (!piwiktracking_get_option( 'piwikurl' ) || !piwiktracking_get_option( 'siteid' )) ) {
				add_action( 'admin_notices', array(&$this, 'piwiktracking_admin_notices') );
			}
		}

		load_plugin_textdomain( 'piwiktracking', false, PIWIKTRACKING_DIR . 'languages/' );
	}

	function piwiktracking_plugin_action_links($links, $file) {

		if ( $file == plugin_basename( dirname( __FILE__ ) . '/piwiktracking.php' ) ) {
			$links[] = '<a href="options-general.php?page=piwiktracking-settings-page">' . __( 'Settings' ) . '</a>';
		}
		return $links;
	}

	function piwiktracking_deactivate_plugin() {
		delete_option( 'piwiktracking_settings' );
	}

	function piwiktracking_admin_notices() {
		$piwik_url = piwiktracking_get_option( 'piwikurl' );
		$site_id = piwiktracking_get_option( 'siteid' );

		if ( !$piwik_url || !$site_id ) {
			if ( !$piwik_url && !$site_id )
				$text = __( 'Please set your <strong>Piwik URL</strong> and your <strong>Piwik SiteID</strong> on the <a href="options-general.php?page=piwiktracking-settings-page"><strong>Piwiktracking Settings Page</strong></a>.', 'piwiktracking' );
			if ( $piwik_url && !$site_id )
				$text = __( 'Please set your <a href="options-general.php?page=piwiktracking-settings-page"><strong>Piwik SiteID</strong></a>.', 'piwiktracking' );
			if ( !$piwik_url && $site_id )
				$text = __( 'Please set your <a href="options-general.php?page=piwiktracking-settings-page"><strong>Piwik URL</strong></a>.', 'piwiktracking' );
			$out = '<div class="updated fade">';
			$out .= '<p>' . $text . '</p>';
			$out .= '</div>';

			echo $out;
		}
	}

}
