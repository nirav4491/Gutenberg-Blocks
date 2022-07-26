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

	class ImportGallery {

		public function __construct() {

			// example constructor called when plugin loads

		}

		public function import_gallery(){
			global $wpdb;

			require_once(ABSPATH . 'wp-admin/includes/media.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			require_once(ABSPATH . 'wp-admin/includes/image.php');


			$img_query = "SELECT * FROM content_galleryitem";
			$result = $wpdb->get_results($img_query);

			$base_domain = "https://performancein.com/assets";


			//echo "<pre>"; print_r($result); exit;
			if ( false !== $result ) {
				foreach ( $result as $gallery ) {
					$ww_image_id  = $gallery->id;
					$image_url    = $base_domain . "/" . $gallery->image;
					//$image_title  = $gallery["title"];
					$image_name   = basename( $gallery->image );
					$post_name    = pathinfo( $gallery->image, PATHINFO_FILENAME);

					$tmp_file = download_url( $image_url );



					if ( ! is_wp_error( $tmp_file ) ) {
						$mime = mime_content_type( $tmp_file );
						//$wp_filetype = wp_check_filetype($image_name, null );

						//echo "<pre>"; print_r($wp_filetype); exit;
						$file_array = array(
							'name'     => $image_name,
							'type'     => $mime,
							'tmp_name' => $tmp_file,
							'error'    => 0,
							'size'     => filesize( $tmp_file ),
						);
						//$media_date = $row["created"];



						$file = wp_handle_sideload( $file_array, array( 'test_form' => false ) );



						if ( isset( $file['error'] ) ) {
							WP_CLI::warning( 'Error uploading file.' );
						} else {
							$attachment = array(
								'post_mime_type' => $file['type'],
								'guid'           => $file['url'],
								'post_title'     => $post_name,
								'post_author'    => 'admin',
							);

							// Save the attachment metadata
							$id = wp_insert_attachment( $attachment, $file['file'] );



							if ( is_wp_error( $id ) ) {
								WP_CLI::warning( 'Error creating attachment.' );
							} else {
								wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file['file'] ) );
								$post_meta_arr = array(
									'_wp_attachment_image_alt' => (empty($gallery->caption))?'':$gallery->caption,
									'django_image' => $gallery->image,
									'django_image_credit' => $gallery->image_credit,
									'django_image_url' => $gallery->image_url,
									'django_visible' => $gallery->visible,
									'django_caption' => $gallery->caption,
									'django_image_license_url' => $gallery->image_license_url,
									'django_article_id' => $gallery->article_id,

								);

								foreach( $post_meta_arr as $key => $value ) {
									update_post_meta( $id, $key, $value );
								}

								$my_image_meta = array(
									'ID'		=> $id,			// Specify the image (ID) to be updated
									'post_excerpt'	=> $gallery->caption,		// Set image Caption (Excerpt) to sanitized title
									'post_content'	=> $gallery->caption,		// Set image Description (Content) to sanitized title
								);

								wp_update_post( $my_image_meta );

								WP_CLI::success( $id . " added!!" );
							}
						}

					} else {
						WP_CLI::warning( 'Could not download image ' . $image_url . '!!' );
					}

				}
			}




		}

		public function delete_gallery(){

			for ($i=2600; $i<3800; $i++){
				wp_delete_attachment( $i, true );
				//wp_delete_post( $i, true );

				WP_CLI::success( 'deleted id = ' .  $i);
			}


		}

	}

	WP_CLI::add_command( 'performance', 'ImportGallery' );

}


