<?php
/**
 * Technote Classes Models Lib Upgrade
 *
 * @version 2.9.8
 * @author technote-space
 * @since 2.4.0
 * @since 2.4.1 Added: show_plugin_update_notices method
 * @since 2.4.3 Fixed: get plugin upgrade notice from plugin directory
 * @since 2.6.0 Fixed: search upgrade file namespace
 * @since 2.6.0 Changed: call setup_update from admin_init filter
 * @since 2.6.0 Fixed: debug code
 * @since 2.7.0 Added: error handling
 * @since 2.9.0 Improved: regexp
 * @since 2.9.1 Fixed: compare version
 * @since 2.9.8 Fixed: ignore if first activated
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Upgrade
 * @package Technote\Classes\Models\Lib
 */
class Upgrade implements \Technote\Interfaces\Loader {

	use \Technote\Traits\Loader;

	/**
	 * upgrade
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function upgrade() {
		if ( ! $this->is_required_upgrade() ) {
			return;
		}
		$last_version = $this->get_last_upgrade_version();
		$this->set_last_upgrade_version();
		if ( empty( $last_version ) ) {
			return;
		}

		try {
			$upgrades = [];
			foreach ( $this->get_class_list() as $class ) {
				/** @var \Technote\Interfaces\Upgrade $class */
				foreach ( $class->get_upgrade_methods() as $items ) {
					if ( ! is_array( $items ) ) {
						continue;
					}
					$version  = $this->app->utility->array_get( $items, 'version' );
					$callback = $this->app->utility->array_get( $items, 'callback' );
					if ( ! isset( $version ) || empty( $callback ) || ! is_string( $version ) ) {
						continue;
					}
					if ( version_compare( $version, $last_version, '<=' ) ) {
						continue;
					}
					if ( ! is_callable( $callback ) && ( ! is_string( $callback ) || ! method_exists( $class, $callback ) || ! is_callable( [ $class, $callback ] ) ) ) {
						continue;
					}
					$upgrades[ $version ][] = is_callable( $callback ) ? $callback : [ $class, $callback ];
				}
			}
			if ( empty( $upgrades ) ) {
				return;
			}

			uksort( $upgrades, 'version_compare' );
			foreach ( $upgrades as $version => $items ) {
				foreach ( $items as $item ) {
					call_user_func( $item );
				}
			}
		} catch ( \Exception $e ) {
			$this->app->log( $e );
		}
	}

	/**
	 * setup update
	 * @since 2.1.0 Added: check develop version
	 * @since 2.1.1 Fixed: check develop version
	 * @since 2.4.1 Added: plugin upgrade notices feature
	 * @since 2.6.0 Changed: call setup_update from admin_init filter
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function setup_update() {
		$update_info_file_url = $this->app->get_config( 'config', 'update_info_file_url' );
		if ( ! empty( $update_info_file_url ) ) {
			if ( $this->apply_filters( 'check_update' ) ) {
				\Puc_v4_Factory::buildUpdateChecker(
					$update_info_file_url,
					$this->app->plugin_file,
					$this->app->plugin_name
				);
			}
		} else {
			$this->app->setting->remove_setting( 'check_update' );
		}

		$this->show_plugin_update_notices();
	}

	/**
	 * @since 2.6.0 Fixed: search upgrade file namespace
	 * @return array
	 */
	protected function get_namespaces() {
		return [
			$this->app->define->plugin_namespace . '\\Classes',
		];
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\Technote\Interfaces\Upgrade';
	}

	/**
	 * @return string
	 */
	private function get_last_upgrade_version_option_key() {
		return 'last_upgrade_version';
	}

	/**
	 * @return mixed
	 */
	private function get_last_upgrade_version() {
		return $this->app->get_option( $this->get_last_upgrade_version_option_key() );
	}

	/**
	 * @return bool
	 */
	private function set_last_upgrade_version() {
		return $this->app->option->set( $this->get_last_upgrade_version_option_key(), $this->app->get_plugin_version() );
	}

	/**
	 * @return bool
	 */
	private function is_required_upgrade() {
		$version = $this->get_last_upgrade_version();

		return empty( $version ) || version_compare( $version, $this->app->get_plugin_version(), '<' );
	}

	/**
	 * show plugin upgrade notices
	 * @since 2.4.1
	 * @since 2.4.3 Fixed: get plugin upgrade notice from plugin directory
	 */
	private function show_plugin_update_notices() {
		add_action( 'in_plugin_update_message-' . $this->app->define->plugin_base_name, function ( $data, $r ) {
			$new_version = $r->new_version;
			$url         = $this->app->utility->array_get( $data, 'PluginURI' );
			$notices     = $this->get_upgrade_notices( $new_version, $url );
			if ( ! empty( $notices ) ) {
				$this->get_view( 'admin/include/upgrade', [
					'notices' => $notices,
				], true );
			}
		}, 10, 2 );
	}

	/**
	 * @return string|false
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function get_config_readme_url() {
		$url = $this->app->get_config( 'config', 'readme_file_check_url' );
		if ( ! empty( $url ) ) {
			return $url;
		}

		return false;
	}

	/**
	 * @return string|false
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function get_readme_url_from_update_info_url() {
		$url = $this->app->get_config( 'config', 'update_info_file_url' );
		if ( ! empty( $url ) ) {
			$info = pathinfo( $url );

			return $info['dirname'] . '/readme.txt';
		}

		return false;
	}

	/**
	 * @param $slug
	 *
	 * @return string
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function get_trunk_readme_url( $slug ) {
		return $this->apply_filters( 'plugin_readme', 'https://plugins.svn.wordpress.org/' . $slug . '/trunk/readme.txt', $slug );
	}

	/**
	 * @param $slug
	 *
	 * @return array|false
	 */
	private function get_upgrade_notice( $slug ) {
		foreach (
			[
				'get_config_readme_url',
				'get_readme_url_from_update_info_url',
				'get_trunk_readme_url',
			] as $method
		) {
			$response = wp_safe_remote_get( $this->$method( $slug ) );
			if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
				return $this->parse_update_notice( $response['body'] );
			}
		}

		return false;
	}

	/**
	 * @since 2.4.3
	 * @since 2.6.0 Fixed: debug code
	 *
	 * @param string $version
	 * @param string $url
	 *
	 * @return bool|mixed
	 */
	private function get_upgrade_notices( $version, $url ) {
		$slug = $this->get_plugin_slug( $url );
		if ( empty( $slug ) ) {
			return false;
		}

		$transient_name = 'upgrade_notice-' . $slug . '_' . $version;
		$upgrade_notice = get_transient( $transient_name );

		if ( false === $upgrade_notice ) {
			$upgrade_notice = $this->get_upgrade_notice( $slug );
			if ( $upgrade_notice ) {
				set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
			}
		}

		return $upgrade_notice;
	}

	/**
	 * @since 2.4.3
	 *
	 * @param string $url
	 *
	 * @return false|string
	 */
	private function get_plugin_slug( $url ) {
		if ( $this->app->utility->starts_with( $url, 'https://wordpress.org/plugins/' ) ) {
			return trim( str_replace( 'https://wordpress.org/plugins/', '', $url ), '/' );
		}

		return false;
	}

	/**
	 * @since 2.4.3
	 *
	 * @param string $content
	 *
	 * @return array
	 */
	private function parse_update_notice( $content ) {
		$notices = [];
		if ( preg_match( '#==\s*Upgrade Notice\s*==([\s\S]+?)==#', $content, $matches ) ) {
			foreach ( (array) preg_split( '~[\r\n]+~', trim( $matches[1] ) ) as $line ) {
				$line = preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line );
				$line = preg_replace( '#\A\s*\*+\s*#', '', $line );
				$line = preg_replace( '#\A\s*=\s*([^\s]+)\s*=\s*\z#', '[ $1 ]', $line );
				$line = trim( $line );
				if ( '' !== $line ) {
					$notices[] = $line;
				}
			}
		}

		return $notices;
	}
}
