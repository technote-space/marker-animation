<?php
/**
 * Technote Views Include Form Color picker
 *
 * @version 1.1.68
 * @author technote-space
 * @since 1.1.68
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Traits\Presenter $instance */
/** @var array $args */
$args['class'] .= ' ' . $instance->get_color_picker_class();
?>
<?php if ( isset( $label ) ): ?>
    <label>
		<?php $instance->h( $label, true ); ?>
		<?php $instance->form( 'input/text', $args ); ?>
    </label>
<?php else: ?>
	<?php $instance->form( 'input/text', $args ); ?>
<?php endif; ?>