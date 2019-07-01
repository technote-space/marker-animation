<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

use WP_Framework_Presenter\Interfaces\Presenter;

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	return;
}
/** @var Presenter $instance */
/** @var string $param_name */
/** @var array $params */
?>

<script>
	const <?php $instance->h( $param_name );?>=<?php $instance->json( $params );?>;
</script>
