<?php
/**
 * WP_Framework_Presenter Classes Models Drawer
 *
 * @version 0.0.7
 * @author technote-space
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace WP_Framework_Presenter\Classes\Models;

if ( ! defined( 'WP_CONTENT_FRAMEWORK' ) ) {
	exit;
}

/**
 * Class Drawer
 * @package WP_Framework_Presenter\Classes\Models
 */
class Drawer implements \WP_Framework_Core\Interfaces\Singleton, \WP_Framework_Core\Interfaces\Hook, \WP_Framework_Presenter\Interfaces\Presenter, \WP_Framework_Common\Interfaces\Uninstall {

	use \WP_Framework_Core\Traits\Singleton, \WP_Framework_Core\Traits\Hook, \WP_Framework_Presenter\Traits\Presenter, \WP_Framework_Common\Traits\Uninstall;

	/**
	 * @var string|false $_package
	 */
	private $_package = false;

	/**
	 * @return string
	 */
	public function get_package() {
		$package        = $this->_package ? $this->_package : 'presenter';
		$this->_package = false;

		return $package;
	}

	/**
	 * @param \WP_Framework_Core\Interfaces\Package $package
	 */
	public function set_package( \WP_Framework_Core\Interfaces\Package $package ) {
		$this->_package = $package->get_package();
	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		$this->app->utility->delete_upload_dir( $this->app );
	}
}
