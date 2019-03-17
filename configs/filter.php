<?php
/**
 * @version 1.7.1
 * @author Technote
 * @since 1.0.0
 * @since 1.2.0
 * @since 1.2.6 Deleted: block_editor_preload_paths filter
 * @since 1.2.7 Added: filter to clear cache
 * @since 1.3.0 Added: filter to clear cache
 * @since 1.3.0 Added: preset color
 * @since 1.4.0 Added: filter of marker setting
 * @since 1.5.0 Changed: trivial change
 * @since 1.6.6 Changed: trivial change
 * @since 1.7.1 #102
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
		/**
		 * @since 1.2.7
		 */
		'${prefix}changed_option'   => [
			'changed_option',
		],
		/**
		 * @since 1.3.0
		 */
		'${prefix}app_activated'    => [
			'clear_options_cache',
		],
		/**
		 * @since 1.3.0
		 */
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
		/**
		 * @since 1.1.9
		 */
		'enqueue_block_editor_assets' => [
			'enqueue_block_editor_assets',
		],
		/**
		 * @since 1.3.0
		 */
		'tiny_mce_before_init'        => [
			'tiny_mce_before_init' => 11,
		],
	],

	/**
	 * @since 1.4.0
	 */
	'\Marker_Animation\Classes\Models\Custom_Post\Setting' => [
		/**
		 * @since 1.7.1
		 */
		'${prefix}app_activated' => [
			'insert_presets',
		],
		'load-edit.php'          => [
			'setup_assets',
		],
	],
];