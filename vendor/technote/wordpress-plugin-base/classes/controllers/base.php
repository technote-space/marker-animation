<?php
/**
 * Technote Controller Base
 *
 * @version 1.1.13
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Controllers;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Base
 * @package Technote\Controllers
 */
abstract class Base implements \Technote\Interfaces\Hook, \Technote\Interfaces\Controller {

	use \Technote\Traits\Hook, \Technote\Traits\Controller;

}
