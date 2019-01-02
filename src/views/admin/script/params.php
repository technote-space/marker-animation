<?php
/**
 * @version 1.4.0
 * @author technote-space
 * @since 1.4.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Interfaces\Presenter $instance */
/** @var string $param_name */
/** @var array $params */
?>

<script>
    const <?php $instance->h( $param_name );?>=<?php $instance->json( $params );?>;
</script>
