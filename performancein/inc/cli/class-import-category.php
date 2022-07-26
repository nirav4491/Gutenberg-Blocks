
<?php


# Register a custom 'foo' command to output a supplied positional param.
#
# $ wp foo bar --append=qux
# Success: bar qux

/**
 * My awesome closure command
 *
 * <message>
 * : An awesome message to display
 *
 * --append=<message>
 * : An awesome message to append to the original message.
 *
 * @when before_wp_load
 */

if ( defined( 'WP_CLI' ) && WP_CLI ) {

	class ImportCategory {

		public function __construct() {

			// example constructor called when plugin loads

		}

		public function import_global_category() {
			global $wpdb;

			$pi_table_get_global_categories_query = 'SELECT * FROM `content_globalcategory`';
			$pi_table_get_global_categories       = $wpdb->get_results( $pi_table_get_global_categories_query );

			foreach ( $pi_table_get_global_categories as $pi_table_category ) {
				if ( isset( $pi_table_category->parent_id ) && ! empty( $pi_table_category->parent_id ) ) {
					$pi_parent_category_query = 'SELECT * FROM `content_globalcategory`  WHERE `id` =' . $pi_table_category->parent_id;
					$pi_parent_category       = $wpdb->get_results( $pi_parent_category_query );
					$term_parent_id           = $this->get_parent_category_name_by_id( $pi_parent_category[0]->slug );
					if ( 0 === $term_parent_id ) {
						$term_parent_id = $this->create_parent_category( $pi_parent_category[0] );
					}
					$this->create_category_from_table_data( $pi_table_category, $term_parent_id );

				} else {
					WP_CLI::success( 'Term = ' . $pi_table_category->name );
					$this->create_category_from_table_data( $pi_table_category, $pi_table_category->parent_id );
				}
			}
		}

		/**
		 * Get parent category id by slug|name.
		 *
		 * @param string $fields_value
		 * @param string $fields_type
		 *
		 * @return int
		 */
		public function get_parent_category_name_by_id( $fields_value = '', $fields_type = 'slug' ) {
			$get_term_by = get_term_by( $fields_type, $fields_value, 'category' );
			if ( isset( $get_term_by ) && ! empty( $get_term_by ) ) {
				return $get_term_by->term_id;
			}

			return 0;
		}

		/**
		 * Create parent category.
		 *
		 * @param $pi_parent_obj
		 *
		 * @return mixed
		 */
		public function create_parent_category( $pi_parent_obj ) {
			$insert_parent_term = wp_insert_term( $pi_parent_obj->name, 'category', array(
				'slug' => $pi_parent_obj->slug,
			) );
			if ( ! is_wp_error( $insert_parent_term ) ) {
				$this->insert_category_meta( $insert_parent_term['term_id'], $pi_parent_obj );
				WP_CLI::success( 'Parent Term Inserted = ' . print_r( $insert_parent_term, true ) );
			} else {
				WP_CLI::error( 'Parent Term = ' . $insert_parent_term->get_error_message(), false );
			}

			return $insert_parent_term['term_id'];
		}

		/**
		 * Insert category meta.
		 *
		 * @param $term_id
		 * @param $pi_category_obj
		 */
		public function insert_category_meta( $term_id, $pi_category_obj ) {
			update_term_meta( $term_id, 'pi_category_information', $pi_category_obj->content );
			update_term_meta( $term_id, 'pi_category_meta', $pi_category_obj->meta );

			// Currently this fields are not display on front side.
			update_term_meta( $term_id, 'pi_category_id', $pi_category_obj->id );
			update_term_meta( $term_id, 'pi_category_takeover_status', $pi_category_obj->takeover_status );
			update_term_meta( $term_id, 'pi_category_takeover_url', $pi_category_obj->takeover_url );
			update_term_meta( $term_id, 'pi_category_takeover_start_date', $pi_category_obj->takeover_start_date );
			update_term_meta( $term_id, 'pi_category_takeover_end_date', $pi_category_obj->takeover_end_date );
			update_term_meta( $term_id, 'pi_category_takeover_image', $pi_category_obj->takeover_image );
			update_term_meta( $term_id, 'pi_category_takeover_background_color', $pi_category_obj->takeover_background_color );
			update_term_meta( $term_id, 'pi_category_parent_id', $pi_category_obj->parent_id );
			update_term_meta( $term_id, 'pi_category_title', $pi_category_obj->category_title );
		}

		/**
		 * Create category.
		 *
		 * @param $table_category
		 * @param $parent_id
		 */
		public function create_category_from_table_data( $table_category, $parent_id ) {
			$args        = array(
				'parent' => $parent_id,
				'slug'   => $table_category->slug,
			);
			$insert_term = wp_insert_term( $table_category->name, 'category', $args );
			if ( ! is_wp_error( $insert_term ) ) {
				$this->insert_category_meta( $insert_term['term_id'], $table_category );
				WP_CLI::success( 'Inserted = ' . print_r( $insert_term, true ) );
			} else {
				WP_CLI::error( '[' . $table_category->name . ']' . $insert_term->get_error_message(), false );
			}
		}

		public function delete_article() {

			for ( $i = 1970; $i < 2000; $i ++ ) {
				wp_delete_post( $i, true );
				WP_CLI::success( 'deleted id = ' . $i );
			}


		}

	}

	WP_CLI::add_command( 'performance_import_cat', 'ImportCategory' );

}


