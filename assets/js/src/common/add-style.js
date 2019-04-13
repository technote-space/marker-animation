/** @var {{
    * title: string,
    * class: string,
    * prefix: string,
    * }} marker_animation_params */

if (marker_animation_params.added_style === undefined) marker_animation_params.added_style = {};

/**
 * add style
 * @param func
 * @param key
 * @param value
 * @param class_name
 */
const _addStyle = function (func, key, value, class_name) {
    if (undefined === class_name) class_name = marker_animation_params.class;
    if (marker_animation_params.added_style[class_name] && marker_animation_params.added_style[class_name][key] && marker_animation_params.added_style[class_name][key][value]) return;

    let style = null;
    if ('color' === key) style = 'background-image:linear-gradient(to right,rgba(255,255,255,0) 50%,' + value + ' 50%)';
    else if ('thickness' === key) style = 'background-size:200% ' + value;
    else if ('font_weight' === key) style = 'font-weight:' + (!value || value === 'null' ? 'normal' : value);
    else if ('padding_bottom' === key) style = 'padding-bottom:' + value;
    else if ('display' === key) style = 'display:' + value;
    else if ('background-position' === key) style = 'background-position:' + value;
    else if ('background-repeat' === key) style = 'background-repeat:' + value;
    if (style) {
        func(class_name, style);
    }
    if (!marker_animation_params.added_style[class_name]) marker_animation_params.added_style[class_name] = {};
    if (!marker_animation_params.added_style[class_name][key]) marker_animation_params.added_style[class_name][key] = {};
    marker_animation_params.added_style[class_name][key][value] = true;
};

/**
 * @param value
 * @param key
 * @param not_check_default
 * @returns {{name: (string|*), value: *}}|bool
 */
const parseInputValue = function (value, key, not_check_default) {
    const detail = marker_animation_params.details[key];
    const name = detail.attributes['data-option_name'] ? detail.attributes['data-option_name'] : key;
    if (detail.form === 'input/checkbox') {
        if (value) {
            if (!not_check_default && detail.attributes['data-value']) return false;
            value = undefined === detail.attributes['data-option_value-true'] ? value : detail.attributes['data-option_value-true'];
        } else {
            if (!not_check_default && !detail.attributes['data-value']) return false;
            value = undefined === detail.attributes['data-option_value-false'] ? value : detail.attributes['data-option_value-false'];
        }
    }
    if (!not_check_default && (value === '' || value === detail.attributes['data-value'])) return false;
    return {
        name: name,
        value: value
    };
};

export {_addStyle, parseInputValue};
