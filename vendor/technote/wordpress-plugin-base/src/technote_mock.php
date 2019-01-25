<?php
/**
 * Technote mock
 *
 * @version 2.10.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.1.0 Changed: load textdomain from plugin data
 * @since 2.10.0 Changed: trivial change
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
define( 'TECHNOTE_IS_MOCK', true );

/**
 * Class Technote
 * @property string $original_plugin_name
 * @property string $plugin_name
 * @property string $plugin_file
 * @property string $plugin_dir
 * @property string $plugin_configs_dir
 * @property string $textdomain
 * @property array $plugin_data
 */
class Technote {

	/**
	 * @since 2.10.0 Changed: trivial change
	 * @var array
	 */
	private static $_instances = array();

	/** @var string $original_plugin_name */
	public $original_plugin_name;

	/** @var string $plugin_name */
	public $plugin_name;

	/** @var string $plugin_file */
	public $plugin_file;

	/** @var string $plugin_dir */
	public $plugin_dir;

	/** @var string $plugin_configs_dir */
	public $plugin_configs_dir;

	/** 
     * @since 2.1.0 Changed: text_domain -> textdomain
     * @var string $textdomain 
     */
	public $textdomain;

	/** 
     * @since 2.1.0
     * @var array $plugin_data 
     */
	public $plugin_data;

	/**
	 * @since 1.2.0
	 * @return mixed
	 */
	private function is_not_enough_php_version() {
		return version_compare( phpversion(), TECHNOTE_REQUIRED_PHP_VERSION, '<' );
	}

	/**
	 * @since 1.2.0
	 * @return mixed
	 */
	private function is_not_enough_wp_version() {
		global $wp_version;

		return version_compare( $wp_version, TECHNOTE_REQUIRED_WP_VERSION, '<' );
	}

	/**
	 * Technote constructor.
	 *
	 * @param string $plugin_name
	 * @param string $plugin_file
	 */
	private function __construct( $plugin_name, $plugin_file ) {
		$this->original_plugin_name = $plugin_name;
		$this->plugin_name          = strtolower( $plugin_name );
		$this->plugin_file          = $plugin_file;
		$this->plugin_dir           = dirname( $this->plugin_file );
		$this->plugin_configs_dir   = $this->plugin_dir . DS . 'configs';

		if ( ! ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) ) {
			if ( $this->is_not_enough_php_version() || $this->is_not_enough_wp_version() ) {
				$this->set_unsupported();
			}
		}
	}

	/**
	 * @since 1.2.1
     * @since 2.1.0 Changed: load textdomain from plugin data
	 * @return string
	 */
	private function get_textdomain() {
		if ( ! isset( $this->textdomain ) ) {
			$domain_path = trim( $this->plugin_data['DomainPath'], '/' . DS );
			if ( empty( $domain_path ) ) {
				$this->textdomain          = false;
				$plugin_languages_rel_path = false;
			} else {
				$this->textdomain          = $this->plugin_data['TextDomain'];
				$plugin_languages_rel_path = ltrim( str_replace( WP_PLUGIN_DIR, '', $this->plugin_dir . DS . $domain_path ), DS );
			}

			$lib_languages_rel_path = ltrim( str_replace( WP_PLUGIN_DIR, '', dirname( TECHNOTE_BOOTSTRAP ) . DS . 'languages' ), DS );
			load_plugin_textdomain( TECHNOTE_PLUGIN, false, $lib_languages_rel_path );
			if ( ! empty( $this->textdomain ) ) {
				load_plugin_textdomain( $this->textdomain, false, $plugin_languages_rel_path );
			}
		}

		return $this->textdomain;
	}

	/**
	 * @since 1.2.1
	 *
	 * @param $value
	 *
	 * @return string
	 */
	private function translate( $value ) {
		$textdomain = $this->get_textdomain();
		if ( ! empty( $textdomain ) ) {
			$translated = __( $value, $textdomain );
			if ( $value !== $translated ) {
				return $translated;
			}
		}

		return __( $value, TECHNOTE_PLUGIN );
	}

	/**
	 * @param string $plugin_name
	 * @param string $plugin_file
	 *
	 * @return Technote
	 */
	public static function get_instance( $plugin_name, $plugin_file ) {
		if ( ! isset( self::$_instances[ $plugin_name ] ) ) {
			self::$_instances[ $plugin_name ] = new static( $plugin_name, $plugin_file );
		}

		return self::$_instances[ $plugin_name ];
	}

	/**
	 * set unsupported
	 */
	private function set_unsupported() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * init
     * @since 2.1.0
	 */
	public function init() {
		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$this->plugin_data = get_plugin_data( $this->plugin_file, false, false );
	}

	/**
	 * @since 1.2.0
	 * @return string
	 */
	private function get_unsupported_php_version_message() {
		$messages   = array();
		$messages[] = sprintf( $this->translate( 'Your PHP version is %s.' ), phpversion() );
		$messages[] = $this->translate( 'Please update your PHP.' );
		$messages[] = sprintf( $this->translate( '<strong>%s</strong> requires PHP version %s or above.' ), $this->translate( $this->original_plugin_name ), TECHNOTE_REQUIRED_PHP_VERSION );

		return implode( '<br>', $messages );
	}

	/**
	 * @since 1.2.0
	 * @return string
	 */
	private function get_unsupported_wp_version_message() {
		global $wp_version;
		$messages   = array();
		$messages[] = sprintf( $this->translate( 'Your WordPress version is %s.' ), $wp_version );
		$messages[] = $this->translate( 'Please update your WordPress.' );
		$messages[] = sprintf( $this->translate( '<strong>%s</strong> requires WordPress version %s or above.' ), $this->translate( $this->original_plugin_name ), TECHNOTE_REQUIRED_WP_VERSION );

		return implode( '<br>', $messages );
	}

	/**
	 * admin_notices
	 */
	public function admin_notices() {
		?>
        <div class="notice error notice-error">
			<?php if ( $this->is_not_enough_php_version() ): ?>
                <p><?php echo $this->get_unsupported_php_version_message(); ?></p>
			<?php endif; ?>
			<?php if ( $this->is_not_enough_wp_version() ): ?>
                <p><?php echo $this->get_unsupported_wp_version_message(); ?></p>
			<?php endif; ?>
        </div>
		<?php
	}
}

