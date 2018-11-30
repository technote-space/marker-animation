<?php
/**
 * Technote Tests Base
 *
 * @version 1.1.13
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Tests;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Base
 * @package Technote\Tests
 */
abstract class Base extends \PHPUnit\Framework\TestCase implements \Technote\Interfaces\Test {

	use \Technote\Traits\Test;

	/** @var \Technote */
	protected static $test_app;

	/**
	 * @param \Technote $app
	 */
	public static function set_app( $app ) {
		static::$test_app = $app;
	}

	/**
	 * @throws \ReflectionException
	 */
	public final function setUp() {
		$class = get_called_class();
		if ( false === $class ) {
			$class = get_class();
		}
		$reflection = new \ReflectionClass( $class );
		$this->init( static::$test_app, $reflection );
		$this->_setup();
	}

	/**
	 * setup
	 */
	public function _setup() {

	}

}
