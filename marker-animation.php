<?php
/**
 * Plugin Name: Marker Animation
 * Plugin URI: https://github.com/technote-space/marker-animation
 * Description: This plugin will add "Marker animation" function
 * Author: Technote
 * Version: 2.2.5
 * Author URI: https://technote.space
 * Text Domain: marker-animation
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( defined( 'MARKER_ANIMATION' ) ) {
	return;
}

define( 'MARKER_ANIMATION', 'Marker_Animation' );

@require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

WP_Framework::get_instance( MARKER_ANIMATION, __FILE__ );
