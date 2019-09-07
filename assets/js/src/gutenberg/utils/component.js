import { Common } from '../wrapper';
import MyTextControl from '../components/text-control';
import { getData, setData, resetData, getActiveFormatData } from './format';
import { body, attrs, getName, translate, getIcon } from './misc';

const { CheckboxControl, SelectControl, ColorPalette, Button } = wp.components;
const { Fragment } = wp.element;
const { getColors } = Common.Helpers;

/**
 * @param {object} setting setting
 * @param {object} args args
 * @param {string} formatName format name
 * @param {*} value value
 * @param {boolean} stripe is stripe?
 * @returns {null|*} panel component
 */
export const getPanelComponent = ( setting, args, formatName, value, stripe ) => {
	if ( ! args.isActive ) {
		return null;
	}

	// eslint-disable-next-line no-magic-numbers
	if ( stripe && [ 'duration', 'delay', 'function', 'repeat' ].indexOf( setting.name ) >= 0 ) {
		return null;
	}

	const id = 'marker-setting-' + setting.name;
	switch ( setting.type ) {
		case 'colorpicker':
			return <ColorPalette
				id={ id }
				key={ id }
				label={ setting.label }
				colors={ getColors() }
				value={ value }
				onChange={ setData( setting, args, formatName ) }
			/>;
		case 'textbox':
			return <MyTextControl
				id={ id }
				key={ id }
				label={ setting.label }
				value={ value }
				args={ args }
				onChange={ setData( setting, args, formatName ) }
			/>;
		case 'checkbox':
			return <CheckboxControl
				key={ id }
				label={ setting.label }
				checked={ value }
				onChange={ setData( setting, args, formatName ) }
			/>;
		case 'listbox':
			return <SelectControl
				id={ id }
				key={ id }
				label={ setting.label }
				options={ setting.values }
				value={ value }
				onChange={ setData( setting, args, formatName ) }
			/>;
		default:
			return null;
	}
};

/**
 * @returns {array} props
 */
export const getDefaultFormatGroupProps = () => ( [
	getName( 'default' ),
	{
		toolbarGroup: getName( 'toolbar' ),
		label: translate( 'Marker Animation' ),
		inspectorSettings: {
			title: translate( 'Marker Animation (detail setting)' ),
			initialOpen: true,
		},
	},
] );

/**
 * @returns {array} props
 */
export const getSettingFormatGroupProps = () => ( [
	getName( 'setting' ),
	{
		toolbarGroup: getName( 'toolbar' ),
		label: translate( 'Marker Animation' ),
		icon: getIcon(),
	},
] );

/**
 * @returns {array} props
 */
export const getDefaultFormatButtonProps = () => ( [
	getName( 'default' ),
	'marker-animation',
	getIcon(),
	{
		title: translate( 'Marker Animation' ),
		className: markerAnimationParams.class,
		inspectorGroup: getName( 'default' ),
		useInspectorSetting: true,
		createInspector: getCreateInspectorFunction(),
		attributes: attrs,
	},
] );

/**
 * @returns {function} create inspector function
 */
export const getCreateInspectorFunction = () => ( { args, formatName } ) => {
	const stripe = getData( ...getActiveFormatData( body[ 'stripe' ], args, formatName ) );
	let isValidReset = false;
	const components = Object.keys( body ).map( key => {
		const setting = body[ key ];
		const data = getActiveFormatData( setting, args, formatName );
		const value = getData( ...data );
		if ( data[ 1 ] ) {
			isValidReset = true;
		}
		return getPanelComponent( setting, args, formatName, value, stripe );
	} );
	return <Fragment>
		{ components }
		{ isValidReset && <Button
			isDefault={ true }
			onClick={
				() => Object.keys( body ).forEach( key => resetData( body[ key ], args, formatName ) )
			}>{ translate( 'Reset' ) }
		</Button> }
	</Fragment>;
};

/** @var {{
 * isValidDetailSetting: boolean,
 * class: string,
 * defaultIcon: string,
 * details: {},
 * prefix: string,
 * settings: {}}} markerAnimationParams */

