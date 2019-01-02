/**
 * @version 1.4.0
 * @author technote-space
 * @since 1.1.4
 * @since 1.2.4 Fixed: for IE11
 * @since 1.3.1 Fixed: preset color style
 * @since 1.4.0 Changed: moved classic editor style
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */
(function (richText, element, editor, $) {
    'use strict';

    /** @var {{
    * title: string,
    * detail_title: string,
    * class: string,
    * prefix: string,
    * details: {ignore: boolean, form: string, label: string, value: string, attributes: object}[],
    * is_valid_color_picker: boolean,
    * }} marker_animation_params */

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
    window.wp.editor,
    jQuery
));