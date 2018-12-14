<?php
/**
 * @version 1.2.7
 * @author technote-space
 * @since 1.0.0
 * @since 1.2.0
 * @since 1.2.6 Deleted: block_editor_preload_paths filter
 * @since 1.2.7 Added: filter to clear cache
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

return [

	'\Marker_Animation\Classes\Models\Assets' => [
		'template_redirect' => [
			'setup_assets' => [],
		],
		/**
		 * @since 1.2.7
		 */
		'${prefix}changed_option'  => [
			'changed_option' => [],
		],
	],

	'\Marker_Animation\Classes\Models\Editor' => [
		'admin_head-post.php'         => [
			'enqueue_editor_params' => [],
		],
		'admin_head-post-new.php'     => [
			'enqueue_editor_params' => [],
		],
		'mce_external_plugins'        => [
			'mce_external_plugins' => [],
		],
		'mce_buttons'                 => [
			'mce_buttons' => [],
		],
		'editor_stylesheets'          => [
			'editor_stylesheets' => [],
		],
		'mce_css'                     => [
			'mce_css' => [],
		],
		/**
		 * @since 1.1.9
		 */
		'enqueue_block_editor_assets' => [
			'enqueue_block_editor_assets' => [],
		],
	],
];