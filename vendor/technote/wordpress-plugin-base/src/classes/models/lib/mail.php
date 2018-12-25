<?php
/**
 * Technote Classes Models Lib Mail
 *
 * @version 2.9.0
 * @author technote-space
 * @since 2.9.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;

/**
 * Class Mail
 * @package Technote\Classes\Models
 */
class Mail implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook, \Technote\Interfaces\Presenter {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook, \Technote\Traits\Presenter;

	/** @var bool $_is_sending */
	private $_is_sending = false;

	/**
	 * @param string $to
	 * @param string $subject
	 * @param string|array $body
	 * @param string|false $text
	 *
	 * @return bool
	 */
	public function send( $to, $subject, $body, $text = false ) {
		if ( empty( $to ) || empty( $subject ) || ( empty( $body ) && empty( $text ) ) ) {
			return false;
		}

		$this->remove_special_space( $subject );
		$this->remove_special_space( $body );

		if ( ! is_array( $body ) ) {
			$body = [ 'text/html' => $body ];
		}
		if ( ! empty( $text ) ) {
			$this->remove_special_space( $text );
			$body['text/plain'] = $text;
		}

		$cssToInlineStyles = new CssToInlineStyles;
		$messages          = [];
		$content_type      = 'text/html';
		foreach ( $body as $type => $message ) {
			is_array( $message ) and $message = reset( $messages );
			if ( 'text/html' === $type ) {
				$message = $this->get_view( 'common/mail', [ 'subject' => $subject, 'body' => $message ] );
				$message = $cssToInlineStyles->convert( $message );
				$message = preg_replace( '/<\s*style.*?>[\s\S]*<\s*\/style\s*>/', '', $message );
			} elseif ( 'text/plain' !== $type ) {
				continue;
			}
			$messages[ $type ] = $message;
			$content_type      = $type;
		}

		if ( empty( $messages ) ) {
			return false;
		}
		if ( count( $messages ) > 1 ) {
			$content_type = 'multipart/alternative';
		}

		// このチケットがマージされたら以下の処理は不要
		// https://core.trac.wordpress.org/ticket/15448

		add_filter( 'wp_mail_content_type', $set_content_type = function () use ( &$set_content_type, $content_type ) {
			remove_filter( 'wp_mail_content_type', $set_content_type );

			return $content_type;
		} );

		add_action( 'phpmailer_init', $set_phpmailer = function ( $phpmailer ) use ( &$set_phpmailer, $messages ) {
			/** @var \PHPMailer $phpmailer */
			remove_action( 'phpmailer_init', $set_phpmailer );
			$phpmailer->Body    = '';
			$phpmailer->AltBody = '';
			foreach ( $messages as $type => $message ) {
				if ( 'text/html' === $type ) {
					$phpmailer->Body = $message;
				} elseif ( 'text/plain' === $type ) {
					$phpmailer->AltBody = $message;
				}
			}
		} );

		// suppress error
		if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
			$_SERVER['SERVER_NAME'] = '';
		}

		// is sending
		$this->_is_sending = true;

		$result = wp_mail( $to, $subject, reset( $messages ) );

		$this->_is_sending = false;

		return $result;
	}

	/**
	 * @param $str string|array
	 */
	private function remove_special_space( &$str ) {
		if ( is_array( $str ) ) {
			foreach ( $str as $k => $v ) {
				$this->remove_special_space( $str[ $k ] );
			}
		} else {
			$specialSpace = [
				"\xC2\xA0",
				"\xE1\xA0\x8E",
				"\xE2\x80\x80",
				"\xE2\x80\x81",
				"\xE2\x80\x82",
				"\xE2\x80\x83",
				"\xE2\x80\x84",
				"\xE2\x80\x85",
				"\xE2\x80\x86",
				"\xE2\x80\x87",
				"\xE2\x80\x88",
				"\xE2\x80\x89",
				"\xE2\x80\x8A",
				"\xE2\x80\x8B",
				"\xE2\x80\xAF",
				"\xE2\x81\x9F",
				"\xEF\xBB\xBF",
			];
			$str          = str_replace( $specialSpace, " ", $str );
		}
	}

	/**
	 * @param \WP_Error $wp_error
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function wp_mail_failed( $wp_error ) {
		if ( $this->_is_sending ) {
			$this->app->log( $wp_error );
		}
	}

	/**
	 * @param string $from_email
	 *
	 * @return string
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function wp_mail_from( $from_email ) {
		if ( $this->_is_sending ) {
			$value = $this->apply_filters( 'mail_from' );
			if ( ! empty( $value ) ) {
				return $value;
			}
		}

		return $from_email;
	}

	/**
	 * @param string $from_name
	 *
	 * @return string
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function wp_mail_from_name( $from_name ) {
		if ( $this->_is_sending ) {
			$value = $this->apply_filters( 'mail_from_name' );
			if ( ! empty( $value ) ) {
				return $value;
			}
		}

		return $from_name;
	}
}
