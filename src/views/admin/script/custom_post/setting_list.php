<?php
/**
 * @version 1.7.6
 * @author Technote
 * @since 1.4.0
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	return;
}
?>
<script>
	( function( $ ) {
		$( function() {
			$( '.marker-animation-preview' ).markerAnimation();
		} );
	} )( jQuery );
</script>
