<?php
/**
 * @version 1.1.0
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Interfaces\Presenter $instance */
/** @var array $args */
/** @var array $setting */
?>

<?php $instance->form( 'open', $args ); ?>
<div id="<?php $instance->id(); ?>-dashboard" class="wrap narrow">
    <div id="<?php $instance->id(); ?>-content-wrap">
        <table class="form-table">
			<?php foreach ( $setting as $k => $v ) : ?>
                <tr>
                    <th>
                        <label for="<?php $instance->h( $v['id'] ); ?>"><?php $instance->h( $v['label'] ); ?></label>
                    </th>
                    <td>
						<?php $instance->form( $v['form'], $args, $v ); ?>
                    </td>
                </tr>
			<?php endforeach; ?>
        </table>
        <div>
	        <?php $instance->form( 'input/submit', $args, [
		        'name'  => 'update',
		        'value' => 'Update',
		        'class' => 'button-primary left',
	        ] ); ?>
	        <?php $instance->form( 'input/button', $args, [
		        'name'  => 'reset',
		        'value' => 'Reset',
		        'class' => 'button-primary left',
	        ] ); ?>
        </div>
        <div class="marker-setting-preview">
            Hello, Dolly<br>
            <span>Well, hello, Dolly</span><br>
            It's so nice to have you back where you belong<br>
            You're lookin' swell, Dolly<br>
            I can tell, Dolly<br>
            <span>You're still glowin', you're still crowin'</span><br>
            You're still goin' strong<br>
            I feel the room swayin'<br>
            While the band's playin'<br>
            One of our old favorite songs from way back when<br>
            So, <span>take her wrap</span>, fellas<br>
            Dolly, never go away again<br>
            Hello, Dolly<br>
            Well, hello, Dolly<br>
            It's so nice to have you back where you belong<br>
            You're lookin' <span>swell, Dolly<br>
            I can tell, Dolly<br>
            You're still glowin', you're still crowin'</span><br>
            You're still goin' strong<br>
            I feel the room swayin'<br>
            While the band's playin'<br>
            One of our old favorite songs from way back when<br>
            So, <span>golly, gee, fellas</span><br>
            Have a little faith in me, fellas<br>
            Dolly, never go away<br>
            Promise, you'll never go away<br>
            Dolly'll <span>never go away again</span>
        </div>
    </div>
</div>
<?php $instance->form( 'close', $args ); ?>





