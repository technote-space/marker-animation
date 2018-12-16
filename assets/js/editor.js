/**
 * @version 1.3.0
 * @author technote-space
 * @since 1.0.0
 * @since 1.2.2 Updated: refine tool button behavior
 * @since 1.2.3 Update: set default style
 * @since 1.2.4 Fixed: for IE11
 * @since 1.2.4 Fixed: detail setting value check
 * @since 1.2.6 Changed: variable name
 * @since 1.3.0 Added: preset color
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */
(function ($) {
    'use strict';

    /** @var {{
    * title: string,
    * detail_title: string,
    * class: string,
    * prefix: string,
    * details: {ignore: boolean, ignore_editor: boolean, form: string, label: string, value: string, attributes: object}[],
    * is_valid_color_picker: boolean,
    * preset_color_count: number
    * }} marker_animation_params */

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
            const defaultStyle = [];
            Object.keys(marker_animation_params.details).forEach(function (key) {
                const detail = marker_animation_params.details[key];
                if (detail.ignore) return;
                let value, item;
                if (detail.form === 'input/checkbox') {
                    value = detail.attributes.checked === 'checked';
                    item = {
                        type: 'checkbox',
                        name: key,
                        label: detail.label,
                        checked: value
                    };
                } else if (marker_animation_params.is_valid_color_picker && detail.form === 'color') {
                    value = detail.value;
                    item = {
                        type: 'colorpicker',
                        name: key,
                        label: detail.label,
                        value: value
                    };
                } else if (detail.form === 'select') {
                    const values = [];
                    Object.keys(detail.options).forEach(function (k) {
                        values.push({
                            text: detail.options[k],
                            value: k
                        });
                    });
                    value = detail.value;
                    item = {
                        type: 'listbox',
                        name: key,
                        label: detail.label,
                        values: values
                    };
                } else {
                    value = detail.value;
                    item = {
                        type: 'textbox',
                        name: key,
                        label: detail.label,
                        value: value
                    };
                }
                const name = detail.attributes['data-option_name'] ? detail.attributes['data-option_name'] : key;
                attrs['data-' + marker_animation_params.prefix + name] = getDataAttribute(name);
                defaultStyle.push(getDialogResult(value, key, true));
                if (detail.ignore_editor) return;
                body.push(item);
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
                defaultStyle.forEach(function (style) {
                    addStyle(ed, style.name, style.value, true);
                });
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
                for (let i = 1; i <= marker_animation_params.preset_color_count; i++) {
                    addPresetColorCommand(ed, i);
                }
            });
        },
        createControl: function () {
            return null;
        }
    });

    /**
     * add styles
     * @since 1.2.2
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
     * @since 1.2.3 Update: set default style
     * @since 1.2.4 Fixed: for IE11
     * @param ed
     * @param key
     * @param value
     * @param is_default
     */
    const addStyle = function (ed, key, value, is_default) {
        if (added_style[key] && added_style[key][value]) return;

        let selector, style = null;
        switch (key) {
            case 'color':
                style = 'background-image: linear-gradient(to right, rgba(255, 255, 255, 0) 50%, ' + value + ' 50%)';
                break;
            case 'thickness':
                style = 'background-size: 200% ' + value;
                break;
            case 'font_weight':
                style = 'font-weight: ' + (!value || value === 'null' ? 'normal' : value);
                break;
            case 'padding_bottom':
                style = 'padding-bottom: ' + value;
                break;
        }
        if (!style && key.lastIndexOf('color', 0) === 0) {
            if (!value) return;
            if (is_default) {
                is_default = false;
                value = 1;
            }
            style = 'background-image: linear-gradient(to right, rgba(255, 255, 255, 0) 50%, ' + marker_animation_params.details[key].value + ' 50%)';
        }
        if (style) {
            selector = 'body .' + marker_animation_params.class;
            if (!is_default) {
                selector += '[data-' + marker_animation_params.prefix + key + '="' + value + '"]';
            }
            ed.dom.addStyle(
                selector + ' { ' + style + ' }'
            );
        }
        if (!added_style[key]) added_style[key] = {};
        added_style[key][value] = true;
    };

    /**
     * dialog results
     * @since 1.2.2
     * @param data
     */
    const getDialogResults = function (data) {
        const attributes = {};
        Object.keys(data).forEach(function (key) {
            let value = data[key];
            const result = getDialogResult(value, key);
            if (result) {
                attributes[result.name] = result.value;
            }
        });
        return attributes;
    };

    /**
     * dialog result
     * @since 1.2.3
     * @since 1.2.4 Fixed: detail setting value check
     * @param value
     * @param key
     * @param not_check
     * @returns {{name: (string|*), value: *}}|bool
     */
    const getDialogResult = function (value, key, not_check) {
        const detail = marker_animation_params.details[key];
        const name = detail.attributes['data-option_name'] ? detail.attributes['data-option_name'] : key;
        if (detail.form === 'input/checkbox') {
            if (value) {
                if (!not_check && detail.attributes['data-value']) return false;
                value = undefined === detail.attributes['data-option_value-true'] ? value : detail.attributes['data-option_value-true'];
            } else {
                if (!not_check && !detail.attributes['data-value']) return false;
                value = undefined === detail.attributes['data-option_value-false'] ? value : detail.attributes['data-option_value-false'];
            }
        }
        if (!not_check && (value === '' || value === detail.attributes['data-value'])) return false;
        return {
            name: name,
            value: value
        };
    };

    /**
     * data attribute
     * @since 1.2.2
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
     * @since 1.2.2
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
     * @since 1.2.2
     * @param ed
     * @param $target
     */
    const nodeChanged = function (ed, $target) {
        ed.on('NodeChange', function (e) {
            $target.active(e.element.classList.contains(marker_animation_params.class));
        });
    };

    /**
     * add preset color command
     * @param ed
     * @param index
     */
    const addPresetColorCommand = function (ed, index) {
        ed.addCommand('marker_animation_preset_color' + index, function () {
            const key = 'color' + index;
            const attributes = {};
            attributes[key] = 1;
            onClick(ed, function () {
                const $deferred = $.Deferred();
                setTimeout(function () {
                    $deferred.resolve(attributes);
                }, 1);
                return $deferred;
            });
        });
    };

    /* Start the buttons */
    tinymce.PluginManager.add('marker_animation_button_plugin', tinymce.plugins.marker_animation_button);
})(jQuery);