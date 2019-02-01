<?php
/**
 * @version 1.5.0
 * @author technote-space
 * @since 1.4.0
 * @since 1.5.0 Changed: trivial change
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
?>

<script>
    (function ($) {
        $(function () {
            $('.marker-animation-preview').markerAnimation();
        });
    })(jQuery);
</script>
