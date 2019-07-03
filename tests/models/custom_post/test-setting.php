<?php
/**
 * Class SettingTest
 *
 * @package Test_Travis
 */

use PHPUnit\Framework\TestCase;

use Marker_Animation\Classes\Models\Assets;
use Marker_Animation\Classes\Models\Custom_Post\Setting;

/**
 * @noinspection PhpUndefinedClassInspection
 * Setting test case.
 *
 * @mixin TestCase
 * @SuppressWarnings(TooManyPublicMethods)
 */
class SettingTest extends WP_UnitTestCase {

	/**
	 * @var WP_Framework
	 */
	protected static $app;

	/**
	 * @var Setting $setting
	 */
	private static $setting;

	/**
	 * @var Assets $assets
	 */
	private static $assets;

	/**
	 * @var string $handle
	 */
	private static $handle;

	/**
	 * @SuppressWarnings(StaticAccess)
	 * @throws ReflectionException
	 */
	public static function setUpBeforeClass() {
		static::$app     = WP_Framework::get_instance( MARKER_ANIMATION );
		static::$setting = Setting::get_instance( static::$app );
		static::$assets  = Assets::get_instance( static::$app );
		static::$handle  = static::$app->slug_name . '-marker_animation';
		static::reset();
	}

	/**
	 * @throws ReflectionException
	 */
	public static function tearDownAfterClass() {
		static::reset();
	}

	private static function reset() {
		static::$app->db->table( 'setting' )->truncate();
		static::$app->db->wp_table( 'posts' )->truncate();
		static::$app->option->delete( 'has_inserted_presets' );
		wp_dequeue_script( static::$handle );
		static::$app->file->delete( static::$app->define->plugin_assets_dir . DS . 'js' . DS . 'marker-animation.min.js' );
	}

