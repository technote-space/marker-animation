<?php
/**
 * @version 1.1.4
 * @author technote-space
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
		'template_redirect' => [
			'setup_assets' => [],
		],
	],

	'\Marker_Animation\Models\Editor' => [
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
		'enqueue_block_editor_assets' => [
			'enqueue_block_editor_assets' => [],
		],
	],
];