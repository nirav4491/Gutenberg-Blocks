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

	class ImportResource {

		public function __construct() {

			// example constructor called when plugin loads

		}

		public function import_resource() {

			global $wpdb;

			$start = microtime(true);

			//$pi_table_get_resource_query = 'SELECT * FROM `content_resource` INNER JOIN `content_resource_categories` ON content_resource_categories.resource_id=content_resource.id';
			$pi_table_get_resource_query = 'SELECT *,resource_id,GROUP_CONCAT(globalcategory_id) as cat_id FROM content_resource_categories  INNER JOIN `content_resource` on content_resource.id=content_resource_categories.resource_id GROUP BY resource_id';
			$pi_table_get_resources       = $wpdb->get_results( $pi_table_get_resource_query );

			$success_count = 1;

			foreach ( $pi_table_get_resources as $pi_table_get_resource ) {

				$author_id = $pi_table_get_resource->author_id;

				$user = get_users(
					array(
						'meta_key' => 'django_user_id',
						'meta_value' => $author_id,
						'number' => 1,
						'count_total' => false
					) );

				$defaults = array(
					'post_author'           => $author_id,
					'post_content'          => $pi_table_get_resource->content,
					'post_title'            => $pi_table_get_resource->title,
					'post_excerpt'          => $pi_table_get_resource->summary,
					'post_date'             => $pi_table_get_resource->date_added,
					'post_date_gmt'         => $pi_table_get_resource->date_added,
					'post_modified'         => $pi_table_get_resource->date_updated,
					'post_modified_gmt'     => $pi_table_get_resource->date_updated,
					'post_type'             => 'pi_resources',
				);

				if( '1' === $pi_table_get_resource->active ) {
					$defaults['post_status'] = 'publish';
				} else {
					$defaults['post_status'] = 'draft';
				}

				$resource_inserted_id = wp_insert_post($defaults);

				if (!empty ( $resource_inserted_id ) ) {

					update_field('pi_landing_page_url',$pi_table_get_resource->landing_page_url , $resource_inserted_id);

					/*
					 * add extra field the pi_django_resource_id
					 */
					update_post_meta( $resource_inserted_id, 'pi_django_resource_id', $pi_table_get_resource->id );
					update_post_meta( $resource_inserted_id, 'image_credit', $pi_table_get_resource->image_credit );
					update_post_meta( $resource_inserted_id, 'image_url', $pi_table_get_resource->image_url );
					update_post_meta( $resource_inserted_id, 'image_license_url', $pi_table_get_resource->image_license_url );
					update_post_meta( $resource_inserted_id, 'primary_category_id', $pi_table_get_resource->primary_category_id );
					update_post_meta( $resource_inserted_id, 'pi_auto_migration', 'pi_auto_migration' );

					if( '1' === $pi_table_get_resource->highlight ) {
						$highlight_val = 'enable';
						$highlight_val = maybe_serialize( $highlight_val );
						update_field('pi_resources_highlight', $highlight_val , $resource_inserted_id);
					} else {
						update_field('pi_resources_highlight', '' , $resource_inserted_id);
					}

					if( isset( $pi_table_get_resource->cat_id )  && !empty( $pi_table_get_resource->cat_id ) ) {
						$cat_lists = explode(",", $pi_table_get_resource->cat_id);
						foreach ( $cat_lists as $cat_list ) {
							$pi_table_get_resource_term = ' SELECT term_id FROM `wp_termmeta` WHERE meta_value = '.$cat_list.' and meta_key = "pi_category_id"';
							$pi_table_get_resources_terms_id = $wpdb->get_var( $pi_table_get_resource_term );

							if( isset( $pi_table_get_resources_terms_id ) && !empty( $pi_table_get_resources_terms_id ) ) {
								wp_set_post_terms( $resource_inserted_id, $pi_table_get_resources_terms_id, 'category' );
							}
						}
					}

					if( isset( $pi_table_get_resource->image ) && !empty( $pi_table_get_resource->image ) ){

						$this->generate_attachment( 'https://performancein.com/assets/' . $pi_table_get_resource->image, $resource_inserted_id, 'pi_the_image_shown_on_article_lists', $pi_table_get_resource->image_caption );
					}

					if( isset( $pi_table_get_resource->document ) && !empty( $pi_table_get_resource->document ) ){

						$this->generate_attachment( 'https://performancein.com/assets/' . $pi_table_get_resource->document, $resource_inserted_id, 'pi_resource_document', $pi_table_get_resource->image_caption );
					}
					WP_CLI::success( 'Inserted = ' . $success_count.') '. print_r( $pi_table_get_resource->title, true ) );
					$success_count++;
				} else {
					WP_CLI::error( '[' . $success_count.') '. $pi_table_get_resource->title. ']' . $resource_inserted_id->get_error_message(), false );
					$success_count++;
				}
			}
			$time_elapsed_secs = microtime(true) - $start;
			WP_CLI::success( 'executed time = ' . $time_elapsed_secs );
		}

		public function generate_attachment( $image_url, $post_id, $custom_field_key, $pi_image_caption = ' '  ) {

			$newUploadPath = explode("/",$image_url,-1);
			$month = end( $newUploadPath );
			$year = prev( $newUploadPath );

			if( !empty( $year )  && !empty( $month ) ) {
				$upload_dir = wp_upload_dir( $year . '/' . $month );
				$image_data = file_get_contents( $image_url );
				$filename   = basename( $image_url );
				if ( wp_mkdir_p( $upload_dir['path'] ) ) {
					$file = $upload_dir['path'] . '/' . $filename;
				} else {
					$file = $upload_dir['basedir'] . '/' . $filename;
				}
				if( file_exists( $file ) ) {
					$file = str_replace( '.', '__pi.', $file );
				}
				file_put_contents( $file, $image_data );
				$wp_filetype = wp_check_filetype($filename, null );
				$attachment = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => sanitize_file_name($filename),
					'post_excerpt' => $pi_image_caption,
					'post_content' => $pi_image_caption,
					'post_status' => 'inherit'
				);
				$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
				$res1= wp_update_attachment_metadata( $attach_id, $attach_data );
				$res2= set_post_thumbnail( $post_id, $attach_id );
				update_field( $custom_field_key, $attach_id, $post_id);
			}
		}



		public function delete_article() {

			for ( $i = 1970; $i < 2000; $i ++ ) {
				wp_delete_post( $i, true );
				WP_CLI::success( 'deleted id = ' . $i );
			}


		}

	}

	WP_CLI::add_command( 'performance', 'ImportResource' );

}


