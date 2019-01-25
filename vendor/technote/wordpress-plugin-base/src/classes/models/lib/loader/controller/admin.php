<?php
/**
 * Technote Classes Models Lib Loader Controller Admin
 *
 * @version 2.10.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.3.0 Changed: public property to readonly property
 * @since 2.7.0 Changed: log message
 * @since 2.8.0 Changed: visibility of get_menu_slug
 * @since 2.9.0 Improved: regexp
 * @since 2.9.12 Improved: enable to set page slug setting from config
 * @since 2.10.0 Improved: submenu order
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib\Loader\Controller;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Admin
 * @package Technote\Classes\Models\Lib\Loader\Controller
 * @property-read \Technote\Classes\Controllers\Admin\Base $page
 */
class Admin implements \Technote\Interfaces\Loader, \Technote\Interfaces\Nonce {

	use \Technote\Traits\Loader, \Technote\Traits\Nonce;

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var array $_messages
	 */
	private $_messages = [];

	/**
	 * @since 2.10.0
	 * @var \Technote\Classes\Controllers\Admin\Base[] $_pages
	 */
	private $_pages = [];

	/**
	 * @since 2.3.0
	 * @var array $readonly_properties
	 */
	protected $readonly_properties = [
		'page',
	];

	/**
	 * @since 2.9.12 Improved: config setting
	 * @return string
	 */
	protected function get_setting_slug() {
		return $this->apply_filters( 'get_setting_slug', $this->app->get_config( 'config', 'setting_page_slug' ) );
	}

	/**
	 * @return string
	 */
	public function get_menu_slug() {
		return $this->get_page_prefix() . $this->apply_filters( 'get_setting_menu_slug', $this->get_setting_slug() );
	}

	/**
	 * @return string
	 */
	private function get_plugin_title() {
		$plugin_title = $this->app->get_config( 'config', 'plugin_title' );
		empty( $plugin_title ) and $plugin_title = $this->app->original_plugin_name;

		return $this->apply_filters( 'get_plugin_title', $this->app->translate( $plugin_title ) );
	}

	/**
	 * @return \Technote\Classes\Controllers\Admin\Base|null
	 */
	private function load_page() {
		try {
			$prefix  = $this->get_page_prefix();
			$pattern = "#\A{$prefix}(.+)#";
			if ( isset( $_GET['page'] ) && preg_match( $pattern, $_GET['page'], $matches ) ) {
				$page          = $matches[1];
				$exploded      = explode( '-', $page );
				$page          = array_pop( $exploded );
				$add_namespace = implode( '\\', array_map( 'ucfirst', $exploded ) );
				! empty( $add_namespace ) and $add_namespace .= '\\';
				$instance = $this->get_class_instance( $this->get_class_setting( $page, $add_namespace ), '\Technote\Classes\Controllers\Admin\Base' );
				if ( false !== $instance ) {
					/** @var \Technote\Classes\Controllers\Admin\Base $instance */
					$this->do_action( 'pre_load_admin_page', $instance );

					return $instance;
				}
				$this->app->log( sprintf( '%s not found.', $_GET['page'] ), [
					'$_GET[\'page\']' => $_GET['page'],
					'$page'           => $page,
					'$add_namespace'  => $add_namespace,
				] );
			}
		} catch ( \Exception $e ) {
			$this->app->log( $e );
		}

		return null;
	}

