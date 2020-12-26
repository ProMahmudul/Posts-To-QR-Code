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
	$image_src          = sprintf( "https://api.qrserver.com/v1/create-qr-code/?size=185x185&data=%s", $current_post_url );

	$content .= sprintf( "<div class='qrcode'><img src='%s' alt='%s'/> </div>", $image_src, $current_post_title );

	return $content;
}

add_filter( "the_content", "pqrc_display_qr_code" );