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

	class ImportTag {

		public function __construct() {

			// example constructor called when plugin loads

		}

		public function import_tag_category() {
			global $wpdb;

			$pi_table_get_tag_query = 'SELECT * FROM `taggit_tag`';
			$pi_table_get_global_tag       = $wpdb->get_results( $pi_table_get_tag_query );

			foreach ( $pi_table_get_global_tag as $pi_table_tag ) {
				if ( isset( $pi_table_category->id ) && ! empty( $pi_table_tag->id ) ) {
					$this->create_tag_from_table_data( $pi_table_tag );
				} else {
					WP_CLI::success( 'Term = ' . $pi_table_tag->name );
					$this->create_tag_from_table_data( $pi_table_tag );
				}
			}
		}

		/**
		 * Create tag.
		 *
		 * @param $table_tag
		 */
		public function create_tag_from_table_data( $table_tag ) {
			$args        = array(
				'description' => $table_tag->content,
				'slug'   => $table_tag->slug,
			);
			$insert_term = wp_insert_term( $table_tag->name, 'partner_network_tag', $args );
			update_term_meta( $insert_term['term_id'],'pi_django_term_id', $table_tag->id );
			if ( ! is_wp_error( $insert_term ) ) {
				WP_CLI::success( 'Inserted = ' . print_r( $insert_term, true ) );
			} else {
				WP_CLI::error( '[' . $table_tag->name . ']' . $insert_term->get_error_message(), false );
			}
		}

	}

	WP_CLI::add_command( 'performance', 'ImportTag' );

}

