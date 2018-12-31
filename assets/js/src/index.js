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
    /** @var {{selector: string, prefix: string, animation_class: string, settings: {options: {}}[]}} marker_animation */
    Object.keys(marker_animation.settings).forEach(function (id) {
        const options = marker_animation.settings[id].options;
        const selector = '.' + marker_animation.animation_class + '-' + id;
        console.log(selector);
        $(selector).filter(function () {
            return !$(this).data('marker_animation_initialized');
        }).data('marker_animation_initialized', true).markerAnimation(options);
    });
    if (marker_animation.selector) {
        $(marker_animation.selector).filter(function () {
            return !$(this).data('marker_animation_initialized');
        }).data('marker_animation_initialized', true).markerAnimation(marker_animation);
    }
});