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

namespace Technote\Tests;

class TestCase extends \PHPUnit\Framework\TestCase {

	/** @var \Technote|\Phake_IMock */
	protected static $app;

	/** @var string */
	protected static $plugin_name;

	/** @var string */
	protected static $plugin_file;

	public static function setUpBeforeClass() {
		static::$app = \Phake::mock( '\Technote' );
		\Phake::when( static::$app )->get_library_directory()->thenReturn( dirname( dirname( __FILE__ ) ) );
		static::$plugin_name      = md5( uniqid() );
		static::$plugin_file      = __FILE__;
		static::$app->plugin_name = static::$plugin_name;
		static::$app->plugin_file = static::$plugin_file;
		static::$app->slug_name   = static::$plugin_name;
		static::$app->define      = \Technote\Classes\Models\Lib\Define::get_instance( static::$app );
		static::$app->input       = \Technote\Classes\Models\Lib\Input::get_instance( static::$app );
		static::$app->utility     = \Technote\Classes\Models\Lib\Utility::get_instance( static::$app );
		static::$app->user        = \Technote\Classes\Models\Lib\User::get_instance( static::$app );
	}

}