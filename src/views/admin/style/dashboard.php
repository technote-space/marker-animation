<?php
/**
 * @version 1.1.3
 * @author technote-space
 * @since 1.0.0
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
    .marker-setting-preview {
        display: inline-block;
        margin: 10px;
        padding: 10px;
        font-size: 1.3em;
        line-height: 1.5em;
        background: white;
        border: 1px solid #CCC;
    }

    #<?php $instance->id(); ?>-main-contents input[type="submit"].button-primary.left {
        margin-right: 10px;
    }
</style>