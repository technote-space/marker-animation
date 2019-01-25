<?php
/**
 * @version 1.4.0
 * @author technote-space
 * @since 1.0.0
 * @since 1.2.5 Added: check develop version
 * @since 1.3.7 Changed: update info file url
 * @since 1.4.0 Changed: use custom post flag
 * @since 1.4.0 Changed: db version
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

return [

	// db version
	'db_version'                     => '0.0.7',

	// twitter
	'twitter'                        => 'technote15',

	// github
	'github'                         => 'technote-space',

	// contact url
	'contact_url'                    => 'https://technote.space/contact/',

	// menu image url
	'menu_image'                     => 'icon-24x24.png',

	// update
	'update_info_file_url'           => 'https://raw.githubusercontent.com/technote-space/marker-animation/develop/update.json',

	// use custom post
	'use_custom_post'                => true,

	// suppress setting help contents
	'suppress_setting_help_contents' => true,

	// setting page title
	'setting_page_title'             => 'Detail Settings',

	// setting page priority
	'setting_page_priority'          => 100,

	// setting page slug
	'setting_page_slug'              => 'dashboard',
];
