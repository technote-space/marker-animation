/**
 * @version 1.1.5
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */
(function ($) {
    'use strict';

    const added_style = {};

    /* Register the buttons */
    tinymce.create('tinymce.plugins.marker_animation_button', {
        init: function (ed) {
            /**
             * Adds HTML tag to selected content
             */
            ed.addButton('marker_animation', {
                title: marker_animation_params.title,
                icon: 'icon highlight-icon',
                cmd: 'marker_animation_cmd'
            });

            const body = [];
            Object.keys(marker_animation_params.details).forEach(function (key) {
                const detail = marker_animation_params.details[key];
                if (detail.ignore) return;
                if (detail.form === 'input/checkbox') {
                    body.push({
                        type: 'checkbox',
                        name: key,
                        label: detail.label,
                        checked: detail.attributes.checked === 'checked'
                    });
                } else if (detail.form === 'color') {
                    body.push({
                        type: 'colorpicker',
                        name: key,
                        label: detail.label,
                        value: detail.value
                    });
                } else if (detail.form === 'select') {
                    const values = [];
                    Object.keys(detail.options).forEach(function (k) {
                        values.push({
                            text: detail.options[k],
                            value: k
                        });
                    });
                    body.push({
                        type: 'listbox',
                        name: key,
                        label: detail.label,
                        values: values
                    });
                } else {
                    body.push({
                        type: 'textbox',
                        name: key,
                        label: detail.label,
                        value: detail.value
                    });
                }
            });
            ed.addButton('marker_animation_detail', {
                title: marker_animation_params.detail_title,
                icon: 'icon highlight-icon',
                onclick: function () {
                    const selected_elem = ed.selection.getNode();
                    const $selected = $(selected_elem);

                    if ($selected.hasClass(marker_animation_params.class)) {
                        unwrap_animation(ed, $selected);
                    } else {
                        const selected_text = ed.selection.getContent();
                        if (selected_text !== '') {
                            ed.windowManager.open({
                                title: marker_animation_params.title,
                                body: body,
                                onsubmit: function (e) {
                                    const attributes = {};
                                    Object.keys(e.data).forEach(function (key) {
                                        let value = e.data[key];
                                        const detail = marker_animation_params.details[key];
                                        const name = detail.attributes['data-option_name'] ? detail.attributes['data-option_name'] : key;
                                        if (detail.form === 'input/checkbox') {
                                            if (value) {
                                                value = undefined === detail.attributes['data-option_value-true'] ? value : detail.attributes['data-option_value-true'];
                                            } else {
                                                value = undefined === detail.attributes['data-option_value-false'] ? value : detail.attributes['data-option_value-false'];
                                            }
                                        }
                                        if (value === '') value = detail.attributes['data-default'];
                                        attributes[name] = value;
                                    });
                                    wrap_animation(ed, selected_text, attributes);
                                }
                            });
                        }
                    }
                }
            });
            ed.addCommand('marker_animation_cmd', function () {
                const selected_elem = ed.selection.getNode();
                const $selected = $(selected_elem);

                if ($selected.hasClass(marker_animation_params.class)) {
                    unwrap_animation(ed, $selected);
                } else {
                    const selected_text = ed.selection.getContent();
                    if (selected_text !== '') {
                        wrap_animation(ed, selected_text, {});
                    }
                }
            });

            ed.on('init', function () {
                ed.dom.select('.' + marker_animation_params.class).forEach(function (elem) {
                    Object.keys(elem.dataset).forEach(function (key) {
                        const regExp = new RegExp('^' + marker_animation_params.prefix, 'g');
                        add_style(ed, key.replace(regExp, ''), elem.dataset[key]);
                    });
                });
            });
        },
        createControl: function () {
            return null;
        }
    });

    /**
     * wrap animation
     * @param ed
     * @param text
     * @param attributes
     */
    const wrap_animation = function (ed, text, attributes) {
        let html = '<span class="' + marker_animation_params.class + '"';
        Object.keys(attributes).forEach(function (key) {
            html += ' data-' + marker_animation_params.prefix + key + '="' + attributes[key] + '"';
            add_style(ed, key, attributes[key]);
        });

        html += '>' + text + '</span>';
        ed.execCommand('mceInsertContent', 0, html);
    };

    /**
     * unwrap animation
     * @param ed
     * @param $selected
     */
    const unwrap_animation = function (ed, $selected) {
        const insert_html = $selected.html();
        $selected.remove();
        ed.execCommand('mceInsertContent', 0, insert_html);
    };

    /**
     * add color
     * @param ed
     * @param key
     * @param value
     */
    const add_style = function (ed, key, value) {
        if (key !== 'color' && key !== 'thickness' && key !== 'font_weight' && key !== 'padding_bottom') return;
        if (added_style[key] && added_style[key][value]) return;

        let style = null;
        switch (key) {
            case 'color':
                style = '[data-' + marker_animation_params.prefix + key + '="' + value + '"] { background-image: linear-gradient(to right, rgba(255, 255, 255, 0) 50%, ' + value + ' 50%) }';
                break;
            case 'thickness':
                style = '[data-' + marker_animation_params.prefix + key + '="' + value + '"] { background-size: 200% ' + value + ' }';
                break;
            case 'font_weight':
                style = '[data-' + marker_animation_params.prefix + key + '="' + value + '"] { font-weight: ' + (!value || value === 'null' ? 'normal' : value) + ' }';
                break;
            case 'padding_bottom':
                style = '[data-' + marker_animation_params.prefix + key + '="' + value + '"] { padding-bottom: ' + value + ' }';
                break;
        }
        if (style) {
            ed.dom.add(
                ed.dom.select("head")[0], // first element
                "style",
                {
                    type: "text/css"
                },
                style
            );
        }
        if (!added_style[key]) added_style[key] = {};
        added_style[key][value] = true;
    };

    /* Start the buttons */
    tinymce.PluginManager.add('marker_animation_button_plugin', tinymce.plugins.marker_animation_button);
})(jQuery);