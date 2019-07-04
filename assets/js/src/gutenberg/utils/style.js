import { getDataName, parseInputValue } from './misc';

/* istanbul ignore next */
if ( markerAnimationParams.addedStyle === undefined ) {
	markerAnimationParams.addedStyle = {};
}

/**
 * @param {string} className class name
 * @param {string} style style
 * @param {string} originalKey original key
 * @param {string} key key
 * @param {*} value value
 * @param {boolean?} isData is data?
 * @param {boolean?} setupStripe setup stripe?
 * @param {boolean?} isAddStyle is add style?
 * @param {boolean?} isStripe is stripe?
 */
export const applyStyle = ( className, style, originalKey, key, value, isData, setupStripe, isAddStyle, isStripe ) => {
	let selector = 'body #editor .' + className;
	if ( 'color' === key ) {
		if ( ( isAddStyle || ! setupStripe ) && isData ) {
			selector += '[' + getDataName( key, true ) + '="' + value + '"]';
		}
		if ( setupStripe && 'color' === originalKey ) {
			selector += '[' + getDataName( 'stripe', true ) + '="' + ( isStripe ? 'false' : 'true' ) + '"]';
		}
	} else if ( isData ) {
		selector += '[' + getDataName( key, true ) + '="' + value + '"]';
	}
	const styleSheetElement = document.createElement( 'style' );

	document.head.appendChild( styleSheetElement );

	const sheet = styleSheetElement.sheet;
	/* istanbul ignore else */
	if ( sheet.insertRule ) {
		sheet.insertRule( selector + '{' + style + '}', sheet.cssRules.length );
	} else {
		sheet.addRule( selector, style );
	}
};

/**
 * add style
 * @param {string} key key
 * @param {*} value value
 * @param {string?} className class name
 * @param {boolean?} isData is data?
 * @param {boolean?} setupStripe setup stripe?
 * @param {boolean?} isAddStyle is add style?
 * @param {boolean?} isStripe is stripe?
 */
export const addStyle = ( key, value, className, isData, setupStripe, isAddStyle, isStripe ) => {
	addStyleHelper( ( className, style, originalKey ) => {
		applyStyle( className, style, originalKey, key, value, isData, setupStripe, isAddStyle, isStripe );
	}, key, value, className, setupStripe, isStripe );
};

/**
 * add style
 * @param {function} func apply function
 * @param {string} key key
 * @param {*} value value
 * @param {string?} className class name
 * @param {boolean?} setupStripe setup stripe?
 * @param {boolean?} isStripe is stripe?
 * @param {string?} originalKey original key
 */
export const addStyleHelper = ( func, key, value, className, setupStripe, isStripe, originalKey ) => {
	if ( undefined === className ) {
		className = markerAnimationParams.class;
	}
	if ( markerAnimationParams.addedStyle[ className ] && markerAnimationParams.addedStyle[ className ][ key ] && markerAnimationParams.addedStyle[ className ][ key ][ value ] ) {
		return;
	}

	if ( 'color' === key ) {
		addColorStyle( func, key, value, className, setupStripe, isStripe );
		return;
	}

	const style = getStyle( key, value );
	if ( style ) {
		func( className, style, originalKey );
	}
	if ( ! markerAnimationParams.addedStyle[ className ] ) {
		markerAnimationParams.addedStyle[ className ] = {};
	}
	if ( ! markerAnimationParams.addedStyle[ className ][ key ] ) {
		markerAnimationParams.addedStyle[ className ][ key ] = {};
	}
	markerAnimationParams.addedStyle[ className ][ key ][ value ] = true;
};

/**
 * @param {function} func apply function
 * @param {string} key key
 * @param {*} value value
 * @param {string?} className class name
 * @param {boolean?} setupStripe setup stripe?
 * @param {boolean?} isStripe is stripe?
 */
const addColorStyle = ( func, key, value, className, setupStripe, isStripe ) => {
	if ( undefined === isStripe ) {
		return;
	}
	if ( isStripe ) {
		addStyleHelper( func, key + '-stripe', value, className );
		if ( setupStripe ) {
			addStyleHelper( func, key + '-normal', value, className, false, false, key );
		}
	} else {
		addStyleHelper( func, key + '-normal', value, className );
		if ( setupStripe ) {
			addStyleHelper( func, key + '-stripe', value, className, false, false, key );
		}
	}
};

/**
 * @param {string} key key
 * @param {*} value value
 * @returns {string|null} style
 */
const getStyle = ( key, value ) => {
	if ( 'color-normal' === key ) {
		return 'background-image:linear-gradient(to right,rgba(255,255,255,0) 50%,' + value + ' 50%)';
	} else if ( 'color-stripe' === key ) {
		return 'background-image:repeating-linear-gradient(-45deg,' + value + ',' + value + ' 2px,transparent 2px,transparent 4px)';
	} else if ( 'thickness' === key ) {
		return 'background-size:200% ' + value;
	} else if ( 'font_weight' === key || 'bold' === key ) {
		return 'font-weight:' + ( ! value || value === 'null' ? 'normal' : value );
	} else if ( 'padding_bottom' === key ) {
		return 'padding-bottom:' + value;
	} else if ( 'display' === key ) {
		return 'display:' + value;
	} else if ( 'background-position' === key ) {
		return 'background-position:' + value;
	} else if ( 'background-repeat' === key ) {
		return 'background-repeat:' + value;
	}
	return null;
};

let hasLoaded = false;

/**
 * apply styles
 */
export const applyStyles = () => {
	if ( hasLoaded ) {
		return;
	}
	hasLoaded = true;

	Object.keys( markerAnimationParams.details ).forEach( key => {
		const detail = markerAnimationParams.details[ key ];
		if ( detail.ignore ) {
			return;
		}
		const { name, value } = parseInputValue( detail.form === 'input/checkbox' ? detail.attributes.checked === 'checked' : detail.value, key, true );
		addStyle( name, value );
		if ( 'color' === name ) {
			addStyle( name, value, undefined, true, true, false, markerAnimationParams.details[ 'stripe' ].detail.value );
		}
	} );
	wp.domReady( () => {
		[].forEach.call( document.getElementById( 'editor' ).getElementsByClassName( markerAnimationParams.class ), elem => {
			Object.keys( elem.dataset ).forEach( key => {
				// eslint-disable-next-line no-magic-numbers
				if ( key.substring( 0, markerAnimationParams.prefix.length ) === markerAnimationParams.prefix ) {
					addStyle( key.slice( markerAnimationParams.prefix.length ), elem.dataset[ key ], undefined, true, true, true, markerAnimationParams.details[ 'stripe' ].detail.value );
				}
			} );
		} );
	} );
};

/** @var {{
 * isValidDetailSetting: boolean,
 * class: string,
 * defaultIcon: string,
 * details: {},
 * prefix: string,
 * settings: {}}} markerAnimationParams */

