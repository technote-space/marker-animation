<?php
/**
 * @version 1.5.0
 * @author technote-space
 * @since 1.0.8
 * @since 1.5.0 Changed: trivial change
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
?>

<style>
    i.highlight-icon {
        background-image: url('<?php $instance->h($instance->get_img_url('icon-128x128.png'));?>');
        background-repeat: no-repeat;
        background-size: contain;
    }
</style>