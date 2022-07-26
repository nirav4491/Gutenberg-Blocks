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

	class ImportEvent {

		public function __construct() {

			// example constructor called when plugin loads

		}

		function import_all_event(){
			global $wpdb;

			$get_all_events = 'SELECT * FROM `content_event`';
			$all_events_list = $wpdb->get_results($get_all_events);

			$success_count = 0;
			$failed_count = 0;


//echo "<pre>"; print_r($all_events_list); echo "</pre>"; exit;


			foreach ($all_events_list as $event) {


//				if ($success_count === 10) {
//					break;
//				}

				$defaults = array(
					'post_content' => $event->description,
					'post_title' => $event->title,
					'post_status' => 'publish',
					'post_type' => 'pi_events',
					'post_name' => $event->slug,
				);

				$event_inserted_id = wp_insert_post($defaults);

				//echo $event_inserted_id;exit;
				if (!empty($event_inserted_id)) {
//		echo "sddssd";
//		$field = get_field_objects('pi_event_location', $event_inserted_id);
//		$field_key = $field['key'];
//		echo "<pre>"; print_r($field); echo "</pre>"; exit;
					update_field('pi_event_location',$event->location , $event_inserted_id);
					update_field('pi_event_hashtag', $event->hashtag, $event_inserted_id);
					update_field('pi_event_start_date', $event->from_date, $event_inserted_id);
					update_field('pi_event_end_date', $event->to_date, $event_inserted_id);
					update_field('pi_event_url', $event->url, $event_inserted_id);
					update_field('pi_event_visible_listing', $event->visible, $event_inserted_id);
					update_post_meta( $event_inserted_id, 'django_event_id', $event->id );
					update_post_meta( $event_inserted_id, 'django_event_visible', $event->visible );
					update_post_meta( $event_inserted_id, 'django_event_internal', $event->internal );

					$this->generate_featured_image( 'https://performancein.com/assets/' . $event->image, $event_inserted_id );

				}
				$success_count++;


				if (!empty($event_inserted_id)) {
					$success_count++;
					WP_CLI::success( 'inserted new post id = ' .  $event_inserted_id);
				} else {
					$failed_count++;

					WP_CLI::success( 'fail new post id = ' .  $event_inserted_id);

				}
			}



		}

		public function generate_featured_image( $image_url, $post_id  ){
			$upload_dir = wp_upload_dir();
			$image_data = file_get_contents($image_url);
			$filename = basename($image_url);
			if(wp_mkdir_p($upload_dir['path']))
				$file = $upload_dir['path'] . '/' . $filename;
			else
				$file = $upload_dir['basedir'] . '/' . $filename;
			file_put_contents($file, $image_data);

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

		public function delete_events(){

			for ($i=1538; $i<2000; $i++){
				wp_delete_post( $i, true );
				WP_CLI::success( 'deleted id = ' .  $i);
			}


		}

	}

	WP_CLI::add_command( 'performance', 'ImportEvent' );

}


