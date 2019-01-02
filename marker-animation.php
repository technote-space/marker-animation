<?php
/*
Plugin Name: Marker Animation
Plugin URI: https://wordpress.org/plugins/marker-animation
Description: This plugin will add "Marker animation" function
Author: technote
Version: 1.4.1
Author URI: https://technote.space
Text Domain: marker-animation
Domain Path: /languages/
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

@require_once dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

Technote::get_instance( 'Marker_Animation', __FILE__ );
