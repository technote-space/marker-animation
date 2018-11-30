<?php
/**
 * Technote Views Admin Include Exception
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
/** @var \Exception $e */
?>
<div class="wrap cf-wrap">
    <div class="icon32 icon32-error"><br/></div>
    <h2>Error</h2>
    <div class="error">
        <h3>
			<?php $instance->h( $e->getMessage() ); ?>
        </h3>
        <p>
			<?php echo nl2br( $e->getTraceAsString() ); ?>
        </p>
    </div>
</div>