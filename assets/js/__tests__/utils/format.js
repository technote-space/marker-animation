/* eslint-disable no-magic-numbers */
import { getActiveFormatData, getData, setData, resetData } from '../../src/gutenberg/utils/format';

describe( 'getActiveFormatData', () => {
	const em = { type: 'em', attributes: { [ 'data-prefix_test' ]: 'test' } };
	const strong = { type: 'strong' };
	it( 'should get active format data', () => {
		const record = {
			formats: [ [ em ], [ strong ], [] ],
			text: 'one',
			start: 0,
			end: 2,
		};

		const data = getActiveFormatData( { name: 'test' }, {
			value: record,
		}, 'em' );

		expect( data ).toHaveLength( 3 );
		expect( data[ 0 ] ).toHaveProperty( 'name' );
		expect( data[ 0 ][ 'name' ] ).toBe( 'test' );
		expect( data[ 1 ] ).toHaveProperty( 'type' );
		expect( data[ 1 ] ).toHaveProperty( 'attributes' );
		expect( data[ 1 ][ 'type' ] ).toBe( 'em' );
		expect( data[ 2 ] ).toBe( 'data-prefix_test' );
	} );

	it( 'should get active format data', () => {
		const record = {
			formats: [ [ em ], [ strong ], [] ],
			text: 'one',
			start: 0,
			end: 2,
		};

		const data = getActiveFormatData( { name: 'test2' }, {
			value: record,
		}, 'em' );

		expect( data ).toHaveLength( 3 );
		expect( data[ 0 ] ).toHaveProperty( 'name' );
		expect( data[ 0 ][ 'name' ] ).toBe( 'test2' );
		expect( data[ 1 ] ).toBeNull();
		expect( data[ 2 ] ).toBe( 'data-prefix_test2' );
	} );

	it( 'should not get active format data', () => {
		const record = {
			formats: [ [ em ], [ strong ], [] ],
			text: 'one',
			start: 0,
			end: 2,
		};

		const data = getActiveFormatData( { name: 'test' }, {
			value: record,
		}, 'strong' );

		expect( data ).toHaveLength( 3 );
		expect( data[ 0 ] ).toHaveProperty( 'name' );
		expect( data[ 0 ][ 'name' ] ).toBe( 'test' );
		expect( data[ 1 ] ).toBeNull();
		expect( data[ 2 ] ).toBe( 'data-prefix_test' );
	} );

	it( 'should not get active format data', () => {
		const record = {
			formats: [ [ em ], [ strong ], [] ],
			text: 'one',
			start: 0,
			end: 2,
		};

		const data = getActiveFormatData( { name: 'test' }, {
			value: record,
		}, 'a' );

		expect( data ).toHaveLength( 3 );
		expect( data[ 0 ] ).toHaveProperty( 'name' );
		expect( data[ 0 ][ 'name' ] ).toBe( 'test' );
		expect( data[ 1 ] ).toBeNull();
		expect( data[ 2 ] ).toBe( 'data-prefix_test' );
	} );
} );

describe( 'getData', () => {
	it( 'should get data', () => {
		const data = getData( {
			type: 'checkbox',
			checked: 'a',
			value: 'b',
		}, null, '' );
		expect( data ).toBe( 'a' );
	} );

	it( 'should get data', () => {
		const data = getData( {
			type: 'text',
			value: 'a',
		}, null, '' );
		expect( data ).toBe( 'a' );
	} );

	it( 'should get data', () => {
		const data = getData( {
			type: 'checkbox',
			value: 'a',
		}, {
			attributes: {
				test: 'true',
			},
		}, 'test' );
		expect( data ).toBe( true );
	} );

	it( 'should get data', () => {
		const data = getData( {
			type: 'checkbox',
			value: 'a',
			name: 'test1-1',
		}, {
			attributes: {
				test: 'false',
			},
		}, 'test' );
		expect( data ).toBe( false );
	} );

	it( 'should get data', () => {
		const data = getData( {
			type: 'checkbox',
			value: 'a',
			name: 'test1-2',
		}, {
			attributes: {
				test: true,
			},
		}, 'test' );
		expect( data ).toBe( true );
	} );

	it( 'should get data', () => {
		const data = getData( {
			type: 'text',
		}, {
			attributes: {
				test: 'a',
			},
		}, 'test' );
		expect( data ).toBe( 'a' );
	} );
} );

