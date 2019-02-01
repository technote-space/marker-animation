<?php
/**
 * @version 1.5.0
 * @author technote-space
 * @since 1.0.0
 * @since 1.3.0 Added: preset color
 * @since 1.4.0 Deleted: preset color
 * @since 1.5.0 Changed: trivial change
 * @copyright technote-space All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var string $name_prefix */
?>

<script>
    (function ($) {
        const $target = $('#<?php $instance->id(); ?>-content-wrap .marker-animation-option');
        const setup_options = function () {
            const options = {};
            $target.each(function () {
                const name = $(this).attr('name');
                if (name && name.match(/^<?php $instance->h( preg_quote( $name_prefix, '/' ) );?>/)) {
                    let option_name = name.replace(/^<?php $instance->h( preg_quote( $name_prefix, '/' ) );?>/, '');
                    let option_value = $(this).val();
                    if ('checkbox' === $(this).attr('type')) {
                        const _option_value_true = $(this).data('option_value-true'), _option_value_false = $(this).data('option_value-false');
                        if ($(this).prop('checked')) {
                            if (undefined === _option_value_true) {
                                option_value = 1;
                            } else {
                                option_value = _option_value_true;
                            }
                        } else {
                            if (undefined === _option_value_false) {
                                option_value = 0;
                            } else {
                                option_value = _option_value_false;
                            }
                        }
                    }
                    const _option_name = $(this).data('option_name');
                    if (_option_name) option_name = _option_name;
                    if (option_value === '') option_value = $(this).data('default');
                    options[option_name] = option_value;
                }
            });
            $('.marker-setting-preview span').markerAnimation(options);
        };
        const reset_options = function () {
            $target.each(function () {
                const name = $(this).attr('name');
                if (name && name.match(/^<?php $instance->h( preg_quote( $name_prefix, '/' ) );?>/)) {
                    let option_value = $(this).data('default');
                    if ('checkbox' === $(this).attr('type')) {
                        $(this).prop('checked', option_value);
                    } else {
                        $(this).val(option_value);
                        if ($(this).hasClass('<?php $instance->h( $instance->get_color_picker_class() );?>')) {
                            $(this).wpColorPicker('color', option_value);
                        }
                    }
                }
            });
            setup_options();
        };

        $target.on('change <?php $instance->h( $instance->app->slug_name . '-' );?>cleared', function () {
            setup_options();
        });
        $('[name="reset"]').on('click', function () {
            reset_options();
            return false;
        });
        setup_options();
    })(jQuery);
</script>
