<?php
/**
 * Technote
 *
 * @version 1.2.0
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}

/**
 * @since 1.2.0
 */
define( 'TECHNOTE_MOCK_REST_RESPONSE', ! class_exists( 'WP_REST_Response' ) );

if ( TECHNOTE_MOCK_REST_RESPONSE ) {
	// < v4.4

	/**
	 * Class WP_REST_Response
	 */
	class WP_REST_Response {
		/**
		 * Response data.
		 * @var mixed
		 */
		public $data;

		/**
		 * Response headers.
		 *
		 * @var array
		 */
		public $headers;

		/**
		 * Response status.
		 *
		 * @var int
		 */
		public $status;

		/**
		 * Constructor.
		 *
		 * @param mixed $data Response data. Default null.
		 * @param int $status Optional. HTTP status code. Default 200.
		 * @param array $headers Optional. HTTP header map. Default empty array.
		 */
		public function __construct( $data = null, $status = 200, $headers = [] ) {
			$this->data    = $data;
			$this->status  = $status;
			$this->headers = $headers;
		}
	}
}
