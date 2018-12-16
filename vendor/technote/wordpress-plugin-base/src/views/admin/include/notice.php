<?php
/**
 * Technote Views Admin Include Notice
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
/** @var array $messages */
?>
<?php if ( ! empty( $messages ) ): ?>
	<?php foreach ( $messages as $group => $m1 ): ?>
		<?php foreach ( $m1 as $class => $m2 ): ?>
            <div class="<?php $instance->h( $class ); ?> <?php $instance->id(); ?>-admin-message">
                <ul>
					<?php foreach ( $m2 as list( $m, $escape ) ): ?>
                        <li><p><?php $instance->h( $m, true, true, $escape ); ?></p></li>
					<?php endforeach; ?>
                </ul>
            </div>
		<?php endforeach; ?>
	<?php endforeach; ?>
<?php endif; ?>