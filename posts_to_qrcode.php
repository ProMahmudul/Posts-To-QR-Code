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

$pqrc_countries = array(
	__( 'Afghanistan', 'posts-to-qrcode' ),
	__( 'Bangladesh', 'posts-to-qrcode' ),
	__( 'Bhutan', 'posts-to-qrcode' ),
	__( 'India', 'posts-to-qrcode' ),
	__( 'Maldives', 'posts-to-qrcode' ),
	__( 'Nepal', 'posts-to-qrcode' ),
	__( 'Pakistan', 'posts-to-qrcode' ),
	__( 'Sri Lanka', 'posts-to-qrcode' )
);

function pqrc_init() {
	global $pqrc_countries;
	$pqrc_countries = apply_filters( 'pqrc_countries', $pqrc_countries );
}

add_action( 'init', 'pqrc_init' );

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

	add_settings_field( 'pqrc_height', __( 'QR Code Height', 'posts-to-qrcode' ), 'pqrc_display_field', 'general', 'pqrc_section', array( 'pqrc_height' ) );
	add_settings_field( 'pqrc_width', __( 'QR Code Width', 'posts-to-qrcode' ), 'pqrc_display_field', 'general', 'pqrc_section', array( 'pqrc_width' ) );
//	add_settings_field( 'pqrc_extra', __( 'Extra Fields', 'posts-to-qrcode' ), 'pqrc_display_field', 'general', 'pqrc_section', array( 'pqrc_extra' ) );
	add_settings_field( 'pqrc_select', __( 'Dropdown', 'posts-to-qrcode' ), 'pqrc_display_select_field', 'general', 'pqrc_section' );
	add_settings_field( 'pqrc_checkbox', __( 'Select Countries', 'posts-to-qrcode' ), 'pqrc_display_checkbox_field', 'general', 'pqrc_section' );
	add_settings_field( 'pqrc_toggle', __( 'Toggle Field', 'posts-to-qrcode' ), 'pqrc_display_toggle_field', 'general', 'pqrc_section' );

	register_setting( 'general', 'pqrc_height', array( 'sanitize_callback' => 'esc_attr' ) );
	register_setting( 'general', 'pqrc_width', array( 'sanitize_callback' => 'esc_attr' ) );
//	register_setting( 'general', 'pqrc_extra', array( 'sanitize_callback' => 'esc_attr' ) );
	register_setting( 'general', 'pqrc_select', array( 'sanitized_callback' => 'esc_attr' ) );
	register_setting( 'general', 'pqrc_checkbox' );
	register_setting( 'general', 'pqrc_toggle' );
}

function pqrc_display_toggle_field() {
	$option = get_option( 'pqrc_toggle' );
	echo '<div id="toggle1"></div>';
	echo "<input type='hidden' name='pqrc_toggle' id='pqrc_toggle' value='{$option}'>";
}

function pqrc_display_checkbox_field() {
	global $pqrc_countries;
	$option = get_option( 'pqrc_checkbox' );

	foreach ( $pqrc_countries as $country ) {
		$selected = '';
		if ( is_array( $option ) && in_array( $country, $option ) ) {
			$selected = 'checked';
		}
		printf( '<input type="checkbox" name="pqrc_checkbox[]" value="%s" %s/>%s<br>', $country, $selected, $country );
	}

}

function pqrc_display_select_field() {
	global $pqrc_countries;
	$option = get_option( 'pqrc_select' );


	printf( '<select id="%s" name="%s">', 'pqrc_select', 'pqrc_select' );
	foreach ( $pqrc_countries as $country ) {
		$selected = '';
		if ( $option == $country ) {
			$selected = 'selected';
		}
		printf( '<option value="%s" %s>%s</option>', $country, $selected, $country );
	}
	echo '</select>';
}

function pqrc_section_callback() {
	echo "<p>" . __( 'Settings for posts to QR Plugin', 'posts-to-qrcode' ) . "</p>";
}

function pqrc_display_field( $args ) {
	$options = get_option( $args[0] );
	printf( '<input type="text" id="%s" name="%s" value="%s"/>', $args[0], $args[0], $options );
}

add_action( "admin_init", "pqrc_settings_init" );

function pqrc_assets( $screen ) {
	if ( 'options-general.php' == $screen ) {
		wp_enqueue_style( 'pqrc-mini-toggle-css', plugin_dir_url( __FILE__ ) . "assets/css/minitoggle.css" );
		wp_enqueue_script( 'pqrc-mini-toggle-js', plugin_dir_url( __FILE__ ) . "assets/js/minitoggle.js", array( 'jquery' ), "1.0", true );
		wp_enqueue_script( 'pqrc-main-js', plugin_dir_url( __FILE__ ) . "assets/js/pqrc-main.js", array( 'jquery' ), time(), true );
	}
}

add_action( 'admin_enqueue_scripts', 'pqrc_assets' );