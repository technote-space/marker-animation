<?php
/**
 * @author Technote
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
$instance->add_style_view( 'admin/style/dashboard', $args );
?>
<style>
	.marker-setting-preview {
		font-size: 1.5em;
		line-height: 2em;
	}
</style>
