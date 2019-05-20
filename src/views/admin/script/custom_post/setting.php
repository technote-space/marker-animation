<?php
/**
 * @version 1.7.4
 * @author Technote
 * @since 1.4.0
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

use WP_Framework_Presenter\Interfaces\Presenter;

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	return;
}
/** @var Presenter $instance */
/** @var array $args */
$instance->add_script_view( 'admin/script/dashboard', $args );
