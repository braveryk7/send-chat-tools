<?php
function get_option( $str ) {
	$value = [];

	if ( '_site_transient_theme_roots' === $str ) {
		$value['my-theme'] = '';
	} elseif ( 'active_plugins' ) {
		$value[] = 'my-plugin/my-plugin.php';
	}

	return $value;
}

function add_action() {
	return true;
}
