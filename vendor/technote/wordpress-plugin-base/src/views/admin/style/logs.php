<?php
/**
 * Technote Views Admin Style Logs
 *
 * @version 2.7.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.7.0
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
    #<?php $instance->id();?>-main-contents .summary {
        display: inline-block;
        padding: 4px;
        margin: 5px 0;
        background: #eee;
        border: 1px solid #aaa;
    }

    #<?php $instance->id();?>-main-contents .summary > div {
        padding: 3px;
    }

    #<?php $instance->id();?>-main-contents .summary .total {
        font-size: 1.2em;
        font-weight: bold;
    }
</style>
