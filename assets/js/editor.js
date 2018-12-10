/**
 * @version 1.1.11
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
                onclick: function () {
                    onClick(ed, null);
                },
                onPostRender: function () {
                    nodeChanged(ed, this);
                }
            });

            const body = [];
            const attrs = {};
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
                } else if (marker_animation_params.colorpicker_enabled && detail.form === 'color') {
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
                const name = detail.attributes['data-option_name'] ? detail.attributes['data-option_name'] : key;
                attrs['data-' + marker_animation_params.prefix + name] = getDataAttribute(name);
            });
            ed.addButton('marker_animation_detail', {
                title: marker_animation_params.detail_title,
                icon: 'icon highlight-icon',
                onclick: function () {
                    onClick(ed, function () {
                        const $defer = $.Deferred();
                        ed.windowManager.open({
                            title: marker_animation_params.title,
                            body: body,
                            onSubmit: function (e) {
                                $defer.resolve(getDialogResults(e.data));
                            },
                            onClose: function () {
                                $defer.reject();
                            }
                        });
                        return $defer;
                    });
                },
                onPostRender: function () {
                    nodeChanged(ed, this);
                }
            });

            ed.on('init', function () {
                ed.dom.select('.' + marker_animation_params.class).forEach(function (elem) {
                    Object.keys(elem.dataset).forEach(function (key) {
                        if (key.substring(0, marker_animation_params.prefix.length) === marker_animation_params.prefix) {
                            addStyle(ed, key.slice(marker_animation_params.prefix.length), elem.dataset[key]);
                        }
                    });
                });
                ed.formatter.register('marker_animation', {
                    inline: 'span',
                    classes: [marker_animation_params.class],
                    attributes: attrs
                });
            });
        },
        createControl: function () {
            return null;
        }
    });

    /**
     * add styles
     * @param ed
     * @param attributes
     */
    const addStyles = function (ed, attributes) {
        Object.keys(attributes).forEach(function (key) {
            addStyle(ed, key, attributes[key]);
        });
    };

    /**
     * add style
     * @param ed
     * @param key
     * @param value
     */
    const addStyle = function (ed, key, value) {
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
            if (undefined === ed.dom.select("head")[0]) {
                const s = document.createElement('style');
                s.setAttribute("type", "text/css");
                document.getElementsByTagName('head').item(0).appendChild(s);
                s.sheet.insertRule(style, 0);
            } else {
                ed.dom.add(
                    ed.dom.select("head")[0], // first element
                    "style",
                    {
                        type: "text/css"
                    },
                    style
                );
            }
        }
        if (!added_style[key]) added_style[key] = {};
        added_style[key][value] = true;
    };

    /**
     * dialog results
     * @param data
     */
    const getDialogResults = function (data) {
        const attributes = {};
        Object.keys(data).forEach(function (key) {
            let value = data[key];
            const detail = marker_animation_params.details[key];
            const name = detail.attributes['data-option_name'] ? detail.attributes['data-option_name'] : key;
            if (detail.form === 'input/checkbox') {
                if (value) {
                    if (detail.attributes['data-default']) return;
                    value = undefined === detail.attributes['data-option_value-true'] ? value : detail.attributes['data-option_value-true'];
                } else {
                    if (!detail.attributes['data-default']) return;
                    value = undefined === detail.attributes['data-option_value-false'] ? value : detail.attributes['data-option_value-false'];
                }
            }
            if (value === '' || value === detail.attributes['data-default']) return;
            attributes[name] = value;
        });
        return attributes;
    };

    /**
     * data attribute
     * @param name
     * @returns {Function}
     */
    const getDataAttribute = function (name) {
        return function (vars) {
            if (undefined === vars) return '';
            if (null === vars[name]) return 'null';
            if (undefined !== vars[name]) return vars[name];
            return '';
        };
    };

    /**
     * on click
     * @param ed
     * @param get_attributes
     */
    const onClick = function (ed, get_attributes) {
        const selected_elem = ed.selection.getNode();
        const $selected = $(selected_elem);
        const bm = ed.selection.getBookmark();

        if ($selected.hasClass(marker_animation_params.class)) {
            ed.formatter.remove('marker_animation', undefined, selected_elem);
            ed.selection.moveToBookmark(bm);
        } else {
            let $deferred;
            if (get_attributes) {
                $deferred = get_attributes();
            } else {
                $deferred = $.Deferred();
                setTimeout(function () {
                    $deferred.resolve({});
                }, 1);
            }

            $deferred.done(function (attributes) {
                addStyles(ed, attributes);
                const selected_text = ed.selection.getContent();
                ed.formatter.apply('marker_animation', attributes, selected_text === '' ? selected_elem : null);
                ed.selection.moveToBookmark(bm);
            });
        }
    };

    /**
     * node changed
     * @param ed
     * @param $target
     */
    const nodeChanged = function (ed, $target) {
        ed.on('NodeChange', function (e) {
            $target.active(e.element.classList.contains(marker_animation_params.class));
        });
    };

    /* Start the buttons */
    tinymce.PluginManager.add('marker_animation_button_plugin', tinymce.plugins.marker_animation_button);
})(jQuery);