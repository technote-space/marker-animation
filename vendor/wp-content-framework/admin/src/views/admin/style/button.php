<?php
/**
 * WP_Framework_Admin Views Admin Style Button
 *
 * @version 0.0.1
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
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
