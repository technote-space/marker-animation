<?php
/**
 * Technote Views Admin Logs
 *
 * @version 2.7.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.7.0 Changed: save log to db
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Interfaces\Presenter $instance */
/** @var int $total */
/** @var int $total_page */
/** @var int $page */
/** @var int $offset */
/** @var int $start */
/** @var int $end */
/** @var string $p */
/** @var array $logs */
?>

<div class="log">
    <div class="summary">
        <div class="total"><?php $instance->h( 'Total: %d', true, true, true, $total ); ?></div>
        <div class="now"><?php $instance->h( '%d to %d', true, true, true, $start, $end ); ?></div>
		<?php $instance->get_view( 'admin/include/pagination', $args, true ); ?>
    </div>
    <table class="widefat striped">
        <tr>
            <th><?php $instance->h( 'No.', true ); ?></th>
            <th><?php $instance->h( 'Datetime', true ); ?></th>
            <th><?php $instance->h( 'Message', true ); ?></th>
            <th><?php $instance->h( 'Context', true ); ?></th>
        </tr>
		<?php if ( $total > 0 ): ?>
			<?php foreach ( $logs as $i => $log ) : ?>
                <tr>
                    <td><?php $instance->h( $offset + $i + 1 ); ?></td>
                    <td><?php $instance->h( $log['created_at'] ); ?></td>
                    <td><?php $instance->h( $log['message'] ); ?></td>
                    <td>
						<?php if ( isset( $log['context'] ) ): ?>
							<?php $instance->dump( @json_decode( $log['context'], true ) ); ?>
						<?php endif; ?>
                    </td>
                </tr>
			<?php endforeach; ?>
		<?php else: ?>
            <tr>
                <td colspan="3"><?php $instance->h( 'Item not found.', true ); ?></td>
            </tr>
		<?php endif; ?>
    </table>
	<?php if ( $total > 0 ) : ?>
		<?php $instance->get_view( 'admin/include/pagination', $args, true ); ?>
	<?php endif; ?>
</div>
