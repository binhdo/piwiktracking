<?php
/*
 * Piwiktracking plugin functions
 */

add_action( 'init', 'piwiktracking_functions_setup' );

function piwiktracking_functions_setup() {

	$old_version = piwiktracking_get_option( 'version' );

	if ( false === get_option( 'piwiktracking_settings' ) ) {
		add_option( 'piwiktracking_settings', piwiktracking_get_default_settings() );
	} elseif ( $old_version !== PIWIKTRACKING_VERSION ) {
		piwiktracking_update_settings();
	}

	/* Insert Piwiktracking code */
	if ( is_user_logged_in() ) {
		$roles_set = piwiktracking_get_option( 'excludedroles' );
		if ( $roles_set ) {
			foreach ( $roles_set as $role => $name ) {
				if ( current_user_can( $role ) && !$roles_set[$role] == 1 ) {
					add_action( 'wp_footer', 'piwiktracking_javascript_tracking', 100 );
				}
			}
		}
	} else {
		add_action( 'wp_footer', 'piwiktracking_javascript_tracking', 100 );
	}
}

function piwiktracking_javascript_tracking() {
	$piwik_url = esc_attr( piwiktracking_get_option( 'piwikurl' ) );
	$site_id = esc_attr( piwiktracking_get_option( 'siteid' ) );

	if ( !$piwik_url && !$site_id )
		return;

	$linktracking = piwiktracking_get_option( 'linktracking' ) ? '_paq.push(["enableLinkTracking"]);' . "\n" : '';
	$subdomaintracking = piwiktracking_get_option( 'subdomaintracking' ) ? '_paq.push(["setCookieDomain", "*.' . piwiktracking_get_domain() . '"]);' . "\n" : '';
	$prependsitedomain = piwiktracking_get_option( 'prependsitedomain' ) ? '_paq.push(["setDocumentTitle", document.domain + "/" + document.title]);' . "\n" : '';
	$hidealiasclicks = piwiktracking_get_option( 'hidealiasclicks' ) ? '_paq.push(["setDomains", ["*.' . piwiktracking_get_domain() . '"]]);' . "\n" : '';
	$cliensidednt = piwiktracking_get_option( 'clientsidednt' ) ? '_paq.push(["setDoNotTrack", true]);' . "\n" : '';
	?>
	<!-- Piwik -->
	<script type="text/javascript">
		var _paq = _paq || [];
		<?php echo $prependsitedomain; ?>
		<?php echo $subdomaintracking; ?>
		<?php echo $hidealiasclicks; ?>
		<?php echo $cliensidednt; ?>
		_paq.push(["trackPageView"]);
		<?php echo $linktracking; ?>
		(function() {
			var u = (("https:" == document.location.protocol) ? "https" : "http") + "://" + "<?php echo $piwik_url; ?>";
			_paq.push(["setTrackerUrl", u + "piwik.php"]);
			_paq.push(["setSiteId", "<?php echo $site_id ?>"]);
			var d = document, g = d.createElement("script"), s = d.getElementsByTagName("script")[0];
			g.type = "text/javascript";
			g.defer = true;
			g.async = true;
			g.src = u + "piwik.js";
			s.parentNode.insertBefore(g, s);
		})();
	</script>
	<!-- End Piwik Code -->
	<?php
}

function piwiktracking_get_option($option) {
	$settings = get_option( 'piwiktracking_settings' );

	if ( !is_array( $settings ) || !array_key_exists( $option, $settings ) )
		return false;

	return $settings[$option];
}

function piwiktracking_update_settings() {
	$settings = get_option( 'piwiktracking_settings' );
	$default_settings = piwiktracking_get_default_settings();
	$settings['version'] = PIWIKTRACKING_VERSION;

	foreach ( $default_settings as $setting_key => $setting_value ) {
		if ( !isset( $settings[$setting_key] ) )
			$settings[$setting_key] = $setting_value;
	}

	update_option( 'piwiktracking_settings', $settings );
}

function piwiktracking_get_default_settings() {

	$settings = array(
		'version' => PIWIKTRACKING_VERSION,
		'piwikurl' => '',
		'siteid' => '',
		'linktracking' => true,
		'subdomaintracking' => false,
		'prependsitedomain' => false,
		'hidealiasclicks' => false,
		'clientsidednt' => false
	);

	return $settings;
}

function piwiktracking_get_domain() {
	$url = $_SERVER['HTTP_HOST'];

	if ( preg_match( "/(?P<url>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $url, $matches ) ) {
		return $matches['url'];
	} else {
		return $url;
	}
}
