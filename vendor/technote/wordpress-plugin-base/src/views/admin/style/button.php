<?php
/**
 * Technote Views Admin Style Button
 *
 * @version 1.1.13
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Traits\Presenter $instance */
?>
<style>
    #<?php $instance->id();?>-main-contents input[type="button"],
    #<?php $instance->id();?>-main-contents input[type="file"],
    #<?php $instance->id();?>-main-contents input[type="reset"],
    #<?php $instance->id();?>-main-contents input[type="submit"] {
        min-width: 100px;
        border: solid 2px #727272;
        box-shadow: #aaa 2px 2px 1px 1px;
        cursor: pointer;
        padding: 5px 30px;
        margin: 10px 0;
        height: auto;
        border-radius: 0;
    }

    #<?php $instance->id();?>-main-contents .button-primary {
        float: right;
        margin-top: 10px !important;
    }

    #<?php $instance->id();?>-main-contents .button-primary.left {
        float: none;
    }
</style>
