<?php
/**
 * Technote Crons Base
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Crons;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Base
 * @package Technote\Classes\Crons
 */
abstract class Base implements \Technote\Interfaces\Cron {

	use \Technote\Traits\Cron;

}
