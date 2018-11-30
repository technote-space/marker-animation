<?php
/**
 * Technote Interfaces Presenter
 *
 * @version 1.1.13
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Interfaces;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Interface Presenter
 * @package Technote\Interfaces
 */
interface Presenter {

	/**
	 * @param string $name
	 * @param array $args
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function get_view( $name, $args = [], $echo = false );

	/**
	 * @param string $name
	 * @param array $args
	 * @param array $overwrite
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function form( $name, $args = [], $overwrite = [], $echo = true );

	/**
	 * @param mixed $data
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function dump( $data, $echo = true );

	/**
	 * @param string $script
	 * @param int $priority
	 */
	public function add_script( $script, $priority = 10 );

	/**
	 * @param string $style
	 * @param int $priority
	 */
	public function add_style( $style, $priority = 10 );

	/**
	 * @param string $name
	 * @param array $args
	 * @param int $priority
	 */
	public function add_script_view( $name, $args = [], $priority = 10 );

	/**
	 * @param string $name
	 * @param array $args
	 * @param int $priority
	 */
	public function add_style_view( $name, $args = [], $priority = 10 );

	/**
	 * @param string $value
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function h( $value, $echo = true );

	/**
	 * echo plugin_name
	 */
	public function id();

	/**
	 * @param array $data
	 * @param bool $echo
	 *
	 * @return int
	 */
	public function n( $data, $echo = true );

	/**
	 * @param string $url
	 * @param string $contents
	 * @param bool $translate
	 * @param bool $new_tab
	 * @param array $args
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function url( $url, $contents, $translate = false, $new_tab = false, $args = [], $echo = true );

	/**
	 * @param string $path
	 * @param string $default
	 * @param bool $append_version
	 *
	 * @return string
	 */
	public function get_assets_url( $path, $default = '', $append_version = true );

	/**
	 * @param string $path
	 * @param string $default
	 * @param bool $append_version
	 *
	 * @return string
	 */
	public function get_img_url( $path, $default = 'img/no_img.png', $append_version = true );

	/**
	 * @param string $url
	 * @param string $view
	 * @param array $args
	 * @param string $field
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function assets( $url, $view, $args, $field, $echo = true );

	/**
	 * @param string $path
	 * @param array $args
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function img( $path, $args = [], $echo = true );

	/**
	 * @param array $args
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function loading( $args = [], $echo = true );

	/**
	 * @param array $args
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function no_img( $args = [], $echo = true );

	/**
	 * @param string $path
	 * @param int $priority
	 *
	 * @return bool
	 */
	public function css( $path, $priority = 10 );

	/**
	 * @param string $path
	 * @param int $priority
	 *
	 * @return bool
	 */
	public function js( $path, $priority = 10 );

	/**
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function modal_class( $echo = true );

}
