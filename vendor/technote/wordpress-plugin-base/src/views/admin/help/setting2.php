<?php
/**
 * Technote Views Admin Help Setting
 *
 * @version 1.1.70
 * @author technote-space
 * @since 1.0.0
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
        <h4>ヘルプ表示設定</h4>
        $slug が setting の時に 空を返すようにします。
        functions.php に以下のコードを追加します。
        <pre>
add_filter( '<?php $instance->h( $prefix ); ?>get_help_contents', function ( $contents, $slug ) {
	if ( 'setting' === $slug ) {
		return [];
	}

	return $contents;
}, 10, 2 );</pre>
    </li>
</ol>
