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

function admin_url(): string {
	return 'https://expamle.com/';
}

function get_bloginfo( $show ) {
	return match ( $show ) {
		'name' => 'Test Blog Site',
		'url'  => 'https://www.example.com',
	};
}

function get_permalink( $id ) {
	return 'https://www.example.com/my-post';
}

function get_the_title( $id ) {
	return 'Test article';
}

function do_action() {
	return true;
}

function add_filter() {
	return true;
}

function plugin_basename() {
	return true;
}

function __( $value ) {
	return $value;
}

function esc_html( $value ) {
	return $value;
}

function esc_html__( $value ) {
	return $value;
}

function register_activation_hook() {
	return true;
}
