<?php
/**
 * @version 1.7.1
 * @author Technote
 * @since 1.0.0
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	exit;
}

return [

	'\Marker_Animation\Classes\Models\Assets' => [
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
		'admin_head-post.php'         => [
			'enqueue_editor_params',
		],
		'admin_head-post-new.php'     => [
			'enqueue_editor_params',
		],
		'mce_external_plugins'        => [
			'mce_external_plugins',
		],
		'mce_buttons'                 => [
			'mce_buttons',
		],
		'editor_stylesheets'          => [
			'editor_stylesheets',
		],
		'mce_css'                     => [
			'mce_css',
		],
		'enqueue_block_editor_assets' => [
			'enqueue_block_editor_assets',
		],
		'tiny_mce_before_init'        => [
			'tiny_mce_before_init' => 11,
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