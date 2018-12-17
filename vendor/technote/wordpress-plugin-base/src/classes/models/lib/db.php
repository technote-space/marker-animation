<?php
/**
 * Technote Classes Models Lib Db
 *
 * @version 2.6.0
 * @author technote-space
 * @since 1.0.0
 * @since 2.0.0 Added: Feature to cache result of conversion type format
 * @since 2.0.0 Changed: set_update_params function
 * @since 2.0.0 Changed: Arguments of select functions
 * @since 2.0.0 Fixed: setup_wp_table_defines function
 * @since 2.0.0 Changed: default db version
 * @since 2.0.2 Added: Uninstall priority
 * @since 2.6.0 Changed: move doing_ajax method to utility
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Classes\Models\Lib;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Class Db
 * @package Technote\Classes\Models\Lib
 */
class Db implements \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook, \Technote\Interfaces\Uninstall {

	use \Technote\Traits\Singleton, \Technote\Traits\Hook, \Technote\Traits\Uninstall;

	/** @var array */
	protected $table_defines = null;

	/**
	 * @since 2.0.0
	 * @var array $_type2format
	 */
	private $_type2format = [];

	/**
	 * initialize
	 */
	protected function initialize() {
		$this->load_table_defines();
		$this->db_update();
		$this->setup_wp_table_defines();
	}

	/**
	 * @since 2.0.0 Added: Feature to cache result of conversion type format
	 *
	 * @param string $type
	 *
	 * @return string
	 */
	private function type2format( $type ) {
		if ( ! isset( $this->_type2format[ $type ] ) ) {
			$format = '%s';
			switch ( true ) {
				case stristr( $type, 'INT' ) !== false:
					$format = '%d';
					break;
				case stristr( $type, 'BIT' ) !== false:
					$format = '%d';
					break;
				case stristr( $type, 'BOOLEAN' ) !== false:
					$format = '%d';
					break;
				case stristr( $type, 'DECIMAL' ) !== false:
					$format = '%f';
					break;
				case stristr( $type, 'FLOAT' ) !== false:
					$format = '%f';
					break;
				case stristr( $type, 'DOUBLE' ) !== false:
					$format = '%f';
					break;
				case stristr( $type, 'REAL' ) !== false:
					$format = '%f';
					break;
			}
			$this->_type2format[ $type ] = $this->apply_filters( 'type2format', $format, $type );
		}

		return $this->_type2format[ $type ];
	}

	/**
	 * load
	 */
	private function load_table_defines() {
		if ( ! $this->need_to_update() ) {
			$cache = $this->app->get_option( 'table_defines_cache' );
			if ( is_array( $cache ) ) {
				$this->table_defines = $cache;

				return;
			}
		}

		$this->table_defines = $this->app->config->load( 'db' );
		empty( $this->table_defines ) and $this->table_defines = [];

		foreach ( $this->table_defines as $table => $define ) {
			list( $id, $columns ) = $this->setup_table_columns( $table, $define );
			if ( empty( $id ) ) {
				continue;
			}
			$this->table_defines[ $table ]['id']       = $id;
			$this->table_defines[ $table ]['columns']  = $columns;
			$this->table_defines[ $table ]['is_added'] = true;
		}
		$this->app->option->set( 'table_defines_cache', $this->table_defines );
	}

	/**
	 * switch blog
	 */
	/** @noinspection PhpUnusedPrivateMethodInspection */
	private function switch_blog() {
		foreach ( $this->table_defines as $table => $table_define ) {
			if ( ! empty( $table_define['wordpress'] ) ) {
				unset( $this->table_defines[ $table ] );
			}
		}
		$this->setup_wp_table_defines();
	}

