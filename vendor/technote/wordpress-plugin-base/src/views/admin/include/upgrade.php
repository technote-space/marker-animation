<?php
/**
 * Technote Views Admin Include Upgrade
 *
 * @version 2.10.0
 * @author technote-space
 * @since 2.4.1
 * @since 2.10.0 Improved: multiple version
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Traits\Presenter $instance */
/** @var array $notices */
?>
<div style="font-weight: normal;overflow:auto">
    <ul style="list-style: disc; margin-left: 20px; margin-top:0;">
		<?php foreach ( $notices as $index => $notice ): ?>
			<?php if ( empty( $notice ) ) {
				continue;
			} ?>
			<?php if ( is_array( $notice ) ): ?>
                <li style="margin:10px 0 0;"><span style="font-size: 1.2em; line-height: 1.3em; font-weight: bold;"><?php $instance->h( 'v' . $index, true ); ?></span>
                    <ul style="list-style: circle; margin-left: 10px;">
						<?php foreach ( $notice as $item ): ?>
                            <li style="margin: 0"><?php $instance->h( $item, true ); ?></li>
						<?php endforeach; ?>
                    </ul>
                </li>
			<?php else: ?>
                <li style="margin: 0"><?php $instance->h( $notice, true ); ?></li>
			<?php endif; ?>
		<?php endforeach; ?>
    </ul>
</div>
