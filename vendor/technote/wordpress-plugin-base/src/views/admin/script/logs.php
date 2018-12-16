<?php
/**
 * Technote Views Admin Script Admin Logs
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
/** @var \Technote\Traits\Presenter $instance */
?>
<script>
    (function ($) {
        $('#<?php $instance->id();?>-delete_log').click(function () {
            return confirm('<?php $instance->h( 'Are you sure you want to delete this file?', true );?>');
        });
        $('#<?php $instance->id();?>-main-contents .close_button').click(function () {
            const ul = $('#<?php $instance->id();?>-main-contents .directory > ul');
            if ($(ul).hasClass('closed')) {
                $(ul).removeClass('closed').slideDown().next('.close_button').val('<?php $instance->h( 'Close', true );?>');
            } else {
                $(ul).addClass('closed').slideUp().next('.close_button').val('<?php $instance->h( 'Open', true );?>');
            }
        });
    })(jQuery);
</script>