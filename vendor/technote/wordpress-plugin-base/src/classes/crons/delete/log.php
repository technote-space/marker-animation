<?php
/**
 * Technote Crons Delete Log
 *
 * @version 2.9.0
 * @author technote-space
 * @since 2.7.0
 * @since 2.9.0 Changed: is_valid_log > is_valid
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Crons\Delete;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Log
 * @package Technote\Classes\Crons\Delete
 */
class Log extends \Technote\Classes\Crons\Base {

	/**
	 * @return int
	 */
	protected function get_interval() {
		if ( ! $this->app->log->is_valid() ) {
			return - 1;
		}

		return $this->apply_filters( 'delete___log_interval' );
	}

	/**
	 * @return string
	 */
	protected function get_hook_name() {
		return $this->get_hook_prefix() . 'delete_log';
	}

	/**
	 * execute
	 */
	protected function execute() {
		$this->app->log( 'delete logs', $this->app->log->delete_old_logs() );
	}
}
