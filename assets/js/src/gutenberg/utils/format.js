import { getDataName, parseInputValue, convertData } from './misc';
import { addStyle } from './style';
import { applyFormat, getActiveFormat } from '@wordpress/rich-text';

/**
 * @param {object} setting setting
 * @param {object} args args
 * @param {string} formatName format name
 * @returns {array} active format
 */
export const getActiveFormatData = (setting, args, formatName) => {
  const activeFormat = getActiveFormat(args.value, formatName);
  const name         = getDataName(setting.name, true);
  if (!activeFormat || !activeFormat.attributes || !(name in activeFormat.attributes)) {
    return [setting, null, name];
  }
  return [setting, activeFormat, name];
};

/**
 * @param {object} setting setting
 * @param {object} activeFormat active format
 * @param {string} name name
 * @returns {boolean|*} data
 */
export const getData = (setting, activeFormat, name) => {
  if (!activeFormat) {
    return setting.type === 'checkbox' ? setting.checked : setting.value;
  }
  if (setting.type === 'checkbox') {
    if (activeFormat.attributes[ name ] === 'true') {
      return true;
    }
    const result = parseInputValue(true, setting.name);
    if (!result) {
      return false;
    }
    return result.value === activeFormat.attributes[ name ];
  }
  return activeFormat.attributes[ name ];
};

/**
 * @param {object} setting setting
 * @param {object} args args
 * @param {string} formatName format name
 * @returns {function} set value function
 */
export const setData = (setting, args, formatName) => {
  return value => {
    const attributes = args.activeAttributes;
    const result     = parseInputValue(value, setting.name);
    const name       = getDataName(setting.name, true);
    if (result) {
      const value        = convertData(result.value);
      attributes[ name ] = value;
      addStyle(result.key, value, markerAnimationParams.class, true, true, true, markerAnimationParams.details[ 'stripe' ].detail.value);
      args.onChange(applyFormat(args.value, {
        type: formatName,
        attributes: attributes,
      }));
    } else {
      if (name in attributes) {
        delete attributes[ name ];
        args.onChange(applyFormat(args.value, {
          type: formatName,
          attributes: attributes,
        }));
      }
    }
  };
};

/**
 * @param {object} setting setting
 * @param {object} args args
 * @param {string} formatName format name
 */
export const resetData = (setting, args, formatName) => {
  const attributes = args.activeAttributes;
  const name       = getDataName(setting.name, true);
  if (name in attributes) {
    delete attributes[ name ];
    args.onChange(applyFormat(args.value, {
      type: formatName,
      attributes: attributes,
    }));
  }
};

/** @var {{
 * isValidDetailSetting: boolean,
 * class: string,
 * defaultIcon: string,
 * details: {},
 * prefix: string,
 * settings: {}}} markerAnimationParams */

