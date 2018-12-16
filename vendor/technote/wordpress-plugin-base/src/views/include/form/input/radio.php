<?php
/**
 * Technote Views Include Form Input Radio
 *
 * @version 1.1.31
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
/** @var string $id */
/** @var string $label */
/** @var array $args */
?>
<?php if ( isset( $label ) ): ?>
    <label <?php if ( isset( $id ) ): ?>for="<?php $instance->h( $id ); ?>"<?php endif; ?>>
		<?php $instance->form( 'input', array_merge( $args, [
			'type' => 'radio',
		] ) ); ?>
		<?php $instance->h( $label, true ); ?>
    </label>
<?php else: ?>
	<?php $instance->form( 'input', array_merge( $args, [
		'type' => 'radio',
	] ) ); ?>
<?php endif; ?>
