<?php
/**
 * Technote mock
 *
 * @version 1.1.13
 * @author technote-space
 * @since 1.0.0
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
 * @property string $text_domain
 */
class Technote {

	/** @var array */
	private static $instances = array();

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
	/** @var string $text_domain */
	public $text_domain;


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

		if ( version_compare( phpversion(), TECHNOTE_REQUIRED_PHP_VERSION, '<' ) ) {
			$config            = $this->load_config_file();
			$this->text_domain = 'default';
			if ( ! empty( $config['text_domain'] ) ) {
				$this->text_domain = $config['text_domain'];
			}
			$this->set_unsupported();
		}
	}

	/**
	 * @param string $plugin_name
	 * @param string $plugin_file
	 *
	 * @return Technote
	 */
	public static function get_instance( $plugin_name, $plugin_file ) {
		if ( ! isset( static::$instances[ $plugin_name ] ) ) {
			static::$instances[ $plugin_name ] = new static( $plugin_name, $plugin_file );
		}

		return static::$instances[ $plugin_name ];
	}

	/**
	 * @return array|mixed
	 */
	private function load_config_file() {
		$path = $this->plugin_configs_dir . DS . 'config.php';
		if ( ! file_exists( $path ) ) {
			return array();
		}
		/** @noinspection PhpIncludeInspection */
		$config = include $path;
		if ( ! is_array( $config ) ) {
			$config = array();
		}

		return $config;
	}

	/**
	 * set unsupported
	 */
	private function set_unsupported() {
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * @return string
	 */
	private function get_unsupported_message() {
		$messages   = array();
		$messages[] = sprintf( __( 'Your PHP version is %s.', $this->text_domain ), phpversion() );
		$messages[] = __( 'Please update your PHP.', $this->text_domain );
		$messages[] = sprintf( __( '<strong>%s</strong> requires PHP version %s or above.', $this->text_domain ), $this->original_plugin_name, TECHNOTE_REQUIRED_PHP_VERSION );

		return implode( '<br>', $messages );
	}

	/**
	 * admin_notices
	 */
	public function admin_notices() {
		?>
        <div class="notice error notice-error">
            <p><?php echo $this->get_unsupported_message(); ?></p>
        </div>
		<?php
	}
}

