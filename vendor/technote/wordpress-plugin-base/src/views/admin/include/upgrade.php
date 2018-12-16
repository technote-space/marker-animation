<?php
/**
 * Technote Views Admin Include Upgrade
 *
 * @version 2.4.1
 * @author technote-space
 * @since 2.4.1
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
            <li style="margin: 0"><?php $instance->h( $notice, true ); ?></li>
		<?php endforeach; ?>
    </ul>
</div>
