<?php
/*
 * Piwiktracking plugin settings
 */

add_action( 'admin_menu', 'piwiktracking_settings_page_setup' );

function piwiktracking_settings_page_setup() {

	global $piwiktracking;
	global $wp_roles;

	$piwiktracking -> trackingmodes = array(
		'standard' => 'Standard javascript tracking',
		'async' => 'Asynchronous javascript tracking'
	);
	$piwiktracking -> roles_available = $wp_roles -> get_names( );

	/* add_options_page( $page_title, $menu_title, $capability, $menu_slug, $funtion ); */
	$piwiktracking -> settings_page = add_options_page( 'Piwiktracking Settings', 'Piwiktracking', 'manage_options', 'piwiktracking-settings-page', 'piwiktracking_display_settings_page' );

	add_action( 'admin_init', 'piwiktracking_register_settings' );

	add_action( 'load-' . $piwiktracking -> settings_page, 'piwiktracking_settings_sections' );

	/* Admin scripts & Styles */
	add_action( 'admin_print_scripts-' . $piwiktracking -> settings_page, 'piwiktracking_admin_scripts' );
	add_action( 'admin_print_styles-' . $piwiktracking -> settings_page, 'piwiktracking_admin_styles' );

}

function piwiktracking_register_settings() {

	/* register_setting( $option_group, $option_name, $sanitize_callback ); */
	register_setting( 'piwiktracking_settings', 'piwiktracking_settings', 'piwiktracking_validate_settings' );
}

function piwiktracking_settings_sections() {

	/* add_settings_section( $id, $title, $callback, $page ); */
	add_settings_section( 'basic_settings', false, 'piwiktracking_section_basic', 'piwiktracking-settings-page' );

	add_settings_section( 'advanced_settings', false, 'piwiktracking_section_advanced', 'piwiktracking-settings-page' );

}

function piwiktracking_section_basic() {

	global $piwiktracking;

	$html = '<div id="piwiktracking-settings-basic" class="piwiktracking-section">' . "\n";
	$html .= '<h3>' . __( 'Piwiktracking Basic Settings', 'piwiktracking' ) . '</h3>' . "\n";

	$html .= piwiktracking_create_setting( array(
		'id' => 'piwikurl',
		'type' => 'text',
		'class' => 'regular-text',
		'label' => __( 'Piwik Base URL', 'piwiktracking' ),
		'desc' => __( 'the base URL to your Piwik installation (without http(s)://)', 'piwiktracking' )
	) );

	$html .= piwiktracking_create_setting( array(
		'id' => 'siteid',
		'type' => 'text',
		'class' => 'small-text',
		'label' => __( 'Your Piwik SiteID', 'piwiktracking' )
	) );

	$html .= piwiktracking_create_setting( array(
		'id' => 'trackingmode',
		'type' => 'select',
		'label' => __( 'Select Tracking Mode', 'piwiktracking' ),
		'options' => $piwiktracking -> trackingmodes
	) );

	$html .= piwiktracking_create_setting( array(
		'id' => 'linktracking',
		'type' => 'checkbox',
		'value' => true,
		'label' => __( 'Enable outlink & downloads tracking', 'piwiktracking' )
	) );
	$html .= '</div>' . "\n";

	echo $html;

}

function piwiktracking_section_advanced() {

	global $piwiktracking;

	$html = '<div id="piwiktracking-settings-advanced" class="piwiktracking-section">' . "\n";
	$html .= '<h3>' . __( 'Select user groups to exclude from being tracked:', 'piwiktracking' ) . '</h3>' . "\n";

	$prefix = 'piwiktracking_settings[excludedroles]';
	$roles_set = piwiktracking_get_option( 'excludedroles' );
	$value = true;

	$html .= '<p>' . "\n";
	$html .= '<label for="select-all">' . __( 'Select / deselect all', 'piwiktracking' ) . '</label>' . "\n";
	$html .= '<input id="select-all" type="checkbox">' . "\n";
	$html .= '</p>' . "\n";

	$html .= '<fieldset id="user-roles">' . "\n";
	foreach ( $piwiktracking->roles_available as $role => $name ) {
		$html .= '<p>' . "\n";
		$html .= '<input type="checkbox" id="' . $role . '" name="' . $prefix . '[' . $role . ']" value="' . $value . '"' . checked( $value, $roles_set[$role], false ) . ' >' . "\n";
		$html .= "\t" . '<label for="' . $role . '">' . $name . '</label>' . "\n";
		$html .= '</p>' . "\n";
	}
	$html .= '</fieldset>' . "\n";

	$html .= '</div>' . "\n";

	echo $html;

}

