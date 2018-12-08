<?php
/**
 * Technote Models Config Test
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
 * Class ConfigTest
 * @package Technote\Tests\Models
 * @group technote
 * @group models
 */
class ConfigTest extends \Technote\Tests\TestCase {

	/** @var \Technote\Classes\Models\Lib\Config */
	private static $config;

	/** @var string */
	private static $config_file;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		static::$config = \Technote\Classes\Models\Lib\Config::get_instance( static::$app );

		static::$config_file = 'technote_test_config';
		touch( static::$app->define->lib_configs_dir . DS . static::$config_file . '.php' );
		file_put_contents( static::$app->define->lib_configs_dir . DS . static::$config_file . '.php', <<< EOS
<?php

return array(

	'test1' => 'test1',
	'test2' => 'test2',

);

EOS
		);

		if ( ! file_exists( static::$app->define->plugin_configs_dir ) ) {
			mkdir( static::$app->define->plugin_configs_dir, true );
		}
		touch( static::$app->define->plugin_configs_dir . DS . static::$config_file . '.php' );
		file_put_contents( static::$app->define->plugin_configs_dir . DS . static::$config_file . '.php', <<< EOS
<?php

return array(

	'test2' => 'test3',
	'test4' => 'test4',

);

EOS
		);
	}

	public static function tearDownAfterClass() {
		parent::tearDownAfterClass();
		if ( file_exists( static::$app->define->plugin_configs_dir . DS . static::$config_file . '.php' ) ) {
			unlink( static::$app->define->plugin_configs_dir . DS . static::$config_file . '.php' );
		}
		if ( file_exists( static::$app->define->lib_configs_dir . DS . static::$config_file . '.php' ) ) {
			unlink( static::$app->define->lib_configs_dir . DS . static::$config_file . '.php' );
		}
	}

	public function test_get_only_lib_config() {
		$this->assertEquals( 'test1', static::$config->get( static::$config_file, 'test1' ) );
	}

	public function test_overwrite_config() {
		$this->assertEquals( 'test3', static::$config->get( static::$config_file, 'test2' ) );
	}

	public function test_get_only_plugin_config() {
		$this->assertEquals( 'test4', static::$config->get( static::$config_file, 'test4' ) );
	}

	public function test_default() {
		$this->assertEquals( 'test6', static::$config->get( static::$config_file, 'test5', 'test6' ) );
	}
}