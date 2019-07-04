<?php
/**
 * Class DashboardTest
 *
 * @package Test_Travis
 */

use PHPUnit\Framework\TestCase;

use Marker_Animation\Classes\Controllers\Admin\Dashboard;

/**
 * @noinspection PhpUndefinedClassInspection
 * Dashboard test case.
 *
 * @mixin TestCase
 */
class DashboardTest extends WP_UnitTestCase {

	/**
	 * @var WP_Framework
	 */
	protected static $app;

	/**
	 * @var Dashboard $dashboard
	 */
	private static $dashboard;

	/**
	 * @SuppressWarnings(StaticAccess)
	 * @throws ReflectionException
	 */
	public static function setUpBeforeClass() {
		static::$app       = WP_Framework::get_instance( MARKER_ANIMATION );
		static::$dashboard = Dashboard::get_instance( static::$app );
		static::reset();
		wp_register_style( 'wp-color-picker', 'test', [], 'v1' );
		wp_register_script( 'wp-color-picker', 'test', [], 'v1', true );
	}

	/**
	 * @throws ReflectionException
	 */
	public static function tearDownAfterClass() {
		static::reset();
	}

	/**
	 * @SuppressWarnings(Superglobals)
	 * @throws ReflectionException
	 */
	private static function reset() {
		wp_dequeue_script( 'wp-color-picker' );
		wp_dequeue_style( 'wp-color-picker' );
		wp_dequeue_script( static::$app->slug_name . '-marker_animation' );
		static::$app->input->delete_post( 'update' );
		static::$app->input->delete_post( 'reset' );
		static::$app->input->delete_request( 'marker_animation_nonce_admin_dashboard' );
		static::$app->input->delete_post( 'marker_animation/color' );
		$_SERVER['REQUEST_METHOD'] = 'GET';
		static::get_output_js();
		static::get_output_css();
		static::$app->file->delete( static::$app->define->plugin_assets_dir . DS . 'js' . DS . 'marker-animation.min.js' );
		static::$app->option->delete( 'marker_animation/color' );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_action() {
		static::reset();
		static::$app->file->put_contents( static::$app->define->plugin_assets_dir . DS . 'js' . DS . 'marker-animation.min.js', '' );
		$this->assertFalse( wp_script_is( 'wp-color-picker' ) );
		$this->assertFalse( wp_style_is( 'wp-color-picker' ) );
		$this->assertFalse( wp_script_is( static::$app->slug_name . '-marker_animation' ) );

		static::$dashboard->action();
		$this->assertTrue( wp_script_is( 'wp-color-picker' ) );
		$this->assertTrue( wp_style_is( 'wp-color-picker' ) );
		$this->assertTrue( wp_script_is( static::$app->slug_name . '-marker_animation' ) );
		$this->assertNotEmpty( static::get_output_js() );
		$this->assertEmpty( static::get_output_css() );
	}

	public function test_presenter() {
		ob_start();
		static::$dashboard->presenter();
		$contents = ob_get_contents();
		ob_end_clean();

		$this->assertContains( 'name="marker_animation_nonce_admin_dashboard"', $contents );
		$this->assertContains( 'id="marker_animation-is_valid"', $contents );
		$this->assertContains( 'id="marker_animation-color"', $contents );
		$this->assertContains( 'id="marker_animation-thickness"', $contents );
		$this->assertContains( 'id="marker_animation-duration"', $contents );
		$this->assertContains( 'id="marker_animation-delay"', $contents );
		$this->assertContains( 'id="marker_animation-function"', $contents );
		$this->assertContains( 'id="marker_animation-bold"', $contents );
		$this->assertContains( 'id="marker_animation-stripe"', $contents );
		$this->assertContains( 'id="marker_animation-repeat"', $contents );
		$this->assertContains( 'id="marker_animation-padding_bottom"', $contents );
		$this->assertContains( 'name="update"', $contents );
		$this->assertContains( 'name="reset"', $contents );
		$this->assertContains( 'class="marker-setting-preview"', $contents );
		$this->assertContains( 'id="marker_animation-info-wrap"', $contents );
	}

	/**
	 * @throws ReflectionException
	 * @SuppressWarnings(Superglobals)
	 */
	public function test_reset() {
		static::reset();
		static::$app->input->set_post( 'reset', 1 );
		$_SERVER['REQUEST_METHOD'] = 'POST';
		static::$app->option->set( 'marker_animation/color', 'red' );
		static::$app->input->set_request( 'marker_animation_nonce_admin_dashboard', static::create_nonce() );
		static::$app->input->set_post( 'marker_animation/color', 'blue' );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertEquals( 'red', static::$app->filter->apply_filters( 'color' ) );

		static::$dashboard->action();
		$this->assertEquals( '#ffff66', static::$app->filter->apply_filters( 'color' ) );
	}

	/**
	 * @throws ReflectionException
	 * @SuppressWarnings(Superglobals)
	 */
	public function test_update() {
		static::reset();
		static::$app->input->set_post( 'update', 1 );
		$_SERVER['REQUEST_METHOD'] = 'POST';
		static::$app->option->set( 'marker_animation/color', 'red' );
		static::$app->input->set_request( 'marker_animation_nonce_admin_dashboard', static::create_nonce() );
		static::$app->input->set_post( 'marker_animation/color', 'blue' );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertEquals( 'red', static::$app->filter->apply_filters( 'color' ) );

		static::$dashboard->action();
		$this->assertEquals( 'blue', static::$app->filter->apply_filters( 'color' ) );
	}

	/**
	 * @return false|string
	 * @throws ReflectionException
	 */
	private static function get_output_js() {
		ob_start();
		static::$app->minify->output_js( true );
		$contents = ob_get_contents();
		ob_end_clean();

		static::set_property( static::$app->minify, '_has_output_script', false );

		return $contents;
	}

	/**
	 * @return false|string
	 * @throws ReflectionException
	 */
	private static function get_output_css() {
		ob_start();
		static::$app->minify->output_css( true );
		$contents = ob_get_contents();
		ob_end_clean();

		static::set_property( static::$app->minify, '_end_footer', false );

		return $contents;
	}

	/**
	 * @return string
	 * @throws ReflectionException
	 */
	private static function create_nonce() {
		$reflection = new ReflectionMethod( static::$dashboard, 'create_nonce' );
		$reflection->setAccessible( true );
		$nonce = $reflection->invoke( static::$dashboard );
		$reflection->setAccessible( false );

		return $nonce;
	}

	/**
	 * @param $target
	 * @param $name
	 * @param $value
	 *
	 * @throws ReflectionException
	 */
	private static function set_property( $target, $name, $value ) {
		$reflection = new ReflectionClass( $target );
		$property   = $reflection->getProperty( $name );
		$property->setAccessible( true );
		$property->setValue( $target, $value );
		$property->setAccessible( false );
	}
}