	/**
	 * @since 2.0.0 Fixed: consider multisite
	 * for wp table
	 */
	private function setup_wp_table_defines() {
		/** @var \wpdb $wpdb */
		global $wpdb, $wp_version;
		$current_blog_id = get_current_blog_id();
		$tables          = $this->apply_filters( 'allowed_wp_tables', [
			$wpdb->posts    => $wpdb->posts,
			$wpdb->postmeta => $wpdb->postmeta,
			$wpdb->users    => $wpdb->users,
			$wpdb->usermeta => $wpdb->usermeta,
			$wpdb->options  => $wpdb->options,
		], $current_blog_id );

		$changed       = false;
		$cache         = $this->app->get_option( 'wp_table_defines_cache', [] );
		$cache_version = $this->app->get_option( 'wp_table_defines_cache_version' );
		if ( ! empty( $wp_version ) && $cache_version != $wp_version ) {
			$this->app->option->set( 'wp_table_defines_cache_version', $wp_version );
			$cache   = [];
			$changed = true;
		}
		foreach ( $tables as $table ) {
			if ( isset( $cache[ $table ] ) ) {
				$table_define = $cache[ $table ];
			} else {
				$changed      = true;
				$sql          = "DESCRIBE $table";
				$columns      = $wpdb->get_results( $sql, ARRAY_A );
				$table_define = [];
				foreach ( $columns as $column ) {
					$name = $column['Field'];
					$key  = $name;
					if ( isset( $column['Key'] ) && $column['Key'] === 'PRI' ) {
						$key                = 'id';
						$table_define['id'] = $name;
					}
					$type     = explode( ' ', $column['Type'] );
					$unsigned = in_array( 'unsigned', $type );
					$type     = reset( $type );
					$null     = $column['Null'] != 'NO';

					$table_define['columns'][ $key ] = [
						'name'     => $name,
						'type'     => $type,
						'format'   => $this->type2format( $type ),
						'unsigned' => $unsigned,
						'null'     => $null,
					];
				}
				$table_define['delete']    = 'physical';
				$table_define['wordpress'] = true;
				$cache[ $table ]           = $table_define;
			}
			$this->table_defines[ $table ] = $table_define;
		}
		if ( $changed ) {
			$this->app->option->set( 'wp_table_defines_cache', $cache );
		}
	}

	/**
	 * @param string $table
	 * @param array $define
	 *
	 * @return array
	 */
	protected function setup_table_columns( $table, $define ) {
		if ( empty( $define['columns'] ) ) {
			return [ false, false ];
		}

		$id = $table . '_id';
		if ( ! empty( $define['id'] ) ) {
			$id = $define['id'];
		}

		$columns       = [];
		$columns['id'] = [
			'name'     => $id,
			'type'     => 'bigint(20)',
			'unsigned' => true,
			'null'     => false,
			'format'   => '%d',
		];

		$check = true;
		foreach ( $define['columns'] as $key => $column ) {
			if ( ! is_array( $column ) ) {
				$check = false;
				break;
			}
			$type = $this->app->utility->array_get( $column, 'type' );
			if ( empty( $type ) ) {
				$check = false;
				break;
			}

			$column['name']   = $this->app->utility->array_get( $column, 'name', $key );
			$column['format'] = $this->app->utility->array_get( $column, 'format', $this->type2format( $type ) );
			$columns[ $key ]  = $column;
		}
		if ( ! $check ) {
			return [ false, false ];
		}

		$columns['created_at'] = [
			'name'       => 'created_at',
			'type'       => 'datetime',
			'null'       => false,
			'format'     => '%s',
			'only_admin' => true,
		];
		$columns['created_by'] = [
			'name'       => 'created_by',
			'type'       => 'varchar(32)',
			'null'       => false,
			'format'     => '%s',
			'only_admin' => true,
		];
		$columns['updated_at'] = [
			'name'       => 'updated_at',
			'type'       => 'datetime',
			'null'       => false,
			'format'     => '%s',
			'only_admin' => true,
		];
		$columns['updated_by'] = [
			'name'       => 'updated_by',
			'type'       => 'varchar(32)',
			'null'       => false,
			'format'     => '%s',
			'only_admin' => true,
		];

		if ( $this->is_logical( $define ) ) {
			$columns['deleted_at'] = [
				'name'       => 'deleted_at',
				'type'       => 'datetime',
				'format'     => '%s',
				'only_admin' => true,
			];
			$columns['deleted_by'] = [
				'name'       => 'deleted_by',
				'type'       => 'varchar(32)',
				'format'     => '%s',
				'only_admin' => true,
			];
		}

		return $this->apply_filters( 'setup_table_columns', [ $id, $columns ], $table, $define, $id, $columns );
	}

	/**
	 * @return string
	 */
	private function get_table_prefix() {
		global $table_prefix;

		return $table_prefix . $this->get_slug( 'table_prefix', '_' );
	}

	/**
	 * @param $table
	 *
	 * @return string
	 */
	public function get_table( $table ) {
		if (
			! isset( $this->table_defines[ $table ] ) ||
			! empty( $this->table_defines[ $table ]['wordpress'] ) ||
			! empty( $this->table_defines[ $table ]['global'] )
		) {
			return $table;
		}

		return $this->get_table_prefix() . $table;
	}

	/**
	 * @param string $table
	 * @param string $key
	 *
	 * @return string
	 */
	public function get_field( $table, $key ) {
		return $this->app->utility->array_get( $this->app->utility->array_get( $this->get_columns( $table ), $key, [] ), 'name', $key );
	}

	/**
	 * @param $table
	 *
	 * @return array
	 */
	public function get_columns( $table ) {
		if ( ! isset( $this->table_defines[ $table ]['columns'] ) ) {
			return [];
		}

		return $this->table_defines[ $table ]['columns'];
	}

