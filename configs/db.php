<?php
/**
 * @author Technote
 * @copyright Technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'MARKER_ANIMATION' ) ) {
	exit;
}

return [

	'setting' => [
		'columns' => [
			'post_id'                      => [
				'type'     => 'BIGINT(20)',
				'unsigned' => true,
				'null'     => false,
				'comment'  => 'post id',
			],
			'is_valid'                     => [
				'type'     => 'BIT(1)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 1,
				'comment'  => 'validity',
			],
			'color'                        => [
				'type'    => 'VARCHAR(32)',
				'default' => '',
				'comment' => 'color',
			],
			'thickness'                    => [
				'type'    => 'VARCHAR(32)',
				'default' => '',
				'comment' => 'thickness',
			],
			'duration'                     => [
				'type'    => 'VARCHAR(32)',
				'default' => '',
				'comment' => 'duration',
			],
			'delay'                        => [
				'type'    => 'VARCHAR(32)',
				'default' => '',
				'comment' => 'delay',
			],
			'timing_function'              => [
				'type'    => 'VARCHAR(32)',
				'default' => '',
				'comment' => 'function',
			],
			'is_font_bold'                 => [
				'type'     => 'BIT(1)',
				'unsigned' => true,
				'default'  => null,
				'comment'  => 'font bold',
			],
			'is_repeat'                    => [
				'type'     => 'BIT(1)',
				'unsigned' => true,
				'default'  => null,
				'comment'  => 'repeat',
			],
			'padding_bottom'               => [
				'type'    => 'VARCHAR(32)',
				'default' => '',
				'comment' => 'padding bottom',
			],
			'is_stripe'                    => [
				'type'     => 'BIT(1)',
				'unsigned' => true,
				'default'  => null,
				'comment'  => 'stripe',
			],
			'priority'                     => [
				'type'     => 'INT(11)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 10,
				'comment'  => 'priority',
			],
			'is_valid_button_block_editor' => [
				'type'     => 'BIT(1)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 1,
				'comment'  => 'is valid block editor button',
			],
			'selector'                     => [
				'type'    => 'VARCHAR(255)',
				'default' => '',
				'comment' => 'selector',
			],
		],
		'index'   => [
			'key'    => [
				'priority' => [ 'priority' ],
			],
			'unique' => [
				'uk_post_id' => [ 'post_id' ],
			],
		],
		'comment' => 'marker settings',
	],

];

