<?php
/**
 * Technote Views Include Form Nonce
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
/** @var string $nonce_key */
/** @var string $nonce_value */
?>
<?php $instance->form( 'input/hidden', array_merge( $args, [
	'name'  => $nonce_key,
	'value' => $nonce_value,
] ) ); ?>