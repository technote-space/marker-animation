<?php
/**
 * Class AssetsTest
 *
 * @package Test_Travis
 */

use PHPUnit\Framework\TestCase;

use Marker_Animation\Classes\Models\Assets;
use Marker_Animation\Classes\Models\Custom_Post\Setting;

/**
 * @noinspection PhpUndefinedClassInspection
 * Assets test case.
 *
 * @mixin TestCase
 */
class AssetsTest extends WP_UnitTestCase {

	/**
	 * @var WP_Framework
	 */
	protected static $app;

	/**
	 * @var Assets $assets
	 */
	private static $assets;

	/**
	 * @var Setting $setting
	 */
	private static $setting;

	/**
	 * @SuppressWarnings(StaticAccess)
	 * @throws ReflectionException
	 */
	public static function setUpBeforeClass() {
		static::$app     = WP_Framework::get_instance( MARKER_ANIMATION );
		static::$assets  = Assets::get_instance( static::$app );
		static::$setting = Setting::get_instance( static::$app );
		static::reset();
	}

	/**
	 * @throws ReflectionException
	 */
	public static function tearDownAfterClass() {
		static::reset();
	}

	private static function reset() {
		wp_dequeue_style( static::$app->slug_name . '-marker_animation' );
		wp_dequeue_style( 'artb-css' );
		wp_dequeue_script( static::$app->slug_name . '-marker_animation' );
		static::$app->setting->edit_setting( 'is_valid', 'is_valid_marker_options_cache', true );
		static::$app->setting->edit_setting( 'is_valid', 'selector', '' );
		static::$app->delete_shared_object( '_hook_cache' );
		static::$app->option->delete( 'marker_options_cache' );
		static::$app->file->delete( static::$app->define->plugin_assets_dir . DS . 'js' . DS . 'marker-animation.min.js' );
	}

	public function test_setup_assets() {
		static::$app->file->put_contents( static::$app->define->plugin_assets_dir . DS . 'js' . DS . 'marker-animation.min.js', '' );
		$handle = static::$app->slug_name . '-marker_animation';
		wp_dequeue_style( $handle );
		$this->assertFalse( wp_script_is( $handle ) );

		static::$app->setting->edit_setting( 'is_valid', 'default', false );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertFalse( static::$app->filter->apply_filters( 'is_valid' ) );
		ob_start();
		do_action( 'wp_head' );
		ob_end_clean();
		$this->assertFalse( wp_script_is( $handle ) );

		static::$app->setting->edit_setting( 'is_valid', 'default', true );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertTrue( static::$app->filter->apply_filters( 'is_valid' ) );
		ob_start();
		do_action( 'wp_head' );
		ob_end_clean();
		$this->assertTrue( wp_script_is( $handle ) );
	}

	public function test_get_marker_options() {
		$key = 'marker_options_cache';
		static::$app->option->delete( $key );
		static::assertFalse( static::$app->option->exists( $key ) );

		static::$app->setting->edit_setting( 'is_valid_marker_options_cache', 'default', true );
		static::$app->setting->edit_setting( 'selector', 'default', 'test1' );
		static::$app->delete_shared_object( '_hook_cache' );
		$option = static::$assets->get_marker_options();
		static::assertTrue( static::$app->option->exists( $key ) );
		static::assertStringStartsWith( 'test1,', $option['selector'] );

		static::$app->setting->edit_setting( 'is_valid_marker_options_cache', 'default', true );
		static::$app->setting->edit_setting( 'selector', 'default', '' );
		static::$app->delete_shared_object( '_hook_cache' );
		$option = static::$assets->get_marker_options();
		static::assertStringStartsWith( 'test1,', $option['selector'] );

		static::$app->setting->edit_setting( 'is_valid_marker_options_cache', 'default', false );
		static::$app->setting->edit_setting( 'selector', 'default', 'test2' );
		static::$app->delete_shared_object( '_hook_cache' );
		$option = static::$assets->get_marker_options();
		static::assertStringStartsWith( 'test2,', $option['selector'] );
	}

	/**
	 * @throws Exception
	 */
	public function test_changed_option() {
		$key = 'marker_options_cache';

		static::$app->option->set( $key, true );
		static::assertTrue( static::$app->option->exists( $key ) );
		static::$app->filter->do_action( 'changed_option', 'test' );
		static::assertTrue( static::$app->option->exists( $key ) );

		static::$app->option->set( $key, true );
		static::assertTrue( static::$app->option->exists( $key ) );
		static::$app->filter->do_action( 'changed_option', 'marker_animation/test' );
		static::assertFalse( static::$app->option->exists( $key ) );
	}

	/**
	 * @dataProvider parse_setting_data_provider
	 *
	 * @param $setting
	 * @param $key
	 * @param $expected1
	 * @param $expected2
	 */
	public function test_parse_setting( $setting, $key, $expected1, $expected2 ) {
		$setting = static::$assets->parse_setting( $setting, $key );
		static::assertCount( 2, $setting );
		static::assertEquals( $expected1, $setting[0] );
		static::assertEquals( $expected2, $setting[1] );
	}

