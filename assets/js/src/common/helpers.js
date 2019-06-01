/** @var {{
 * title: string,
 * class: string,
 * prefix: string,
 * }} markerAnimationParams */

if ( markerAnimationParams.added_style === undefined ) {
	markerAnimationParams.added_style = {};
}

/**
 * add style
 * @param func
 * @param key
 * @param value
 * @param className
 * @param setupStripe
 * @param isStripe
 * @param _key
 */
const _addStyle = ( func, key, value, className, setupStripe, isStripe, _key ) => {
	if ( undefined === className ) {
		className = markerAnimationParams.class;
	}
	if ( markerAnimationParams.added_style[ className ] && markerAnimationParams.added_style[ className ][ key ] && markerAnimationParams.added_style[ className ][ key ][ value ] ) {
		return;
	}

	let style = null;
	if ( 'color' === key ) {
		if ( undefined === isStripe ) {
			return;
		}
		if ( isStripe ) {
			_addStyle( func, key + '-stripe', value, className );
			if ( setupStripe ) {
				_addStyle( func, key + '-normal', value, className, false, false, key );
			}
		} else {
			_addStyle( func, key + '-normal', value, className );
			if ( setupStripe ) {
				_addStyle( func, key + '-stripe', value, className, false, false, key );
			}
		}
		return;
	} else if ( 'color-normal' === key ) {
		style = 'background-image:linear-gradient(to right,rgba(255,255,255,0) 50%,' + value + ' 50%)';
	} else if ( 'color-stripe' === key ) {
		style = 'background-image:repeating-linear-gradient(-45deg,' + value + ',' + value + ' 2px,transparent 2px,transparent 4px)';
	} else if ( 'thickness' === key ) {
		style = 'background-size:200% ' + value;
	} else if ( 'font_weight' === key || 'bold' === key ) {
		style = 'font-weight:' + ( ! value || value === 'null' ? 'normal' : value );
	} else if ( 'padding_bottom' === key ) {
		style = 'padding-bottom:' + value;
	} else if ( 'display' === key ) {
		style = 'display:' + value;
	} else if ( 'background-position' === key ) {
		style = 'background-position:' + value;
	} else if ( 'background-repeat' === key ) {
		style = 'background-repeat:' + value;
	}
	if ( style ) {
		func( className, style, _key );
	}
	if ( ! markerAnimationParams.added_style[ className ] ) {
		markerAnimationParams.added_style[ className ] = {};
	}
	if ( ! markerAnimationParams.added_style[ className ][ key ] ) {
		markerAnimationParams.added_style[ className ][ key ] = {};
	}
	markerAnimationParams.added_style[ className ][ key ][ value ] = true;
};

/**
 * @param value
 * @param key
 * @param not_check_default
 * @returns {{name: (string|*), value: *}}|bool
 */
const parseInputValue = ( value, key, not_check_default ) => {
	const detail = markerAnimationParams.details[ key ];
	const name = getDataName( key );
	if ( detail.form === 'input/checkbox' ) {
		if ( value ) {
			if ( ! not_check_default && detail.attributes[ 'data-value' ] ) {
				return false;
			}
			value = undefined === detail.attributes[ 'data-option_value-1' ] ? value : detail.attributes[ 'data-option_value-1' ];
		} else {
			if ( ! not_check_default && ! detail.attributes[ 'data-value' ] ) {
				return false;
			}
			value = undefined === detail.attributes[ 'data-option_value-0' ] ? value : detail.attributes[ 'data-option_value-0' ];
		}
	}
	if ( ! not_check_default && ( value === undefined || value === '' || value === detail.attributes[ 'data-value' ] ) ) {
		return false;
	}
	return {
		name: name,
		key: key,
		value: value,
	};
};

const getDataName = ( key, isData = false ) => {
	const detail = markerAnimationParams.details[ key ];
	const name = detail && detail.attributes && detail.attributes[ 'data-option_name' ] ? detail.attributes[ 'data-option_name' ] : key;
	return isData ? ( 'data-' + markerAnimationParams.prefix + name ) : name;
};

const getDetailSettings = () => {
	const body = {};
	const defaultStyle = [];
	const attrs = {};

	Object.keys( markerAnimationParams.details ).forEach( key => {
		const detail = markerAnimationParams.details[ key ];
		if ( detail.ignore ) {
			return;
		}
		let value, item;
		if ( detail.form === 'input/checkbox' ) {
			value = detail.attributes.checked === 'checked';
			item = {
				type: 'checkbox',
				name: key,
				label: detail.title,
				checked: value,
			};
		} else if ( detail.form === 'color' ) {
			value = detail.value;
			item = {
				type: 'colorpicker',
				name: key,
				label: detail.title,
				value: value,
			};
		} else if ( detail.form === 'select' ) {
			const values = [];
			Object.keys( detail.options ).forEach( k => {
				values.push( {
					text: detail.options[ k ],
					label: detail.options[ k ],
					value: k,
				} );
			} );
			value = detail.value;
			item = {
				type: 'listbox',
				name: key,
				label: detail.title,
				values: values,
				value: value,
			};
		} else {
			value = detail.value;
			item = {
				type: 'textbox',
				name: key,
				label: detail.title,
				value: value,
			};
		}
		attrs[ getDataName( key, true ) ] = getDataAttribute( getDataName( key ) );
		defaultStyle.push( parseInputValue( value, key, true ) );
		body[ key ] = item;
	} );

	return {
		attrs,
		defaultStyle,
		body,
	};
};

/**
 * data attribute
 * @param name
 * @returns {Function}
 */
const getDataAttribute = name => {
	return vars => {
		if ( undefined === vars ) {
			return '';
		}
		return convertData( vars[ name ] );
	};
};

const convertData = data => {
	if ( undefined === data ) {
		return '';
	}
	if ( null === data ) {
		return 'null';
	}
	return data + '';
};

export { _addStyle, parseInputValue, getDetailSettings, convertData, getDataName };
