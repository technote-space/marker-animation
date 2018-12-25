<?php
/**
 * Technote Configs Db
 *
 * @version 2.9.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.7.0 Added: __log table
 * @since 2.9.0 Added: level column to __log
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

return [

	// example
//	'test' => array(
//		'id'      => 'test_id',     // optional [default = $table_name . '_id']
//		'columns' => array(
//			'name'   => array(
//				'name'     => 'name',          // optional
//				'type'     => 'VARCHAR(32)',   // required
//				'unsigned' => false,          // optional [default = false]
//				'null'     => true,           // optional [default = true]
//				'default'  => null,           // optional [default = null]
//				'comment'  => '',             // optional
//			),
//			'value1' => array(
//				'type'    => 'VARCHAR(32)',
//				'null'    => false,
//				'default' => 'test',
//			),
//			'value2' => array(
//				'type'    => 'VARCHAR(32)',
//				'comment' => 'aaaa',
//			),
//			'value3' => array(
//				'type'    => 'INT(11)',
//				'null'    => false,
//				'comment' => 'bbb',
//			),
//		),
//		'index'   => array(
//			'key'    => array( // key index
//				'name' => array( 'name' ),
//			),
//			'unique' => array( // unique index
//				'value' => array( 'value1', 'value2' ),
//			),
//		),
//		'delete'  => 'logical', // physical or logical [default = physical]
//	),

	/**
	 * @since 2.7.0
	 */
	'__log' => [
		'columns' => [
			/**
			 * @since 2.9.0
			 */
			'level'          => [
				'type' => 'VARCHAR(32)',
				'null' => false,
			],
			'message'        => [
				'type' => 'TEXT',
				'null' => false,
			],
			'context'        => [
				'type' => 'LONGTEXT',
				'null' => true,
			],
			'file'           => [
				'type' => 'VARCHAR(255)',
				'null' => true,
			],
			'line'           => [
				'type'     => 'INT(11)',
				'unsigned' => true,
				'null'     => true,
			],
			'lib_version'    => [
				'type' => 'VARCHAR(32)',
				'null' => false,
			],
			'plugin_version' => [
				'type' => 'VARCHAR(32)',
				'null' => false,
			],
		],
		'index'   => [
			'key' => [
				'created_at' => [ 'created_at' ],
			],
		],
	],

];
