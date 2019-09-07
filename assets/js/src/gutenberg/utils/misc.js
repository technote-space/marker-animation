import { Common } from '../wrapper';

const { getTranslator } = Common.Helpers;
const { Icon } = Common.Components;

/**
 * @param {*} value value
 * @param {string} key key
 * @param {boolean?} notCheckDefault not check default?
 * @returns {{name: (string|*), value: *}}|bool
 */
export const parseInputValue = ( value, key, notCheckDefault ) => {
	const detail = markerAnimationParams.details[ key ];
	const name = getDataName( key );
	if ( detail.form === 'input/checkbox' ) {
		if ( value ) {
			if ( ! notCheckDefault && detail.attributes[ 'data-value' ] ) {
				return false;
			}
			value = undefined === detail.attributes[ 'data-option_value-1' ] ? value : detail.attributes[ 'data-option_value-1' ];
		} else {
			if ( ! notCheckDefault && ! detail.attributes[ 'data-value' ] ) {
				return false;
			}
			value = undefined === detail.attributes[ 'data-option_value-0' ] ? value : detail.attributes[ 'data-option_value-0' ];
		}
	}
	if ( ! notCheckDefault && ( value === undefined || value === '' || value === detail.attributes[ 'data-value' ] ) ) {
		return false;
	}
	return {
		name: name,
		key: key,
		value: value,
	};
};

/**
 * @param {string} key key
 * @param {boolean} isData is data?
 * @returns {string} name
 */
export const getDataName = ( key, isData = false ) => {
	const detail = markerAnimationParams.details[ key ];
	const name = detail && detail.attributes && detail.attributes[ 'data-option_name' ] ? detail.attributes[ 'data-option_name' ] : key;
	return isData ? ( 'data-' + markerAnimationParams.prefix + name ) : name;
};

/**
 * @returns {{defaultStyle: Array, body, attrs}} settings
 */
export const getDetailSettings = () => {
	const body = {};
	const defaultStyle = [];
	const attrs = {};

	Object.keys( markerAnimationParams.details ).forEach( key => {
		const detail = markerAnimationParams.details[ key ];
		if ( detail.ignore ) {
			return;
		}

		const { value, item } = parseDetail( detail, key );
		attrs[ getDataName( key, true ) ] = getDataName( key, true );
		defaultStyle.push( parseInputValue( value, key, true ) );
		body[ key ] = item;
	} );

	return {
		attrs,
		defaultStyle,
		body,
	};
};

const parseDetail = ( detail, key ) => {
	if ( detail.form === 'input/checkbox' ) {
		return parseCheckbox( detail, key );
	} else if ( detail.form === 'color' ) {
		return parseColor( detail, key );
	} else if ( detail.form === 'select' ) {
		return parseSelect( detail, key );
	}
	return parseOther( detail, key );
};

const parseCheckbox = ( detail, key ) => {
	const value = detail.attributes.checked === 'checked';
	const item = {
		type: 'checkbox',
		name: key,
		label: detail.title,
		checked: value,
	};
	return { value, item };
};

const parseColor = ( detail, key ) => {
	const value = detail.value;
	const item = {
		type: 'colorpicker',
		name: key,
		label: detail.title,
		value: value,
	};
	return { value, item };
};

const parseSelect = ( detail, key ) => {
	const values = [];
	Object.keys( detail.options ).forEach( key => {
		values.push( {
			text: detail.options[ key ],
			label: detail.options[ key ],
			value: key,
		} );
	} );
	const value = detail.value;
	const item = {
		type: 'listbox',
		name: key,
		label: detail.title,
		values: values,
		value: value,
	};
	return { value, item };
};

const parseOther = ( detail, key ) => {
	const value = detail.value;
	const item = {
		type: 'textbox',
		name: key,
		label: detail.title,
		value: value,
	};
	return { value, item };
};

/**
 * @param {*} data data
 * @returns {string} converted data
 */
export const convertData = data => {
	if ( undefined === data ) {
		return '';
	}
	if ( null === data ) {
		return 'null';
	}
	return data + '';
};

/**
 * @param {string} name name
 * @returns {string} wrapped name
 */
export const getName = name => 'marker-animation-' + name;

/**
 * @returns {*} icon
 */
export const getIcon = () => Icon( { icon: markerAnimationParams.defaultIcon } );

/**
 * @param {string} text
 * @return {string} translated text
 */
export const translate = getTranslator( markerAnimationParams );

const { attrs, defaultStyle, body } = getDetailSettings();
export { attrs, defaultStyle, body };

/** @var {{
 * isValidDetailSetting: boolean,
 * class: string,
 * defaultIcon: string,
 * details: {},
 * prefix: string,
 * settings: {}}} markerAnimationParams */

