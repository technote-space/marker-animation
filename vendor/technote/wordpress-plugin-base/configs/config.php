<?php
/**
 * Technote Configs Config
 *
 * @version 2.0.2
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
	// library version
	'library_version'           => '2.0.3',

	// plugin title
	'plugin_title'              => '',

	// contact url
	'contact_url'               => '',

	// twitter
	'twitter'                   => '',

	// github
	'github'                    => '',

	// db version
	'db_version'                => '0.0.0',

	// update
	'update_info_file_url'      => '',

	// text domain
	'text_domain'               => '',

	// menu image url
	'menu_image'                => '',

	// log dir
	'log_dir'                   => '${Y}/${m}',

	// log name
	'log_name'                  => '${d}',

	// log extension
	'log_extension'             => 'txt',

	// api version
	'api_version'               => 'v1',

	// default delete rule
	'default_delete_rule'       => 'physical',

	// cache filter result
	'cache_filter_result'       => true,

	// cache filter exclude list
	'cache_filter_exclude_list' => [],
];