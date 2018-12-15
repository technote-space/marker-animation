<?php
/**
 * Technote Classes Models Lib Test Base
 *
 * @version 2.3.2
 * @author technote-space
 * @since 2.3.2
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib\Test;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

if ( class_exists( '\PHPUnit\Framework\TestCase' ) ) {
	class Base extends \PHPUnit\Framework\TestCase {
	}
} else {
	class Base {
	}
}
