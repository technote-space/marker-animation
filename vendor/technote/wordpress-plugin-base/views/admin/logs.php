<?php
/**
 * Technote Views Admin Logs
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
/** @var \Technote\Controllers\Admin\Base $instance */
/** @var array $action */
/** @var array $root */
/** @var array $search */
/** @var array $field */
/** @var array $data */
/** @var array $segments */
/** @var array $segments_scandir */
/** @var array $args */
/** @var bool $deleted */
?>

<div class="log">
	<?php if ( ! $deleted && isset( $field['name'] ) ): ?>
        <h3><?php $instance->h( $field['name'] ); ?></h3>
		<?php $instance->form( 'open', $args ); ?>
		<?php $instance->form( 'input/hidden', $args, [
			'name'  => 'path',
			'value' => $field['path'],
		] ); ?>
		<?php $instance->form( 'input/hidden', $args, [
			'name'  => 'name',
			'value' => $field['name'],
		] ); ?>
		<?php $instance->form( 'input/submit', $args, [
			'id'    => $instance->id( false ) . '-delete_log',
			'name'  => 'delete',
			'value' => 'Delete',
		] ); ?>
		<?php $instance->form( 'close', $args ); ?>
	<?php endif; ?>
    <table class="widefat striped">
        <tr>
            <th><?php $instance->h( 'Datetime', true ); ?></th>
            <th><?php $instance->h( 'Message', true ); ?></th>
        </tr>
		<?php if ( ! $deleted && isset( $field['name'] ) ): ?>
			<?php if ( false === $data ): ?>
                <tr>
                    <td rowspan="2">
						<?php $instance->h( 'Invalid log file.', true ); ?>
                    </td>
                </tr>
			<?php else: ?>
				<?php if ( empty( $data ) ): ?>
                    <tr>
                        <td rowspan="2">
                        </td>
                    </tr>
				<?php else: ?>
					<?php foreach ( $data as list( $a, $b ) ): ?>
                        <tr>
                            <td><?php $instance->h( $a ); ?></td>
                            <td><?php $instance->h( $b, false, true, false ); ?></td>
                        </tr>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php else: ?>
            <tr>
                <td rowspan="2">
					<?php $instance->h( 'Please select log file.', true ); ?>
                </td>
            </tr>
		<?php endif; ?>
    </table>
</div>
<div class="directory">
    <ul>
        <li>
			<?php $instance->url( add_query_arg( [
				'path' => null,
				'name' => null,
			], $action ), 'Logs', true ); ?>
            <ul>
				<?php foreach ( $root[0] as $dir ): ?>
					<?php if ( ! empty( $segments ) && $segments[0] === $dir ): ?>
						<?php $seg = ''; ?>
						<?php foreach ( $segments as $segment ): ?>
							<?php
							! empty( $seg ) and $seg .= '/';
							$seg .= $segment;
							?>
                            <li<?php if ( $seg === $field['path'] ): ?> class="selected"<?php endif; ?>>
							<?php $instance->url( add_query_arg( [
								'path' => $seg,
								'name' => null,
							], $action ), '+ ' . $segment ); ?>
                            <ul>
						<?php endforeach; ?>
						<?php foreach ( $search[0] as $search_dir ): ?>
                            <li>
								<?php $instance->url( add_query_arg( [
									'path' => $seg . '/' . $search_dir,
									'name' => null,
								], $action ), '+ ' . $search_dir ); ?>
                            </li>
						<?php endforeach; ?>
						<?php foreach ( $search[1] as $search_file ): ?>
                            <li<?php if ( ! $deleted && isset( $field['name'] ) && $search_file === $field['name'] ): ?> class="selected"<?php endif; ?>>
								<?php $instance->url( add_query_arg( [
									'path' => $seg,
									'name' => $search_file,
								], $action ), $search_file ); ?>
                            </li>
						<?php endforeach; ?>
						<?php foreach ( array_reverse( $segments ) as $segment ): ?>
                            </ul>
                            </li>
							<?php
							$seg = preg_replace( '#' . preg_quote( $segment ) . '$#', '', $seg );
							$seg = rtrim( $seg, '/' );
							?>
							<?php if ( isset( $segments_scandir[ $seg ] ) ): ?>
								<?php foreach ( $segments_scandir[ $seg ][0] as $segments_dir ): ?>
									<?php if ( $segment === $segments_dir ): continue; endif; ?>
                                    <li>
										<?php $instance->url( add_query_arg( [
											'path' => $seg . '/' . $segments_dir,
											'name' => null,
										], $action ), '+ ' . $segments_dir ); ?>
                                    </li>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php else: ?>
                        <li>
							<?php $instance->url( add_query_arg( [
								'path' => $dir,
								'name' => null,
							], $action ), '+ ' . $dir ); ?>
                        </li>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php foreach ( $root[1] as $file ): ?>
                    <li>
						<?php $instance->url( add_query_arg( [
							'path' => null,
							'name' => $file,
						], $action ), $file ); ?>
                    </li>
				<?php endforeach; ?>
            </ul>
        </li>
    </ul>
	<?php $instance->form( 'input/button', $args, [
		'class' => 'close_button',
		'name'  => 'close',
		'value' => 'Close',
	] ); ?>
</div>

