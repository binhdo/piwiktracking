<?php
/*
 * Piwiktracking plugin functions
 */

add_action( 'init', 'piwiktracking_functions_setup' );

function piwiktracking_functions_setup() {

	$old_version = piwiktracking_get_option( 'version' );

	if ( false === get_option( 'piwiktracking_settings' ) ) {
		add_option( 'piwiktracking_settings', piwiktracking_get_default_settings( ) );
	} elseif ( $old_version !== PIWIKTRACKING_VERSION ) {
		piwiktracking_update_settings( );
	}

	/* Insert Piwiktracking code */
	if ( is_user_logged_in( ) ) {
		$roles_set = piwiktracking_get_option( 'excludedroles' );
		if ( $roles_set )
			foreach ( $roles_set as $role => $name ) {
				if ( current_user_can( $role ) && ! $roles_set[$role] == 1 ) {
					switch( piwiktracking_get_option( 'trackingmode' ) ) {
						case 'standard' :
							add_action( 'wp_footer', 'piwiktracking_standard_tracking' );
							break;
						case 'async' :
							add_action( 'wp_head', 'piwiktracking_async_tracking' );
							break;
					}
				}
			}
	} else {
		switch (piwiktracking_get_option( 'trackingmode')) {
			case 'standard' :
				add_action( 'wp_footer', 'piwiktracking_standard_tracking' );
				break;
			case 'async' :
				add_action( 'wp_head', 'piwiktracking_async_tracking' );
				break;
		}
	}

}

function piwiktracking_async_tracking() {
	$piwik_url = piwiktracking_get_option( 'piwikurl' );
	$site_id = piwiktracking_get_option( 'siteid' );

	if ( $piwik_url && $site_id ) {
		$output = '<!-- Piwik Async -->' . "\n";
		$output .= '<script type="text/javascript">' . "\n";
		$output .= "\t" . 'var _paq = _paq || []; (function() {' . "\n";
		$output .= "\t" . "var u=('https:' == document.location.protocol ? 'https://' : 'http://') + '" . $piwik_url . "';" . "\n";
		$output .= "\t" . '_paq.push([\'setSiteId\', ' . $site_id . ']);' . "\n";
		$output .= "\t" . '_paq.push([\'setTrackerUrl\', u + \'piwik.php\']);' . "\n";
		$output .= "\t" . '_paq.push([\'trackPageView\']);' . "\n";
		if ( piwiktracking_get_option( 'linktracking' ) )
			$output .= "\t" . '_paq.push([\'enableLinkTracking\']);' . "\n";
		$output .= "\t" . 'var d = document, g = d.createElement(\'script\'), s = d.getElementsByTagName(\'script\')[0];' . "\n";
		$output .= "\t" . 'g.type = \'text/javascript\';' . "\n";
		$output .= "\t" . 'g.defer = true;' . "\n";
		$output .= "\t" . 'g.async = true;' . "\n";
		$output .= "\t" . 'g.src = u + \'piwik.js\';' . "\n";
		$output .= "\t" . 's.parentNode.insertBefore(g, s);' . "\n";
		$output .= "\t" . '})();' . "\n";
		$output .= '</script>' . "\n";
		$output .= '<!-- End Piwik Code -->' . "\n";

		echo $output;
	}
}

function piwiktracking_standard_tracking() {
	$piwik_url = piwiktracking_get_option( 'piwikurl' );
	$site_id = piwiktracking_get_option( 'siteid' );

	if ( $piwik_url && $site_id ) {
		$output = '<!-- Piwik Standard-->' . "\n";
		$output .= '<script type="text/javascript">' . "\n";
		$output .= "\t" . "var pkBaseURL = (('https:' == document.location.protocol) ? 'https://') + '" . $piwik_url . "';" . "\n";
		$output .= "\t" . 'document.write(unescape("%3Cscript src=\'" + pkBaseURL + "piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));' . "\n";
		$output .= '</script>' . "\n";
		$output .= '<script type="text/javascript">' . "\n";
		$output .= "\t" . 'try {' . "\n";
		$output .= "\t\t" . 'var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", ' . $site_id . ');' . "\n";
		$output .= "\t\t" . 'piwikTracker.trackPageView();' . "\n";
		if ( piwiktracking_get_option( 'linktracking' ) )
			$output .= "\t\t" . 'piwikTracker.enableLinkTracking();' . "\n";
		$output .= "\t" . '} catch( err ) {' . "\n";
		$output .= "\t" . '}' . "\n";
		$output .= '</script>' . "\n";
		$output .= '<!-- End Piwik Code -->' . "\n";

		echo $output;
	}
}

function piwiktracking_get_option($option) {
	$settings = get_option( 'piwiktracking_settings');
	
	return $settings[$option];
}

function piwiktracking_update_settings() {

	/* Get the settings from the database. */
	$settings = get_option( 'piwiktracking_settings' );
	/* Get the default plugin settings. */
	$default_settings = piwiktracking_get_default_settings( );
	/* Loop through each of the default plugin settings. */
	foreach ( $default_settings as $setting_key => $setting_value ) {
		/* If the setting didn't previously exist, add the default value to the $settings array. */
		if ( ! isset( $settings[$setting_key] ) )
			$settings[$setting_key] = $setting_value;
	}
	$settings['version'] = PIWIKTRACKING_VERSION;
	/* Update the plugin settings. */
	update_option( 'piwiktracking_settings', $settings );

}

function piwiktracking_get_default_settings() {

	// preg_replace( "/^https?:\/\/(.+)$/i", "\\1", site_url( '/piwik/' ) )

	$settings = array(
		'version' => PIWIKTRACKING_VERSION,
		'piwikurl' => '',
		'siteid' => '',
		'linktracking' => true,
		'trackingmode' => 'standard',
	);

	return $settings;

}
?>