	/**
	 * db update
	 */
	private function db_update() {
		if ( ! $this->need_to_update() ) {
			return;
		}
		$this->update_db_version();

		if ( empty( $this->table_defines ) ) {
			return;
		}

		set_time_limit( 60 * 5 );

		foreach ( $this->table_defines as $table => $define ) {
			$results = $this->table_update( $table, $define );
			if ( $results ) {
				$message = implode( '<br>', array_filter( $results, function ( $d ) {
					return ! empty( $d );
				} ) );
				if ( $message ) {
					$this->app->add_message( $message, 'db' );
				}
			}
		}

		$this->do_action( 'db_updated' );
	}

	/**
	 * @param string $table
	 * @param array $define
	 *
	 * @return array
	 */
	protected function table_update( $table, $define ) {
		require_once ABSPATH . "wp-admin" . DS . "includes" . DS . "upgrade.php";
		$char = defined( "DB_CHARSET" ) ? DB_CHARSET : "utf8";
		if ( empty( $define['id'] ) ) {
			$define['id'] = $table . '_id';
		}

		$table = $this->get_table( $table );
		$sql   = "CREATE TABLE {$table} (\n";
		foreach ( $define['columns'] as $key => $column ) {
			$name     = $this->app->utility->array_get( $column, 'name' );
			$type     = $this->app->utility->array_get( $column, 'type' );
			$unsigned = $this->app->utility->array_get( $column, 'unsigned', false );
			$null     = $this->app->utility->array_get( $column, 'null', true );
			$default  = $this->app->utility->array_get( $column, 'default', null );
			$comment  = $this->app->utility->array_get( $column, 'comment', '' );

			$sql .= $name . ' ' . strtolower( $type );
			if ( $unsigned ) {
				$sql .= ' unsigned';
			}
			if ( $null ) {
				$sql .= ' NULL';
			} else {
				$sql .= ' NOT NULL';
			}
			if ( $key === 'id' ) {
				$sql .= ' AUTO_INCREMENT';
			} elseif ( isset( $default ) ) {
				$default = str_replace( '\'', '\\\'', $default );
				$sql     .= " DEFAULT '{$default}'";
			}
			if ( ! empty( $comment ) ) {
				$comment = str_replace( '\'', '\\\'', $comment );
				$sql     .= " COMMENT '{$comment}'";
			}
			$sql .= ",\n";
		}

		$index   = [];
		$index[] = "PRIMARY KEY  ({$define['columns']['id']['name']})";
		if ( ! empty( $define['index']['key'] ) ) {
			foreach ( $define['index']['key'] as $name => $columns ) {
				if ( ! [ $columns ] ) {
					$columns = [ $columns ];
				}
				$columns = implode( ', ', $columns );
				$index[] = "INDEX {$name} ({$columns})";
			}
		}
		if ( ! empty( $define['index']['unique'] ) ) {
			foreach ( $define['index']['unique'] as $name => $columns ) {
				if ( ! [ $columns ] ) {
					$columns = [ $columns ];
				}
				$columns = implode( ', ', $columns );
				$index[] = "UNIQUE KEY {$name} ({$columns})";
			}
		}
		$sql .= implode( ",\n", $index );
		$sql .= "\n) ENGINE = InnoDB DEFAULT CHARSET = {$char}";
		if ( ! empty( $define['comment'] ) ) {
			$define['comment'] = str_replace( '\'', '\\\'', $define['comment'] );
			$sql               .= " COMMENT '{$define['comment']}'";
		}
		$sql .= ';';

		return dbDelta( $sql );
	}

	/**
	 * @return bool
	 */
	private function need_to_update() {
		return version_compare( $this->get_version(), $this->get_db_version() ) > 0;
	}

	/**
	 * @return string
	 */
	private function get_version() {
		return $this->app->get_config( 'config', 'db_version', '0.0.0' );
	}

	/**
	 * @return string
	 */
	private function get_db_version() {
		return $this->app->get_option( 'db_version', '0.0.0' );
	}

	/**
	 * @return bool
	 */
	private function update_db_version() {
		return $this->app->option->set( 'db_version', $this->get_version() );
	}


	/**
	 * @param array $define
	 *
	 * @return bool
	 */
	private function is_logical( $define ) {
		return $this->apply_filters( 'is_logical', 'physical' !== $this->app->utility->array_get( $define, 'delete', $this->app->get_config( 'config', 'default_delete_rule' ) ), $define );
	}

