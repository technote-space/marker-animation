<?php
/**
 * @version 1.0.0
 * @author technote
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

return [

	'\Marker_Animation\Models\Assets' => [
		'wp_enqueue_scripts' => [
			'wp_enqueue_scripts' => [],
		],
	],

	'\Marker_Animation\Models\Editor' => [
		'admin_head-post.php'     => [
			'enqueue_editor_params' => [],
		],
		'admin_head-post-new.php' => [
			'enqueue_editor_params' => [],
		],
		'mce_external_plugins'    => [
			'mce_external_plugins' => [],
		],
		'mce_buttons'             => [
			'mce_buttons' => [],
		],
		'editor_stylesheets'      => [
			'editor_stylesheets' => [],
		],
	],
];