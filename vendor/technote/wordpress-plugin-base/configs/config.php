<?php
/**
 * Technote Configs Config
 *
 * @version 2.10.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.1.0 Deleted: text_domain
 * @since 2.7.0 Added: prevent use log flag
 * @since 2.8.1 Added: use custom post flag
 * @since 2.8.1 Added: use social login flag
 * @since 2.8.5 Added: capture shutdown flag
 * @since 2.9.0 Added: target error flag
 * @since 2.9.0 Added: log level settings
 * @since 2.9.6 Added: prior default flag
 * @since 2.9.9 Added: readme file check url
 * @since 2.9.12 Added: setting page settings
 * @since 2.9.12 Added: suppress log messages setting
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

return [
	// library version
	'library_version'                => '2.10.0',

	// plugin title
	'plugin_title'                   => '',

	// contact url
	'contact_url'                    => '',

	// twitter
	'twitter'                        => '',

	// github
	'github'                         => '',

	// db version
	'db_version'                     => '0.0.1',

	// update
	'update_info_file_url'           => '',

	/**
	 * @since 2.9.9
	 */
	// readme
	'readme_file_check_url'          => '',

	// menu image url
	'menu_image'                     => '',

	// api version
	'api_version'                    => 'v1',

	// default delete rule
	'default_delete_rule'            => 'physical',

	/**
	 * @since 2.9.6
	 */
	// prior default (to nullable)
	'prior_default'                  => false,

	// cache filter result
	'cache_filter_result'            => true,

	// cache filter exclude list
	'cache_filter_exclude_list'      => [],

	// prevent use log
	'prevent_use_log'                => false,

	// use custom post
	'use_custom_post'                => false,

	// use social login
	'use_social_login'               => false,

	/**
	 * @since 2.8.5
	 */
	// capture shutdown error
	'capture_shutdown_error'         => defined( 'WP_DEBUG' ) && WP_DEBUG,

	/**
	 * @since 2.9.0
	 */
	// target shutdown error
	'target_shutdown_error'          => E_ALL & ~E_NOTICE & ~E_WARNING,

	/**
	 * @since 2.9.0
	 */
	// log level (for developer)
	'log_level'                      => [
		'error' => [
			'is_valid_log'  => true,
			'is_valid_mail' => true,
			'roles'         => [
				// 'administrator',
			],
			'emails'        => [
				// 'test@example.com',
			],
		],
		'info'  => [
			'is_valid_log'  => true,
			'is_valid_mail' => false,
			'roles'         => [
				// 'administrator',
			],
			'emails'        => [
				// 'test@example.com',
			],
		],
		// set default level
		''      => 'info',
	],

	/**
	 * @since 2.9.12
	 */
	// suppress setting help contents
	'suppress_setting_help_contents' => false,

	/**
	 * @since 2.9.12
	 */
	// setting page title
	'setting_page_title'             => 'Dashboard',

	/**
	 * @since 2.9.12
	 */
	// setting page priority
	'setting_page_priority'          => 0,

	/**
	 * @since 2.9.12
	 */
	// setting page slug
	'setting_page_slug'              => 'setting',

	/**
	 * @since 2.9.12
	 */
	// suppress log messages
	'suppress_log_messages'          => [
		'Non-static method WP_Feed_Cache::create() should not be called statically',
	],
];