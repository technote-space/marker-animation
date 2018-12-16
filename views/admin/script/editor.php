<?php
/**
 * @version 1.3.1
 * @author technote-space
 * @since 1.0.0
 * @since 1.3.1 Added: preset button color
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
/** @var array $marker_options */
?>

<script>
    const <?php $instance->h( $param_name );?>=<?php $instance->json( $params );?>;
    (function ($) {
        $(function () {
            /** @var {{preset_color_count: number, details: {}}} marker_animation_params */
            for (let i = 1; i <= marker_animation_params.preset_color_count; i++) {
                $('<style type="text/css">' +
                    '.mce-menu-item:nth-of-type(' + i + ') > .highlight-icon + span {background-color:' + marker_animation_params.details['color' + i].value + '}' +
                    '</style>').appendTo('head');
            }
        });
    })(jQuery);
</script>
