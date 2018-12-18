<?php
/**
 * Technote Interfaces Presenter
 *
 * @version 2.7.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.7.0 Added: property doc
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
 * @property \Technote $app
 */
interface Presenter {

	/**
	 * @param string $name
	 * @param array $args
	 * @param bool $echo
	 * @param bool $error
	 * @param bool $remove_nl
	 *
	 * @return string
	 */
	public function get_view( $name, $args = [], $echo = false, $error = true, $remove_nl = false );

	/**
	 * @param string $name
	 * @param array $args
	 * @param array $overwrite
	 * @param bool $echo
	 * @param bool $error
	 *
	 * @return string
	 */
	public function form( $name, $args = [], $overwrite = [], $echo = true, $error = true );

	/**
	 * @param string $name
	 * @param mixed $data
	 * @param string|null $key
	 * @param string $default
	 * @param bool $checkbox
	 *
	 * @return mixed
	 */
	public function old( $name, $data, $key = null, $default = '', $checkbox = false );

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
	 * @param bool $translate
	 * @param bool $echo
	 * @param bool $escape
	 * @param array $args
	 *
	 * @return string
	 */
	public function h( $value, $translate = false, $echo = true, $escape = true, ...$args );

	/**
	 * @param mixed $value
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function json( $value, $echo = true );

	/**
	 * @param $value
	 *
	 * @return string
	 */
	public function translate( $value );

	/**
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function id( $echo = true );

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
	 * @param bool $escape
	 *
	 * @return string
	 */
	public function url( $url, $contents, $translate = false, $new_tab = false, $args = [], $echo = true, $escape = true );

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
	 * @param string $handle
	 * @param string $file
	 * @param array $depends
	 * @param string|bool|null $ver
	 * @param string $media
	 * @param string $dir
	 */
	public function enqueue_style( $handle, $file, $depends = [], $ver = false, $media = 'all', $dir = 'css' );

	/**
	 * @param string $handle
	 * @param string $file
	 * @param array $depends
	 * @param string|bool|null $ver
	 * @param bool $in_footer
	 * @param string $dir
	 */
	public function enqueue_script( $handle, $file, $depends = [], $ver = false, $in_footer = true, $dir = 'js' );

	/**
	 * setup modal
	 */
	public function setup_modal();

	/**
	 * setup color picker
	 */
	public function setup_color_picker();

	/**
	 * @return string
	 */
	public function get_color_picker_class();

	/**
	 * @param bool $echo
	 *
	 * @return string
	 */
	public function modal_class( $echo = true );

}
