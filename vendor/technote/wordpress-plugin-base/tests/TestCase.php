<?php
/**
 * Technote Models Define Test
 *
 * @version 1.1.62
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Tests;

class TestCase extends \PHPUnit\Framework\TestCase {

	/** @var \Technote */
	protected static $app;

	/** @var string */
	protected static $plugin_name;

	/** @var string */
	protected static $plugin_file;

	public static function setUpBeforeClass() {
		static::$app              = \Phake::mock( '\Technote' );
		static::$plugin_name      = md5( uniqid() );
		static::$plugin_file      = __FILE__;
		static::$app->plugin_name = static::$plugin_name;
		static::$app->plugin_file = static::$plugin_file;
		static::$app->slug_name   = static::$plugin_file;
		static::$app->define      = \Technote\Models\Define::get_instance( static::$app );
		static::$app->input       = \Technote\Models\Input::get_instance( static::$app );
		static::$app->user        = \Technote\Models\User::get_instance( static::$app );
	}

}