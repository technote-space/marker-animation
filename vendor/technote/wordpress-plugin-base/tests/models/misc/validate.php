<?php
/**
 * Technote Tests Models Misc Validate
 *
 * @version 2.9.0
 * @author technote-space
 * @since 2.9.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Tests\Models\Misc;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Validate
 * @package Technote\Tests\Models\Misc
 */
class Validate implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Helper\Validate {

	use \Technote\Traits\Singleton, \Technote\Traits\Helper\Validate;

	/**
	 * @param string $name
	 * @param array $arguments
	 *
	 * @return mixed
	 */
	public function __call( $name, $arguments ) {
		return $this->$name( ...$arguments );
	}
}
