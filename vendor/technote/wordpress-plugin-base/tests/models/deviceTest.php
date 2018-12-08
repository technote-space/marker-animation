<?php
/**
 * Technote Models Device Test
 *
 * @version 2.0.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Tests\Models;

/**
 * Class DeviceTest
 * @package Technote\Tests\Models
 * @group technote
 * @group models
 */
class DeviceTest extends \Technote\Tests\TestCase {

	/** @var \Technote\Classes\Models\Lib\Device $device */
	private static $device;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		static::$device = \Technote\Classes\Models\Lib\Device::get_instance( static::$app );
	}

	/**
	 * @dataProvider _test_is_bot_provider
	 *
	 * @param string $ua
	 * @param bool $is_robot
	 * @param bool $is_tablet
	 * @param bool $is_mobile
	 */
	public function test_device( $ua, $is_robot, $is_tablet, $is_mobile ) {
		$_SERVER['HTTP_USER_AGENT'] = $ua;
		$this->assertEquals( $is_robot, static::$device->is_robot( false ) );
		$this->assertEquals( $is_tablet, static::$device->is_tablet( $ua ) );
		$this->assertEquals( $is_mobile, static::$device->is_mobile( $ua ) );
	}

	/**
	 * @return array
	 * @link http://www.openspc2.org/userAgent/
	 */
	public function _test_is_bot_provider() {
		return [
			// iPhone
			[
				'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1C28 Safari/419.3',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (iPhone; U; CPU iPhone OS 2_0 like Mac OS X; ja-jp) AppleWebKit/525.18.1 (KHTML, like Gecko) Version/3.1.1 Mobile/5A347 Safari/52',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (iPhone; U; CPU iPhone OS 3_0 like Mac OS X; en-us) AppleWebKit/528.18 (KHTML, like Gecko) Version/4.0 Mobile/7A341 Safari/528.16',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_0_1 like Mac OS X; ja-jp) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8A306 Safari/6531.22.7',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (iPhone; U; CPU iPhone OS 5_0 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Mobile/9A334 Safari/7534.48.3',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (iPhone; CPU iPhone OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A403 Safari/8536.25',
				false,
				false,
				true,
			],

			// iPod
			[
				'Mozilla/5.0 (iPod; U; CPU like Mac OS X; en) AppleWebKit/420.1 (KHTML, like Gecko) Version/3.0 Mobile/3A100a Safari/419.3',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (iPod; U; CPU iPhone OS 4_1 like Mac OS X; ja-jp) AppleWebKit/532.9 (KHTML, like Gecko) Version/4.0.5 Mobile/8B118 Safari/6531.22.7',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (iPod; CPU iPhone OS 5_0_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A405 Safari/7534.48.3',
				false,
				false,
				true,
			],

			// iPad
			[
				'Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B367 Safari/531.21.10',
				false,
				true,
				true,
			],
			[
				'Mozilla/5.0 (iPad; U; CPU OS 4_2 like Mac OS X; zh-cn) AppleWebKit/533.17.9 (KHTML, like Gecko) Mobile/8C134',
				false,
				true,
				true,
			],
			[
				'Mozilla/5.0 (iPad; CPU OS 5_0_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9A405 Safari/7534.48.3',
				false,
				true,
				true,
			],
			[
				'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A403 Safari/8536.25',
				false,
				true,
				true,
			],

			// Android
			[
				'Mozilla/5.0 (Linux; U; Android 1.5; ja-jp; GDDJ-09 Build/CDB56) AppleWebKit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (Linux; U; Android 1.6; ja-jp; IS01 Build/S3082) AppleWebKit/528.5+ (KHTML, like Gecko) Version/3.1.2 Mobile Safari/525.20.1',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (Linux; U; Android 2.1-update1; ja-jp; SonyEricssonSO-01B Build/2.0.2.B.0.29) AppleWebKit/530.17 (KHTML, like Gecko) Version/4.0 Mobile Safari/530.17',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (Linux; U; Android 3.1; en-us; K1 Build/HMJ37) AppleWebKit/534.13(KHTML, like Gecko) Version/4.0 Safari/534.13',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (Linux; U; Android 4.0.1; ja-jp; Galaxy Nexus Build/ITL41D) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30',
				false,
				false,
				true,
			],
			[
				'Opera/9.80 (Android 2.3.3; Linux; Opera Mobi/ADR-1111101157; U; ja) Presto/2.9.201 Version/11.50',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (Android; Linux armv7l; rv:9.0) Gecko/20111216 Firefox/9.0 Fennec/9.0',
				false,
				false,
				true,
			],

			// Windows Phone
			[
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; KDDI-TS01; Windows Phone 6.5.3.5)',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (compatible; MSIE 9.0; Windows Phone OS 7.5; Trident/5.0; IEMobile/9.0; FujitsuToshibaMobileCommun; IS12T; KDDI)',
				false,
				false,
				true,
			],

			// BlackBeryy
			[
				'BlackBerry9000/4.6.0.294 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/220',
				false,
				false,
				true,
			],
			[
				'BlackBerry9300/5.0.0.1007 Profile/MIDP-2.1 Configuration/CLDC-1.1 VendorID/220',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (BlackBerry; U; BlackBerry 9700; ja) AppleWebKit/534.8+ (KHTML, like Gecko) Version/6.0.0.570 Mobile Safari/534.8+',
				false,
				false,
				true,
			],
			[
				'Opera/9.80 (BlackBerry; Opera Mini/6.1.25376/26.958; U; en) Presto/2.8.119 Version/10.54',
				false,
				false,
				true,
			],

			// Symbian
			[
				'Mozilla/5.0 (Symbian/3; Series60/5.2 NokiaN8-00/013.016; Profile/MIDP-2.1 Configuration/CLDC-1.1 ) AppleWebKit/525 (KHTML, like Gecko) Version/3.0 BrowserNG/7.2.8.10 3gpp-gba',
				false,
				false,
				true,
			],
			[
				'Nokia6600/1.0 (4.03.24) SymbianOS/6.1 Series60/2.0 Profile/MIDP-2.0 Configuration/CLDC-1.0',
				false,
				false,
				true,
			],
			[
				'Mozilla/5.0 (SymbianOS/9.2; U; Series60/3.1 NokiaN95/10.0.018; Profile/MIDP-2.0 Configuration/CLDC-1.1) AppleWebKit/413 (KHTML, like Gecko) Safari/413',
				false,
				false,
				true,
			],

			// Internet Explorer
			[
				'Mozilla/4.0 (compatible; MSIE 4.0; MSN 2.5; Windows 95)',
				false,
				false,
				false,
			],
			[
				'Mozilla/4.0 (compatible; MSIE 4.5; Mac_PowerPC)',
				false,
				false,
				false,
			],
			[
				'Mozilla/4.0 (compatible; MSIE 5.0; MSN 2.5; Windows 98)',
				false,
				false,
				false,
			],
			[
				'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)',
				false,
				false,
				false,
			],
			[
				'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)',
				false,
				false,
				false,
			],
			[
				'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; InfoPath.1',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Windows NT 6.3; WOW64; Trident/7.0; Touch; rv:11.0) like Gecko',
				false,
				false,
				false,
			],

			// Google Chrome
			[
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/14.0.835.202 Safari/535.1',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/535.7 (KHTML, like Gecko) Chrome/16.0.912.75 Safari/535.7',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/535.19 (KHTML, like Gecko) Chrome/18.0.1025.1634 Safari/535.19 YE',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.4 (KHTML, like Gecko) Chrome/22.0.1229.94 Safari/537.4 Sleipnir/3.8.4',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.97 Safari/537.22',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/27.0.1453.116 Safari/537.36',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/28.0.1500.52 Safari/537.36',
				false,
				false,
				false,
			],

			// Firefox
			[
				'Mozilla/5.0 (Windows; U; Windows NT 5.0; de-DE; rv:1.7.5) Gecko/20041108 Firefox/1.0',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (X11; U; Linux i686; ja-JP; rv:1.7.5) Gecko/20041108 Firefox/1.0',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.6) Gecko/20050223 Firefox/1.0.1',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Windows; U; Windows NT 5.1; ja; rv:1.8.1.20) Gecko/20081217 Firefox/2.0.0.20',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:5.0) Gecko/20100101 Firefox/5.0',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:7.0.1) Gecko/20100101 Firefox/7.0.1',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:9.0.1) Gecko/20100101 Firefox/9.0.1',
				false,
				false,
				false,
			],

			// Safari
			[
				'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; ja-jp) AppleWebKit/522.11.1 (KHTML, like Gecko) Version/3.0.3 Safari/522.12.1',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Macintosh; U; PPC Mac OS X 10_4_11; ja-jp) AppleWebKit/525.13 (KHTML, like Gecko) Version/3.1 Safari/525.13',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_6; ja-jp) AppleWebKit/525.27.1 (KHTML, like Gecko) Version/3.2.1 Safari/525.27.1',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_6_2; ja-jp) AppleWebKit/531.21.8 (KHTML, like Gecko) Version/4.0.4 Safari/531.21.10',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/534.52.7 (KHTML, like Gecko) Version/5.1.2 Safari/534.52.7',
				false,
				false,
				false,
			],
			[
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8) AppleWebKit/536.25 (KHTML, like Gecko) Version/6.0 Safari/536.25',
				false,
				false,
				false,
			],

			// robot
			[
				'Googlebot/2.1 (+http://www.google.com/bot.html)',
				true,
				false,
				false,
			],
			[
				'Mozilla/5.0 (compatible; Yahoo! Slurp; http://help.yahoo.com/help/us/ysearch/slurp)',
				true,
				false,
				false,
			],
			[
				'msnbot/0.11 (+http://search.msn.com/msnbot.htm)',
				true,
				false,
				false,
			],
			[
				'msnbot/0.3 (+http://search.msn.com/msnbot.htm)',
				true,
				false,
				false,
			],
			[
				'msnbot/1.0 (+http://search.msn.com/msnbot.htm)',
				true,
				false,
				false,
			],
			[
				'proodleBot (www.proodle.com)',
				true,
				false,
				false,
			],
			[
				'psbot/0.1 (+http://www.picsearch.com/bot.html)',
				true,
				false,
				false,
			],
			[
				'ScSpider/0.2',
				true,
				false,
				false,
			],
			[
				'TutorGigBot/1.5 ( +http://www.tutorgig.info )',
				true,
				false,
				false,
			],
			[
				'YottaShopping_Bot/4.12 (+http://www.yottashopping.com) Shopping Search Engine',
				true,
				false,
				false,
			],
			[
				'Faxobot/1.0',
				true,
				false,
				false,
			],
			[
				'Gigabot/2.0',
				true,
				false,
				false,
			],
			[
				'MJ12bot/v0.8.7 (http://www.majestic12.co.uk/projects/dsearch/mj12bot.php?V=v0.8.7&NID=B0E44C4EE98B33C4&MID=EE1DD60ABC2AE863&BID=4B63485ECF966068726CCEAA8B8D2509&+)',
				true,
				false,
				false,
			],
			[
				'Mozilla/2.0 (compatible; Ask Jeeves/Teoma; +http://about.ask.com/en/docs/about/webmasters.shtml)',
				true,
				false,
				false,
			],
		];
	}

}