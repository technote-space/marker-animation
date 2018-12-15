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