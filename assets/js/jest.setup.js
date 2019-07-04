import Enzyme from 'enzyme';
import Adapter from 'enzyme-adapter-react-16';

Enzyme.configure( {
	adapter: new Adapter(),
	snapshotSerializers: [ 'enzyme-to-json/serializer' ],
} );

const Mousetrap = require( 'mousetrap' );
const lodash = require( 'lodash' );
global.Mousetrap = Mousetrap;
global.window.lodash = lodash;
global.window.matchMedia = () => ( {
	matches: true, addListener: () => {
	},
} );
global.markerAnimationParams = {
	translate: {},
	class: 'test-marker-animation-class',
	prefix: 'prefix_',
	details: {
		'test1-1': {
			form: 'input/checkbox',
			attributes: {
				'data-value': 'test1',
				'data-option_name': 'test1-option',
			},
		},
		'test1-2': {
			form: 'input/checkbox',
			attributes: {
				'data-value': false,
			},
		},
		test2: {
			form: 'input/checkbox',
			attributes: {
				'data-value': 'test2',
				'data-option_value-1': 'value1',
				'data-option_value-0': 'value0',
			},
		},
		test3: {
			form: 'input/text',
			attributes: {
				'data-value': 'test3',
			},
		},
		test4: {
			form: 'select',
			attributes: {
				'data-value': 'test4',
			},
			options: { t1: 1, t2: 2 },
		},
		test5: {
			form: 'color',
			attributes: {
				'data-value': 'test5',
			},
		},
		test6: {
			form: 'color',
			ignore: true,
			attributes: {
				'data-value': 'test6',
			},
		},
		color: {
			form: 'color',
			value: 'blue',
			attributes: {},
		},
		stripe: {
			form: 'input/checkbox',
			detail: {
				value: true,
			},
			attributes: {},
		},
	},
};

import domReady from '@wordpress/dom-ready' ;

const blockEditor = require( '@wordpress/block-editor' );
const blocks = require( '@wordpress/blocks' );
const components = require( '@wordpress/components' );
const compose = require( '@wordpress/compose' );
const coreData = require( '@wordpress/core-data' );
const data = require( '@wordpress/data' );
const editor = require( '@wordpress/editor' );
const element = require( '@wordpress/element' );
const formatLibrary = require( '@wordpress/format-library' );
const hooks = require( '@wordpress/hooks' );
const i18n = require( '@wordpress/i18n' );
const keycodes = require( '@wordpress/keycodes' );
const richText = require( '@wordpress/rich-text' );
const url = require( '@wordpress/url' );

global.wp = {
	blockEditor,
	blocks,
	components,
	compose,
	coreData,
	data,
	domReady,
	editor,
	element,
	formatLibrary,
	hooks,
	i18n,
	keycodes,
	richText,
	url,
};

{
	const editorDiv = document.createElement( 'div' );
	editorDiv.id = 'editor';

	const addChild = ( tag, dataset ) => {
		const child = document.createElement( tag );
		child.className = markerAnimationParams.class;
		Object.keys( dataset ).forEach( key => {
			child.dataset[ key ] = dataset[ key ];
		} );
		editorDiv.append( child );
	};
	const getDataKey = key => `${ global.markerAnimationParams.prefix }${ key }`;

	addChild( 'div', { [ getDataKey( 'color' ) ]: 'red', test: '123' } );
	addChild( 'div', { [ getDataKey( 'font_weight' ) ]: 'bold' } );

	document.body.appendChild( editorDiv );
}
