<?php
/**
 * @version 1.7.4
 * @author Technote
 * @since 1.0.0
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	return;
}
/** @var \WP_Framework_Presenter\Interfaces\Presenter $instance */
/** @var string $name_prefix */
/** @var string $target_selector */
/** @var string $marker_target_selector */
?>

<script>
	( function( $ ) {
		$( function() {
			const $target = $( '<?php $instance->h( $target_selector );?>' );
			const setup_options = function() {
				const options = {};
				$target.each( function() {
					const name = $( this ).attr( 'name' );
					if ( name && name.match( /^<?php $instance->h( preg_quote( $name_prefix, '/' ) );?>/ ) ) {
						let option_name = name.replace( /^<?php $instance->h( preg_quote( $name_prefix, '/' ) );?>/, '' );
						const _option_name = $( this ).data( 'option_name' );
						if ( _option_name ) {
							option_name = _option_name;
						}

						let option_value = $( this ).val();
						if ( 'checkbox' === $( this ).attr( 'type' ) ) {
							const _option_value_true = $( this ).data( 'option_value-true' ), _option_value_false = $( this ).data( 'option_value-false' );
							if ( $( this ).prop( 'checked' ) ) {
								if ( undefined === _option_value_true ) {
									option_value = 1;
								} else {
									option_value = _option_value_true;
								}
							} else {
								if ( undefined === _option_value_false ) {
									option_value = 0;
								} else {
									option_value = _option_value_false;
								}
							}
						} else if ( option_value === '' ) {
							option_value = $( this ).data( 'default' );
						}
						options[ option_name ] = option_value;
					}
				} );
				$( '<?php $instance->h( $marker_target_selector );?>' ).markerAnimation( options );
			};

			$target.on( 'change <?php $instance->h( $instance->app->slug_name . '-' );?>cleared', function() {
				setup_options();
			} );
			setup_options();
		} );
	} )( jQuery );
</script>
