<?php
/**
 * @version 1.5.0
 * @author Technote
 * @since 1.4.0
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var array $details */
?>
<table>
    <tr>
        <td>
            <table class="widefat striped">
				<?php foreach ( $details as $name => $value ): ?>
                    <tr>
                        <th><?php $instance->h( $name, true ); ?></th>
                        <td><?php $instance->h( $value ); ?></td>
                    </tr>
				<?php endforeach; ?>
            </table>
        </td>
    </tr>
</table>
