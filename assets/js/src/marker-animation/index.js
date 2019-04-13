
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