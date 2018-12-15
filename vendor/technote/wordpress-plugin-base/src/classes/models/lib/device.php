<?php
/**
 * Technote Classes Models Lib Device
 *
 * @version 2.3.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @since 2.3.0 Updated: comment
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Device
 * @package Technote\Classes\Models\Lib
 */
class Device implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook;

	/** @var bool */
	private $is_robot = null;

	/** @var \Mobile_Detect */
	private $mobile_detect;

	/**
	 * initialize
	 */
	protected function initialize() {
		$this->mobile_detect = new \Mobile_Detect();
	}

	/**
	 * @param bool $cache
	 *
	 * @return bool
	 */
	public function is_robot( $cache = true ) {
		if ( $cache && isset( $this->is_robot ) ) {
			return $this->is_robot;
		}

		$this->is_robot = $this->apply_filters( 'pre_check_bot', null );
		if ( is_bool( $this->is_robot ) ) {
			return $this->is_robot;
		}

		$bot_list = explode( ',', $this->apply_filters( 'bot_list', implode( ',', [
			'facebookexternalhit',
			'Googlebot',
			'Baiduspider',
			'bingbot',
			'Yeti',
			'NaverBot',
			'Yahoo! Slurp',
			'Y!J-BRI',
			'Y!J-BRJ/YATS crawler',
			'Tumblr',
			//		'livedoor',
			//		'Hatena',
			'Twitterbot',
			'Page Speed',
			'Google Web Preview',
			'msnbot/',
			'proodleBot',
			'psbot/',
			'ScSpider/',
			'TutorGigBot/',
			'YottaShopping_Bot/',
			'Faxobot/',
			'Gigabot/',
			'MJ12bot/',
			'Ask Jeeves/Teoma; ',
		] ) ) );

		$this->is_robot = false;
		$ua             = $this->app->input->user_agent();
		foreach ( $bot_list as $value ) {
			$value = trim( $value );
			if ( preg_match( '/' . str_replace( '/', '\\/', $value ) . '/i', $ua ) ) {
				$this->is_robot = true;
				break;
			}
		}

		return $this->is_robot;
	}

	/**
	 * @param null|string $ua
	 *
	 * @return bool
	 */
	public function is_tablet( $ua = null ) {
		return $this->mobile_detect->isTablet( $ua );
	}

	/**
	 * @param null|string $ua
	 *
	 * @return bool
	 */
	public function is_mobile( $ua = null ) {
		return $this->mobile_detect->isMobile( $ua );
	}

	/**
	 * @return \Mobile_Detect
	 */
	public function get_mobile_detect() {
		return $this->mobile_detect;
	}
}
