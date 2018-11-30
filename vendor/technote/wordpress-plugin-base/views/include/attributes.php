<?php
/**
 * Technote Views Include Attributes
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
/** @var array $attributes */
?>
<?php if ( ! empty( $attributes ) && is_array( $attributes ) ): ?>
	<?php foreach ( $attributes as $k => $v ): ?>
		<?php $instance->h( $k ); ?>="<?php $instance->h( $v, ! empty( $translate ) && is_array( $translate ) && in_array( $k, $translate ) ); ?>"
	<?php endforeach; ?>
<?php endif; ?>
