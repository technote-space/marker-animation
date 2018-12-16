<?php
/**
 * Technote Views Admin Style Admin Logs
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
    #<?php $instance->id();?>-main-contents .log {
        display: inline-block;
    }

    #<?php $instance->id();?>-main-contents .directory {
        position: fixed;
        display: inline-block;
        background-color: bisque;
        border: #0000cc solid 1px;
        padding: 10px;
        font-size: 20px;
        line-height: 1em;
        right: 10px;
        top: 50px;
    }

    #<?php $instance->id();?>-main-contents ul li ul {
        margin-top: .5em;
        margin-bottom: .75em;
        margin-left: 48px;
    }

    #<?php $instance->id();?>-main-contents ul li ul li {
        margin-bottom: .25em;
        position: relative;
    }

    #<?php $instance->id();?>-main-contents ul li ul li::before {
        content: "";
        position: absolute;
        top: -0.5em;
        left: -16px;
        width: 10px;
        height: calc(100% + .75em);
        border-left: 1px solid #3972b2;
    }

    #<?php $instance->id();?>-main-contents ul li ul li:last-child::before {
        height: calc(1em + .25em);
    }

    #<?php $instance->id();?>-main-contents ul li ul li::after {
        content: "";
        position: absolute;
        top: .75em;
        left: -16px;
        width: 10px;
        border-bottom: 1px solid #3972b2;
    }

    #<?php $instance->id();?>-main-contents li > a {
        text-decoration: none;
    }

    #<?php $instance->id();?>-main-contents li.selected > a {
        text-decoration: underline;
    }

    #<?php $instance->id();?>-main-contents .close_button {
        float: right;
    }
</style>