	public function parse_setting_data_provider() {
		return [
			[
				[
					'form'       => 'input/checkbox',
					'attributes' => [
						'data-option_name' => 'test-option1',
						'data-value'       => 0,
					],
				],
				'test1',
				'test-option1',
				false,
			],
			[
				[
					'form'       => 'input/checkbox',
					'attributes' => [
						'data-value' => 1,
					],
				],
				'test2',
				'test2',
				true,
			],
			[
				[
					'form'       => 'input/checkbox',
					'attributes' => [
						'data-value'          => 1,
						'data-option_value-1' => 'a',
					],
				],
				'test3',
				'test3',
				'a',
			],
			[
				[
					'form'       => 'input/checkbox',
					'attributes' => [
						'data-value'          => 0,
						'data-option_value-0' => 'b',
					],
				],
				'test4',
				'test4',
				'b',
			],
			[
				[
					'form'       => 'input/text',
					'attributes' => [
						'data-value' => 'c',
					],
				],
				'test5',
				'test5',
				'c',
			],
		];
	}

	/**
	 * @dataProvider setup_form_setting_data_provider
	 *
	 * @param $args
	 * @param $callback
	 *
	 * @throws ReflectionException
	 */
	public function test_setup_form_setting( $args, $callback ) {
		static::private_access_test( 'setup_form_setting', $args, $callback );
	}

	public function setup_form_setting_data_provider() {
		return [
			[
				[
					[],
					'test1',
					'',
				],
				function ( $ret ) {
					static::assertArrayHasKey( 'form', $ret );
					static::assertEquals( 'test1', $ret['form'] );
				},
			],
			[
				[
					[
						'form'     => 'a',
						'nullable' => false,
					],
					[
						'form' => 'test2',
						'args' => [
							'test2-arg1' => '21',
						],
					],
					'setting',
				],
				function ( $ret ) {
					static::assertArrayHasKey( 'form', $ret );
					static::assertArrayHasKey( 'test2-arg1', $ret );
					static::assertArrayNotHasKey( 'options', $ret );
					static::assertEquals( 'test2', $ret['form'] );
					static::assertEquals( '21', $ret['test2-arg1'] );
				},
			],
			[
				[
					[
						'nullable' => true,
					],
					[
						'form' => 'test3',
					],
					'setting',
				],
				function ( $ret ) {
					static::assertArrayHasKey( 'form', $ret );
					static::assertArrayNotHasKey( 'args', $ret );
					static::assertArrayHasKey( 'options', $ret );
					static::assertEquals( 'select', $ret['form'] );
					static::assertCount( 1, $ret['options'] );
				},
			],
			[
				[
					[
						'nullable' => true,
						'options'  => [ 'test4' => 4 ],
					],
					[
						'form' => 'test4',
					],
					'setting',
				],
				function ( $ret ) {
					static::assertArrayHasKey( 'form', $ret );
					static::assertArrayNotHasKey( 'args', $ret );
					static::assertArrayHasKey( 'options', $ret );
					static::assertEquals( 'select', $ret['form'] );
					static::assertCount( 2, $ret['options'] );
				},
			],
		];
	}

	/**
	 * @dataProvider setup_bool_setting_data_provider
	 *
	 * @param $args
	 * @param $callback
	 *
	 * @throws ReflectionException
	 */
	public function test_setup_bool_setting( $args, $callback ) {
		static::private_access_test( 'setup_bool_setting', $args, $callback );
	}

	public function setup_bool_setting_data_provider() {
		return [
			[
				[
					[
						'form'    => 'select',
						'options' => [
							'1' => 'a',
							'2' => 'b',
						],
					],
					'',
				],
				function ( $ret ) {
					static::assertEquals( 'Valid', $ret['options']['1'] );
					static::assertEquals( 'Invalid', $ret['options']['0'] );
				},
			],
			[
				[
					[
						'form'  => 'test',
						'value' => 'a',
					],
					'',
				],
				function ( $ret ) {
					static::assertEquals( 1, $ret['value'] );
					static::assertArrayNotHasKey( 'attributes', $ret );
					static::assertArrayHasKey( 'label', $ret );
				},
			],
			[
				[
					[
						'form'  => 'test',
						'value' => 'a',
					],
					'1',
				],
				function ( $ret ) {
					static::assertEquals( 1, $ret['value'] );
					static::assertArrayHasKey( 'attributes', $ret );
					static::assertArrayHasKey( 'label', $ret );
				},
			],
		];
	}

	/**
	 * @dataProvider setup_select_setting_data_provider
	 *
	 * @param $args
	 * @param $callback
	 *
	 * @throws ReflectionException
	 */
	public function test_setup_select_setting( $args, $callback ) {
		static::private_access_test( 'setup_select_setting', $args, $callback );
	}

	public function setup_select_setting_data_provider() {
		return [
			[
				[
					[
						'selected' => 'a',
					],
					'b',
				],
				function ( $ret ) {
					static::assertEquals( 'b', $ret['selected'] );
				},
			],
			[
				[
					[
						'options' => [
							'a' => 'b',
						],
					],
					'a',
				],
				function ( $ret ) {
					static::assertEquals( 'a', $ret['selected'] );
					static::assertArrayHasKey( 'a', $ret['options'] );
				},
			],
			[
				[
					[
						'options' => [
							'a' => 'b',
						],
					],
					'c',
				],
				function ( $ret ) {
					static::assertEquals( 'c', $ret['selected'] );
					static::assertArrayHasKey( 'c', $ret['options'] );
				},
			],
		];
	}

	/**
	 * @param $method
	 * @param $args
	 * @param $callback
	 *
	 * @throws ReflectionException
	 */
	private static function private_access_test( $method, $args, $callback ) {
		$reflection = new ReflectionMethod( static::$assets, $method );
		$reflection->setAccessible( true );
		$ret = $reflection->invoke( static::$assets, ...$args );
		$callback( $ret );
		$reflection->setAccessible( false );
	}
}
