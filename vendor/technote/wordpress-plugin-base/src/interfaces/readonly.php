<?php
/**
 * Technote Interfaces Readonly
 *
 * @version 2.3.0
 * @author technote-space
 * @since 2.3.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Interfaces;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Interface Readonly
 * @package Technote\Interfaces
 */
interface Readonly {

	/**
	 * @param string $name
	 * @param mixed $value
	 *
	 * @throws \OutOfRangeException
	 */
	public function __set( $name, $value );

	/**
	 * @param string $name
	 *
	 * @return mixed
	 * @throws \OutOfRangeException
	 */
	public function __get( $name );

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function __isset( $name );

}