	/**
	 * @param $data
	 * @param $columns
	 *
	 * @return array
	 */
	private function filter( $data, $columns ) {
		$_format  = [];
		$_data    = [];
		$_columns = $columns;
		foreach ( $data as $k => $v ) {
			$columns = $_columns;
			list( $name, $columns ) = $this->get_field_data( $k, $columns );
			if ( isset( $columns[ $k ] ) ) {
				$_format[] = $columns[ $k ]['format'];
			} else {
				$_format[] = '%s';
			}
			$_data[ $name ] = $v;
		}

		return [ $_data, $_format ];
	}

	/**
	 * @param string $k
	 * @param array|null $columns
	 *
	 * @return array
	 */
	private function get_field_data( $k, $columns ) {
		$table = null;
		if ( strpos( $k, '.' ) !== false && strpos( $k, '(' ) === false ) {
			$exploded = explode( '.', $k );
			$table    = trim( $exploded[0], '`' );
			$k        = trim( $exploded[1], '`' );
			if ( isset( $this->table_defines[ $table ]['columns'][ $k ] ) ) {
				$name    = $this->table_defines[ $table ]['columns'][ $k ]['name'];
				$columns = $this->table_defines[ $table ];
				$table   = $this->get_table( $table );
			} else {
				$name = $k;
			}
		} else {
			if ( empty( $columns ) ) {
				return [ $k, $columns ];
			}
			$k = trim( $k, '`' );
			if ( isset( $columns[ $k ] ) ) {
				$name = $columns[ $k ]['name'];
			} else {
				$name = $k;
			}
		}
		if ( ! empty( $table ) ) {
			$name = $table . '.' . $name;
		}

		return [ $name, $columns ];
	}

	/**
	 * @param string $k
	 * @param array|null $columns
	 *
	 * @return string
	 */
	private function get_field_name( $k, $columns ) {
		return $this->get_field_data( $k, $columns )[0];
	}

	/**
	 * @since 2.0.0 Changed: return $data
	 *
	 * @param array $data
	 * @param bool $create
	 * @param bool $update
	 * @param bool $delete
	 *
	 * @return array
	 */
	private function set_update_params( $data, $create, $update, $delete ) {
		$now  = $this->apply_filters( 'set_update_params_date', date_i18n( 'Y-m-d H:i:s' ) );
		$user = $this->apply_filters( 'set_update_params_user', substr( $this->app->user->user_name, 0, 32 ) );

		if ( $create ) {
			$data['created_at'] = $now;
			$data['created_by'] = $user;
		}
		if ( $update ) {
			$data['updated_at'] = $now;
			$data['updated_by'] = $user;
		}
		if ( $delete ) {
			$data['deleted_at'] = $now;
			$data['deleted_by'] = $user;
		}

		return $data;
	}

