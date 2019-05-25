<?php
/**
 * @version 1.7.6
 * @author Technote
 * @since 1.0.0
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

use WP_Framework_Presenter\Interfaces\Presenter;

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	return;
}
/** @var Presenter $instance */
/** @var string $name_prefix */
/** @var string $id_prefix */
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

						let option_value = $( this ).val().replace( /^\s+|\s+$/g, '' );
						if ( $( this ).data( 'nullable' ) ) {
							if ( 'checkbox' === $( this ).attr( 'type' ) ) {
								if ( $( this ).prop( 'checked' ) ) {
									option_value = 1;
								} else {
									option_value = 0;
								}
							} else if ( option_value === '' ) {
								option_value = $( this ).data( 'default' );
							}

							const _option_value = $( this ).data( 'option_value-' + option_value );
							if ( undefined !== _option_value ) {
								option_value = _option_value;
							}
						} else if ( option_value === '' ) {
							option_value = $( this ).data( 'default' );
						}
						if ( 'text' !== $( this ).attr( 'type' ) && /^-?\d+$/.test( option_value ) ) {
							option_value = option_value - 0;
						}
						options[ option_name ] = option_value;

						if ( 'stripe' === option_name ) {
							const readonly = option_value && $( this ).val() !== '';
							[ 'duration', 'delay', 'function', 'repeat' ].forEach( function( target ) {
								$( '#<?php $instance->h( $id_prefix );?>' + target ).prop( 'readonly', readonly ).attr( 'data-readonly', readonly ? 1 : '' );
							} );
						}
					}
				} );
				$( '<?php $instance->h( $marker_target_selector );?>' ).markerAnimation( options );
			};

			$target.on( 'click', function() {
				return ! $( this ).attr( 'data-readonly' );
			} ).on( 'change <?php $instance->h( $instance->app->slug_name . '-' );?>cleared', function() {
				setup_options();
			} );
			setup_options();
		} );
	} )( jQuery );
</script>
