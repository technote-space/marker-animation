<?php
/**
 * Technote Traits Test
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Traits;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Trait Test
 * @package Technote\Traits
 * @property \Technote $app
 */
trait Test {

	use Singleton, Hook;

	/** @var array $objects */
	private $objects = [];

	/**
	 * Test constructor.
	 *
	 * @param $arg1
	 * @param $arg2
	 * @param $arg3
	 *
	 * @throws \ReflectionException
	 */
	public function __construct( $arg1 = null, $arg2 = [], $arg3 = '' ) {
		$args = func_get_args();
		if ( count( $args ) > 1 && $args[0] instanceof \Technote && $args[1] instanceof \ReflectionClass ) {
			// Singleton
			$this->init( ...$args );
		} elseif ( count( $args ) > 2 ) {
			// \PHPUnit_Framework_TestCase
			$reflectionClass = new \ReflectionClass( '\PHPUnit_Framework_TestCase' );
			if ( $arg1 !== null ) {
				$this->setName( $arg1 );
			}
			$data = $reflectionClass->getProperty( 'data' );
			$data->setAccessible( true );
			$data->setValue( $this, $arg2 );
			$data->setAccessible( false );
			$dataName = $reflectionClass->getProperty( 'dataName' );
			$dataName->setAccessible( true );
			$dataName->setValue( $this, $arg3 );
			$dataName->setAccessible( false );
		}
	}

	/**
	 * @return string
	 */
	public function get_test_slug() {
		return $this->get_file_slug();
	}

	/**
	 * @param mixed $obj
	 */
	protected function dump( $obj ) {
		$this->objects[] = print_r( $obj, true );
	}

	/**
	 * @return bool
	 */
	public function has_dump_objects() {
		return ! empty( $this->objects );
	}

	/**
	 * @return array
	 */
	public function get_dump_objects() {
		return $this->objects;
	}

}
