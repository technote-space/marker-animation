<?php
/**
 * @version 1.4.0
 * @author technote-space
 * @since 1.4.0
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space/
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

return [

	'setting' => [
		'columns' => [
			'post_id'        => [
				'type'     => 'BIGINT(20)',
				'unsigned' => true,
				'null'     => false,
				'comment'  => 'post id',
			],
			'is_valid'       => [
				'type'     => 'TINYINT(1)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 1,
				'comment'  => 'validity',
			],
			'color'          => [
				'type'    => 'VARCHAR(32)',
				'default' => '#ffff66',
				'comment' => 'color',
			],
			'thickness'      => [
				'type'    => 'VARCHAR(32)',
				'default' => '.6em',
				'comment' => 'thickness',
			],
			'duration'       => [
				'type'    => 'VARCHAR(32)',
				'default' => '2s',
				'comment' => 'duration',
			],
			'delay'          => [
				'type'    => 'VARCHAR(32)',
				'default' => '.1s',
				'comment' => 'delay',
			],
			'function'       => [
				'type'    => 'VARCHAR(32)',
				'default' => 'ease',
				'comment' => 'function',
			],
			'is_font_bold'   => [
				'type'     => 'TINYINT(1)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 1,
				'comment'  => 'font bold',
			],
			'is_repeat'      => [
				'type'     => 'TINYINT(1)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 0,
				'comment'  => 'repeat',
			],
			'padding_bottom' => [
				'type'    => 'VARCHAR(32)',
				'default' => '.6em',
				'comment' => 'padding bottom',
			],
			'priority'       => [
				'type'     => 'INT(11)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 10,
				'comment'  => 'priority',
			],
			'is_button'      => [
				'type'     => 'TINYINT(1)',
				'unsigned' => true,
				'null'     => false,
				'default'  => 1,
				'comment'  => 'is button',
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

