<?php
/**
 * @version 1.5.0
 * @author technote-space
 * @since 1.4.0
 * @since 1.5.0 Changed: trivial change
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Traits\Presenter $instance */
/** @var array $data */
/** @var array $column */
/** @var string $name */
/** @var string $prefix */
$attr  = $instance->app->utility->array_get( $column, 'attributes', [] );
$_data = [];
! empty( $data[ $name ] ) and $_data[ $name ] = $data[ $name ];
?>
<?php $instance->form( 'color', [
	'name'       => $prefix . $name,
	'id'         => $prefix . $name,
	'value'      => $instance->old( $prefix . $name, $_data, $name, $instance->app->utility->array_get( $column, 'default' ) ),
	'attributes' => $attr,
], $instance->app->utility->array_get( $column, 'args', [] ) ); ?>
