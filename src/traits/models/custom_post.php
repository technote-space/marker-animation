<?php
/**
 * @version 1.4.0
 * @author technote-space
 * @since 1.4.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

namespace Marker_Animation\Traits\Models;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Trait Custom_Post
 * @package Marker_Animation\Traits\Models
 */
trait Custom_Post {

	use \Technote\Traits\Helper\Custom_Post, \Technote\Traits\Helper\Data_Helper, \Technote\Traits\Helper\Validate;

	/**
	 * @return string
	 */
	public function get_post_type() {
		return 'ma-' . $this->get_post_type_slug();
	}

	/**
	 * @return array
	 */
	protected function get_capabilities() {
		return $this->get_post_capabilities();
	}

	/**
	 * @return string|false
	 */
	protected function get_post_type_parent() {
		return $this->app->loader->admin->get_menu_slug();
	}

	/**
	 * @return array
	 */
	protected function get_post_capabilities() {
		return (array) get_post_type_object( 'post' )->cap;
	}
}
