<?php
/**
 * Technote Classes Models Lib Log
 *
 * @version 2.9.13
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.7.0 Changed: save log to db
 * @since 2.9.0 Improved: log level
 * @since 2.9.0 Added: send mail feature
 * @since 2.9.13 Changed: log validity
 * @since 2.9.13 Added: versions
 * @since 2.9.13 Changed: moved shutdown function
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
class Log implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook, \Technote\Interfaces\Presenter {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook, \Technote\Traits\Presenter;

	/**
	 * setup shutdown
	 * @since 2.9.13
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_shutdown() {
		if ( $this->apply_filters( 'capture_shutdown_error' ) && $this->is_valid() ) {
			add_action( 'shutdown', function () {
				$this->shutdown();
			}, 0 );
		}
	}

	/**
	 * shutdown
	 * @since 2.8.5
	 * @since 2.9.0 Changed: capture error target
	 * @since 2.9.13 Changed: moved function
	 */
	private function shutdown() {
		$error = error_get_last();
		if ( $error === null ) {
			return;
		}

		if ( $error['type'] & $this->app->get_config( 'config', 'target_shutdown_error' ) ) {
			$suppress = $this->app->get_config( 'config', 'suppress_log_messages' );
			$message  = str_replace( [ "\r\n", "\r", "\n" ], "\n", $error['message'] );
			$messages = explode( "\n", $message );
			$message  = reset( $messages );
			if ( empty( $suppress ) || ( is_array( $suppress ) && ! in_array( $message, $suppress ) ) ) {
				$this->app->log( $message, $error, 'error' );
			}
		}
	}

	/**
	 * @since 2.7.0
	 * @return bool
	 */
	public function is_valid() {
		if ( $this->app->get_config( 'config', 'prevent_use_log' ) ) {
			return false;
		}

		return $this->apply_filters( 'log_validity', $this->apply_filters( 'is_valid_log' ) );
	}

	/**
	 * @since 2.7.0 Added: $context
	 * @since 2.9.0 Added: log level arg
	 * @since 2.9.0 Added: send mail feature
	 *
	 * @param string $message
	 * @param mixed $context
	 * @param string $level
	 *
	 * @return bool
	 */
	public function log( $message, $context = null, $level = '' ) {
		if ( ! $this->is_valid() ) {
			return false;
		}

		$log_level = $this->app->get_config( 'config', 'log_level' );
		$level     = $this->get_log_level( $level, $log_level );
		if ( empty( $log_level[ $level ] ) ) {
			return false;
		}

		global $wp_version;
		$data                      = $this->get_called_info();
		$data['message']           = is_string( $message ) ? $this->app->translate( $message ) : json_encode( $message );
		$data['lib_version']       = $this->app->get_library_version();
		$data['plugin_version']    = $this->app->get_plugin_version();
		$data['php_version']       = phpversion();
		$data['wordpress_version'] = $wp_version;
		$data['level']             = $level;
		if ( isset( $context ) ) {
			$data['context'] = json_encode( $context );
		}

		$this->send_mail( $level, $log_level, $message, $data );
		$this->insert_log( $level, $log_level, $data );

		return true;
	}

	/**
	 * @since 2.9.0
	 *
	 * @param string $level
	 * @param array $log_level
	 *
	 * @return string
	 */
	private function get_log_level( $level, $log_level ) {
		if ( ! isset( $log_level[ $level ] ) && ! isset( $log_level[''] ) ) {
			return 'info';
		}
		'' === $level || ! isset( $log_level[ $level ] ) and $level = $log_level[''];
		if ( empty( $log_level[ $level ] ) ) {
			return 'info';
		}

		return $level;
	}

	/**
	 * @since 2.9.0
	 *
	 * @param string $level
	 * @param array $log_level
	 * @param array $data
	 */
	private function insert_log( $level, $log_level, $data ) {
		if ( empty( $log_level[ $level ]['is_valid_log'] ) ) {
			return;
		}
		if ( $this->apply_filters( 'save___log_term' ) <= 0 ) {
			return;
		}
		$this->app->db->insert( '__log', $data );
	}

	/**
	 * @since 2.9.0
	 *
	 * @param string $level
	 * @param array $log_level
	 * @param string $message
	 * @param array $data
	 */
	private function send_mail( $level, $log_level, $message, $data ) {
		if ( empty( $log_level[ $level ]['is_valid_mail'] ) ) {
			return;
		}

		$level  = $log_level[ $level ];
		$roles  = $this->app->utility->array_get( $level, 'roles' );
		$emails = $this->app->utility->array_get( $level, 'emails' );

		if ( empty( $roles ) && empty( $emails ) ) {
			return;
		}

		$emails = array_unique( $emails );
		$emails = array_combine( $emails, $emails );
		foreach ( $roles as $role ) {
			foreach ( get_users( [ 'role' => $role ] ) as $user ) {
				/** @var \WP_User $user */
				! empty( $user->user_email ) and $emails[ $user->user_email ] = $user->user_email;
			}
		}

		foreach ( $emails as $email ) {
			$this->app->mail->send( $email, $message, $this->dump( $data, false ) );
		}
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
