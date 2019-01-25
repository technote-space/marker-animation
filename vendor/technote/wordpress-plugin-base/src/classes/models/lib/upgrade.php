<?php
/**
 * Technote Classes Models Lib Upgrade
 *
 * @version 2.10.0
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
 * @since 2.9.9 Changed: behavior to get readme file
 * @since 2.9.11 Added: upgrade log
 * @since 2.10.0 Changed: multiple version
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

		$this->app->log( sprintf( $this->translate( 'upgrade: %s to %s' ), $last_version, $this->app->get_plugin_version() ) );

		try {
			$upgrades = [];
			$count    = 0;
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
					$count ++;
				}
			}

			$this->app->log( sprintf( $this->translate( 'total upgrade process count: %d' ), $count ) );

			if ( empty( $upgrades ) ) {
				return;
			}

			uksort( $upgrades, 'version_compare' );
			foreach ( $upgrades as $version => $items ) {
				foreach ( $items as $item ) {
					call_user_func( $item );
				}
				$this->app->log( sprintf( $this->translate( 'upgrade process count of version %s: %d' ), $version, count( $items ) ) );
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
	 * @since 2.9.9
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
	 * @since 2.9.9
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
	 * @since 2.9.9
	 *
	 * @param $slug
	 *
	 * @return string
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function get_trunk_readme_url( $slug ) {
		return $this->apply_filters( 'trunk_readme_url', 'https://plugins.svn.wordpress.org/' . $slug . '/trunk/readme.txt', $slug );
	}

	/**
	 * @since 2.9.9
	 *
	 * @param $slug
	 *
	 * @return array|false
	 */
	private function get_upgrade_notice( $slug ) {
		$notice = $this->apply_filters( 'pre_get_update_notice', false, $slug );
		if ( is_array( $notice ) ) {
			return $notice;
		}

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
	 * @since 2.10.0 Improved: multiple version
	 *
	 * @param string $content
	 *
	 * @return array
	 */
	private function parse_update_notice( $content ) {
		$notices         = [];
		$version_notices = [];
		if ( preg_match( '#==\s*Upgrade Notice\s*==([\s\S]+?)==#', $content, $matches ) ) {
			$version = false;
			foreach ( (array) preg_split( '~[\r\n]+~', trim( $matches[1] ) ) as $line ) {
				$line = preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line );
				$line = preg_replace( '#\A\s*\*+\s*#', '', $line );
				if ( preg_match( '#\A\s*=\s*([^\s]+)\s*=\s*\z#', $line, $m1 ) && preg_match( '#\s*(v\.?)?(\d+[\d.]*)*\s*#', $m1[1], $m2 ) ) {
					$version = $m2[2];
					continue;
				}
				if ( $version && version_compare( $version, $this->app->get_plugin_version(), '<=' ) ) {
					continue;
				}
				$line = preg_replace( '#\A\s*=\s*([^\s]+)\s*=\s*\z#', '[ $1 ]', $line );
				$line = trim( $line );
				if ( '' !== $line ) {
					if ( $version ) {
						$version_notices[ $version ][] = $line;
					} else {
						$notices[] = $line;
					}
				}
			}
			if ( ! empty( $version_notices ) ) {
				ksort( $version_notices );
				foreach ( $version_notices as $version => $items ) {
					$notices[ $version ] = $items;
				}
			}
		}

		return $notices;
	}
}
