<?php
/**
 * Technote Classes Models Lib Minify
 *
 * @version 2.9.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.9.0 Added: method to clear cache
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

	/** @var array */
	private $script = [];

	/** @var bool */
	private $has_output_script = false;

	/** @var array */
	private $css = [];

	/** @var bool */
	private $end_footer = false;

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

		$this->script[ $priority ][] = $script;
		if ( $this->has_output_script ) {
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
		if ( empty( $this->script ) ) {
			return;
		}
		ksort( $this->script );
		$script = implode( "\n", array_map( function ( $s ) {
			return implode( "\n", $s );
		}, $this->script ) );

		if ( $this->apply_filters( 'minify_js' ) ) {
			$minify = new \MatthiasMullie\Minify\JS();
			$minify->add( $script );
			echo '<script>' . $minify->minify() . '</script>';
		} else {
			echo '<script>' . $script . '</script>';
		}
		$this->script            = [];
		$this->has_output_script = true;
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

		$this->css[ $priority ][] = $css;
		if ( $this->end_footer ) {
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
		if ( empty( $this->css ) ) {
			return;
		}
		ksort( $this->css );
		$css = implode( "\n", array_map( function ( $s ) {
			return implode( "\n", $s );
		}, $this->css ) );

		if ( $this->apply_filters( 'minify_css' ) ) {
			$minify = new \MatthiasMullie\Minify\CSS();
			$minify->add( $css );
			echo '<style>' . $minify->minify() . '</style>';
		} else {
			echo '<style>' . $css . '</style>';
		}
		$this->css = [];
	}
}
