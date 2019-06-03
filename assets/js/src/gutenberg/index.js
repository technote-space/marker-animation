const { InspectorControls } = wp.editor;
const { PanelBody, CheckboxControl, SelectControl, ColorPalette } = wp.components;
const { applyFormat, getActiveFormat } = wp.richText;
const { select } = wp.data;

import { _addStyle, parseInputValue, getDetailSettings, convertData, getDataName } from '../common/helpers';
import { registerFormatType } from '../../../../../../../../misc/gutenberg/richtext-helpers';
import MyTextControl from './components/text-control';

/** @var {{
 * is_valid_detail_setting: boolean,
 * class: string,
 * details: {},
 * settings: {}}} markerAnimationParams */

const maClassName = markerAnimationParams.class;
const { attrs, defaultStyle, body } = getDetailSettings();

/**
 * add style
 * @param key
 * @param value
 * @param className
 * @param isData
 * @param setupStripe
 * @param isAddStyle
 * @param isStripe
 */
const addStyle = ( key, value, className, isData, setupStripe, isAddStyle, isStripe ) => {
	_addStyle( ( className, style, _key ) => {
		let selector = 'body #editor .' + className;
		if ( 'color' === key ) {
			if ( ( isAddStyle || ! setupStripe ) && isData ) {
				selector += '[' + getDataName( key, true ) + '="' + value + '"]';
			}
			if ( setupStripe && 'color' === _key ) {
				selector += '[' + getDataName( 'stripe', true ) + '="' + ( isStripe ? 'false' : 'true' ) + '"]';
			}
		} else if ( isData ) {
			selector += '[' + getDataName( key, true ) + '="' + value + '"]';
		}
		const styleSheetElement = document.createElement( 'style' );

		document.head.appendChild( styleSheetElement );

		const sheet = styleSheetElement.sheet;
		if ( sheet.insertRule ) {
			sheet.insertRule( selector + '{' + style + '}', sheet.cssRules.length );
		} else {
			sheet.addRule( selector, style );
		}
	}, key, value, className, setupStripe, isStripe );
};

const getPanelComponent = ( setting, control, args, stripe ) => {
	if ( stripe && [ 'duration', 'delay', 'function', 'repeat' ].indexOf( setting.name ) >= 0 ) {
		return null;
	}

	const id = 'marker-setting-' + setting.name;
	const value = getData( setting, control, args );
	switch ( setting.type ) {
		case 'colorpicker':
			const settings = select( 'core/editor' ).getEditorSettings();
			const colors = settings.colors;
			return <ColorPalette
				id={ id }
				label={ setting.label }
				colors={ colors }
				value={ value }
				onChange={ setData( setting, control, args ) }
			/>;
		case 'textbox':
			return <MyTextControl
				id={ id }
				label={ setting.label }
				value={ value }
				onChange={ setData( setting, control, args ) }
			/>;
		case 'checkbox':
			return <CheckboxControl
				label={ setting.label }
				checked={ value }
				onChange={ setData( setting, control, args ) }
			/>;
		case 'listbox':
			return <SelectControl
				id={ id }
				label={ setting.label }
				options={ setting.values }
				value={ value }
				onChange={ setData( setting, control, args ) }
			/>;
	}
	return null;
};

const getData = ( setting, control, args ) => {
	const activeFormat = getActiveFormat( args.value, control.setting.name );
	const name = getDataName( setting.name, true );
	if ( ! activeFormat || ! activeFormat.attributes || ! ( name in activeFormat.attributes ) ) {
		return setting.type === 'checkbox' ? setting.checked : setting.value;
	}
	if ( setting.type === 'checkbox' ) {
		if ( activeFormat.attributes[ name ] === 'true' ) {
			return true;
		}
		const result = parseInputValue( true, setting.name );
		if ( ! result ) {
			return false;
		}
		return result.value === activeFormat.attributes[ name ];
	}
	return activeFormat.attributes[ name ];
};

const setData = ( setting, control, args ) => {
	return value => {
		const attributes = args.activeAttributes;
		const result = parseInputValue( value, setting.name );
		const name = getDataName( setting.name, true );
		if ( result ) {
			const value = convertData( result.value );
			attributes[ name ] = value;
			addStyle( result.key, value, maClassName, true, true, true, markerAnimationParams.details[ 'stripe' ].detail.value );
			args.onChange( applyFormat( args.value, {
				type: control.setting.name,
				attributes: attributes,
			} ) );
		} else {
			if ( name in attributes ) {
				delete attributes[ name ];
				args.onChange( applyFormat( args.value, {
					type: control.setting.name,
					attributes: attributes,
				} ) );
			}
		}
	};
};

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
	[].forEach.call( document.getElementById( 'editor' ).getElementsByClassName( maClassName ), elem => {
		Object.keys( elem.dataset ).forEach( key => {
			if ( key.substring( 0, markerAnimationParams.prefix.length ) === markerAnimationParams.prefix ) {
				addStyle( key.slice( markerAnimationParams.prefix.length ), elem.dataset[ key ], undefined, true, true, true, markerAnimationParams.details[ 'stripe' ].detail.value );
			}
		} );
	} );
} );

let getComponent = null;
if ( markerAnimationParams.is_valid_detail_setting ) {
	getComponent = ( args, control ) => {
		const stripe = getData( body[ 'stripe' ], control, args );
		return <InspectorControls>
			<PanelBody
				title={ markerAnimationParams.title }
				initialOpen={ true }
			>
				{ Object.keys( body ).map( key => getPanelComponent( body[ key ], control, args, stripe ) ) }
			</PanelBody>
		</InspectorControls>;
	};
}
registerFormatType( {
	id: 'default',
	title: markerAnimationParams.title,
	className: maClassName,
	attributes: attrs,
	getComponent: getComponent,
} );

Object.keys( markerAnimationParams.settings ).forEach( key => {
	const setting = markerAnimationParams.settings[ key ];
	/**
	 * @var {bool} setting.options.is_valid_button_block_editor
	 */

	addStyle( 'display', 'inline', setting.options.class );
	addStyle( 'background-position', 'left -100% center', setting.options.class );
	addStyle( 'background-repeat', 'repeat-x', setting.options.class );

	defaultStyle.forEach( style => {
		addStyle( style.name, style.value, setting.options.class );
	} );
	Object.keys( setting.options ).forEach( key => {
		addStyle( key, setting.options[ key ], setting.options.class, false, false, false, setting.options[ 'stripe' ] );
	} );

	if ( setting.options.is_valid_button_block_editor ) {
		registerFormatType( {
			id: setting.id,
			title: setting.title,
			className: setting.options.class,
			group: markerAnimationParams.title,
		} );
	}
} );
