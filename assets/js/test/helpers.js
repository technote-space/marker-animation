/* eslint-disable no-magic-numbers */
const should = require( 'should' );
import { _addStyle, parseInputValue, getDetailSettings, convertData, getDataName } from '../src/common/helpers';

describe( '_addStyle test', () => {
	it( 'should set styles', () => {
		[
			'thickness',
			'padding_bottom',
			'display',
			'background-position',
			'background-repeat',
		].forEach( key => {
			let called = 0;
			const func = ( expectedValue, expectedClassName ) => ( className, style, _key ) => {
				className.should.equal( expectedClassName );
				style.should.endWith( expectedValue );
				should( _key ).undefined();
				called++;
			};
			_addStyle( func( 'test1', 'test-class1' ), key, 'test1' );
			called.should.equal( 1 );
			_addStyle( func( 'test1', 'test-class1' ), key, 'test1' );
			called.should.equal( 1 );
			_addStyle( func( 'test2', 'test-class1' ), key, 'test2' );
			called.should.equal( 2 );
			_addStyle( func( 'test2', 'test-class2' ), key, 'test2', 'test-class2' );
			called.should.equal( 3 );
		} );
	} );
	it( 'should set font_weight', () => {
		let called = 0;
		const func = ( expectedValue, expectedClassName ) => ( className, style, _key ) => {
			className.should.equal( expectedClassName );
			style.should.endWith( expectedValue );
			should( _key ).undefined();
			called++;
		};
		_addStyle( func( 'normal', 'test-class1' ), 'font_weight', 0 );
		called.should.equal( 1 );
		_addStyle( func( 'normal', 'test-class1' ), 'font_weight', 0 );
		called.should.equal( 1 );
		_addStyle( func( 'normal', 'test-class1' ), 'font_weight', 'null' );
		called.should.equal( 2 );
		_addStyle( func( 'bold', 'test-class1' ), 'font_weight', 'bold' );
		called.should.equal( 3 );
		_addStyle( func( 'bold', 'test-class2' ), 'font_weight', 'bold', 'test-class2' );
		called.should.equal( 4 );
	} );
	it( 'should not set color', () => {
		let called = 0;
		const func = () => {
			called++;
		};
		_addStyle( func, 'color', 'red' );
		called.should.equal( 0 );
	} );
	it( 'should set color', () => {
		let called = 0;
		const func = ( expectedValue, expectedClassName ) => ( className, style, _key ) => {
			className.should.equal( expectedClassName );
			style.should.startWith( expectedValue );
			should( _key ).undefined();
			called++;
		};
		_addStyle( func( 'background-image:linear-gradient', 'test-class1' ), 'color', 'red', undefined, false, false );
		called.should.equal( 1 );
		_addStyle( func( 'background-image:repeating-linear-gradient', 'test-class2' ), 'color', 'red', 'test-class2', false, true );
		called.should.equal( 2 );
	} );
	it( 'should set stripe color', () => {
		let called = 0;
		const func = () => {
			called++;
		};
		_addStyle( func, 'color', 'red', 'test-class3', true, true );
		called.should.equal( 2 );
		_addStyle( func, 'color', 'red', 'test-class4', true, false );
		called.should.equal( 4 );
	} );
} );

describe( 'parseInputValue test', () => {
	it( 'should return false', () => {
		parseInputValue( 1, 'test1-1', false ).should.false();
		parseInputValue( 0, 'test1-2', false ).should.false();
		parseInputValue( undefined, 'test1-1', false ).should.false();
		parseInputValue( '', 'test1-1', false ).should.false();
		parseInputValue( 'test1', 'test1-1', false ).should.false();
	} );

	it( 'should return value', () => {
		parseInputValue( 1, 'test1-1', true ).value.should.equal( 1 );
		parseInputValue( 0, 'test1-1', true ).value.should.equal( 0 );
		parseInputValue( 1, 'test2', true ).value.should.equal( 'value1' );
		parseInputValue( 0, 'test2', true ).value.should.equal( 'value0' );
	} );
} );

describe( 'getDetailSettings test', () => {
	it( 'should get settings', () => {
		const settings = getDetailSettings();
		settings.should.ownProperty( 'attrs' );
		settings.should.ownProperty( 'defaultStyle' );
		settings.should.ownProperty( 'body' );
		settings.attrs.should.ownProperty( 'data-prefix-test1-1' );
		settings.attrs.should.ownProperty( 'data-prefix-test1-2' );
		settings.attrs.should.ownProperty( 'data-prefix-test2' );
		settings.attrs.should.ownProperty( 'data-prefix-test3' );
		settings.attrs.should.ownProperty( 'data-prefix-test4' );
		settings.attrs.should.ownProperty( 'data-prefix-test5' );
		settings.attrs.should.not.ownProperty( 'data-prefix-test6' );
		settings.defaultStyle.should.length( 6 );
		settings.body.should.ownProperty( 'test1-1' );
		settings.body.should.ownProperty( 'test1-2' );
		settings.body.should.ownProperty( 'test2' );
		settings.body.should.ownProperty( 'test3' );
		settings.body.should.ownProperty( 'test4' );
		settings.body.should.ownProperty( 'test5' );
		settings.body.should.not.ownProperty( 'test6' );

		settings.attrs[ 'data-prefix-test1-1' ]().should.equal( '' );
		settings.attrs[ 'data-prefix-test1-1' ]( { 'test1-1': 1 } ).should.equal( '1' );
	} );
} );

describe( 'convertData test', () => {
	it( 'should', () => {
		convertData( undefined ).should.equal( '' );
		convertData( null ).should.equal( 'null' );
		convertData( 'test' ).should.equal( 'test' );
		convertData( 1 ).should.equal( '1' );
		convertData( true ).should.equal( 'true' );
	} );
} );

describe( 'getDataName test', () => {
	it( 'should', () => {
		getDataName( 'test1-1' ).should.equal( 'test1-1' );
		getDataName( 'test1-1', true ).should.equal( 'data-prefix-test1-1' );
	} );
} );