	/**
	 * @param null|array|string $fields
	 * @param array $columns
	 *
	 * @return array
	 */
	private function build_fields( $fields, $columns ) {
		if ( ! isset( $fields ) ) {
			$fields = [ '*' ];
		}
		if ( is_string( $fields ) ) {
			$fields = [ $fields ];
		}
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $k => $option ) {
				$key = $k;
				if ( is_int( $key ) ) {
					$key    = $option;
					$option = null;
				}
				if ( $key === '*' ) {
					if ( ! is_array( $option ) ) {
						unset ( $fields[ $k ] );
						$is_admin = is_admin() && ! $this->app->utility->doing_ajax();
						foreach ( $columns as $key => $column ) {
							if ( ! $is_admin && ! empty( $column['only_admin'] ) ) {
								continue;
							}
							$name     = $this->app->utility->array_get( $column, 'name' );
							$fields[] = $name === $key ? $name : $name . ' AS ' . $key;
						}
						continue;
					}
					$name = $key;
				} elseif ( isset( $columns[ $key ] ) ) {
					$name = $columns[ $key ]['name'];
				} else {
					$name = $key;
				}
				if ( is_array( $option ) ) {
					$group_func = $option[0];
					if ( strtoupper( $group_func ) == 'AS' ) {
						$fields[ $k ] = $name;
						if ( count( $option ) >= 2 ) {
							$fields[ $k ] .= ' AS ' . $option[1];
						}
					} else {
						$fields[ $k ] = "$group_func( $name )";
						if ( count( $option ) >= 2 ) {
							$fields[ $k ] .= ' AS ' . $option[1];
						}
					}
				} elseif ( ! isset( $option ) ) {
					$fields[ $k ] = $name === $key ? $name : $name . ' AS ' . $key;
				} else {
					$fields[ $k ] = $name . ' AS ' . $option;
				}
			}
		}
		if ( empty( $fields ) || ! is_array( $fields ) ) {
			$fields   = [];
			$is_admin = is_admin() && ! $this->app->utility->doing_ajax();
			foreach ( $columns as $key => $column ) {
				if ( ! $is_admin && ! empty( $column['only_admin'] ) ) {
					continue;
				}
				$name     = $this->app->utility->array_get( $column, 'name' );
				$fields[] = $name === $key ? $name : $name . ' AS ' . $key;
			}
		}
		empty( $fields ) and $fields = [ '*' ];
		$fields = implode( ', ', $fields );

		return $fields;
	}

	/**
	 * @param array $where
	 * @param array $columns
	 * @param string $glue
	 *
	 * @return array
	 */
	private function build_conditions( $where, $columns, $glue = 'AND' ) {
		list ( $_where, $_where_format ) = $this->filter( $where, $columns );
		$conditions = $values = [];
		$index      = 0;
		foreach ( $_where as $field => $value ) {
			$field  = trim( $field );
			$format = $_where_format[ $index ++ ];
			if ( is_null( $value ) ) {
				$conditions[] = "$field IS NULL";
				continue;
			}

			if ( in_array( strtoupper( $field ), [
				'EXISTS',
				'NOT EXISTS',
			] ) ) {
				! is_array( $value ) and $value = [ $value ];
				foreach ( $value as $sub_query ) {
					$conditions[] = "$field ($sub_query)";
				}
				continue;
			}

			$op = '=';
			if ( is_array( $value ) ) {
				if ( count( $value ) > 1 ) {
					$op  = trim( $value[0] );
					$val = $value[1];
					if ( in_array( strtoupper( $op ), [
						'OR',
						'AND',
					] ) ) {
						array_shift( $value );
						$_conditions = [];
						foreach ( $value as $v ) {
							if ( ! is_array( $v ) ) {
								$_conditions[] = "1=0";
								continue;
							}
							list( $c, $v ) = $this->build_conditions( $v, $columns );
							$values        = array_merge( $values, $v );
							$_conditions[] = "({$c})";
						}
						$conditions[] = implode( " {$op} ", $_conditions );

						continue;
					}
					if ( is_array( $val ) ) {
						if ( empty( $val ) ) {
							$conditions[] = "1=0";
						} else {
							foreach ( $val as $v ) {
								$values[] = $v;
							}
							$conditions[] = "$field $op (" . str_repeat( $format . ',', count( $val ) - 1 ) . $format . ')';
						}
						continue;
					}
					if ( count( $value ) > 2 ) {
						$val          = $this->get_field_name( $val, $columns );
						$conditions[] = "$field $op $val";
						continue;
					}
				} else {
					$value        = reset( $value );
					$conditions[] = "$field ($value)";
					continue;
				}
			} else {
				$val = $value;
			}

			$conditions[] = "$field $op $format";
			$values[]     = $val;
		}
		$conditions = implode( " {$glue} ", $conditions );

		return [ $conditions, $values ];
	}

	/**
	 * @param null|array $group_by
	 * @param array $columns
	 *
	 * @return string
	 */
	private function build_group_by( $group_by, $columns ) {
		$sql = '';
		if ( ! empty( $group_by ) ) {
			$items = [];
			foreach ( $group_by as $k ) {
				$items[] = $this->get_field_name( $k, $columns );
			}
			if ( ! empty( $items ) ) {
				$sql .= ' GROUP BY ' . implode( ', ', $items );
			}
		}

		return $sql;
	}

	/**
	 * @param null|array $order_by
	 * @param array $columns
	 *
	 * @return string
	 */
	private function build_order_by( $order_by, $columns ) {
		$sql = '';
		if ( ! empty( $order_by ) ) {
			$items = [];
			foreach ( $order_by as $k => $order ) {
				if ( is_int( $k ) ) {
					$k     = $order;
					$order = 'ASC';
				} else {
					$order = trim( strtoupper( $order ) );
				}
				if ( $order !== 'DESC' && $order !== 'ASC' ) {
					continue;
				}
				$k       = $this->get_field_name( $k, $columns );
				$items[] = "$k $order";
			}
			if ( ! empty( $items ) ) {
				$sql .= ' ORDER BY ' . implode( ', ', $items );
			}
		}

		return $sql;
	}

	/**
	 * @param null|array $join
	 *
	 * @return string
	 */
	private function build_join( $join ) {
		$sql = '';
		if ( ! empty( $join ) ) {
			$items = [];
			foreach ( $join as $data ) {
				if ( ! is_array( $data ) || count( $data ) < 3 ) {
					continue;
				}
				$table = $data[0];
				$rule  = $data[1];
				$rule  = strtoupper( $rule );
				if ( ! in_array( $rule, [
					'JOIN',
					'INNER JOIN',
					'LEFT JOIN',
					'RIGHT JOIN',
				] ) ) {
					continue;
				}

				$conditions = $data[2];
				if ( empty( $conditions ) ) {
					continue;
				}
				$check = reset( $conditions );
				if ( ! is_array( $check ) ) {
					$conditions = [ $conditions ];
				}
				$values = [];
				foreach ( $conditions as $condition ) {
					if ( ! is_array( $condition ) || count( $condition ) < 3 ) {
						continue;
					}
					$left     = $condition[0];
					$op       = $condition[1];
					$right    = $condition[2];
					$values[] = $this->get_field_name( $left, null ) . " $op " . $this->get_field_name( $right, null );
				}
				if ( ! empty( $values ) ) {
					$as = null;
					if ( is_array( $table ) && count( $table ) > 1 ) {
						$as    = $table[1];
						$table = $table[0];
					}
					$items[] = $rule . ' ' . $this->get_table( $table ) . ( isset( $as ) ? " AS $as" : '' ) . ' ON ' . implode( ' AND ', $values );
				}
			}
			if ( ! empty( $items ) ) {
				$sql .= ' ' . implode( ' ', $items );
			}
		}

		return $sql;
	}

	/**
	 * @param null|int $limit
	 * @param null|int $offset
	 *
	 * @return string
	 */
	private function build_limit( $limit, $offset ) {
		$sql = '';
		if ( isset( $limit ) && $limit > 0 ) {
			if ( isset( $offset ) && $offset > 0 ) {
				$sql .= " LIMIT {$offset}, {$limit}";
			} else {
				$sql .= " LIMIT {$limit}";
			}
		}

		return $sql;
	}

	/**
	 * @param array|string $tables
	 * @param null|array $where
	 * @param null|array|string $fields
	 * @param null|int $limit
	 * @param null|int $offset
	 * @param null|array $order_by
	 * @param null|array $group_by
	 * @param bool $for_update
	 *
	 * @return string|false
	 */
	public function get_select_sql( $tables, $where = null, $fields = null, $limit = null, $offset = null, $order_by = null, $group_by = null, $for_update = false ) {
		$as = null;
		if ( is_array( $tables ) ) {
			if ( empty( $tables ) ) {
				return false;
			}
			$table = array_shift( $tables );
			$join  = $tables;
			if ( count( $table ) > 1 ) {
				$as = $table[1];
			}
			$table = $table[0];
		} else {
			$table = $tables;
			$join  = null;
		}
		if ( ! isset( $this->table_defines[ $table ] ) ) {
			return false;
		}

		$columns = $this->table_defines[ $table ]['columns'];

		! is_array( $where ) and $where = [];
		if ( $this->is_logical( $this->table_defines[ $table ] ) ) {
			$where['deleted_at'] = null;
		}

		list( $conditions, $values ) = $this->build_conditions( $where, $columns );
		$table  = $this->get_table( $table );
		$fields = $this->build_fields( $fields, $columns );
		$sql    = "SELECT {$fields} FROM $table";
		if ( isset( $as ) ) {
			$sql .= " AS $as";
		}
		$sql .= $this->build_join( $join );
		if ( ! empty( $conditions ) ) {
			$sql .= " WHERE $conditions";
		}
		$sql .= $this->build_group_by( $group_by, $columns );
		$sql .= $this->build_order_by( $order_by, $columns );
		$sql .= $this->build_limit( $limit, $offset );
		if ( $for_update ) {
			$sql .= ' FOR UPDATE';
		}

		return $this->prepare( $sql, $values );
	}

	/**
	 * @since 2.0.0 Changed: default value of $output
	 *
	 * @param array|string $tables
	 * @param array $where
	 * @param null|array|string $fields
	 * @param null|int $limit
	 * @param null|int $offset
	 * @param null|array $order_by
	 * @param null|array $group_by
	 * @param null|string $output
	 * @param bool $for_update
	 *
	 * @return array|bool|null
	 */
	public function select( $tables, $where = [], $fields = null, $limit = null, $offset = null, $order_by = null, $group_by = null, $output = null, $for_update = false ) {
		$sql = $this->get_select_sql( $tables, $where, $fields, $limit, $offset, $order_by, $group_by, $for_update );
		if ( false === $sql ) {
			return false;
		}

		/** @var \wpdb $wpdb */
		global $wpdb;
		! isset( $output ) and $output = ARRAY_A;

		return $wpdb->get_results( $sql, $output );
	}

	/**
	 * @since 2.0.0 Changed: default value of $output
	 *
	 * @param array|string $tables
	 * @param array $where
	 * @param null|array|string $fields
	 * @param null|int $offset
	 * @param null|array $order_by
	 * @param null|array $group_by
	 * @param null|string $output
	 * @param bool $for_update
	 *
	 * @return array|bool|null
	 */
	public function select_row( $tables, $where = [], $fields = null, $offset = null, $order_by = null, $group_by = null, $output = null, $for_update = false ) {
		$sql = $this->get_select_sql( $tables, $where, $fields, 1, $offset, $order_by, $group_by, $for_update );
		if ( false === $sql ) {
			return false;
		}

		/** @var \wpdb $wpdb */
		global $wpdb;
		! isset( $output ) and $output = ARRAY_A;

		return $wpdb->get_row( $sql, $output );
	}

	/**
	 * @since 2.0.0 Changed: default values of $offset, $order_by, $group_by
	 *
	 * @param $table
	 * @param string $field
	 * @param array $where
	 * @param null|int $limit
	 * @param null|int $offset
	 * @param null|array $order_by
	 * @param null|array $group_by
	 * @param bool $for_update
	 *
	 * @return int
	 */
	public function select_count( $table, $field = '*', $where = [], $limit = null, $offset = null, $order_by = null, $group_by = null, $for_update = false ) {
		empty( $field ) and $field = '*';
		$result = $this->select( $table, $where, [
			$field => [
				'COUNT',
				'num',
			],
		], $limit, $offset, $order_by, $group_by, ARRAY_A, $for_update );
		if ( empty( $result ) ) {
			return 0;
		}

		return isset( $result[0]['num'] ) ? $result[0]['num'] - 0 : 0;
	}

	/**
	 * @param string $table
	 * @param array $data
	 * @param string $method
	 *
	 * @return bool|false|int
	 */
	private function _insert_replace( $table, $data, $method ) {
		if ( ! isset( $this->table_defines[ $table ] ) ) {
			return false;
		}
		if ( $method !== 'insert' && $method !== 'replace' ) {
			return false;
		}
		if ( $method === 'replace' && ! isset( $data['id'] ) ) {
			return false;
		}

		/** @var \wpdb $wpdb */
		global $wpdb;
		$columns = $this->table_defines[ $table ]['columns'];

		$data = $this->set_update_params( $data, $method === 'insert', true, false );
		list ( $_data, $_format ) = $this->filter( $data, $columns );

		return $wpdb->$method( $this->get_table( $table ), $_data, $_format );
	}

	/**
	 * @return int
	 */
	public function get_insert_id() {
		/** @var \wpdb $wpdb */
		global $wpdb;

		return $wpdb->insert_id;
	}

	/**
	 * @param string $table
	 * @param array $data
	 *
	 * @return bool|false|int
	 */
	public function insert( $table, $data ) {
		return $this->_insert_replace( $table, $data, 'insert' );
	}

	/**
	 * @param string $table
	 * @param array $fields
	 * @param array $data_list
	 *
	 * @return bool|false|int
	 */
	public function bulk_insert( $table, $fields, $data_list ) {
		if ( ! isset( $this->table_defines[ $table ] ) || empty( $fields ) || empty( $data_list ) ) {
			return false;
		}
		$columns     = $this->table_defines[ $table ]['columns'];
		$table       = $this->get_table( $table );
		$sql         = "INSERT INTO {$table} ";
		$names       = [];
		$placeholder = [];
		$time        = $this->set_update_params( [], true, true, false );
		foreach ( $fields as $field ) {
			if ( ! isset( $columns[ $field ] ) ) {
				return false;
			}
			$names[]       = $columns[ $field ]['name'];
			$placeholder[] = $columns[ $field ]['format'];
		}
		foreach ( $time as $k => $v ) {
			$names[]       = $columns[ $k ]['name'];
			$placeholder[] = $columns[ $k ]['format'];
		}
		$placeholder = '(' . implode( ', ', $placeholder ) . ')';
		$sql         .= '(' . implode( ', ', $names ) . ') VALUES ';

		$values = [];
		foreach ( $data_list as $data ) {
			$data += $time;
			if ( count( $names ) != count( $data ) ) {
				return false;
			}

			$values[] = $this->prepare( $placeholder, $data );
		}
		$sql .= implode( ', ', $values );

		return $this->query( $sql );
	}

	/**
	 * @param string $table
	 * @param array $data
	 *
	 * @return bool|false|int
	 */
	public function replace( $table, $data ) {
		return $this->_insert_replace( $table, $data, 'replace' );
	}

	/**
	 * @param string $table
	 * @param array $data
	 * @param array $where
	 *
	 * @return bool|false|int
	 */
	public function update( $table, $data, $where ) {
		if ( ! isset( $this->table_defines[ $table ] ) ) {
			return false;
		}

		/** @var \wpdb $wpdb */
		global $wpdb;
		$columns = $this->table_defines[ $table ]['columns'];

		if ( $this->is_logical( $this->table_defines[ $table ] ) ) {
			$where['deleted_at'] = null;
		}

		$data = $this->set_update_params( $data, false, true, false );
		list ( $_data, $_format ) = $this->filter( $data, $columns );
		list ( $_where, $_where_format ) = $this->filter( $where, $columns );

		return $wpdb->update( $this->get_table( $table ), $_data, $_where, $_format, $_where_format );
	}

	/**
	 * @param $table
	 * @param $data
	 * @param $where
	 *
	 * @return int
	 */
	public function insert_or_update( $table, $data, $where ) {
		if ( ! isset( $this->table_defines[ $table ] ) ) {
			return false;
		}

		if ( $this->is_logical( $this->table_defines[ $table ] ) ) {
			$where['deleted_at'] = null;
		}

		$row = $this->select_row( $table, $where, 'id' );
		if ( empty( $row ) ) {
			$this->insert( $table, $data );

			return $this->get_insert_id();
		}
		$where = [ 'id' => $row['id'] ];
		$this->update( $table, $data, $where );

		return $row['id'];
	}

	/**
	 * @param string $table
	 * @param array $where
	 *
	 * @return bool|false|int
	 */
	public function delete( $table, $where ) {
		if ( ! isset( $this->table_defines[ $table ] ) ) {
			return false;
		}

		if ( $this->is_logical( $this->table_defines[ $table ] ) ) {
			$data = $this->set_update_params( [], false, false, true );

			return $this->update( $table, $data, $where );
		}

		/** @var \wpdb $wpdb */
		global $wpdb;
		$columns = $this->table_defines[ $table ]['columns'];

		list ( $_where, $_where_format ) = $this->filter( $where, $columns );

		return $wpdb->delete( $this->get_table( $table ), $_where, $_where_format );
	}

	/**
	 * @param $table
	 *
	 * @return bool|false|int
	 */
	public function truncate( $table ) {
		if ( ! isset( $this->table_defines[ $table ] ) ) {
			return false;
		}

		if ( $this->is_logical( $this->table_defines[ $table ] ) ) {
			return $this->delete( $table, [] );
		}

		$sql = 'TRUNCATE TABLE `' . $this->get_table( $table ) . '`';

		return $this->query( $sql );
	}

	/**
	 * @param string $sql
	 *
	 * @return false|int
	 */
	public function query( $sql ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		return $wpdb->query( $sql );
	}

	/**
	 * @param string $sql
	 * @param array $values
	 *
	 * @return string
	 */
	public function prepare( $sql, $values ) {
		/** @var \wpdb $wpdb */
		global $wpdb;

		return empty( $values ) ? $sql : $wpdb->prepare( $sql, $values );
	}

	/**
	 * @return false|int
	 */
	public function begin() {
		return $this->query( 'START TRANSACTION' );
	}

	/**
	 * @param string $table
	 * @param bool $write
	 *
	 * @return bool|string
	 */
	public function lock( $table, $write ) {
		if ( ! isset( $this->table_defines[ $table ] ) ) {
			return false;
		}

		return $this->query( 'LOCK TABLES `' . $this->get_table( $table ) ) . '` ' . ( $write ? 'WRITE' : 'READ' );
	}

	/**
	 * @return false|int
	 */
	public function unlock() {
		return $this->query( 'UNLOCK TABLES' );
	}

	/**
	 * @return false|int
	 */
	public function commit() {
		return $this->query( 'COMMIT' );
	}

	/**
	 * @return false|int
	 */
	public function rollback() {
		return $this->query( 'ROLLBACK' );
	}

	/** @var int $transaction_level */
	private $transaction_level = 0;

	/**
	 * @param callable $func
	 *
	 * @return bool
	 */
	public function transaction( $func ) {
		$level = $this->transaction_level;
		$this->transaction_level ++;
		if ( $level === 0 ) {
			try {
				$this->begin();
				$func();
				$this->commit();

				return true;
			} catch ( \Exception $e ) {
				$this->rollback();
				$this->app->log( $e->getMessage() );
			} finally {
				$this->transaction_level = $level;
			}
		} else {
			try {
				$func();

				return true;
			} finally {
				$this->transaction_level = $level;
			}
		}

		return false;
	}

	/**
	 * uninstall
	 */
	public function uninstall() {
		foreach ( $this->table_defines as $table => $define ) {
			if ( ! empty( $define['is_added'] ) ) {
				$sql = 'DROP TABLE IF EXISTS `' . $this->get_table( $table ) . '`';
				$this->query( $sql );
			}
		}
	}

	/**
	 * @since 2.0.2 Added: Uninstall priority
	 * @return int
	 */
	public function get_uninstall_priority() {
		return 500;
	}
}
