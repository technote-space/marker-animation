require('jquery.marker-animation');
import $ from 'jquery';

$(() => {
  /** @var {{selector: string, prefix: string, settings: {id: number, options: {}}[]}} markerAnimation */
  Object.keys(markerAnimation.settings).forEach(key => {
    const options = markerAnimation.settings[ key ].options;
    $(options.selector).filter(() => {
      return !$(this).data('marker_animation_initialized');
    }).data('marker_animation_initialized', true).markerAnimation(options);
  });
  if (markerAnimation.selector) {
    $(markerAnimation.selector).filter(() => {
      return !$(this).data('marker_animation_initialized');
    }).data('marker_animation_initialized', true).markerAnimation(markerAnimation.default);
  }
});
