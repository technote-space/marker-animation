<?php
/**
 * Technote Classes Models Lib Minify
 *
 * @version 2.10.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.9.0 Added: method to clear cache
 * @since 2.10.0 Fixed: output css after footer
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Minify
 * @package Technote\Classes\Models\Lib
 */
class Minify implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook;

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var array $_script
	 */
	private $_script = [];

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var bool $_has_output_script
	 */
	private $_has_output_script = false;

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var array $_css
	 */
	private $_css = [];

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var bool $_end_footer
	 */
	private $_end_footer = false;

	/**
	 * @param string $src
	 * @param string $name
	 *
	 * @return bool
	 */
	private function check_cache( $src, $name ) {
		$name  = $name . '_minify_cache';
		$hash  = sha1( $src );
		$cache = $this->app->get_shared_object( $name, 'all' );
		if ( $cache ) {
			if ( isset( $cache[ $hash ] ) ) {
				return true;
			}
		} else {
			$cache = [];
		}
		$cache[ $hash ] = true;
		$this->app->set_shared_object( $name, $cache, 'all' );

		return false;
	}

	/**
	 * @since 2.9.0
	 *
	 * @param string $name
	 */
	private function clear_cache( $name ) {
		$name = $name . '_minify_cache';
		$this->app->delete_shared_object( $name, 'all' );
	}

	/**
	 * @param string $script
	 * @param int $priority
	 */
	public function register_script( $script, $priority = 10 ) {
		$this->set_script( preg_replace( '/<\s*\/?script\s*>/', '', $script ), $priority );
	}

	/**
	 * @param string $file
	 * @param int $priority
	 */
	public function register_js_file( $file, $priority = 10 ) {
		$this->set_script( @file_get_contents( $file ), $priority );
	}

	/**
	 * @param string $script
	 * @param int $priority
	 */
	private function set_script( $script, $priority ) {
		$script = trim( $script );
		if ( '' === $script ) {
			return;
		}

		if ( $this->check_cache( $script, 'script' ) ) {
			return;
		}

		$this->_script[ $priority ][] = $script;
		if ( $this->_has_output_script ) {
			$this->output_js();
		}
	}

	/**
	 * @since 2.9.0 Added: clear cache
	 *
	 * @param bool $clear_cache
	 */
	public function output_js( $clear_cache = false ) {
		if ( $clear_cache ) {
			$this->clear_cache( 'script' );
		}
		if ( empty( $this->_script ) ) {
			return;
		}
		ksort( $this->_script );
		$script = implode( "\n", array_map( function ( $s ) {
			return implode( "\n", $s );
		}, $this->_script ) );

		if ( $this->apply_filters( 'minify_js' ) ) {
			$minify = new \MatthiasMullie\Minify\JS();
			$minify->add( $script );
			echo '<script>' . $minify->minify() . '</script>';
		} else {
			echo '<script>' . $script . '</script>';
		}
		$this->_script            = [];
		$this->_has_output_script = true;
	}

	/**
	 * @param string $css
	 * @param int $priority
	 */
	public function register_style( $css, $priority = 10 ) {
		$this->set_style( preg_replace( '/<\s*\/?style\s*>/', '', $css ), $priority );
	}

	/**
	 * @param string $file
	 * @param int $priority
	 */
	public function register_css_file( $file, $priority = 10 ) {
		$this->set_style( @file_get_contents( $file ), $priority );
	}

	/**
	 * @param string $css
	 * @param int $priority
	 */
	private function set_style( $css, $priority ) {
		$css = trim( $css );
		if ( '' === $css ) {
			return;
		}

		if ( $this->check_cache( $css, 'style' ) ) {
			return;
		}

		$this->_css[ $priority ][] = $css;
		if ( $this->_end_footer ) {
			$this->output_css();
		}
	}

	/**
	 * @since 2.9.0 Added: clear cache
	 *
	 * @param bool $clear_cache
	 */
	public function output_css( $clear_cache = false ) {
		if ( $clear_cache ) {
			$this->clear_cache( 'style' );
		}
		if ( empty( $this->_css ) ) {
			return;
		}
		ksort( $this->_css );
		$css = implode( "\n", array_map( function ( $s ) {
			return implode( "\n", $s );
		}, $this->_css ) );

		if ( $this->apply_filters( 'minify_css' ) ) {
			$minify = new \MatthiasMullie\Minify\CSS();
			$minify->add( $css );
			echo '<style>' . $minify->minify() . '</style>';
		} else {
			echo '<style>' . $css . '</style>';
		}
		$this->_css = [];
	}

	/**
	 * end footer
	 * @since 2.10.0
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function end_footer() {
		$this->_end_footer = true;
	}
}