	/**
	 * add menu
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function add_menu() {
		$capability = $this->app->get_config( 'capability', 'admin_menu', 'manage_options' );
		if ( ! $this->app->user_can( $capability ) ) {
			return;
		}

		$this->set_readonly_property( 'page', $this->load_page() );
		if ( isset( $this->page ) && $this->app->user_can( $this->apply_filters( 'admin_menu_capability', $this->page->get_capability(), $this->page ) ) ) {
			$this->page->action();
			$this->do_action( 'post_load_admin_page', $this->page );
		}

		$this->_pages = [];
		foreach ( $this->get_class_list() as $page ) {
			/** @var \Technote\Classes\Controllers\Admin\Base $page */
			if ( $this->app->user_can( $this->apply_filters( 'admin_menu_capability', $page->get_capability(), $page ) ) ) {
				$this->_pages[] = $page;
			}
		}

		$hook = add_menu_page(
			$this->get_plugin_title(),
			$this->get_plugin_title(),
			$capability,
			$this->get_menu_slug(),
			function () {
			},
			$this->get_img_url( $this->app->get_config( 'config', 'menu_image' ), '' ),
			$this->apply_filters( 'admin_menu_position' )
		);

		if ( isset( $this->page ) && $this->app->user_can( $this->page->get_capability() ) ) {
			add_action( "load-$hook", function () {
				$this->page->setup_help();
			} );
		}

		add_filter( 'plugin_action_links_' . $this->app->define->plugin_base_name, function ( $links ) {
			return $this->plugin_action_links( $links );
		} );

		/** @var \Technote\Classes\Controllers\Admin\Base $page */
		foreach ( $this->_pages as $page ) {
			$hook = add_submenu_page(
				$this->get_menu_slug(),
				$this->app->translate( $page->get_page_title() ),
				$this->app->translate( $page->get_menu_name() ),
				$capability,
				$this->get_page_prefix() . $page->get_page_slug(),
				function () {
					$this->load();
				}
			);
			if ( $this->page ) {
				add_action( "load-$hook", function () {
					$this->page->setup_help();
				} );
			}
		}
	}

	/**
	 * sort menu
	 * @since 2.10.0
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function sort_menu() {
		if ( ! $this->app->get_config( 'config', 'use_custom_post' ) ) {
			return;
		}

		global $submenu;
		$slug = $this->get_menu_slug();
		if ( empty( $submenu[ $slug ] ) ) {
			return;
		}

		$pages = $this->app->utility->array_map( $this->_pages, function ( $p ) {
			/** @var \Technote\Classes\Controllers\Admin\Base $p */
			return $this->get_page_prefix() . $p->get_page_slug();
		} );
		$pages = array_combine( $pages, $this->_pages );

		/** @var \Technote\Classes\Models\Lib\Custom_Post $custom_post */
		$custom_post = \Technote\Classes\Models\Lib\Custom_Post::get_instance( $this->app );
		$types       = $custom_post->get_custom_posts();
		$types       = array_combine( $this->app->utility->array_map( $types, function ( $p ) {
			/** @var \Technote\Interfaces\Helper\Custom_Post $p */
			return "edit.php?post_type={$p->get_post_type()}";
		} ), $types );

		$sort = [];
		foreach ( $submenu[ $slug ] as $item ) {
			if ( isset( $pages[ $item[2] ] ) ) {
				/** @var \Technote\Classes\Controllers\Admin\Base $p */
				$p = $pages[ $item[2] ];
				if ( method_exists( $p, 'get_load_priority' ) ) {
					$priority = $p->get_load_priority();
				} else {
					$priority = 10;
				}
				$sort[] = $priority;
			} elseif ( isset( $types[ $item[2] ] ) ) {
				/** @var \Technote\Interfaces\Helper\Custom_Post $p */
				$p = $types[ $item[2] ];
				if ( method_exists( $p, 'get_load_priority' ) ) {
					$priority = $p->get_load_priority();
				} else {
					$priority = 10;
				}
				$sort[] = $priority;
			} else {
				$sort[] = 10;
			}
		}
		if ( count( $sort ) !== count( $submenu[ $slug ] ) ) {
			return;
		}
		array_multisort( $sort, $submenu[ $slug ] );
	}

	/**
	 * @param $links
	 *
	 * @return array
	 */
	private function plugin_action_links( $links ) {
		$link = $this->get_view( 'admin/include/action_links', [
			'url' => menu_page_url( $this->get_menu_slug(), false ),
		] );
		array_unshift( $links, $link );

		return $links;
	}

	/**
	 * @return string
	 */
	public function get_nonce_slug() {
		return '_admin_main';
	}

	/**
	 * @return string
	 */
	private function get_page_prefix() {
		return $this->apply_filters( 'get_page_prefix', $this->get_slug( 'page_prefix', '' ) ) . '-';
	}

	/**
	 * @return array
	 */
	protected function get_namespaces() {
		return [
			$this->app->define->plugin_namespace . '\\Classes\\Controllers\\Admin\\',
			$this->app->define->lib_namespace . '\\Classes\\Controllers\\Admin\\',
		];
	}

	/**
	 * @return string
	 */
	protected function get_instanceof() {
		return '\Technote\Classes\Controllers\Admin\Base';
	}

	/**
	 * load
	 */
	private function load() {
		if ( isset( $this->page ) ) {
			if ( $this->app->user_can( $this->page->get_capability() ) ) {
				$this->get_view( 'admin/include/layout', [
					'page' => $this->page,
					'slug' => $this->page->get_page_slug(),
				], true );
			} else {
				$this->get_view( 'admin/include/error', [ 'message' => 'Forbidden.' ], true );
			}
		} else {
			$this->get_view( 'admin/include/error', [ 'message' => 'Page not found.' ], true );
		}
	}

	/**
	 * admin notice
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function admin_notice() {
		if ( $this->app->user_can( $this->app->get_config( 'capability', 'admin_notice_capability', 'manage_options' ) ) ) {
			$this->get_view( 'admin/include/notice', [
				'messages' => $this->_messages,
			], true );
		}
	}

	/**
	 * @param string $message
	 * @param string $group
	 * @param bool $escape
	 * @param bool $error
	 */
	public function add_message( $message, $group = '', $error = false, $escape = true ) {
		$this->_messages[ $group ][ $error ? 'error' : 'updated' ][] = [ $message, $escape ];
	}
}
