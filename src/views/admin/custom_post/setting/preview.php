<?php
/**
 * @version 1.4.0
 * @author technote-space
 * @since 1.4.0
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var array $attributes */
/** @var array $details */
?>
<table>
    <tr>
        <td>
            <span class="marker-animation-preview" <?php $instance->h( implode( ' ', $attributes ), false, true, false ); ?>><?php $instance->h( 'Marker Animation', true ); ?></span>
        </td>
    </tr>
    <tr>
        <td>
            <table class="widefat striped">
				<?php foreach ( $details as $name => $value ): ?>
                    <tr>
                        <th><?php $instance->h( $name ); ?></th>
                        <td><?php $instance->h( $value ); ?></td>
                    </tr>
				<?php endforeach; ?>
            </table>
        </td>
    </tr>
</table>
