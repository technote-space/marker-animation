(function ($) {
    'use strict';

    /* Register the buttons */
    tinymce.create('tinymce.plugins.marker_animation_button', {
        init: function (ed) {
            /**
             * Adds HTML tag to selected content
             */
            ed.addButton('marker_animation', {
                title: marker_animation_params.title,
                text: marker_animation_params.text,
                icon: false,
                cmd: 'marker_animation_cmd'
            });
            ed.addCommand('marker_animation_cmd', function () {
                const selected_elem = ed.selection.getNode();
                const selected = $(selected_elem);

                if (selected.hasClass(marker_animation_params.class)) {
                    const insert_html = selected.html();
                    selected.remove();
                    ed.execCommand('mceInsertContent', 0, insert_html);
                } else {
                    const selected_text = ed.selection.getContent();
                    ed.execCommand('mceInsertContent', 0, '<span class="' + marker_animation_params.class + '">' + selected_text + '</span>');
                }
            });
        },
        createControl: function () {
            return null;
        }
    });
    /* Start the buttons */
    tinymce.PluginManager.add('marker_animation_button_plugin', tinymce.plugins.marker_animation_button);
})(jQuery);