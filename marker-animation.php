<?php
/*
Plugin Name: Marker Animation
Plugin URI: https://wordpress.org/plugins/marker-animation
Description: This plugin will add "Marker animation" function
Author: technote
Version: 1.6.0
Author URI: https://technote.space
Text Domain: marker-animation
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

@require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

define( 'MARKER_ANIMATION', 'Marker_Animation' );

WP_Framework::get_instance( MARKER_ANIMATION, __FILE__ );
