<?php
/**
 * Technote Views Admin Help Setting
 *
 * @version 2.9.12
 * @author technote-space
 * @since 1.0.0
 * @since 2.9.12 Changed: explain
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Interfaces\Presenter $instance */
/** @var string $prefix */
?>

<ol>
    <li>
        <h4>ヘルプの抑制設定の追加</h4>
        configs/config.php に以下の設定を追加します。
        <pre>'suppress_setting_help_contents' => true</pre>
    </li>
</ol>
