<?php
/**
 * Technote Models Loader Controller Api
 *
 * @version 1.1.58
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Models\Loader\Controller;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Admin
 * @package Technote\Models\Loader\Controller
 */
class Api implements \Technote\Interfaces\Loader, \Technote\Interfaces\Nonce {

	use \Technote\Traits\Loader, \Technote\Traits\Nonce;

	/**
	 * @return bool
	 */
	private function use_admin_ajax() {
		return $this->apply_filters( 'use_admin_ajax', true );
	}

	/**
	 * @return string
	 */
	public function get_nonce_slug() {
		return 'wp_rest';
	}

	/**
	 * register script
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function register_script() {
		if ( $this->use_admin_ajax() ) {
			$this->register_ajax_script();
		} else {
			$this->register_json_script();
		}
	}

	/**
	 * register api for wp-json
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function register_rest_api() {
		if ( $this->use_admin_ajax() ) {
			return;
		}
		foreach ( $this->get_api_controllers( false ) as $api ) {
			/** @var \Technote\Controllers\Api\Base $api */
			register_rest_route( $this->get_api_namespace(), $api->get_endpoint(), [
				'methods'             => strtoupper( $api->get_method() ),
				'permission_callback' => function () use ( $api ) {
					return $this->app->user_can( $api->get_capability() );
				},
				'args'                => $api->get_args_setting(),
				'callback'            => [ $api, 'callback' ],
			] );
		}
	}

	/**
	 * register api for admin-ajax.php
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function register_ajax_api() {
		if ( ! $this->use_admin_ajax() ) {
			return;
		}
		foreach ( $this->get_api_controllers( false ) as $api ) {
			/** @var \Technote\Controllers\Api\Base $api */
			$action   = $this->get_api_namespace() . '_' . $api->get_endpoint();
			$callback = function () use ( $api ) {
				$this->ajax_action_execute( $api );
			};
			add_action( 'wp_ajax_' . $action, $callback );
			add_action( 'wp_ajax_nopriv_' . $action, $callback );
		}
	}

	/**
	 * @param callable $get_view_params
	 */
	private function register_script_common( $get_view_params ) {
		$functions = [];
		$scripts   = [];
		/** @var \Technote\Traits\Controller\Api $api */
		foreach ( $this->get_api_controllers( true ) as $api ) {
			$name               = $api->get_call_function_name();
			$functions[ $name ] = [
				'method'   => $api->get_method(),
				'endpoint' => $api->get_endpoint(),
			];
			$script             = is_admin() ? $api->admin_script() : $api->front_script();
			if ( ! empty( $script ) ) {
				$scripts[] = $script;
			}
		}
		if ( ! empty( $functions ) ) {
			$this->add_script_view( 'include/script/api', array_merge( [
				'namespace' => $this->get_api_namespace(),
				'functions' => $functions,
			], $get_view_params( $functions ) ), 9 );
			foreach ( $scripts as $script ) {
				$this->add_script( $script );
			}
		}
	}

	/**
	 * register script for wp-json
	 */
	private function register_json_script() {
		return $this->register_script_common( function () {
			return [
				'endpoint'      => rest_url(),
				'nonce'         => wp_create_nonce( $this->get_nonce_slug() ),
				'is_admin_ajax' => false,
			];
		} );
	}

	/**
	 * register script for admin-ajax.php
	 */
	private function register_ajax_script() {
		return $this->register_script_common( function () {
			return [
				'endpoint'      => $this->apply_filters( 'admin_ajax', admin_url( 'admin-ajax.php' ) ),
				'nonce_key'     => $this->get_nonce_key(),
				'nonce_value'   => $this->create_nonce(),
				'is_admin_ajax' => true,
			];
		} );
	}

	/**
	 * @return array
	 */
	public function get_nonce_data() {
		return [
			'nonce'         => wp_create_nonce( $this->get_nonce_slug() ),
			'nonce_key'     => $this->get_nonce_key(),
			'nonce_value'   => $this->create_nonce(),
			'is_admin_ajax' => $this->use_admin_ajax(),
		];
	}

	/**
	 * @param \Technote\Controllers\Api\Base $api
	 */
	private function ajax_action_execute( $api ) {
		$result = $this->get_ajax_action_result( $api );
		if ( ! is_wp_error( $result ) && ! ( $result instanceof \WP_REST_Response ) ) {
			$result = new \WP_REST_Response( $result );
		}
		if ( is_wp_error( $result ) ) {
			$result = $this->error_to_response( $result );
		}

		foreach ( $result->headers as $key => $value ) {
			$value = preg_replace( '/\s+/', ' ', $value );
			header( sprintf( '%s: %s', $key, $value ) );
		}
		status_header( $result->status );

		wp_send_json( $result->data );
	}

	/**
	 * @param \WP_Error $error
	 *
	 * @return \WP_REST_Response
	 */
	private function error_to_response( $error ) {
		$error_data = $error->get_error_data();

		if ( is_array( $error_data ) && isset( $error_data['status'] ) ) {
			$status = $error_data['status'];
		} else {
			$status = 500;
		}

		$errors = [];
		foreach ( (array) $error->errors as $code => $messages ) {
			foreach ( (array) $messages as $message ) {
				$errors[] = [ 'code' => $code, 'message' => $message, 'data' => $error->get_error_data( $code ) ];
			}
		}

		$data = $errors[0];
		if ( count( $errors ) > 1 ) {
			array_shift( $errors );
			$data['additional_errors'] = $errors;
		}
		$response = new \WP_REST_Response( $data, $status );

		return $response;
	}

	/**
	 * @param \Technote\Controllers\Api\Base $api
	 *
	 * @return int|\WP_Error|\WP_REST_Response
	 */
	private function get_ajax_action_result( $api ) {
		if ( ! $this->nonce_check() ) {
			return new \WP_Error( 'rest_forbidden', 'Forbidden', [ 'status' => 403 ] );
		}
		if ( ! $this->app->user_can( $api->get_capability() ) ) {
			return new \WP_Error( 'rest_forbidden', 'Forbidden', [ 'status' => 403 ] );
		}
		if ( strtoupper( $api->get_method() ) !== $this->app->input->method() ) {
			return new \WP_Error( 'rest_no_route', __( 'No route was found matching the URL and request method' ), [ 'status' => 404 ] );
		}

		if ( in_array( $this->app->input->method(), [
			'GET',
			'HEAD',
		] ) ) {
			$params = $this->app->input->get();
		} else {
			$params = $this->app->input->post();
		}

		$args           = $api->get_args_setting();
		$required       = [];
		$invalid_params = [];
		$request        = new \WP_REST_Request( $_SERVER['REQUEST_METHOD'] );
		$request->set_query_params( wp_unslash( $_GET ) );
		$request->set_body_params( wp_unslash( $_POST ) );
		foreach ( $args as $name => $setting ) {
			if ( array_key_exists( 'default', $setting ) && ! array_key_exists( $name, $params ) ) {
				$params[ $name ] = $setting['default'];
			}
			if ( ! isset( $params[ $name ] ) ) {
				if ( ! empty( $setting['required'] ) ) {
					$required[] = $name;
				}
				continue;
			}
			if ( ! empty( $setting['validate_callback'] ) ) {
				$valid_check = call_user_func( $setting['validate_callback'], $params[ $name ], $request, $name );
				if ( false === $valid_check ) {
					$invalid_params[ $name ] = __( 'Invalid parameter.' );
					continue;
				}
				if ( is_wp_error( $valid_check ) ) {
					$invalid_params[ $name ] = $valid_check->get_error_message();
					continue;
				}
			}
			if ( ! empty( $setting['sanitize_callback'] ) && is_callable( $setting['sanitize_callback'] ) ) {
				$sanitized_value = call_user_func( $setting['sanitize_callback'], $params[ $name ], $request, $name );
				if ( is_wp_error( $sanitized_value ) ) {
					$invalid_params[ $name ] = $sanitized_value->get_error_message();
				} else {
					$params[ $name ] = $sanitized_value;
				}
			}
		}

		if ( ! empty( $required ) ) {
			return new \WP_Error( 'rest_missing_callback_param', sprintf( __( 'Missing parameter(s): %s' ), implode( ', ', $required ) ), [
				'status' => 400,
				'params' => $required,
			] );
		}

		if ( $invalid_params ) {
			return new \WP_Error( 'rest_invalid_param', sprintf( __( 'Invalid parameter(s): %s' ), implode( ', ', array_keys( $invalid_params ) ) ), [
				'status' => 400,
				'params' => $invalid_params,
			] );
		}

		return $api->callback( $params );
	}

	/**
	 * @return bool
	 */
	protected function need_nonce_check() {
		if ( $this->app->input->request( 'action' ) === $this->get_api_namespace() . '_nonce' ) {
			return false;
		}

		return true;
	}

	/**
	 * @return array
	 */
	protected function get_namespaces() {
		return [
			$this->app->define->plugin_namespace . '\\Controllers\\Api\\',
			$this->app->define->lib_namespace . '\\Controllers\\Api\\',
		];
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\Technote\Controllers\Api\Base';
	}

	/**
	 * @param bool $filter
	 *
	 * @return array
	 */
	private function get_api_controllers( $filter ) {
		$api_controllers = $this->get_class_list();

		if ( $filter ) {
			/** @var \Technote\Traits\Controller\Api $class */
			foreach ( $api_controllers as $name => $class ) {
				if ( ! $class->is_valid() || ( is_admin() && $class->is_only_front() ) || ( ! is_admin() && $class->is_only_admin() ) ) {
					unset( $api_controllers[ $name ] );
				}
			}
		}

		return $api_controllers;
	}

	/**
	 * @param $result
	 * @param \WP_REST_Server $server
	 * @param \WP_REST_Request $request
	 *
	 * @return mixed
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function rest_pre_dispatch(
		/** @noinspection PhpUnusedParameterInspection */
		$result,
		$server,
		$request
	) {
		if ( $this->use_admin_ajax() ) {
			return $result;
		}

		$namespaces = $request->get_route();
		if ( strpos( $namespaces, $this->get_api_namespace() ) === 1 ) {
			return null;
		}

		return $result;
	}

	/**
	 * @return string
	 */
	private function get_api_namespace() {
		if ( $this->use_admin_ajax() ) {
			return $this->get_slug( 'api_namespace', '' );
		}

		return $this->get_slug( 'api_namespace', '' ) . '/' . $this->app->get_config( 'config', 'api_version' );
	}
}
