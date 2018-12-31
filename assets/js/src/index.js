/**
 * @version 1.4.0
 * @author technote-space
 * @since 1.0.0
 * @since 1.3.0 Added: preset color
 * @since 1.4.0 Deleted: preset color
 * @since 1.4.0 Added: marker setting feature
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */
require('jquery.marker-animation');
import $ from 'jquery';

$(function () {
    /** @var {{selector: string, prefix: string, settings: {id: number, options: {}}[]}} marker_animation */
    Object.keys(marker_animation.settings).forEach(function (key) {
        const options = marker_animation.settings[key].options;
        $(options.selector).filter(function () {
            return !$(this).data('marker_animation_initialized');
        }).data('marker_animation_initialized', true).markerAnimation(options);
    });
    if (marker_animation.selector) {
        $(marker_animation.selector).filter(function () {
            return !$(this).data('marker_animation_initialized');
        }).data('marker_animation_initialized', true).markerAnimation(marker_animation);
    }
});