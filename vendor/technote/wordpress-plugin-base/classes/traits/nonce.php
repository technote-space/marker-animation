<?php
/**
 * Technote Traits Nonce
 *
 * @version 1.1.41
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Traits;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Trait Nonce
 * @package Technote\Traits
 * @property \Technote $app
 */
trait Nonce {

	/**
	 * @return string
	 */
	abstract public function get_nonce_slug();

	/**
	 * @return string
	 */
	private function get_nonce_key() {
		$slug       = $this->get_slug( 'nonce_key', '_nonce' );
		$nonce_slug = $this->get_nonce_slug();

		return $this->apply_filters( 'get_nonce_key', $slug . '_' . $nonce_slug, $slug, $nonce_slug );
	}

	/**
	 * @return string
	 */
	private function get_nonce_action() {
		$slug       = $this->get_slug( 'nonce_action', '_nonce_action' );
		$nonce_slug = $this->get_nonce_slug();

		return $this->apply_filters( 'get_nonce_action', $slug . '_' . $nonce_slug, $slug, $nonce_slug );
	}

	/**
	 * @return string
	 */
	protected function create_nonce() {
		return wp_create_nonce( $this->get_nonce_action() );
	}

	/**
	 * @return bool
	 */
	private function nonce_check() {
		$nonce_key = $this->get_nonce_key();

		return ! $this->need_nonce_check( $nonce_key ) || ( isset( $_REQUEST[ $nonce_key ] ) && $this->verify_nonce( $_REQUEST[ $nonce_key ] ) );
	}

	/**
	 * @param string $nonce
	 *
	 * @return false|int
	 */
	public function verify_nonce( $nonce ) {
		return wp_verify_nonce( $nonce, $this->get_nonce_action() );
	}

	/**
	 * @return bool
	 */
	protected function is_post() {
		return $this->app->input->is_post();
	}

	/**
	 * @param string $nonce_key
	 *
	 * @return bool
	 */
	protected function need_nonce_check(
		/** @noinspection PhpUnusedParameterInspection */
		$nonce_key
	) {
		return $this->is_post();
	}
}
