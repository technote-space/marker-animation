<?php
/**
 * @version 1.0.0
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}

add_filter( 'marker_animation-get_help_contents', function ( $contents, $slug ) {
	if ( 'setting' === $slug ) {
		return [];
	}

	return $contents;
}, 10, 2 );

add_filter( 'marker_animation-setting_page_title', function () {
	return 'Detail Settings';
} );

add_filter( 'marker_animation-setting_page_priority', function () {
	return 100;
} );

add_filter( 'marker_animation-get_setting_menu_slug', function () {
	return 'dashboard';
} );
