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

	class ImportJob {

		public function __construct() {

			// example constructor called when plugin loads

		}

		function import_jobs(){

			global $wpdb;

			$get_jobs = 'SELECT * FROM `monetisation_job`';
			$all_job_list = $wpdb->get_results($get_jobs);

			$success_count = 0;
			$failed_count = 0;

			foreach ($all_job_list as $job) {

				$account_id = $job->account_id;

				$user_select = $wpdb->get_row( "select user_id from $wpdb->usermeta where meta_key = 'django_user_account_id' AND meta_value = '".$account_id."'", ARRAY_A );

				$results = $wpdb->get_results( "select post_id from $wpdb->postmeta where meta_key = 'pi_product_uuid' AND meta_value = '".$job->product_id."'", ARRAY_A );
				if ( '1' === $job->active ) {
					$post_status = 'publish';
				} else {
					$post_status = 'draft';
				}
				$defaults = array(
					'post_title' => $job->title,
					'post_status' => $post_status,
					'post_type' => 'pi_jobs',
					'post_name' => $job->slug,
					'post_date'         => $job->date_added,
					'post_date_gmt'     => $job->date_added,
					'post_modified'     => $job->date_added,
					'post_modified_gmt' => $job->date_added,
					'post_author' => (!empty($user_select))? $user_select['user_id']: '',
				);
				$job_inserted_id = wp_insert_post($defaults);

				//echo $event_inserted_id;exit;
				if (!empty($job_inserted_id)) {

					update_field('pi_description',$job->description , $job_inserted_id);
					update_field('pi_jobs_packages',$results[0]['post_id'] , $job_inserted_id);
					update_field('pi_geographic_location',$job->job_area , $job_inserted_id);
					update_field('pi_job_type',$job->job_type , $job_inserted_id);
					update_field('pi_contract_length', $job->job_length, $job_inserted_id);
					update_field('pi_minimum_salary', $job->minimum_salary, $job_inserted_id);
					update_field('pi_maximum_salary', $job->maximum_salary, $job_inserted_id);
					update_field('pi_closing_date', $job->closing_date, $job_inserted_id);
					update_field('pi_contact_phone', $job->contact_phone, $job_inserted_id);
					update_field('pi_contact_email', $job->contact_email, $job_inserted_id);
					update_field('pi_jobs_employer', $job->employer, $job_inserted_id);
					update_field('pi_jobs_apply_count', $job->applications, $job_inserted_id);
					update_post_meta( $job_inserted_id, 'django_event_id', $job->id );
					if(! empty($job->visible)){
						update_post_meta( $job_inserted_id, 'django_event_visible', $job->visible );
					}
					//update_post_meta( $job_inserted_id, 'django_event_visible', $job->visible );
					update_post_meta( $job_inserted_id, 'django_target_region', $job->target_region );
					update_post_meta( $job_inserted_id, 'django_product_id', $job->product_id );
					update_post_meta( $job_inserted_id, 'django_jobs_recruiter_id', $job->account_id );

					//$this->generate_featured_image( 'https://performancein.com/assets/' . $job->image, $job_inserted_id );

				}
				$success_count++;


				if (!empty($job_inserted_id)) {
					$success_count++;
					WP_CLI::success( 'inserted new post id = ' .  $job_inserted_id);
				} else {
					$failed_count++;

					WP_CLI::success( 'Fail number of post' . $failed_count);

				}
			}



		}

		public function generate_featured_image( $image_url, $post_id  ){

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
				if ( file_exists( $file ) ) {
					$file = str_replace( '.', '__pi.', $file );
				}
				file_put_contents( $file, $image_data );
				$wp_filetype = wp_check_filetype($filename, null );
				$attachment = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => sanitize_file_name($filename),
					'post_content' => '',
					'post_status' => 'inherit'
				);
				$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
				$res1= wp_update_attachment_metadata( $attach_id, $attach_data );
				$res2= set_post_thumbnail( $post_id, $attach_id );
				update_field('pi_event_image', $attach_id, $post_id);
			}
		}

		public function delete_events(){

			for ($i=1538; $i<2000; $i++){
				wp_delete_post( $i, true );
				WP_CLI::success( 'deleted id = ' .  $i);
			}


		}

		/*
		 * Update the job date as per the old database
		 */
		/*public function pi_update_jobs() {
			global $wpdb;

			$get_jobs = "SELECT * FROM `wp_posts` WHERE post_type='pi_jobs' AND post_status = 'publish' ";
			$all_job_list = $wpdb->get_results( $get_jobs );

			$success_count = 0;
			$failed_count = 0;

			foreach ( $all_job_list as $job ) {
				
//				if ($success_count === 2) {
//					break;
//				}

				$job_ID = $job->ID;
				if( isset( $job_ID )  &&  !empty( $job_ID ) ) {
					$results = $wpdb->get_results( "select meta_value from $wpdb->postmeta where meta_key = 'django_event_id' AND post_id = '" . $job_ID . "'" );
					if ( isset( $results ) && is_array( $results ) ) {
						$old_job_id   = $results[0]->meta_value;
						$old_job_data = $wpdb->get_results( "select * from monetisation_job where id = '" . $old_job_id . "'" );
						if ( isset( $old_job_data ) && is_array( $old_job_data ) ) {
							$new_date = $old_job_data[0]->date_added;
							$my_post  = array(
								'ID'                => $job_ID,
								'post_date'         => $new_date,
								'post_date_gmt'     => $new_date,
								'post_modified'     => $new_date,
								'post_modified_gmt' => $new_date,
							);
							// Update the post into the database
							wp_update_post( $my_post );
							$success_count++;
							WP_CLI::success( 'Updated post id = ' .  $job_ID );
						} else {
							$failed_count++;
							WP_CLI::error( 'Fail number of post = ' . $job_ID );
						}
					}
				}
			}
		}*/
	}

	WP_CLI::add_command( 'performance', 'ImportJob' );

}


