<?php
/**
 * Technote Traits Cron
 *
 * @version 1.1.62
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Traits;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Trait Cron
 * @package Technote\Traits
 * @property \Technote $app
 */
trait Cron {

	use Singleton, Hook, Uninstall;

	/**
	 * initialize
	 */
	protected final function initialize() {
		add_action( $this->get_hook_name(), function () {
			$this->run();
		} );
		$this->set_event();
	}

	/**
	 * set event
	 */
	private function set_event() {
		$interval = $this->get_interval();
		if ( $interval > 0 ) {
			if ( ! wp_next_scheduled( $this->get_hook_name() ) ) {
				if ( $this->is_process_running() ) {
					return;
				}
				wp_schedule_single_event( time() + $interval, $this->get_hook_name() );
			}
		}
	}

	/**
	 * @return bool
	 */
	private function is_process_running() {
		if ( get_site_transient( $this->get_transient_key() ) ) {
			return true;
		}

		return false;
	}

	/**
	 * lock
	 */
	private function lock_process() {
		set_site_transient( $this->get_transient_key(), microtime(), $this->apply_filters( 'cron_process_expire', $this->get_expire(), $this->get_hook_name() ) );
	}

	/**
	 * unlock
	 */
	private function unlock_process() {
		delete_site_transient( $this->get_transient_key() );
	}

	/**
	 * @return int
	 */
	protected function get_interval() {
		return - 1;
	}

	/**
	 * @return int
	 */
	protected function get_expire() {
		return 10 * MINUTE_IN_SECONDS;
	}

	/**
	 * @return string
	 */
	protected function get_hook_prefix() {
		return $this->app->slug_name . '-';
	}

	/**
	 * @return string
	 */
	protected function get_hook_name() {
		return $this->get_hook_prefix() . $this->get_file_slug();
	}

	/**
	 * @return string
	 */
	protected function get_transient_key() {
		return $this->get_hook_name() . '-transient';
	}

	/**
	 * clear event
	 */
	protected function clear_event() {
		wp_clear_scheduled_hook( $this->get_hook_name() );
	}

	/**
	 * run
	 */
	public final function run() {
		if ( $this->is_process_running() ) {
			return;
		}
		set_time_limit( 0 );
		$this->lock_process();
		$this->do_action( 'before_cron_run', $this->get_hook_name() );
		$this->execute();
		$this->do_action( 'after_cron_run', $this->get_hook_name() );
		$this->set_event();
		$this->unlock_process();
	}

	/**
	 * run now
	 */
	public final function run_now() {
		$this->clear_event();
		$this->run();
	}

	/**
	 * execute
	 */
	protected function execute() {

	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		$this->clear_event();
		$this->unlock_process();
	}

}
