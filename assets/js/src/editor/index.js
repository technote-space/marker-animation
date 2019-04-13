import $ from 'jquery';
import {_addStyle, parseInputValue} from '../common/add-style';

$(function () {
    /** @var {{
    * title: string,
    * detail_title: string,
    * class: string,
    * prefix: string,
    * details: {ignore: boolean, form: string, label: string, value: string, attributes: object}[],
    * is_valid_color_picker: boolean,
    * is_block_editor: boolean,
    * settings: {id: number, options: {is_valid_button: boolean, is_valid_style: boolean}, title: string}[]
    * }} marker_animation_params */

    const formatters = [];
    const attrs = {};

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
                        label: detail.title,
                        checked: value
                    };
                } else if (marker_animation_params.is_valid_color_picker && detail.form === 'color') {
                    value = detail.value;
                    item = {
                        type: 'colorpicker',
                        name: key,
                        label: detail.title,
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
                        label: detail.title,
                        values: values,
                        value: value
                    };
                } else {
                    value = detail.value;
                    item = {
                        type: 'textbox',
                        name: key,
                        label: detail.title,
                        value: value
                    };
                }
                const name = detail.attributes['data-option_name'] ? detail.attributes['data-option_name'] : key;
                attrs['data-' + marker_animation_params.prefix + name] = getDataAttribute(name);
                defaultStyle.push(parseInputValue(value, key, true));
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

            Object.keys(marker_animation_params.settings).forEach(function (key) {
                addColorSettingButton(ed, marker_animation_params.settings[key]);
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

                formatters.length = 0;
                formatters.push('marker_animation');
                ed.formatter.register('marker_animation', {
                    inline: 'span',
                    classes: [marker_animation_params.class],
                    attributes: attrs
                });
                Object.keys(marker_animation_params.settings).forEach(function (key) {
                    addColorSettingFormatter(ed, marker_animation_params.settings[key], defaultStyle);
                });
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
     * @param class_name
     */
    const addStyle = function (ed, key, value, is_default, class_name) {
        _addStyle((class_name, style) => {
            let selector = (marker_animation_params.is_block_editor ? 'body #editor' : 'body') + ' .' + class_name;
            if (!is_default) {
                selector += '[data-' + marker_animation_params.prefix + key + '="' + value + '"]';
            }
            ed.dom.addStyle(
                selector + ' { ' + style + ' }'
            );
        }, key, value, class_name);
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
            const result = parseInputValue(value, key);
            if (result) {
                attributes[result.name] = result.value;
            }
        });
        return attributes;
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
     * @param formatter
     * @param class_name
     */
    const onClick = function (ed, get_attributes, formatter, class_name) {
        let selected_elem = ed.selection.getNode();
        const classList = ed.selection.getSel().focusNode.classList;
        if (undefined === formatter) formatter = 'marker_animation';
        if (undefined === class_name) class_name = marker_animation_params.class;

        if (classList && classList.contains(class_name)) {
            selected_elem = ed.selection.getSel().focusNode;
        }
        const $selected = $(selected_elem);
        const bm = ed.selection.getBookmark();

        if ($selected.hasClass(class_name)) {
            Object.keys(attrs).forEach(function (key) {
                selected_elem.removeAttribute(key);
            });
            formatters.forEach(function (formatter) {
                try {
                    ed.formatter.remove(formatter, undefined, selected_elem);
                } catch (e) {
                }
            });
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
                ed.formatter.apply(formatter, attributes, selected_text === '' ? selected_elem : null);
                ed.selection.moveToBookmark(bm);
            });
        }
    };

    /**
     * node changed
     * @since 1.2.2
     * @param ed
     * @param $target
     * @param class_name
     */
    const nodeChanged = function (ed, $target, class_name) {
        if (undefined === class_name) class_name = marker_animation_params.class;
        ed.on('NodeChange', function (e) {
            $target.active(e.element.classList.contains(class_name));
        });
    };

    /**
     * add color setting button
     * @param {{}} ed
     * @param {{id: number, options: {is_valid_button: boolean, is_valid_style: boolean}, title: string}} setting
     */
    const addColorSettingButton = function (ed, setting) {
        const id = setting.id;
        const formatter = 'marker_animation-' + id;
        const class_name = marker_animation_params.class + '-' + id;

        if (setting.options.is_valid_button) {
            ed.addButton(formatter, {
                title: setting.title,
                icon: 'icon highlight-icon setting-' + id,
                onclick: function () {
                    onClick(ed, null, formatter, class_name);
                },
                onPostRender: function () {
                    nodeChanged(ed, this, class_name);
                }
            });
        }
        if (setting.options.is_valid_style) {
            ed.addCommand('marker_animation_preset_color' + id, function () {
                onClick(ed, null, formatter, class_name);
            });
        }
    };

    /**
     * add color setting formatter
     * @param ed
     * @param setting
     * @param defaultStyle
     */
    const addColorSettingFormatter = function (ed, setting, defaultStyle) {
        const id = setting.id;
        const formatter = 'marker_animation-' + id;
        const class_name = marker_animation_params.class + '-' + id;

        formatters.push(formatter);
        ed.formatter.register(formatter, {
            inline: 'span',
            classes: [class_name],
            attributes: attrs
        });

        addStyle(ed, 'display', 'inline', true, class_name);
        addStyle(ed, 'background-position', 'left -100% center', true, class_name);
        addStyle(ed, 'background-repeat', 'repeat-x', true, class_name);

        defaultStyle.forEach(function (style) {
            addStyle(ed, style.name, style.value, true, class_name);
        });
        Object.keys(setting.options).forEach(function (key) {
            addStyle(ed, key, setting.options[key], true, class_name);
        });
    };

    /* Start the buttons */
    tinymce.PluginManager.add('marker_animation_button_plugin', tinymce.plugins.marker_animation_button);
});