	public function test_insert_presets() {
		$this->assertEmpty( static::$app->get_option( 'has_inserted_presets' ) );
		$this->assertEmpty( static::$setting->get_list_data( null, false )['data'] );
		static::$app->filter->do_action( 'app_activated' );
		$this->assertNotEmpty( static::$app->get_option( 'has_inserted_presets' ) );
		$list = static::$setting->get_list_data( null, false );
		$this->assertNotEmpty( $list['data'] );

		static::$app->option->delete( 'has_inserted_presets' );
		static::$app->filter->do_action( 'app_activated' );
		$list2 = static::$setting->get_list_data( null, false );
		$this->assertEquals( $list['data'], $list2['data'] );

		static::reset();
		static::$app->option->set( 'has_inserted_presets', true );
		$this->assertNotEmpty( static::$app->get_option( 'has_inserted_presets' ) );
		$this->assertEmpty( static::$setting->get_list_data( null, false )['data'] );
		static::$app->filter->do_action( 'app_activated' );
		$this->assertNotEmpty( static::$app->get_option( 'has_inserted_presets' ) );
		$this->assertEmpty( static::$setting->get_list_data( null, false )['data'] );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_setup_assets() {
		$this->get_output_js();
		$this->get_output_css();
		static::$app->file->put_contents( static::$app->define->plugin_assets_dir . DS . 'js' . DS . 'marker-animation.min.js', '' );

		global $typenow;
		wp_dequeue_script( static::$handle );
		$typenow = 'post';
		do_action( 'load-edit.php' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		$this->assertFalse( wp_script_is( static::$handle ) );
		$this->assertEmpty( $this->get_output_js() );

		wp_dequeue_script( static::$handle );
		$typenow = 'ma-setting';
		do_action( 'load-edit.php' ); // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		$this->assertTrue( wp_script_is( static::$handle ) );
		$this->assertNotEmpty( $this->get_output_js() );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_output_edit_form() {
		static::insert_settings();
		ob_start();
		static::$setting->output_edit_form( get_post( 1 ) );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertContains( '<div class="block form custom-post">', $contents );
		$this->assertContains( 'id="marker_animation-is_valid"', $contents );
		$this->assertContains( 'id="marker_animation-color"', $contents );
		$this->assertContains( 'id="marker_animation-thickness"', $contents );
		$this->assertContains( 'id="marker_animation-duration"', $contents );
		$this->assertContains( 'id="marker_animation-delay"', $contents );
		$this->assertContains( 'id="marker_animation-function"', $contents );
		$this->assertContains( 'id="marker_animation-is_font_bold"', $contents );
		$this->assertContains( 'id="marker_animation-is_repeat"', $contents );
		$this->assertContains( 'id="marker_animation-padding_bottom"', $contents );
		$this->assertContains( 'id="marker_animation-is_stripe"', $contents );
		$this->assertContains( 'id="marker_animation-priority"', $contents );
		$this->assertContains( 'id="marker_animation-is_valid_button_block_editor"', $contents );
		$this->assertContains( 'id="marker_animation-selector"', $contents );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_manage_posts_columns() {
		static::insert_settings();

		$columns = static::$setting->manage_posts_columns( [ 'title' => 'タイトル' ] );
		$this->assertArrayHasKey( 'title', $columns );
		$this->assertArrayHasKey( 'ma-setting-is_valid', $columns );
		$this->assertArrayHasKey( 'ma-setting-display', $columns );
		$this->assertArrayHasKey( 'ma-setting-others', $columns );
		$this->assertEquals( 'Setting name', $columns['title'] );

		ob_start();
		static::$setting->manage_posts_custom_column( 'ma-setting-is_valid', get_post( 1 ) );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertEquals( 'Valid', $contents );

		ob_start();
		static::$setting->manage_posts_custom_column( 'ma-setting-is_valid', get_post( 4 ) );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertEquals( 'Invalid', $contents );

		ob_start();
		static::$setting->manage_posts_custom_column( 'ma-setting-display', get_post( 1 ) );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertContains( 'class="marker-animation-preview"', $contents );
		$this->assertContains( 'Marker Animation', $contents );
		$this->assertContains( '<table class="widefat striped">', $contents );
		$this->assertContains( '<th>color</th>', $contents );
		$this->assertContains( '<td>color1</td>', $contents );
		$this->assertContains( '<th>thickness</th>', $contents );
		$this->assertContains( '<td>default (.6em)</td>', $contents );
		$this->assertContains( '<th>duration</th>', $contents );
		$this->assertContains( '<td>duration1</td>', $contents );
		$this->assertContains( '<th>delay</th>', $contents );
		$this->assertContains( '<td>default (.1s)</td>', $contents );
		$this->assertContains( '<th>function</th>', $contents );
		$this->assertContains( '<td>function1</td>', $contents );
		$this->assertContains( '<th>font bold</th>', $contents );
		$this->assertContains( '<td>Yes</td>', $contents );
		$this->assertContains( '<th>stripe</th>', $contents );
		$this->assertContains( '<td>default (No)</td>', $contents );
		$this->assertContains( '<th>repeat</th>', $contents );
		$this->assertContains( '<td>default (No)</td>', $contents );
		$this->assertContains( '<th>padding bottom</th>', $contents );
		$this->assertContains( '<td>default (.6em)</td>', $contents );

		ob_start();
		static::$setting->manage_posts_custom_column( 'ma-setting-others', get_post( 1 ) );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertContains( '<table class="widefat striped">', $contents );
		$this->assertContains( '<th>is valid block editor button</th>', $contents );
		$this->assertContains( '<td>Yes</td>', $contents );
		$this->assertContains( '<th>selector</th>', $contents );
		$this->assertContains( '<td>.marker-animation-1</td>', $contents );
	}

	public function test_get_error_messages() {
		$errors = static::$setting->get_error_messages( 'post_title', [ 'test1', 'test2' ] );
		$this->assertCount( 2, $errors );
		$this->assertEquals( 'test1: [Setting name]', $errors[0] );
		$this->assertEquals( 'test2: [Setting name]', $errors[1] );
	}

	public function test_edit_form_after_editor() {
		ob_start();
		do_action( 'edit_form_after_editor', get_post( 1 ) );
		$contents = ob_get_contents();
		ob_end_clean();
		$this->assertContains( 'class="marker-setting-preview"', $contents );
	}

	public function test_call_clear_option() {
		$post = get_post( 1 );
		static::$setting->data_updated( 1, $post, [], [] );
		static::$setting->data_inserted( 1, $post, [] );
		static::$setting->untrash_post( 1, $post );
		static::$setting->trash_post( 1 );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_delete() {
		static::insert_settings();
		$this->assertEquals( 1, static::$setting->delete_data( 2 ) );
	}

	/**
	 * @throws ReflectionException
	 */
	public function test_get_settings() {
		static::insert_settings();

		$settings = static::$setting->get_settings( 'editor' );
		$this->assertCount( 3, $settings );
		$this->assertArrayHasKey( 'id', $settings[0] );
		$this->assertArrayHasKey( 'options', $settings[0] );
		$this->assertArrayHasKey( 'title', $settings[0] );
		$this->assertArrayHasKey( 'color', $settings[0]['options'] );
		$this->assertArrayHasKey( 'thickness', $settings[0]['options'] );
		$this->assertArrayHasKey( 'duration', $settings[0]['options'] );
		$this->assertArrayHasKey( 'delay', $settings[0]['options'] );
		$this->assertArrayHasKey( 'function', $settings[0]['options'] );
		$this->assertArrayHasKey( 'stripe', $settings[0]['options'] );
		$this->assertArrayHasKey( 'repeat', $settings[0]['options'] );
		$this->assertArrayHasKey( 'selector', $settings[0]['options'] );
		$this->assertArrayHasKey( 'class', $settings[0]['options'] );
		$this->assertArrayHasKey( 'fontWeight', $settings[0]['options'] );
		$this->assertArrayHasKey( 'paddingBottom', $settings[0]['options'] );
		$this->assertArrayHasKey( 'isValidButtonBlockEditor', $settings[0]['options'] );
	}

	public function test_get_post_type_args() {
		$args = static::$setting->get_post_type_args();
		$this->assertEquals( 'Settings', $args['labels']['name'] );
		$this->assertEquals( 'marker_animation-dashboard', $args['show_in_menu'] );
	}

	/**
	 * @return false|string
	 * @throws ReflectionException
	 */
	private function get_output_js() {
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
	private function get_output_css() {
		ob_start();
		static::$app->minify->output_css( true );
		$contents = ob_get_contents();
		ob_end_clean();

		static::set_property( static::$app->minify, '_end_footer', false );

		return $contents;
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

	/**
	 * @throws ReflectionException
	 */
	private static function insert_settings() {
		static::reset();
		foreach (
			[
				[
					'post_title'                   => 'test title1',
					'is_valid'                     => 1,
					'color'                        => 'color1',
					'duration'                     => 'duration1',
					'function'                     => 'function1',
					'is_font_bold'                 => 1,
					'is_valid_button_block_editor' => 1,
					'priority'                     => 25,
				],
				[
					'post_title'                   => 'test title2',
					'is_valid'                     => 1,
					'color'                        => 'color2',
					'delay'                        => 'delay2',
					'is_repeat'                    => 1,
					'is_valid_button_block_editor' => 0,
					'priority'                     => 50,
				],
				[
					'post_title'                   => 'test title3',
					'is_valid'                     => 1,
					'color'                        => 'color3',
					'function'                     => 'function3',
					'padding_bottom'               => 'padding_bottom3',
					'is_valid_button_block_editor' => 1,
					'selector'                     => 'selector3',
				],
				[
					'post_title'                   => 'test title4',
					'is_stripe'                    => 1,
					'is_valid_button_block_editor' => 1,
				],
			] as $item
		) {
			static::$setting->insert( $item );
		}
	}
}
