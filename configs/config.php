<?php
/**
 * @version 2.0.0
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

	// required wordpress version
	'required_wordpress_version'     => '5.2',

	// db version
	'db_version'                     => '0.1.0',

	// menu image url
	'menu_image'                     => 'icon-24x24.png',

	// suppress setting help contents
	'suppress_setting_help_contents' => true,

	// setting page title
	'setting_page_title'             => 'Detail Settings',

	// setting page priority
	'setting_page_priority'          => 100,

	// setting page slug
	'setting_page_slug'              => 'dashboard',

	// detail url
	'detail_url'                     => 'https://technote.space/marker-animation',

	// twitter
	'twitter'                        => 'technote15',

	// github repo
	'github_repo'                    => 'technote-space/marker-animation',
];
