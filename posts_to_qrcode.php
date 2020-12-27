<?php
/**
 * Plugin Name:       Posts To QR Code
 * Plugin URI: https://mahmudulhassan.me
 * Description: Show QR Code for any WordPress posts.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Mahmudul Hassan
 * Author URI:        https://mahmudulhassan.me/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       posts-to-qrcode
 * Domain Path:       /languages
 */

function posts_to_qrcode_textdoamin() {
	load_plugin_textdomain( 'posts-to-qrcode', false, dirname( __FILE__ ) . "/languages" );
}

add_action( "plugins_loaded", "posts_to_qrcode_textdoamin" );

function pqrc_display_qr_code( $content ) {
	$current_post_id    = get_the_ID();
	$current_post_title = get_the_title( $current_post_id );
	$current_post_url   = urlencode( get_the_permalink( $current_post_id ) );
	$current_post_type  = get_post_type( $current_post_id );

	//Post Type Check

	$excluded_post_types = apply_filters( 'pqrc_excloded_post_types', array() );
	if ( in_array( $current_post_type, $excluded_post_types ) ) {
		return $content;
	}

	//Dimension Hook
	$height = get_option( 'pqrc_height' );
	$width  = get_option( 'pqrc_width' );

	$height = $height ? $height : 185;
	$width  = $width ? $width : 185;

	$dimension = apply_filters( 'pqrc_qrcode_dimension', "{$width}x{$height}" );

	//Image Attributes
	$image_attributes = apply_filters( 'pqrc_image_attributes', null );

	$image_src = sprintf( "https://api.qrserver.com/v1/create-qr-code/?size=%s&data=%s", $dimension, $current_post_url );
	$content   .= sprintf( "<div class='qrcode'><img src='%s' %s alt='%s'/> </div>", $image_src, $image_attributes, $current_post_title );

	return $content;
}

add_filter( "the_content", "pqrc_display_qr_code" );

function pqrc_settings_init() {
	add_settings_section( 'pqrc_section', __( 'Posts to QR Code', 'posts-to-qrcode' ), 'pqrc_section_callback', 'general' );

	add_settings_field( 'pqrc_height', __( 'QR Code Height', 'posts-to-qrcode' ), 'pqrc_display_height', 'general', 'pqrc_section' );
	add_settings_field( 'pqrc_width', __( 'QR Code Width', 'posts-to-qrcode' ), 'pqrc_display_width', 'general', 'pqrc_section' );

	register_setting( 'general', 'pqrc_height', array( 'sanitize_callback' => 'esc_attr' ) );
	register_setting( 'general', 'pqrc_width', array( 'sanitize_callback' => 'esc_attr' ) );
}

function pqrc_section_callback() {
	echo "<p>" . __( 'Settings for posts to QR Plugin', 'posts-to-qrcode' ) . "</p>";
}

function pqrc_display_height() {
	$height = get_option( 'pqrc_height' );
	printf( '<input type="text" id="%s" name="%s" value="%s"/>', 'pqrc_height', 'pqrc_height', $height );
}

function pqrc_display_width() {
	$height = get_option( 'pqrc_width' );
	printf( '<input type="text" id="%s" name="%s" value="%s"/>', 'pqrc_width', 'pqrc_width', $height );
}

add_action( "admin_init", "pqrc_settings_init" );