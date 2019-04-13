import {_addStyle, parseInputValue} from '../common/add-style';
import {registerFormatType} from '../../../../../abc/assets/js/richtext-helpers';

const maClassName = marker_animation_params.class;

/**
 * add style
 * @param key
 * @param value
 * @param class_name
 * @param is_data
 */
const addStyle = (key, value, class_name, is_data) => {
    _addStyle((class_name, style) => {
        const selector = 'body #editor .' + class_name + (is_data ? '[data-' + marker_animation_params.prefix + key + '="' + value + '"]' : '');
        const styleSheetElement = document.createElement('style');

        document.head.appendChild(styleSheetElement);

        const sheet = styleSheetElement.sheet;
        if (sheet.insertRule) {
            sheet.insertRule(selector + '{' + style + '}', sheet.cssRules.length);
        } else {
            sheet.addRule(selector, style);
        }
    }, key, value, class_name);
};

Object.keys(marker_animation_params.details).forEach((key) => {
    const detail = marker_animation_params.details[key];
    if (detail.ignore) return;
    const {name, value} = parseInputValue(detail.form === 'input/checkbox' ? detail.attributes.checked === 'checked' : detail.value, key, true);
    addStyle(name, value);
});
wp.domReady(() => {
    [].forEach.call(document.getElementById('editor').getElementsByClassName(marker_animation_params.class), (elem) => {
        Object.keys(elem.dataset).forEach(function (key) {
            if (key.substring(0, marker_animation_params.prefix.length) === marker_animation_params.prefix) {
                addStyle(key.slice(marker_animation_params.prefix.length), elem.dataset[key], undefined, true);
            }
        });
    });
});

registerFormatType('default', marker_animation_params.title, maClassName);

Object.keys(marker_animation_params.settings).forEach((key) => {
    const setting = marker_animation_params.settings[key];
    /**
     * @var {bool} setting.options.is_valid_button_block_editor
     */
    if (setting.options.is_valid_button_block_editor) {
        Object.keys(setting.options).forEach((key) => {
            addStyle(key, setting.options[key], setting.options.class);
        });
        addStyle('display', 'inline', setting.options.class);
        addStyle('background-position', 'left -100% center', setting.options.class);
        addStyle('background-repeat', 'repeat-x', setting.options.class);

        registerFormatType(setting.id, setting.title, setting.options.class, false, marker_animation_params.title);
    }
});
