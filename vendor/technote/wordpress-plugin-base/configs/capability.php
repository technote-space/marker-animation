<?php
/**
 * Technote Configs Capability
 *
 * @version 1.1.13
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

	// user can
	'default_user'     => 'manage_options',

	// admin menu
	'admin_menu'       => 'manage_options',

	// admin
	'admin_capability' => 'manage_options',

];