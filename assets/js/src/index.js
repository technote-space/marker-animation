/**
 * @version 1.3.0
 * @author technote-space
 * @since 1.0.0
 * @since 1.3.0 Added: preset color
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */
require('jquery.marker-animation');
import $ from 'jquery';

$(function () {
    /** @var {{selector: string, prefix: string, preset_color_count: number}} marker_animation */
    if (marker_animation.selector) {
        $(marker_animation.selector).filter(function () {
            for (let i = 1; i <= marker_animation.preset_color_count; i++) {
                if ($(this).data(marker_animation.prefix + 'color' + i)) {
                    return false;
                }
            }
            return true;
        }).markerAnimation(marker_animation);
        for (let i = 1; i <= marker_animation.preset_color_count; i++) {
            marker_animation.color = marker_animation['color' + i];
            $(marker_animation.selector).filter(function () {
                return !!$(this).data(marker_animation.prefix + 'color' + i);
            }).markerAnimation(marker_animation);
        }
    }
});