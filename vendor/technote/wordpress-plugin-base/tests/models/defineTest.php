<?php
/**
 * Technote Models Define Test
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Tests\Models;

/**
 * Class DefineTest
 * @package Technote\Tests\Models
 * @group technote
 * @group models
 */
class DefineTest extends \Technote\Tests\TestCase {

	/** @var \Technote\Classes\Models\Lib\Define $define */
	private static $define;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		static::$define = static::$app->define;
	}

	public function test_lib_property() {
		$this->assertEquals( TECHNOTE_PLUGIN, static::$define->lib_name );
		$this->assertEquals( ucfirst( TECHNOTE_PLUGIN ), static::$define->lib_namespace );
		$this->assertNotEmpty( static::$define->lib_dir );
		$this->assertNotEmpty( static::$define->lib_assets_dir );
		$this->assertNotEmpty( static::$define->lib_src_dir );
		$this->assertNotEmpty( static::$define->lib_configs_dir );
		$this->assertNotEmpty( static::$define->lib_views_dir );
		$this->assertNotEmpty( static::$define->lib_language_dir );
		$this->assertNotEmpty( static::$define->lib_vendor_dir );
		$this->assertNotEmpty( static::$define->lib_assets_url );
	}

	public function test_plugin_property() {
		$this->assertEquals( static::$plugin_name, static::$define->plugin_name );
		$this->assertEquals( ucfirst( static::$plugin_name ), static::$define->plugin_namespace );
		$this->assertNotEmpty( static::$define->plugin_file );
		$this->assertNotEmpty( static::$define->plugin_dir );
		$this->assertNotEmpty( static::$define->plugin_dir_name );
		$this->assertNotEmpty( static::$define->plugin_base_name );
		$this->assertNotEmpty( static::$define->plugin_assets_dir );
		$this->assertNotEmpty( static::$define->plugin_src_dir );
		$this->assertNotEmpty( static::$define->plugin_configs_dir );
		$this->assertNotEmpty( static::$define->plugin_views_dir );
		$this->assertNotEmpty( static::$define->plugin_languages_dir );
		$this->assertNotEmpty( static::$define->plugin_logs_dir );
		$this->assertNotEmpty( static::$define->plugin_assets_url );
	}

}