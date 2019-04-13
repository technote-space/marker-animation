<?php
/**
 * @version 1.6.0
 * @author Technote
 * @since 1.4.0
 * @since 1.5.0 Changed: trivial change
 * @since 1.6.0 Changed: Gutenbergへの対応 (#3)
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
				'type'     => 'TINYINT(1)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 1,
				'comment'  => 'validity',
			],
			'color'                        => [
				'type'    => 'VARCHAR(32)',
				'default' => '#ffff66',
				'comment' => 'color',
			],
			'thickness'                    => [
				'type'    => 'VARCHAR(32)',
				'default' => '.6em',
				'comment' => 'thickness',
			],
			'duration'                     => [
				'type'    => 'VARCHAR(32)',
				'default' => '2s',
				'comment' => 'duration',
			],
			'delay'                        => [
				'type'    => 'VARCHAR(32)',
				'default' => '.1s',
				'comment' => 'delay',
			],
			'function'                     => [
				'type'    => 'VARCHAR(32)',
				'default' => 'ease',
				'comment' => 'function',
			],
			'is_font_bold'                 => [
				'type'     => 'TINYINT(1)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 1,
				'comment'  => 'font bold',
			],
			'is_repeat'                    => [
				'type'     => 'TINYINT(1)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 0,
				'comment'  => 'repeat',
			],
			'padding_bottom'               => [
				'type'    => 'VARCHAR(32)',
				'default' => '.6em',
				'comment' => 'padding bottom',
			],
			'priority'                     => [
				'type'     => 'INT(11)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 10,
				'comment'  => 'priority',
			],
			'is_valid_button'              => [
				'type'     => 'TINYINT(1)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 1,
				'comment'  => 'is valid button',
			],
			'is_valid_style'               => [
				'type'     => 'TINYINT(1)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 0,
				'comment'  => 'is valid style',
			],
			/**
			 * @since 1.6.0
			 */
			'is_valid_button_block_editor' => [
				'type'     => 'TINYINT(1)',
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

