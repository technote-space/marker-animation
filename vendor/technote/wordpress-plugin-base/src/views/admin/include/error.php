<?php
/**
 * Technote Views Admin Include Error
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
/** @var string $message */
?>
<div class="wrap cf-wrap">
    <div class="icon32 icon32-error"><br/></div>
    <h2>Error</h2>
    <div class="error">
        <p>
			<?php $instance->h( $message, true ); ?>
        </p>
    </div>
</div>