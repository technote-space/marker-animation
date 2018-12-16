<?php
/**
 * Technote Views Admin Style Table
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
    #<?php $instance->id();?>-main-contents table .<?php $instance->id(); ?>-td-0 {
        background: #e0e0e0 !important;
    }

    #<?php $instance->id();?>-main-contents table .<?php $instance->id(); ?>-td-1 {
        background: #eaeaea !important;
    }
</style>
