<?php
/**
 * @version 1.1.1
 * @author technote
 * @since 1.0.8
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Interfaces\Presenter $instance */
?>

<style>
    i.highlight-icon {
        background-image: url('<?php $instance->h($instance->get_img_url('icon-128x128.png'));?>');
        background-repeat: no-repeat;
        background-size: contain;
    }
</style>