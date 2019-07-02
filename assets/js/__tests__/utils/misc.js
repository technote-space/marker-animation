/* eslint-disable no-magic-numbers */
import { parseInputValue, getDetailSettings, convertData, getDataName } from '../../src/gutenberg/utils/misc';

describe( 'parseInputValue', () => {
	it( 'should return false', () => {
		expect( parseInputValue( 1, 'test1-1', false ) ).toBeFalse();
		expect( parseInputValue( 0, 'test1-2', false ) ).toBeFalse();
		expect( parseInputValue( undefined, 'test1-1', false ) ).toBeFalse();
		expect( parseInputValue( '', 'test1-1', false ) ).toBeFalse();
		expect( parseInputValue( 'test1', 'test1-1', false ) ).toBeFalse();
	} );

	it( 'should return value', () => {
		expect( parseInputValue( 1, 'test1-1', true ).value ).toBe( 1 );
		expect( parseInputValue( 0, 'test1-1', true ).value ).toBe( 0 );
		expect( parseInputValue( 1, 'test2', true ).value ).toBe( 'value1' );
		expect( parseInputValue( 0, 'test2', true ).value ).toBe( 'value0' );
	} );
} );

describe( 'getDetailSettings', () => {
	it( 'should get settings', () => {
		const settings = getDetailSettings();
		expect( settings ).toHaveProperty( 'attrs' );
		expect( settings ).toHaveProperty( 'defaultStyle' );
		expect( settings ).toHaveProperty( 'body' );
		expect( settings.attrs ).toHaveProperty( 'data-prefix-test1-1' );
		expect( settings.attrs ).toHaveProperty( 'data-prefix-test1-2' );
		expect( settings.attrs ).toHaveProperty( 'data-prefix-test2' );
		expect( settings.attrs ).toHaveProperty( 'data-prefix-test3' );
		expect( settings.attrs ).toHaveProperty( 'data-prefix-test4' );
		expect( settings.attrs ).toHaveProperty( 'data-prefix-test5' );
		expect( settings.attrs ).not.toHaveProperty( 'data-prefix-test6' );
		expect( settings.defaultStyle ).toHaveLength( 6 );
		expect( settings.body ).toHaveProperty( 'test1-1' );
		expect( settings.body ).toHaveProperty( 'test1-2' );
		expect( settings.body ).toHaveProperty( 'test2' );
		expect( settings.body ).toHaveProperty( 'test3' );
		expect( settings.body ).toHaveProperty( 'test4' );
		expect( settings.body ).toHaveProperty( 'test5' );
		expect( settings.body ).not.toHaveProperty( 'test6' );
	} );
} );

describe( 'convertData', () => {
	it( 'should', () => {
		expect( convertData( undefined ) ).toBe( '' );
		expect( convertData( null ) ).toBe( 'null' );
		expect( convertData( 'test' ) ).toBe( 'test' );
		expect( convertData( 1 ) ).toBe( '1' );
		expect( convertData( true ) ).toBe( 'true' );
	} );
} );

describe( 'getDataName', () => {
	it( 'should', () => {
		expect( getDataName( 'test1-1' ) ).toBe( 'test1-1' );
		expect( getDataName( 'test1-1', true ) ).toBe( 'data-prefix-test1-1' );
	} );
} );
