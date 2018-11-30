require('jquery.marker-animation');
import $ from 'jquery';

$(function () {
    if (marker_animation.selector) {
        $(marker_animation.selector).markerAnimation(marker_animation);
    }
});