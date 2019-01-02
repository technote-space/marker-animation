<?php
/**
 * @version 1.4.0
 * @author technote-space
 * @since 1.4.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Traits\Presenter $instance */
/** @var array $data */
/** @var array $column */
/** @var string $name */
/** @var string $prefix */
$attr     = $instance->app->utility->array_get( $column, 'attributes', [] );
$options  = $instance->app->utility->array_get( $column, 'options', [] );
$selected = [];
$value    = $instance->old( $prefix . $name, $data, $name );
if ( ! empty( $value ) ) {
	$selected[] = $value;
} else {
	$selected[] = $column['default'];
}
?>
<?php $instance->form( 'select', [
	'name'       => $prefix . $name,
	'id'         => $prefix . $name,
	'attributes' => $attr,
	'options'    => $options,
	'selected'   => $selected,
], $instance->app->utility->array_get( $column, 'args', [] ) ); ?>
