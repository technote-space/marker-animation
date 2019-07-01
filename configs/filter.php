<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	exit;
}

return [

	'\Marker_Animation\Classes\Models\Assets'              => [
		'template_redirect'         => [
			'setup_assets',
		],
		'${prefix}changed_option'   => [
			'changed_option',
		],
		'${prefix}app_activated'    => [
			'clear_options_cache',
		],
		'upgrader_process_complete' => [
			'clear_options_cache',
		],
	],
	'\Marker_Animation\Classes\Models\Editor'              => [
		'enqueue_block_editor_assets' => [
			'enqueue_block_editor_assets',
		],
	],
	'\Marker_Animation\Classes\Models\Custom_Post\Setting' => [
		'${prefix}app_activated' => [
			'insert_presets',
		],
		'load-edit.php'          => [
			'setup_assets',
		],
	],
];
