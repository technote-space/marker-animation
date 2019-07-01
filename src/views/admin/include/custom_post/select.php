<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

use WP_Framework_Presenter\Traits\Presenter;

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	return;
}
/** @var Presenter $instance */
/** @var array $data */
/** @var array $column */
/** @var string $name */
/** @var string $prefix */
$options  = $instance->app->array->get( $column, 'options', [] );
$selected = [];
$value    = false === $data && isset( $options[ null ] ) ? null : $instance->old( $prefix . $name, $data, $name, '' );
if ( '' !== $value ) {
	$selected[] = $value;
} else {
	$selected[] = $column['default'];
}
?>
<?php $instance->form( 'select', [
	'name'     => $prefix . $name,
	'id'       => $prefix . $name,
	'options'  => $options,
	'selected' => $selected,
], $instance->app->array->get( $column, 'args', [] ) ); ?>
