<?php
/**
 * Technote Classes Models Lib Log
 *
 * @version 2.7.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.7.0 Changed: save log to db
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Log
 * @package Technote\Classes\Models\Lib
 */
class Log implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook;

	/**
	 * @since 2.7.0
	 * @return bool
	 */
	public function is_valid_log() {
		return $this->apply_filters( 'log_validity', defined( 'WP_DEBUG' ) && WP_DEBUG && ! $this->app->get_config( 'config', 'prevent_use_log' ) );
	}

	/**
	 * @since 2.7.0 Added: $context
	 *
	 * @param string $message
	 * @param mixed $context
	 *
	 * @return bool
	 */
	public function log( $message, $context = null ) {
		if ( ! $this->is_valid_log() ) {
			return false;
		}
		if ( $this->apply_filters( 'save___log_term' ) <= 0 ) {
			return false;
		}
		$data                   = $this->get_called_info();
		$data['message']        = is_string( $message ) ? $this->app->translate( $message ) : json_encode( $message );
		$data['lib_version']    = $this->app->get_library_version();
		$data['plugin_version'] = $this->app->get_plugin_version();
		if ( isset( $context ) ) {
			$data['context'] = json_encode( $context );
		}
		$this->app->db->insert( '__log', $data );

		return true;
	}

	/**
	 * @since 2.7.0
	 * @return array
	 */
	private function get_called_info() {
		$next = false;
		foreach ( $this->app->utility->get_debug_backtrace() as $item ) {
			if ( $next ) {
				$return = [];
				isset( $item['file'] ) and $return['file'] = preg_replace( '/' . preg_quote( ABSPATH, '/' ) . '/A', '', $item['file'] );
				isset( $item['line'] ) and $return['line'] = $item['line'];

				return $return;
			}
			if ( ! empty( $item['class'] ) && __CLASS__ === $item['class'] && $item['function'] === 'log' ) {
				$next = true;
			}
		}

		return [];
	}

	/**
	 * @since 2.7.0
	 * @return int
	 */
	public function delete_old_logs() {
		$count = 0;
		$term  = $this->apply_filters( 'save___log_term' );
		foreach (
			$this->app->db->select( '__log', [
				'created_at' => [ '<', 'NOW() - INTERVAL ' . (int) $term . ' SECOND', true ],
			] ) as $log
		) {
			$this->app->db->delete( '__log', [
				'id' => $log['id'],
			] );
			$count ++;
		}

		return $count;
	}
}
