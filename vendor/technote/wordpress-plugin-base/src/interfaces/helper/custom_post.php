<?php
/**
 * Technote Interfaces Helper Custom Post
 *
 * @version 2.9.10
 * @author technote-space
 * @since 2.8.0
 * @since 2.9.0 Changed: implements Singleton, Validate
 * @since 2.9.2 Added: trash post
 * @since 2.9.2 Changed: delete data arg
 * @since 2.9.3 Added: insert, update methods
 * @since 2.9.7 Added: get_post_type_object method
 * @since 2.9.10 Changed: return type
 * @since 2.9.10 Added: user_can method
 * @copyright technote All Rights Reserved
 * @license http://www.opensource.org/licenses/gpl-2.0.php GNU General Public License, version 2
 * @link https://technote.space
 */

namespace Technote\Interfaces\Helper;

if ( ! defined( 'TECHNOTE_PLUGIN' ) ) {
	exit;
}

/**
 * Interface Custom_Post
 * @package Technote\Interfaces\Helper
 */
interface Custom_Post extends \Technote\Interfaces\Singleton, \Technote\Interfaces\Hook, \Technote\Interfaces\Presenter, Data_Helper, Validate {

	/**
	 * @since 2.9.3
	 *
	 * @param array $data
	 * @param bool $convert_name
	 *
	 * @return array|bool|int
	 */
	public function insert( $data, $convert_name = true );

	/**
	 * @since 2.9.3
	 *
	 * @param array $data
	 * @param array $where
	 * @param bool $convert_name
	 *
	 * @return array|bool|int
	 */
	public function update( $data, $where, $convert_name = true );

	/**
	 * @return string
	 */
	public function get_post_type_slug();

	/**
	 * @return string
	 */
	public function get_related_table_name();

	/**
	 * @return string
	 */
	public function get_post_type();

	/**
	 * @since 2.9.7
	 * @since 2.9.10 Changed: return type
	 * @return \WP_Post_Type|\WP_Error
	 */
	public function get_post_type_object();

	/**
	 * @since 2.9.10
	 *
	 * @param $capability
	 *
	 * @return bool
	 */
	public function user_can( $capability );

	/**
	 * @param null|array $capabilities
	 *
	 * @return array
	 */
	public function get_post_type_args( $capabilities = null );

	/**
	 * @return array
	 */
	public function get_post_type_labels();

	/**
	 * @return string
	 */
	public function get_post_type_single_name();

	/**
	 * @return string
	 */
	public function get_post_type_plural_name();

	/**
	 * @return string|array
	 */
	public function get_post_type_capability_type();

	/**
	 * @return array
	 */
	public function get_post_type_supports();

	/**
	 * @return string
	 */
	public function get_post_type_menu_icon();

	/**
	 * @return int|null
	 */
	public function get_post_type_position();

	/**
	 * @param string $search
	 * @param \WP_Query $wp_query
	 *
	 * @return string
	 */
	public function posts_search( $search, $wp_query );

	/**
	 * @param string $join
	 * @param \WP_Query $wp_query
	 *
	 * @return string
	 */
	public function posts_join( $join, $wp_query );

	/**
	 * @param array $columns
	 * @param bool $sortable
	 *
	 * @return array
	 */
	public function manage_posts_columns( $columns, $sortable = false );

	/**
	 * @param string $column_name
	 * @param \WP_Post $post
	 */
	public function manage_posts_custom_column( $column_name, $post );

	/**
	 * @param int $post_id
	 *
	 * @return array|false
	 */
	public function get_related_data( $post_id );

	/**
	 * @param int $id
	 * @param bool $is_valid
	 *
	 * @return array|false
	 */
	public function get_data( $id, $is_valid = true );

	/**
	 * @param bool $is_valid
	 * @param int|null $per_page
	 * @param int $page
	 * @param array $where
	 * @param null|array $orderby
	 *
	 * @return array
	 */
	public function list_data( $is_valid = true, $per_page = null, $page = 1, $where = [], $orderby = null );

	/**
	 * @param array $params
	 * @param array $where
	 * @param \WP_Post $post
	 * @param bool $update
	 *
	 * @return int
	 */
	public function update_data( $params, $where, $post, $update );

	/**
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param array $old
	 * @param array $new
	 */
	public function data_updated( $post_id, $post, $old, $new );

	/**
	 * @param int $post_id
	 * @param \WP_Post $post
	 * @param array $data
	 */
	public function data_inserted( $post_id, $post, $data );

	/**
	 * @param \WP_Post $post
	 * @param bool $update
	 *
	 * @return array
	 */
	public function get_update_data_params( $post, $update );

	/**
	 * @since 2.9.2
	 *
	 * @param int $post_id
	 */
	public function trash_post( $post_id );

	/**
	 * @param int $post_id
	 *
	 * @return bool|false|int
	 */
	public function delete_data( $post_id );

	/**
	 * @return string
	 */
	public function get_post_field_name_prefix();

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	public function get_post_field_name( $key );

	/**
	 * @return array
	 */
	public function get_data_field_settings();

	/**
	 * @param \WP_Post $post
	 */
	public function output_edit_form( $post );

	/**
	 * @param \WP_Post $post
	 */
	public function output_after_editor( $post );

	/**
	 * @param array|null $post_array
	 *
	 * @return array
	 */
	public function validate_input( $post_array = null );

	/**
	 * @param array $data
	 * @param array $post_array
	 *
	 * @return array
	 */
	public function filter_post_data( $data, $post_array );

	/**
	 * @param string $key
	 * @param array $errors
	 *
	 * @return array
	 */
	public function get_error_messages( $key, $errors );

	/**
	 * @param int $post_id
	 *
	 * @return string
	 */
	public function get_edit_post_link( $post_id );

	/**
	 * @return int
	 */
	public function get_load_priority();
}
