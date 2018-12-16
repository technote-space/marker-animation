<?php
/**
 * Technote Classes Models Lib Define
 *
 * @version 2.5.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0 Changed: directory structure
 * @since 2.1.0 Changed: load textdomain from plugin data
 * @since 2.3.0 Changed: public properties to readonly properties
 * @since 2.5.0 Changed: views directory
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Define
 * @package Technote\Classes\Models\Lib
 * @property-read string $plugin_name
 * @property-read string $plugin_file
 * @property-read string $plugin_namespace
 * @property-read string $plugin_dir
 * @property-read string $plugin_dir_name
 * @property-read string $plugin_base_name
 * @property-read string $lib_name
 * @property-read string $lib_namespace
 * @property-read string $lib_dir
 * @property-read string $lib_assets_dir
 * @property-read string $lib_src_dir
 * @property-read string $lib_configs_dir
 * @property-read string $lib_views_dir
 * @property-read string $lib_textdomain
 * @property-read string $lib_languages_dir
 * @property-read string $lib_languages_rel_path
 * @property-read string $lib_vendor_dir
 * @property-read string $lib_assets_url
 * @property-read string $plugin_assets_dir
 * @property-read string $plugin_src_dir
 * @property-read string $plugin_configs_dir
 * @property-read string $plugin_views_dir
 * @property-read string|false $plugin_textdomain
 * @property-read string|false $plugin_languages_dir
 * @property-read string|false $plugin_languages_rel_path
 * @property-read string $plugin_logs_dir
 * @property-read string $plugin_url
 * @property-read string $plugin_assets_url
 */
class Define implements \Technote\Interfaces\Singleton {

	use \Technote\Traits\Singleton;

	/**
	 * @since 2.3.0
	 * @var array $readonly_properties
	 */
	protected $readonly_properties = [
		'plugin_name',
		'plugin_file',
		'plugin_namespace',
		'plugin_dir',
		'plugin_dir_name',
		'plugin_base_name',
		'lib_name',
		'lib_namespace',
		'lib_dir',
		'lib_assets_dir',
		'lib_src_dir',
		'lib_configs_dir',
		'lib_views_dir',
		'lib_textdomain',
		'lib_languages_dir',
		'lib_languages_rel_path',
		'lib_vendor_dir',
		'lib_assets_url',
		'plugin_assets_dir',
		'plugin_src_dir',
		'plugin_configs_dir',
		'plugin_views_dir',
		'plugin_textdomain',
		'plugin_languages_dir',
		'plugin_languages_rel_path',
		'plugin_logs_dir',
		'plugin_url',
		'plugin_assets_url',
	];

	/**
	 * initialize
	 * @since 2.1.0 Changed: load textdomain from plugin data
	 */
	protected function initialize() {
		$this->plugin_name = $this->app->plugin_name;
		$this->plugin_file = $this->app->plugin_file;

		$this->plugin_namespace = ucwords( $this->plugin_name, '_' );
		$this->plugin_dir       = dirname( $this->plugin_file );
		$this->plugin_dir_name  = basename( $this->plugin_dir );
		$this->plugin_base_name = plugin_basename( $this->plugin_file );

		$cache = $this->app->get_shared_object( 'lib_defines_cache', 'all' );
		if ( ! isset( $cache ) ) {
			$cache                           = [];
			$cache['lib_name']               = TECHNOTE_PLUGIN;
			$cache['lib_dir']                = $this->app->get_library_directory();
			$cache['lib_namespace']          = ucfirst( $cache['lib_name'] );
			$cache['lib_assets_dir']         = $cache['lib_dir'] . DS . 'assets';
			$cache['lib_src_dir']            = $cache['lib_dir'] . DS . 'src';
			$cache['lib_configs_dir']        = $cache['lib_dir'] . DS . 'configs';
			$cache['lib_views_dir']          = $cache['lib_src_dir'] . DS . 'views';
			$cache['lib_textdomain']         = TECHNOTE_PLUGIN;
			$cache['lib_languages_dir']      = $cache['lib_dir'] . DS . 'languages';
			$cache['lib_languages_rel_path'] = ltrim( str_replace( WP_PLUGIN_DIR, '', $cache['lib_languages_dir'] ), DS );
			$cache['lib_vendor_dir']         = $cache['lib_dir'] . DS . 'vendor';
			$cache['lib_assets_url']         = plugins_url( 'assets', $cache['lib_assets_dir'] );
			$this->app->set_shared_object( 'lib_defines_cache', $cache, 'all' );
		}
		foreach ( $cache as $k => $v ) {
			$this->$k = $v;
		}

		$this->plugin_assets_dir  = $this->plugin_dir . DS . 'assets';
		$this->plugin_src_dir     = $this->plugin_dir . DS . 'src';
		$this->plugin_configs_dir = $this->plugin_dir . DS . 'configs';
		$this->plugin_views_dir   = $this->plugin_src_dir . DS . 'views';
		$domain_path              = trim( $this->app->plugin_data['DomainPath'], '/' . DS );
		if ( empty( $domain_path ) || ! is_dir( $this->plugin_dir . DS . $domain_path ) ) {
			$this->plugin_textdomain         = false;
			$this->plugin_languages_dir      = false;
			$this->plugin_languages_rel_path = false;
		} else {
			$this->plugin_textdomain         = $this->app->plugin_data['TextDomain'];
			$this->plugin_languages_dir      = $this->plugin_dir . DS . $domain_path;
			$this->plugin_languages_rel_path = ltrim( str_replace( WP_PLUGIN_DIR, '', $this->plugin_languages_dir ), DS );
		}
		$this->plugin_logs_dir = $this->plugin_dir . DS . 'logs';

		$this->plugin_url        = plugins_url( '', $this->plugin_file );
		$this->plugin_assets_url = $this->plugin_url . '/assets';
	}
}
