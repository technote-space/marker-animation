<?php
/**
 * Technote Models Option Test
 *
 * @version 1.1.13
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Tests\Models;

/**
 * Class OptionTest
 * @package Technote\Tests\Models
 * @group technote
 * @group models
 */
class OptionTest extends \Technote\Tests\TestCase {

	/** @var \Technote\Models\Option */
	private static $option;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		static::$option = \Technote\Models\Option::get_instance( static::$app );
		foreach ( static::get_test_value() as $value ) {
			static::$option->delete( $value[0] );
		}
	}

	public static function tearDownAfterClass() {
		parent::tearDownAfterClass();
		static::$option->uninstall();
	}

	/**
	 * @dataProvider _test_value_provider
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function test_set( $key, $value ) {
		$this->assertEquals( true, static::$option->set( $key, $value ) );
	}

	/**
	 * @dataProvider _test_value_provider
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function test_get( $key, $value ) {
		$this->assertEquals( $value, static::$option->get( $key ) );
	}

	/**
	 * @dataProvider _test_value_provider
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function test_delete(
		/** @noinspection PhpUnusedParameterInspection */
		$key, $value
	) {
		$this->assertEquals( true, static::$option->delete( $key ) );
		$this->assertEquals( '', static::$option->get( $key ) );
	}

	/**
	 * @return array
	 */
	private static function get_test_value() {
		return [
			[ 'technote_test_option_bool', true ],
			[ 'technote_test_option_int', 123 ],
			[ 'technote_test_option_float', 0.987 ],
			[ 'technote_test_option_string', 'test' ],
			[
				'technote_test_option_array',
				[
					'test1' => 'test1',
					'test2' => 2,
					'test3' => false,
				],
			],
			[ 'technote_test_option_null', null ],
		];
	}

	/**
	 * @return array
	 */
	public function _test_value_provider() {
		return static::get_test_value();
	}

}