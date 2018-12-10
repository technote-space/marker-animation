/**
 * @version 1.2.4
 * @author technote-space
 * @since 1.1.4
 * @since 1.2.4 Fixed: for IE11
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */
(function (richText, element, editor) {
    'use strict';

    const el = element.createElement;

    const name = 'marker-animation/marker-animation';
    richText.registerFormatType(name, {
        title: marker_animation_params.title,
        tagName: 'span',
        className: marker_animation_params.class,
        edit: function (args) {
            return el(editor.RichTextToolbarButton, {
                icon: 'admin-customizer',
                title: marker_animation_params.title,
                onClick: function () {
                    args.onChange(richText.toggleFormat(args.value, {
                        type: name
                    }));
                },
                isActive: args.isActive,
            });
        },
    });
}(
    window.wp.richText,
    window.wp.element,
    window.wp.editor
));