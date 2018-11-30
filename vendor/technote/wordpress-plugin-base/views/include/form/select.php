<?php
/**
 * Technote Views Include Form Select
 *
 * @version 1.1.25
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
/** @var array $args */
/** @var string $id */
/** @var string $class */
/** @var string $name */
/** @var string $size */
/** @var string $multiple */
/** @var string $disabled */
/** @var array $attributes */
/** @var array $options */
/** @var array $selected */
empty( $attributes ) and $attributes = [];
isset( $id ) and $attributes['id'] = $id;
isset( $class ) and $attributes['class'] = $class;
$attributes['name'] = $name;
$attributes['size'] = isset( $size ) ? $size : '1';
! empty( $multiple ) and $attributes['multiple'] = 'multiple';
! empty( $disabled ) and $attributes['disabled'] = 'disabled';
isset( $selected ) and ! is_array( $selected ) and $selected = [ $selected ];
empty( $multiple ) and ! empty( $selected ) and count( $selected ) > 1 and $selected = array_splice( $selected, 0, 1 );
?>
<select <?php $instance->get_view( 'include/attributes', array_merge( $args, [ 'attributes' => $attributes ] ), true ); ?> >
	<?php if ( ! empty( $options ) ): ?>
		<?php foreach ( $options as $value => $option ): ?>
            <option value="<?php $instance->h( $value ); ?>"<?php if ( ! empty( $selected ) && in_array( $value, $selected ) ): ?> selected="selected"<?php endif; ?>><?php $instance->h( $option, true ); ?></option>
		<?php endforeach; ?>
	<?php endif; ?>
</select>