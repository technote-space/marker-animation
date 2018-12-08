<?php
/**
 * Technote Classes Controller Admin Base
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Controllers\Admin;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Base
 * @package Technote\Classes\Controllers\Admin
 */
abstract class Base extends \Technote\Classes\Controllers\Base implements \Technote\Interfaces\Controller\Admin, \Technote\Interfaces\Admin {

	use \Technote\Traits\Controller\Admin, \Technote\Traits\Admin;

}
