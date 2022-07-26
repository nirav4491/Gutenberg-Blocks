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

	class ImportPartners {

		public function __construct() {

			// example constructor called when plugin loads

		}
		function pi_partner_inactive_status_change(){
			global $wpdb;
			$getInactivePosts = $this->get_pi_partner_get_inactive_posts();
			;foreach ($getInactivePosts as $getInactivePostId){
				$getdJangoSupplierID = get_post_meta($getInactivePosts,'django_supplier_id',true);
				$djangoStatus = "SELECT is_active FROM `partner_directory_supplier` WHERE id=".$getdJangoSupplierID;
				$djangoStatusResult = $wpdb->get_results( $djangoStatus );
				if ( '1' === $djangoStatusResult->is_active ) {
					$defaults = 'publish';
				} else {
					$defaults = 'draft';
				}
				echo '<pre>';
				print_r($getInactivePostId);
				echo '</pre>';
				
				/*wp_update_post(
					array (
						'ID'        => $getInactivePostId,
						'post_status' => $defaults
					)
				);*/
				WP_CLI::success( $getInactivePostId . " Updated post]!!" );
			}

		}
		protected function get_pi_partner_get_inactive_posts(){
			$args =  array(
				'post_type'      => 'pi_partner_networks',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_query' => array(
					array(
						'key' => 'django_supplier_id',
					)
				)
			);
			$wc_query = new WP_Query( $args );
			$postsIds = $wc_query->posts;
			wp_reset_postdata();
			wp_reset_query();

			return isset( $postsIds ) ? $postsIds : false;
		}

		function import_partners() {

			global $wpdb;

//			update_post_meta( 1648,'pi_product_id',5 );
//			update_post_meta( 1647,'pi_product_id',2 );
//			update_post_meta( 1646,'pi_product_id',4 );
//			update_post_meta( 1664,'pi_product_id',3 );
//			die();

			$pi_get_partner      = 'SELECT *,pi_partner.id as object_id, pi_partner.product_id as pid FROM `partner_directory_supplier` as pi_partner INNER JOIN `partner_directory_supplierdirectoryproduct` as pi_product on pi_product.id = pi_partner.product_id';
			$pi_all_partner_list = $wpdb->get_results( $pi_get_partner );

			$success_count = 0;
			$failed_count  = 0;

			foreach ( $pi_all_partner_list as $pi_partner ) {

//				if ( $success_count > 5 ) {
//					break;
//				}
				$account_id = $pi_partner->account_id;

				$user_select = $wpdb->get_results( "select user_id from $wpdb->usermeta where meta_key = 'django_user_account_id' AND meta_value = '" . $account_id . "'", ARRAY_A );

				//$results = $wpdb->get_results( "select post_id from $wpdb->postmeta where meta_key = 'pi_product_uuid' AND meta_value = '".$job->product_id."'", ARRAY_A );
				$results = $wpdb->get_results( "select post_id from $wpdb->postmeta where meta_key = 'pi_product_uuid' AND meta_value = '" . $pi_partner->pid . "'", ARRAY_A );

				$defaults = array(
					'post_title'        => wp_strip_all_tags( $pi_partner->company_name ),
					'post_name'         => $pi_partner->slug,
					'post_type'         => 'pi_partner_networks',
					'post_content'      => $pi_partner->company_description,
					'post_author'       => ( empty( $account_id ) ) ? $account_id : '',
					'post_date'         => $pi_partner->creation_date,
					'post_date_gmt'     => $pi_partner->creation_date,
					'post_modified'     => $pi_partner->last_updated,
					'post_modified_gmt' => $pi_partner->last_updated,
				);

				if ( '1' === $pi_partner->is_active ) {
					$defaults['post_status'] = 'publish';
				} else {
					$defaults['post_status'] = 'draft';
				}

				if ( isset( $user_select ) && ! empty( $user_select ) ) {
//					$admin_role_set = array(
//						'read' => 1,
//						'level_0' => 1
//					);
//
//					$role = 'account';
//					$display_name = 'Account';
//					add_role( $role, $display_name, $admin_role_set );
					$user_new_id = $user_select[0]['user_id'];
					$new_role    = 'account';
					wp_update_user( array( 'ID' => $user_new_id, 'role' => $new_role ) );

				} else {
					$user_new_id = 1;
				}
				$defaults['post_author'] = $user_new_id;

				$pi_partner_inserted_id = wp_insert_post( $defaults );

				//echo $event_inserted_id;exit;
				if ( ! empty( $pi_partner_inserted_id ) ) {

					update_post_meta( $pi_partner_inserted_id,'django_supplier_id',$pi_partner->object_id );

					$pi_tag_get_query     = 'SELECT GROUP_CONCAT(tag_id) as old_tags_id FROM `taggit_taggeditem` WHERE content_type_id = 69 and object_id = ' . $pi_partner->object_id . '';
					$pi_tag_get_resources = $wpdb->get_var( $pi_tag_get_query );

					if ( isset( $pi_tag_get_resources ) && ! empty( $pi_tag_get_resources ) ) {
						$pi_new_tag_get_query     = "SELECT GROUP_CONCAT(term_id) as new_tags_id  FROM `wp_termmeta` WHERE `meta_key` LIKE 'pi_django_term_id' and meta_value IN ($pi_tag_get_resources)";
						$pi_new_tag_get_resources = $wpdb->get_var( $pi_new_tag_get_query );
						$tags_id_data             = explode( ',', $pi_new_tag_get_resources );

						wp_set_post_terms( $pi_partner_inserted_id, $tags_id_data, 'partner_network_tag', false );

						//update_field('pi_partner_key_services_pi_partner_tags', $tags_id_data, $pi_partner_inserted_id );
						$key_services = array(

							'pi_partner_key_services_title' => 'Key Services',
							'pi_partner_tags'               => $tags_id_data,
						);
						update_field( 'pi_partner_key_services', $key_services, $pi_partner_inserted_id );

					}

					if ( isset( $pi_partner->number_of_staff ) && ! empty( $pi_partner->number_of_staff ) ) {
						$staff_no                    = $pi_partner->number_of_staff;
						$staff_val_arr               = array(
							1 => '1-10',
							2 => '10-50',
							3 => '50-200',
							4 => '200-500',
							5 => '500-1000',
							6 => '1000-2000',
							7 => '2000-5000',
							8 => '5000+',
						);
						$pi_partner->number_of_staff = $staff_val_arr[ $staff_no ];
					}

					if ( isset( $pi_partner->custom_header ) && ! empty( $pi_partner->custom_header ) ) {
						$this->generate_attachment( 'https://performancein.com/assets/' . $pi_partner->custom_header, $pi_partner_inserted_id );
					}

					if ( isset( $pi_partner->logo ) && ! empty( $pi_partner->logo ) ) {
						$this->generate_logo( 'https://performancein.com/assets/' . $pi_partner->logo, $pi_partner_inserted_id );
					}

					update_field( 'pi_user_selection', $user_new_id, $pi_partner_inserted_id );
					update_field( 'pi_package_selection', $results[0]['post_id'], $pi_partner_inserted_id );
					update_field( 'pi_facebook_link', $pi_partner->facebook_profile, $pi_partner_inserted_id );
					update_field( 'pi_twitter_link', $pi_partner->twitter_profile, $pi_partner_inserted_id );
					update_field( 'pi_linkedin_link', $pi_partner->linkedin_profile, $pi_partner_inserted_id );

					update_field( 'pi_partner_description_pi_partner_description_title', 'Profile', $pi_partner_inserted_id );
					update_field( 'pi_partner_description_pi_partner_description', $pi_partner->company_description, $pi_partner_inserted_id );

					update_post_meta( $pi_partner_inserted_id,'django_partner_active_status', $pi_partner->is_active );

					$sidebar_content = array(

							'pi_contact_info' =>array(
								'pi_contact_info_title' => 'Contact',
								'pi_website_url'        => $pi_partner->website_url,
								'pi_email_id'           => $pi_partner->company_email,
								'pi_telephone_number'   => $pi_partner->telephone_number,
							),
							'pi_further_info' =>array(
								'pi_further_info_title' => 'Further Info',
								'pi_founded_year'       => $pi_partner->founded_year,
								'pi_number_of_staff'    => $pi_partner->number_of_staff,
							),
							'pi_head_office_info' =>array(
								'pi_head_office_info_title' => 'Head Office',
								'pi_address1'        => $pi_partner->address1,
								'pi_address2'        => $pi_partner->address2,
								'pi_postcode'        => $pi_partner->postcode,
								'pi_city'            => $pi_partner->city,
								'pi_country'         => $pi_partner->country,
								'pi_company_location'=> '',
							)

					);
					update_field( 'pi_partner_sidebar', $sidebar_content, $pi_partner_inserted_id );

					update_field('pi_partner_related_articles', array('yes'), $pi_partner_inserted_id);


					//update_field('pi_partner_sidebar_pi_head_office_info_pi_company_location', $pi_partner->pi_company_location, $pi_partner_inserted_id );

					if ( '1' === $pi_partner->is_approved ) {
						update_field( 'pi_partner_is_conform', 'yes', $pi_partner_inserted_id );
					}


					$key                          = 'pi_partner_description_pi_partner_description_title';
					$pi_client_testimonials_value = array(
						'pi_client_testimonials_title' => 'Client Testimonials',
						'pi_client_testimonial1'       => $pi_partner->client_testimonial_1,
						'pi_client_testimonial2'       => $pi_partner->client_testimonial_2,
						'pi_client_testimonial3'       => $pi_partner->client_testimonial_3,
					);
					update_field( 'pi_client_testimonials', $pi_client_testimonials_value, $pi_partner_inserted_id );

				}
				$success_count ++;


				if ( ! empty( $pi_partner_inserted_id ) ) {
					$success_count ++;
					WP_CLI::success( 'inserted new post id = ' . $pi_partner_inserted_id );
				} else {
					$failed_count ++;
					WP_CLI::success( 'Fail number of post' . $failed_count );
				}
			}


		}

		public function acf_field_key( $field_name, $post_id ) {
			echo "asdsa";
			die();
			if ( $post_id ) {
				return get_field_reference( $field_name, $post_id );
			}

			if ( ! empty( $GLOBALS['acf_register_field_group'] ) ) {

				foreach ( $GLOBALS['acf_register_field_group'] as $acf ) :

					foreach ( $acf['fields'] as $field ) :

						if ( $field_name === $field['name'] ) {
							return $field['key'];
						}

					endforeach;

				endforeach;
			}

			return $field_name;
		}

		public function generate_attachment( $image_url, $post_id ) {
			$upload_dir = wp_upload_dir();
			$image_data = file_get_contents( $image_url );
			$filename   = basename( $image_url );
			if ( wp_mkdir_p( $upload_dir['path'] ) ) {
				$file = $upload_dir['path'] . '/' . $filename;
			} else {
				$file = $upload_dir['basedir'] . '/' . $filename;
			}
			file_put_contents( $file, $image_data );

			$wp_filetype = wp_check_filetype( $filename, null );
			$attachment  = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => sanitize_file_name( $filename ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);
			$attach_id   = wp_insert_attachment( $attachment, $file, $post_id );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			$res1        = wp_update_attachment_metadata( $attach_id, $attach_data );
			$res2        = set_post_thumbnail( $post_id, $attach_id );
			update_field( 'pi_partner_network_banner_image', $attach_id, $post_id );
		}

		public function generate_logo( $image_url, $post_id ) {
			$upload_dir = wp_upload_dir();
			$image_data = file_get_contents( $image_url );
			$filename   = basename( $image_url );
			if ( wp_mkdir_p( $upload_dir['path'] ) ) {
				$file = $upload_dir['path'] . '/' . $filename;
			} else {
				$file = $upload_dir['basedir'] . '/' . $filename;
			}
			file_put_contents( $file, $image_data );

			$wp_filetype = wp_check_filetype( $filename, null );
			$attachment  = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => sanitize_file_name( $filename ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);
			$attach_id   = wp_insert_attachment( $attachment, $file, $post_id );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
			$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
			$res1        = wp_update_attachment_metadata( $attach_id, $attach_data );
			$res2        = set_post_thumbnail( $post_id, $attach_id );
			set_post_thumbnail( $post_id, $attach_id );
		}

		public function delete_events() {

			for ( $i = 1538; $i < 2000; $i ++ ) {
				wp_delete_post( $i, true );
				WP_CLI::success( 'deleted id = ' . $i );
			}


		}

	}

	WP_CLI::add_command( 'performance', 'ImportPartners' );

}
