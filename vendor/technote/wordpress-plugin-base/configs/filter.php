<?php
/**
 * Technote Configs Filter
 *
 * @version 2.9.9
 * @author technote-space
 * @since 1.0.0
 * @since 2.4.0 Added: filter for upgrade
 * @since 2.6.0 Changed: call setup_update from admin_init filter
 * @since 2.6.1 Changed: filter for ajax api
 * @since 2.8.2 Changed: filter priority of admin_menu
 * @since 2.9.0 Added: filters for mail
 * @since 2.9.9 Changed: call upgrade from init filter
 * @since 2.9.13 Added: filter for shutdown
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
			'end_footer' => [ 999 ],
		],

		'wp_print_footer_scripts' => [
			'output_js'  => [ 999 ],
			'output_css' => [ 998 ],
			'end_footer' => [ 999 ],
		],
		'wp_print_styles'         => [
			'output_css' => [ 999 ],
		],
	],

	'db'   => [
		'switch_blog' => [
			'switch_blog' => [],
		],
	],

	/**
	 * @since 2.9.13
	 */
	'log'  => [
		'${prefix}app_initialize' => [
			'setup_shutdown' => [],
		],
	],

	/**
	 * @since 2.9.0
	 */
	'mail' => [
		'wp_mail_failed'    => [
			'wp_mail_failed' => [],
		],
		'wp_mail_from'      => [
			'wp_mail_from' => [],
		],
		'wp_mail_from_name' => [
			'wp_mail_from_name' => [],
		],
	],

	'uninstall' => [
		'${prefix}app_activated' => [
			'register_uninstall' => [],
		],
	],

	/**
	 * @since 2.4.0
	 */
	'upgrade'   => [
		/**
		 * @since 2.9.9
		 */
		'init'       => [
			'upgrade' => [],
		],
		/**
		 * @since 2.6.0
		 */
		'admin_init' => [
			'setup_update' => [],
		],
	],

	'loader->admin' => [
		'admin_menu'    => [
			'add_menu'  => [ 9 ],
			'sort_menu' => [ 11 ],
		],
		'admin_notices' => [
			'admin_notice' => [],
		],
	],

	'loader->api' => [
		'rest_api_init'     => [
			'register_rest_api' => [],
		],
		'admin_init'        => [
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