function piwiktracking_create_setting($args = array(), $before = '<p>', $after = '</p>') {

	extract( $args );

	$field_value = piwiktracking_get_option( $id );
	$prefix = 'piwiktracking_settings';

	$html = $before . "\n";

	switch ($type) {
		case 'text':
			if ( isset( $label ) )
				$html .= "\t" . '<label for="' . $id . '">' . $label . '</label>' . "\n";

			$html .= "\t" . '<input type="text" id="' . $id . '" name="' . $prefix . '[' . $id . ']" class="' . $class . '" value="' . esc_attr( $field_value ) . '" >' . "\n";
			if ( isset( $desc ) )
				$html .= '<span class="description">' . esc_attr( $desc ) . '</span>' . "\n";
			break;

		case 'checkbox':
			if ( isset( $label ) )
				$html .= "\t" . '<label for="' . $id . '">' . $label . '</label>' . "\n";
			$html .= "\t" . '<input type="checkbox" id="' . $id . '" name="' . $prefix . '[' . $id . ']" value="' . $value . '"' . checked( $value, $field_value, false ) . ' >' . "\n";
			if ( isset( $desc ) )
				$html .= '<span class="description">' . esc_attr( $desc ) . '</span>' . "\n";
			break;

		case 'select':
			if ( isset( $label ) )
				$html .= "\t" . '<label for="' . $id . '">' . $label . '</label>' . "\n";
			$html .= "\t" . '<select id="' . $id . '" name="' . $prefix . '[' . $id . ']">';
			foreach ( $options as $value => $name ) {
				$html .= "\t\t" . '<option value="' . esc_attr( $value ) . '"' . selected( $value, $field_value, false ) . '>' . esc_attr( $name ) . '</option>' . "\n";
			}
			$html .= "\t" . '</select>' . "\n";
			if ( isset( $desc ) )
				$html .= '<span class="description">' . esc_attr( $desc ) . '</span>' . "\n";
			break;

		default:
			break;
	}

	$html .= $after . "\n";

	return $html;

}

function piwiktracking_display_settings_page() {

	echo '<div class="wrap piwiktracking-settings-page">' . "\n";

	screen_icon( );

	echo '<h2>' . __( 'Piwiktracking Settings', 'piwiktracking' ) . '</h2>' . "\n";

	settings_errors( );

	echo '<form class="piwiktracking-settings" method="post" action="options.php">' . "\n";

	settings_fields( 'piwiktracking_settings' );

	echo '<div class="ui-tabs">' . "\n";
	echo '<h2 class="nav-tab-wrapper">' . "\n";
	echo '<ul class="ui-tabs-nav">' . "\n";
	echo '<li><a href="#piwiktracking-settings-basic" class="nav-tab">Basic Settings</a></li>' . "\n";
	echo '<li><a href="#piwiktracking-settings-advanced" class="nav-tab">Advanced Settings</a></li>' . "\n";
	echo '</ul>' . "\n";
	echo '</h2>' . "\n";

	do_settings_sections( $_GET['page'] );

	submit_button( esc_attr__( 'Update Settings' ) );

	echo '</div>' . "\n";

	echo '</form>' . "\n";
	echo '</div>' . "\n";

}

function piwiktracking_validate_settings($input) {

	global $piwiktracking;

	$settings['piwikurl'] = empty( $input['piwikurl'] ) ? '' : trailingslashit( preg_replace( "/^https?:\/\/(.+)$/i", "\\1", $input['piwikurl'] ) );

	$settings['siteid'] = is_numeric( $input['siteid'] ) ? intval( $input['siteid'] ) : '';

	$checkbox_options = array( 'linktracking' );

	$settings['linktracking'] = isset( $input['linktracking'] ) ? true : false;

	foreach ( $piwiktracking -> roles_available as $role => $name ) {
		$settings['excludedroles'][$role] = isset( $input['excludedroles'][$role] ) ? true : false;
	}

	if ( array_key_exists( $input['trackingmode'], $piwiktracking -> trackingmodes ) ) {
		$settings['trackingmode'] = $input['trackingmode'];
	}

	return $settings;

}

function piwiktracking_admin_scripts() {

	//wp_enqueue_script( 'jquery-cookie', PIWIKTRACKING_URI . 'js/jquery.cookie.js', array( 'jquery' ) );
	wp_print_scripts( 'jquery-ui-tabs' );
	wp_enqueue_script( 'piwiktracking-admin-functions', PIWIKTRACKING_URI . 'js/admin-script.js', array( 'jquery' ), '1.0' );

}

function piwiktracking_admin_styles() {

	wp_enqueue_style( 'piwiktracking-style', PIWIKTRACKING_URI . 'css/admin-style.css', false, '1.0' );

}
?>