import { Helpers, RichText } from '../wrapper';
import { addStyle } from './style';
import { defaultStyle, getName, getIcon } from './misc';

const { registerGroupedFormatType } = RichText;
const { getToolbarButtonProps }     = Helpers;

export { applyStyles } from './style';
export { getDefaultFormatGroupProps, getSettingFormatGroupProps, getDefaultFormatButtonProps } from './component';

/**
 * @param {object} setting setting
 */
export const registerSettingFormat = setting => {
	/**
	 * @var {bool} setting.options.isValidButtonBlockEditor
	 */

	addStyle('display', 'inline', setting.options.class);
	addStyle('background-position', 'left -100% center', setting.options.class);
	addStyle('background-repeat', 'repeat-x', setting.options.class);

	defaultStyle.forEach(style => {
		addStyle(style.name, style.value, setting.options.class);
	});
	Object.keys(setting.options).forEach(key => {
		addStyle(key, setting.options[ key ], setting.options.class, false, false, false, setting.options[ 'stripe' ]);
	});

	if (setting.options.isValidButtonBlockEditor) {
		registerGroupedFormatType(getToolbarButtonProps(getName('setting'), setting.options.class, getIcon(), {
			title: setting.title,
		}));
	}
};
