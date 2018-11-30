<?php
/**
 * Technote Views Include Img
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
/** @var array $args */
/** @var string $id */
/** @var string $class */
/** @var string $src */
/** @var string $alt */
/** @var string $width */
/** @var string $height */
/** @var array $attributes */
empty( $attributes ) and $attributes = [];
isset( $id ) and $attributes['id'] = $id;
isset( $class ) and $attributes['class'] = $class;
$attributes['src'] = $src;
isset( $alt ) and $attributes['alt'] = $alt;
isset( $width ) and $attributes['width'] = $width;
isset( $height ) and $attributes['height'] = $height;
?>
<img <?php $instance->get_view( 'include/attributes', array_merge( $args, [ 'attributes' => $attributes ] ), true ); ?> />
