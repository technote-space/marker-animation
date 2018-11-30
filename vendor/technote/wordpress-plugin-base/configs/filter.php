<?php
/**
 * Technote Configs Filter
 *
 * @version 1.1.39
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

return [

	'minify' => [
		'admin_print_footer_scripts' => [
			'output_js' => [ 999 ],
		],
		'admin_head'                 => [
			'output_css' => [ 999 ],
		],
		'admin_footer'               => [
			'output_css' => [ 999 ],
		],

		'wp_print_footer_scripts' => [
			'output_js'  => [ 999 ],
			'output_css' => [ 998 ],
		],
		'wp_print_styles'         => [
			'output_css' => [ 999 ],
		],
	],

	'db' => [
		'switch_blog' => [
			'switch_blog' => [],
		],
	],

	'uninstall' => [
		'${prefix}app_activated' => [
			'register_uninstall' => [],
		],
	],

	'loader->admin' => [
		'admin_menu'    => [
			'add_menu' => [],
		],
		'admin_notices' => [
			'admin_notice' => [],
		],
	],

	'loader->api' => [
		'rest_api_init'     => [
			'register_rest_api' => [],
		],
		'init'              => [
			'register_ajax_api' => [],
		],
		'wp_footer'         => [
			'register_script' => [],
		],
		'admin_footer'      => [
			'register_script' => [],
		],
		'rest_pre_dispatch' => [
			'rest_pre_dispatch' => [ 999 ],
		],
	],

];