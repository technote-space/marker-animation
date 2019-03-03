<?php
/**
 * @version 1.6.9
 * @author technote-space
 * @since 1.0.0
 * @since 1.2.5 Added: check develop version
 * @since 1.3.7 Changed: update info file url
 * @since 1.4.0 Changed: use custom post flag
 * @since 1.4.0 Changed: db version
 * @since 1.5.0 Changed: ライブラリの変更 (#37)
 * @since 1.6.0 Changed: Gutenbergへの対応 (#3)
 * @since 1.6.9 Deleted: update checker の設定を削除 (#87)
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	exit;
}

return [

	// db version
	'db_version'                     => '0.0.8',

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
];
