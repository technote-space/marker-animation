<?php
/**
 * Technote Views Include Script Modal
 *
 * @version 1.1.71
 * @author technote-space
 * @since 1.1.68
 * @copyright technote All Rights Reserved
 * @license https://opensource.org/licenses/mit-license.html MIT License
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	return;
}
/** @var \Technote\Traits\Presenter $instance */
?>

<script>
    (function ($) {
        $(".<?php $instance->h( $instance->get_color_picker_class() );?>").each(function () {
            const $this = $(this);
            $this.wpColorPicker({
                defaultColor: false,
                change: function (event, ui) {
                    $this.val(ui.color.toString()).trigger('change');
                },
                clear: function () {
                    $this.trigger('<?php $instance->h( $instance->app->slug_name . '-' );?>cleared');
                },
                hide: true,
                palettes: true
            });
        });
    })(jQuery);
</script>
