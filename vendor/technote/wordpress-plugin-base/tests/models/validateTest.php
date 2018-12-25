<?php
/**
 * Technote Models Validate Test
 *
 * @version 2.9.0
 * @author technote-space
 * @since 2.9.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Tests\Models;

require_once __DIR__ . DS . 'misc' . DS . 'validate.php';

/**
 * Class ValidateTest
 * @package Technote\Tests\Models
 * @group technote
 * @group models
 */
class ValidateTest extends \Technote\Tests\TestCase {

	/** @var Misc\Validate $validate */
	private static $validate;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		static::$validate = Misc\Validate::get_instance( static::$app );
	}

	/**
	 * @dataProvider _test_validate_provider
	 *
	 * @param string $method
	 * @param mixed $var
	 * @param bool $expected
	 */
	public function test_validate( $method, $var, $expected ) {
		$result = static::$validate->$method( $var );
		if ( $expected ) {
			$this->assertEquals( true, $result );
		} else {
			$this->assertInstanceOf( \WP_Error::class, $result );
		}
	}

	/**
	 * @return array
	 */
	public function _test_validate_provider() {
		return [
			[ 'validate_not_empty', null, false ],
			[ 'validate_not_empty', '', false ],
			[ 'validate_not_empty', 0, false ],
			[ 'validate_not_empty', '  ', false ],
			[ 'validate_not_empty', [], false ],
			[ 'validate_not_empty', '0', true ],
			[ 'validate_not_empty', 'test', true ],
			[ 'validate_not_empty', [ 'test' ], true ],

			[ 'validate_kana', null, false ],
			[ 'validate_kana', '', false ],
			[ 'validate_kana', 0, false ],
			[ 'validate_kana', [], false ],
			[ 'validate_kana', 'test', false ],
			[ 'validate_kana', 'あいうえお', false ],
			[ 'validate_kana', 'アイウえお', false ],
			[ 'validate_kana', 'アイウエオ', true ],

			[ 'validate_date', null, false ],
			[ 'validate_date', '', false ],
			[ 'validate_date', 0, false ],
			[ 'validate_date', [], false ],
			[ 'validate_date', 'test', false ],
			[ 'validate_date', 'あいうえお', false ],
			[ 'validate_date', '20000101', false ],
			[ 'validate_date', date( 'Y-m-d' ), true ],

			[ 'validate_time', null, false ],
			[ 'validate_time', '', false ],
			[ 'validate_time', 0, false ],
			[ 'validate_time', [], false ],
			[ 'validate_time', 'test', false ],
			[ 'validate_time', 'あいうえお', false ],
			[ 'validate_time', '1212', false ],
			[ 'validate_time', date( 'H:i:s' ), true ],
			[ 'validate_time', date( 'H:i' ), true ],

			[ 'validate_email', null, false ],
			[ 'validate_email', '', false ],
			[ 'validate_email', 0, false ],
			[ 'validate_email', [], false ],
			[ 'validate_email', 'test', false ],
			[ 'validate_email', 'あいうえお', false ],
			[ 'validate_email', 'test@example.com', true ],

			[ 'validate_phone', null, false ],
			[ 'validate_phone', '', false ],
			[ 'validate_phone', 0, false ],
			[ 'validate_phone', [], false ],
			[ 'validate_phone', 'test', false ],
			[ 'validate_phone', 'あいうえお', false ],
			[ 'validate_phone', '03-1234-5678', true ],
			[ 'validate_phone', '090-1234-5678', true ],
			[ 'validate_phone', '0312345678', true ],

			[ 'validate_positive', null, false ],
			[ 'validate_positive', '', false ],
			[ 'validate_positive', [], false ],
			[ 'validate_positive', 'test', false ],
			[ 'validate_positive', 'あいうえお', false ],
			[ 'validate_positive', '03-1234-5678', false ],
			[ 'validate_positive', - 1, false ],
			[ 'validate_positive', - 0.1, false ],
			[ 'validate_positive', '-0.1', false ],
			[ 'validate_positive', 0, false ],
			[ 'validate_positive', 0.1, true ],
			[ 'validate_positive', '0.1', true ],
			[ 'validate_positive', 1, true ],

			[ 'validate_negative', null, false ],
			[ 'validate_negative', '', false ],
			[ 'validate_negative', [], false ],
			[ 'validate_negative', 'test', false ],
			[ 'validate_negative', 'あいうえお', false ],
			[ 'validate_negative', '03-1234-5678', false ],
			[ 'validate_negative', 1, false ],
			[ 'validate_negative', 0.1, false ],
			[ 'validate_negative', '0.1', false ],
			[ 'validate_negative', 0, false ],
			[ 'validate_negative', - 0.1, true ],
			[ 'validate_negative', '-0.1', true ],
			[ 'validate_negative', - 1, true ],

			[ 'validate_int', null, false ],
			[ 'validate_int', '', false ],
			[ 'validate_int', [], false ],
			[ 'validate_int', 'test', false ],
			[ 'validate_int', 'あいうえお', false ],
			[ 'validate_int', '03-1234-5678', false ],
			[ 'validate_int', 0.1, false ],
			[ 'validate_int', - 0.1, false ],
			[ 'validate_int', '0.1', false ],
			[ 'validate_int', '-0.1', false ],
			[ 'validate_int', 0, true ],
			[ 'validate_int', 1, true ],
			[ 'validate_int', - 1, true ],
			[ 'validate_int', '- 1', false ],

			[ 'validate_float', null, false ],
			[ 'validate_float', '', false ],
			[ 'validate_float', [], false ],
			[ 'validate_float', 'test', false ],
			[ 'validate_float', 'あいうえお', false ],
			[ 'validate_float', '03-1234-5678', false ],
			[ 'validate_float', 0.1, true ],
			[ 'validate_float', - 0.1, true ],
			[ 'validate_float', '0.1', true ],
			[ 'validate_float', '- 0.1', false ],
			[ 'validate_float', 0, true ],
			[ 'validate_float', 1, true ],
			[ 'validate_float', - 1, true ],
			[ 'validate_float', '- 1', false ],

			[ 'validate_positive_int', null, false ],
			[ 'validate_positive_int', '', false ],
			[ 'validate_positive_int', [], false ],
			[ 'validate_positive_int', 'test', false ],
			[ 'validate_positive_int', 'あいうえお', false ],
			[ 'validate_positive_int', '03-1234-5678', false ],
			[ 'validate_positive_int', 0.1, false ],
			[ 'validate_positive_int', - 0.1, false ],
			[ 'validate_positive_int', '0.1', false ],
			[ 'validate_positive_int', '- 0.1', false ],
			[ 'validate_positive_int', 0, false ],
			[ 'validate_positive_int', 1, true ],
			[ 'validate_positive_int', '1', true ],
			[ 'validate_positive_int', - 1, false ],
			[ 'validate_positive_int', '- 1', false ],

			[ 'validate_negative_int', null, false ],
			[ 'validate_negative_int', '', false ],
			[ 'validate_negative_int', [], false ],
			[ 'validate_negative_int', 'test', false ],
			[ 'validate_negative_int', 'あいうえお', false ],
			[ 'validate_negative_int', '03-1234-5678', false ],
			[ 'validate_negative_int', 0.1, false ],
			[ 'validate_negative_int', - 0.1, false ],
			[ 'validate_negative_int', '0.1', false ],
			[ 'validate_negative_int', '- 0.1', false ],
			[ 'validate_negative_int', 0, false ],
			[ 'validate_negative_int', 1, false ],
			[ 'validate_negative_int', - 1, true ],
			[ 'validate_negative_int', '-1', true ],
			[ 'validate_negative_int', '- 1', false ],

			[ 'validate_positive_float', null, false ],
			[ 'validate_positive_float', '', false ],
			[ 'validate_positive_float', [], false ],
			[ 'validate_positive_float', 'test', false ],
			[ 'validate_positive_float', 'あいうえお', false ],
			[ 'validate_positive_float', '03-1234-5678', false ],
			[ 'validate_positive_float', 0.1, true ],
			[ 'validate_positive_float', - 0.1, false ],
			[ 'validate_positive_float', '0.1', true ],
			[ 'validate_positive_float', '- 0.1', false ],
			[ 'validate_positive_float', 0, false ],
			[ 'validate_positive_float', 1, true ],
			[ 'validate_positive_float', '1', true ],
			[ 'validate_positive_float', - 1, false ],
			[ 'validate_positive_float', '- 1', false ],

			[ 'validate_negative_float', null, false ],
			[ 'validate_negative_float', '', false ],
			[ 'validate_negative_float', [], false ],
			[ 'validate_negative_float', 'test', false ],
			[ 'validate_negative_float', 'あいうえお', false ],
			[ 'validate_negative_float', '03-1234-5678', false ],
			[ 'validate_negative_float', 0.1, false ],
			[ 'validate_negative_float', - 0.1, true ],
			[ 'validate_negative_float', '0.1', false ],
			[ 'validate_negative_float', '-0.1', true ],
			[ 'validate_negative_float', '- 0.1', false ],
			[ 'validate_negative_float', 0, false ],
			[ 'validate_negative_float', 1, false ],
			[ 'validate_negative_float', - 1, true ],
			[ 'validate_negative_float', '-1', true ],
			[ 'validate_negative_float', '- 1', false ],
		];
	}

	/**
	 * @dataProvider _test_validate_string_length_provider
	 *
	 * @param mixed $var
	 * @param int $len
	 * @param bool $expected
	 */
	public function test_validate_string_length( $var, $len, $expected ) {
		$result = static::$validate->validate_string_length( $var, $len );
		if ( $expected ) {
			$this->assertEquals( true, $result );
		} else {
			$this->assertInstanceOf( \WP_Error::class, $result );
		}
	}

	/**
	 * @return array
	 */
	public function _test_validate_string_length_provider() {
		return [
			[ null, 0, false ],
			[ '', 0, true ],
			[ [], 0, false ],
			[ 'test', 0, false ],
			[ 'test', 3, false ],
			[ 'test', 4, true ],
			[ 'test', 5, true ],
		];
	}
}