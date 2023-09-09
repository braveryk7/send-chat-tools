<?php

declare(strict_types = 1);

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

function get_comment( $comment ) {
	$comment_data                       = new stdClass();
	$comment_data->comment_ID           = $comment;
	$comment_data->comment_post_ID      = '123';
	$comment_data->comment_author       = 'test user';
	$comment_data->comment_author_email = 'test@example.com';
	$comment_data->comment_author_url   = 'https://example.com';
	$comment_data->comment_author_IP    = '123.456.789.123';
	$comment_data->comment_date         = '2023-01-21 00:40:15';
	$comment_data->comment_date_gmt     = '2023-01-20 15:40:15';
	$comment_data->comment_content      = 'test comment';
	$comment_data->comment_karma        = 0;
	$comment_data->comment_approved     = '1';
	$comment_data->comment_agent        = '';
	$comment_data->comment_type         = '';
	$comment_data->comment_parent       = '';
	$comment_data->user_id              = 0;

	return $comment_data;
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
