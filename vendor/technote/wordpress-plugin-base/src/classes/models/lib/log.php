<?php
/**
 * Technote Classes Models Lib Log
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
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

	/** @var string */
	private $path = null;

	/**
	 * initialize
	 */
	protected function initialize() {
		$dir  = trim( $this->app->get_config( 'config', 'log_dir' ) );
		$dir  = trim( $dir, '/' . DS );
		$name = trim( $this->app->get_config( 'config', 'log_name' ) );
		$name = trim( $name, '/' . DS );
		$ext  = trim( $this->app->get_config( 'config', 'log_extension' ) );
		$ext  = trim( $ext, '.' );
		if ( empty( $name ) ) {
			return;
		}
		$path       = $dir . DS . $name . '.' . $ext;
		$path       = $this->app->define->plugin_logs_dir . DS . $this->app->utility->replace_time( $path );
		$this->path = $path;
	}

	/**
	 * @param mixed $message
	 *
	 * @return bool
	 */
	public function log( $message ) {
		if ( empty( $this->path ) ) {
			return false;
		}
		$dir = dirname( $this->path );
		if ( ! file_exists( $dir ) ) {
			@mkdir( $dir, 0777, true );
			@chmod( $dir, 0777 );
			if ( ! file_exists( $dir ) ) {
				$this->path = null;

				return false;
			}
		}
		if ( ! file_exists( $this->path ) ) {
			@touch( $this->path );
		}
		if ( ! is_writable( $this->path ) ) {
			$this->path = null;

			return false;
		}
		@error_log( sprintf( "[%s] %s\n", date_i18n( DATE_W3C ), $this->apply_filters( 'log_message', is_string( $message ) ? $this->app->translate( $message ) : json_encode( $message ), $message ) ), 3, $this->path );

		return true;
	}
}
