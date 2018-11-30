<?php
/**
 * Technote Controller Admin Logs
 *
 * @version 1.1.54
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Controllers\Admin;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Logs
 * @package Technote\Controllers\Admin
 */
class Logs extends Base {

	/**
	 * @return int
	 */
	public function get_load_priority() {
		return $this->apply_filters( 'logs_page_priority', defined( 'WP_DEBUG' ) && WP_DEBUG ? 999 : - 1 );
	}

	/**
	 * @return string
	 */
	public function get_page_title() {
		return $this->apply_filters( 'logs_page_title', 'Logs' );
	}

	/**
	 * post
	 */
	public function post_action() {

		$ext    = $this->app->get_config( 'config', 'log_extension' );
		$root   = $this->app->define->plugin_logs_dir . DS;
		$path   = $this->app->input->get( 'path', '' );
		$name   = $this->app->input->get( 'name' );
		$search = trim( $path );
		$search = trim( $search, '/' . DS );
		$search = $root . $search;
		if ( ! empty( $name ) && pathinfo( $name, PATHINFO_EXTENSION ) === $ext ) {
			$file = $search . DS . $name;
			if ( file_exists( $file ) && is_file( $file ) ) {
				@unlink( $file );
			}
			if ( ! file_exists( $file ) ) {
				$this->app->add_message( 'File deleted.', 'logs' );
			}
		}
	}

	/**
	 * @return array
	 */
	protected function get_view_args() {

		$ext    = $this->app->get_config( 'config', 'log_extension' );
		$root   = $this->app->define->plugin_logs_dir . DS;
		$path   = $this->app->input->get( 'path', '' );
		$name   = $this->app->input->get( 'name' );
		$search = trim( $path );
		$search = trim( $search, '/' . DS );
		$search = $root . $search;

		$data    = [];
		$deleted = true;
		if ( ! empty( $name ) && pathinfo( $name, PATHINFO_EXTENSION ) === $ext ) {
			$file = $search . DS . $name;
			if ( file_exists( $file ) && is_file( $file ) ) {
				$deleted = false;
				if ( is_readable( $file ) ) {
					$data = $this->load_log_data( $file );
				}
			}
		}
		$segments         = explode( '/', $path );
		$segments_scandir = [];
		$seg              = '';
		$count            = count( $segments );
		$max              = $count - 1;
		for ( $i = 0; $i < $max; $i ++ ) {
			$segment = $segments[ $i ];
			! empty( $seg ) and $seg .= '/';
			$seg     .= $segment;
			$exclude = [];
			if ( $i < $count - 1 ) {
				$exclude [] = $segments[ $i + 1 ];
			}
			$scandir                  = $this->scandir( $root . $seg, $ext, $exclude );
			$segments_scandir[ $seg ] = $scandir;
		}

		return [
			'root'             => $this->scandir( $root, $ext ),
			'search'           => $this->scandir( $search, $ext ),
			'field'            => [
				'path' => '',
			],
			'segments'         => explode( '/', $path ),
			'segments_scandir' => $segments_scandir,
			'data'             => $data,
			'deleted'          => $deleted,
		];
	}

	/**
	 * @param string $dir
	 * @param string $ext
	 * @param array $exclude
	 *
	 * @return array
	 */
	private function scandir( $dir, $ext, $exclude = [] ) {
		$files = [];
		$dirs  = [];
		if ( is_dir( $dir ) ) {
			foreach ( scandir( $dir ) as $file ) {
				if ( $file === '.' || $file === '..' || in_array( $file, $exclude ) ) {
					continue;
				}

				$path = rtrim( $dir, DS ) . DS . $file;
				if ( is_file( $path ) && pathinfo( $path, PATHINFO_EXTENSION ) === $ext ) {
					$files[] = $file;
				}
				if ( is_dir( $path ) ) {
					$dirs[] = $file;
				}
			}
		}

		return [ $dirs, $files ];
	}

	/**
	 * @param string $path
	 *
	 * @return array|false
	 */
	private function load_log_data( $path ) {
		$data = @file_get_contents( $path );
		if ( empty( $data ) ) {
			return [];
		}

		try {
			$exploded = explode( "\n", $data );
			$ret      = [];
			$time     = false;
			$buffer   = '';
			foreach ( $exploded as $item ) {
				if ( preg_match( '#^\[(\d{4}\-\d{2}\-\d{2}\T\d{2}:\d{2}:\d{2}(\s*[+-]\d{2}:\d{2})?)\] (.+)#', $item, $matches ) ) {
					if ( '' !== $buffer ) {
						$ret[]  = [ $time, $buffer ];
						$buffer = '';
					}
					$item = $matches[3];
					$time = $matches[1];
				}

				if ( '' !== $buffer ) {
					$buffer .= "<br>";
				}
				$buffer .= $item;
			}
			if ( false !== $time && '' !== $buffer ) {
				$ret[] = [ $time, $buffer ];
			}
			if ( ! empty( $data ) && empty( $ret ) ) {
				return false;
			}
		} catch ( \Exception $e ) {
			return false;
		}

		return $ret;
	}
}
