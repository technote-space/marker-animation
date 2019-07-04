<?php
/**
 * Class EditorTest
 *
 * @package Test_Travis
 */

use PHPUnit\Framework\TestCase;

use Marker_Animation\Classes\Models\Assets;
use Marker_Animation\Classes\Models\Editor;

/**
 * @noinspection PhpUndefinedClassInspection
 * Editor test case.
 *
 * @mixin TestCase
 */
class EditorTest extends WP_UnitTestCase {

	/**
	 * @var WP_Framework
	 */
	protected static $app;

	/**
	 * @var Editor $editor
	 */
	private static $editor;

	/**
	 * @var Assets $assets
	 */
	private static $assets;

	/**
	 * @SuppressWarnings(StaticAccess)
	 */
	public static function setUpBeforeClass() {
		static::$app    = WP_Framework::get_instance( MARKER_ANIMATION );
		static::$editor = Editor::get_instance( static::$app );
		static::$assets = Assets::get_instance( static::$app );
		static::reset();
	}

	public static function tearDownAfterClass() {
		static::reset();
	}

	private static function reset() {
		wp_dequeue_script( 'marker_animation-editor' );
		wp_dequeue_style( 'marker_animation-editor' );
		static::$app->file->delete( static::$app->define->plugin_assets_dir . DS . 'js' . DS . 'gutenberg.min.js' );
	}

	public function test_enqueue_block_editor_assets() {
		wp_dequeue_script( 'marker_animation-editor' );
		wp_dequeue_style( 'marker_animation-editor' );
		static::$app->file->put_contents( static::$app->define->plugin_assets_dir . DS . 'js' . DS . 'gutenberg.min.js', '' );

		static::$app->setting->edit_setting( 'is_valid', 'default', false );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertFalse( static::$app->filter->apply_filters( 'is_valid' ) );
		do_action( 'enqueue_block_editor_assets' );
		$this->assertFalse( wp_script_is( 'marker_animation-editor' ) );
		$this->assertFalse( wp_style_is( 'marker_animation-editor' ) );

		static::$app->setting->edit_setting( 'is_valid', 'default', true );
		static::$app->delete_shared_object( '_hook_cache' );
		$this->assertTrue( static::$app->filter->apply_filters( 'is_valid' ) );
		do_action( 'enqueue_block_editor_assets' );
		$this->assertTrue( wp_script_is( 'marker_animation-editor' ) );
		$this->assertTrue( wp_style_is( 'marker_animation-editor' ) );
	}
}