describe( 'setData', () => {
	const em = { type: 'em', attributes: { [ 'data-prefix_test' ]: 'test' } };
	const strong = { type: 'strong' };
	const color = { type: 'color', attributes: { [ 'data-prefix_color' ]: 'red' } };
	it( 'should set data', () => {
		const record = {
			formats: [ [ em ], [ strong, em ], [ em ] ],
			text: 'one',
			start: 0,
			end: 2,
		};
		const onChange = jest.fn( value => {
			expect( value.formats ).toHaveLength( 3 );
			expect( value.formats[ 0 ] ).toHaveLength( 2 );
			expect( value.formats[ 0 ][ 1 ].type ).toBe( 'color' );
			expect( value.formats[ 0 ][ 1 ].attributes ).toEqual( { [ 'data-prefix_color' ]: 'green' } );
			expect( value.formats[ 1 ] ).toHaveLength( 3 );
			expect( value.formats[ 1 ][ 1 ].type ).toBe( 'color' );
			expect( value.formats[ 1 ][ 1 ].attributes ).toEqual( { [ 'data-prefix_color' ]: 'green' } );
			expect( value.formats[ 2 ] ).toHaveLength( 1 );
			expect( value.formats[ 2 ][ 0 ].type ).toBe( 'em' );
		} );
		const func = setData( {
			name: 'color',
		}, {
			activeAttributes: {
				[ 'data-prefix_color' ]: 'red',
			},
			onChange: onChange,
			value: record,
		}, 'color' );
		expect( typeof func ).toBe( 'function' );
		func( 'green' );
		expect( onChange ).toBeCalled();
	} );

	it( 'should set data', () => {
		const record = {
			formats: [ [ em ], [ strong, em ], [ em ] ],
			text: 'one',
			start: 0,
			end: 2,
		};
		const onChange = jest.fn();
		const func = setData( {
			name: 'color',
		}, {
			activeAttributes: {},
			onChange: onChange,
			value: record,
		}, 'color' );
		expect( typeof func ).toBe( 'function' );
		func( undefined );
		expect( onChange ).not.toBeCalled();
	} );

	it( 'should set data', () => {
		const record = {
			formats: [ [ color ], [ strong, color ], [ em, color ] ],
			text: 'one',
			start: 0,
			end: 2,
		};
		const onChange = jest.fn( value => {
			expect( value.formats ).toHaveLength( 3 );
			expect( value.formats[ 0 ] ).toHaveLength( 1 );
			expect( value.formats[ 0 ][ 0 ].type ).toBe( 'color' );
			expect( value.formats[ 0 ][ 0 ].attributes ).toEqual( {} );
			expect( value.formats[ 1 ] ).toHaveLength( 2 );
			expect( value.formats[ 1 ][ 0 ].type ).toBe( 'color' );
			expect( value.formats[ 1 ][ 0 ].attributes ).toEqual( {} );
			expect( value.formats[ 2 ] ).toHaveLength( 2 );
			expect( value.formats[ 2 ][ 1 ].type ).toBe( 'color' );
			expect( value.formats[ 2 ][ 1 ].attributes ).toHaveProperty( 'data-prefix_color' );
		} );
		const func = setData( {
			name: 'color',
		}, {
			activeAttributes: {
				[ 'data-prefix_color' ]: 'red',
			},
			onChange: onChange,
			value: record,
		}, 'color' );
		expect( typeof func ).toBe( 'function' );
		func( undefined );
		expect( onChange ).toBeCalled();
	} );
} );

describe( 'resetData', () => {
	it( 'should reset data', () => {
		const onChange = jest.fn();
		resetData( {
			name: 'test',
		}, {
			activeAttributes: {},
			onChange: onChange,
		}, 'test1' );
		expect( onChange ).not.toBeCalled();
	} );

	it( 'should reset data', () => {
		const onChange = jest.fn( value => {
			expect( value.formats ).toHaveLength( 3 );
			expect( value.formats[ 0 ] ).toHaveLength( 1 );
			expect( value.formats[ 0 ][ 0 ].type ).toBe( 'color' );
			expect( value.formats[ 0 ][ 0 ].attributes ).not.toHaveProperty( 'data-prefix_color' );
			expect( value.formats[ 0 ][ 0 ].attributes ).toHaveProperty( 'test' );
			expect( value.formats[ 1 ][ 0 ].type ).toBe( 'color' );
			expect( value.formats[ 1 ][ 0 ].attributes ).not.toHaveProperty( 'data-prefix_color' );
			expect( value.formats[ 1 ][ 0 ].attributes ).toHaveProperty( 'test' );
			expect( value.formats[ 2 ][ 0 ].type ).toBe( 'color' );
			expect( value.formats[ 2 ][ 0 ].attributes ).toHaveProperty( 'data-prefix_color' );
			expect( value.formats[ 2 ][ 0 ].attributes ).not.toHaveProperty( 'test' );
		} );
		const color = { type: 'color', attributes: { [ 'data-prefix_color' ]: 'red' } };
		resetData( {
			name: 'color',
		}, {
			value: {
				formats: [ [ color ], [ color ], [ color ] ],
				text: 'one',
				start: 0,
				end: 2,
			},
			activeAttributes: {
				[ 'data-prefix_color' ]: 'red',
				test: 'red',
			},
			onChange: onChange,
		}, 'color' );
		expect( onChange ).toBeCalled();
	} );
} );
