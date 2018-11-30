<?php
/**
 * Technote Views Include Style Modal
 *
 * @version 1.1.13
 * @author technote
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license https://opensource.org/licenses/mit-license.html MIT License
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Traits\Presenter $instance */
?>

<style>
    .<?php $instance->id();?>-loading {
        background-size: contain;
        background: url(<?php echo $instance->get_img_url('loading.gif');?>) no-repeat;
        text-align: center;
        margin: 0 auto;
        height: 30px;
        width: 30px;
        display: inline-block;
        vertical-align: middle;
    }

    #<?php $instance->id();?>-modal {
        background: url(<?php echo $instance->get_img_url('back.png');?>);
        background-size: cover;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10000;
    }

    #<?php $instance->id();?>-modal .<?php $instance->id();?>-loading {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        margin-top: -15px;
        margin-left: -15px;
    }

    #<?php $instance->id();?>-modal .<?php $instance->id();?>-loading-message {
        display: none;
        position: fixed;
        top: 50%;
        color: white;
        margin-top: 25px;
        width: 100%;
        text-align: center;
        max-height: 90%;
    }

    #<?php $instance->id();?>-modal-message-wrap {
        position: fixed;
        display: inline-block;
        color: black;
        width: 100%;
        max-height: 90%;
        z-index: 10001;
        overflow-y: scroll;
        text-align: center;
        top: 50%;
    }

    #<?php $instance->id();?>-modal-message-wrap #<?php $instance->id();?>-modal-message {
        background: white;
        display: inline-block;
        color: black;
        padding: 20px;
    }
</style>
