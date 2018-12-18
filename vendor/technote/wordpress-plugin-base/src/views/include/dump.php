<?php
/**
 * Technote Views Include Dump
 *
 * @version 2.7.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.7.0 Changed: ver_dump to ver_export
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Traits\Presenter $instance */
/** @var mixed $data */
?>
<pre>
<?php var_export( $data ); ?>
</pre>
