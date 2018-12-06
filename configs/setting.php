<?php
/**
 * @version 1.1.6
 * @author technote-space
 * @since 1.0.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

return [

	9 => [
		'Marker Animation' => [
			10 => [
				'is_valid'        => [
					'label'   => 'validity',
					'type'    => 'bool',
					'default' => true,
				],
				'selector'        => [
					'label'   => 'selector (other than [.marker-animation])',
					'default' => '',
				],
				'color'           => [
					'label'   => 'color',
					'default' => '#ffff66',
				],
				'thickness'       => [
					'label'   => 'thickness',
					'default' => '.6em',
				],
				'duration'        => [
					'label'   => 'duration',
					'default' => '2s',
				],
				'delay'           => [
					'label'   => 'delay',
					'default' => '.1s',
				],
				'function'        => [
					'label'   => 'function',
					'default' => 'ease',
				],
				'bold'            => [
					'label'   => 'font bold',
					'type'    => 'bool',
					'default' => true,
				],
				'repeat'          => [
					'label'   => 'repeat',
					'type'    => 'bool',
					'default' => false,
				],
				'padding_bottom'  => [
					'label'   => 'padding bottom',
					'default' => '.6em',
				],
			],
		],
	],

];