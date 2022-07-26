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

	class ImportArticleCLI {

		public function __construct() {

			// example constructor called when plugin loads

		}

		function import_article() {
			$start_time = microtime( true );
			global $wpdb;

			$get_all_article      = 'SELECT * FROM `content_article` WHERE id= 7371';
			$content_article_list = $wpdb->get_results( $get_all_article );

			$success_count = 0;
			$failed_count  = 0;

			//echo "<pre>"; print_r($content_article_list); exit;

			foreach ( $content_article_list as $article ) {


//				if ($success_count === 3) {
//					break;
//				}

				$author_id = $article->author_id;

				$user = get_users(
					array(
						'meta_key'    => 'django_user_id',
						'meta_value'  => $author_id,
						'number'      => 1,
						'count_total' => false
					) );

				$user_data = $user[0]->data;

				$get_all_category_list = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `content_globalcategoryarticle` WHERE `article_id` = %d', $article->id ) );

				// echo "<pre>"; print_r($get_all_category_list); exit;

				$category_array = array();

				foreach ( $get_all_category_list as $category ) {
					$pi_table_get_resource_term = $wpdb->prepare( "SELECT term_id FROM `wp_termmeta` WHERE meta_value = %d and meta_key = 'pi_category_id'", $category->globalcategory_id );
					//echo $pi_table_get_resource_term; exit;

					$pi_table_get_resources_terms_id = $wpdb->get_var( $pi_table_get_resource_term );

					$category_array[] = $pi_table_get_resources_terms_id;
				}
				//echo "<pre>"; print_r($category_array); exit;

				$defaults = array(
					'post_author'  => $user_data->ID,
					'post_content' => $article->content,
					'post_title'   => $article->title,
					'post_excerpt' => $article->summary,
					'post_name'    => $article->slug,
					'post_date'    => $article->scheduled_time,
					'post_status'  => ( $article->active === 0 ) ? 'draft' : 'publish',
					'post_type'    => 'post',
				);

				$post_new_id = wp_insert_post( $defaults );


				if ( ! empty( $post_new_id ) ) {
					$post_meta_arr = array(
						'django_id'                     => $article->id,
						'django_author_id'              => $article->author_id,
						'django_image'                  => $article->image,
						'django_active'                 => $article->active,
						'django_image_credit'           => $article->image_credit,
						'django_view_count'             => $article->view_count,
						'django_is_press_release'       => $article->is_press_release,
						//'django_related_event_id' => $article->related_event_id,
						'django_available_to_home_page' => $article->available_to_home_page,
						'django_poll_id'                => $article->poll_id,
						'django_highlight'              => $article->highlight,
						'django_sponsored'              => $article->sponsored,
						'django_primary_category_id'    => $article->primary_category_id,
						'django_video'                  => $article->video,
						'django_hide_image'             => $article->hide_image,

					);

					foreach ( $post_meta_arr as $key => $value ) {
						update_post_meta( $post_new_id, $key, $value );
					}

					//$related_event_id_django = $wpdb->prepare("SELECT post_id FROM `wp_postmeta` WHERE meta_value = %d and meta_key = 'django_event_id'", $article->related_event_id);
					//$related_event_post_id = $wpdb->get_var($related_event_id_django);

					$pi_django_resource_id = $wpdb->prepare( "SELECT post_id FROM `wp_postmeta` WHERE meta_value = %d and meta_key = 'pi_django_resource_id'", $article->related_resource_id );
					$resource_post_id      = $wpdb->get_var( $pi_django_resource_id );

					$post_update_field = array(
						//'pi_article_related_event' => $related_event_post_id,
						'pi_related_resources' => $resource_post_id,

					);

					foreach ( $post_update_field as $key => $value ) {
						update_field( $key, $value, $post_new_id );
					}

					if ( '1' === $article->sponsored ) {
						$sponsored_value = 'enable';
						$sponsored_val   = maybe_serialize( $sponsored_value );
						update_field( 'pi_sponsored', $sponsored_val, $post_new_id );
					} else {
						update_field( 'pi_sponsored', '', $post_new_id );
					}

					if ( '1' === $article->highlight ) {
						stick_post( $post_new_id );
					}


					wp_set_post_terms( $post_new_id, $category_array, 'category', true );

					// image upload
					if ( ! empty( $article->image ) ) {
						$image_url = 'https://performancein.com/assets/' . $article->image;

						$this->generate_attachment( $image_url, $post_new_id, 'pi_article_image', '' );
					}

				} else {
					WP_CLI::warning( 'Failed to create post' );
				}


				if ( ! empty( $post_new_id ) ) {
					$success_count ++;
					WP_CLI::success( $post_new_id . " added!!" );
				} else {
					$failed_count ++;
					WP_CLI::warning( 'Failed to post' );
				}
			}

			$end_time       = microtime( true );
			$execution_time = ( $end_time - $start_time );

			WP_CLI::success( $execution_time . " Execution time of script in sec." );

		}

		function pi_get_post_idsby_django() {
			global $wpdb;

			$allPostsIDs          = $this->pi_get_post_idsby_django_fun();
			$get_all_article      = 'SELECT id FROM `content_article`';
			$content_article_list = $wpdb->get_results( $get_all_article );
			$getArray             = array();
			$getArrayWPDjangoIDS  = array();
			foreach ( $content_article_list as $djangoID ) {
				$getArray[] = $djangoID->id;
			}
			foreach ( $allPostsIDs as $allPostsID ) {
				$getDjangoIDByWp       = get_post_meta( $allPostsID, 'django_id', true );
				$getArrayWPDjangoIDS[] = $getDjangoIDByWp;
			}
			$diff_result = array_diff( $getArray, $getArrayWPDjangoIDS );
			echo '<pre>';
			print_r( $diff_result );
			echo '</pre>';
			exit;
			/*$arrayCompare = arra
			foreach ($allPostsIDs as $allPostsID){
				$getDjangoID = get_post_meta($allPostsID,'django_id', true);
			}*/

			/*$get_all_article      = 'SELECT id FROM `content_article`';
			$content_article_list = $wpdb->get_results( $get_all_article );
			foreach ($content_article_list as $djangoID) {
				$allPostsID = $this->pi_get_post_idsby_django_fun();
				if(in_array('','',false))






			}*/


		}

		protected function pi_get_post_idsby_django_fun() {
			$params   = array(
				'post_type'      => 'post',
				'meta_query'     => 'django_id',
				'fields'         => 'ids',
				'posts_per_page' => - 1
			);
			$wc_query = new WP_Query( $params );
			$postsIds = $wc_query->posts;
			wp_reset_postdata();
			wp_reset_query();

			return isset( $postsIds ) ? $postsIds : false;
		}

		function patch_import_artical() {
			$start_time = microtime( true );
			global $wpdb;
			$success_count        = 0;
			$get_all_article      = 'SELECT * FROM `content_article` WHERE video != " "';
			$content_article_list = $wpdb->get_results( $get_all_article, ARRAY_A );
			$success_count        = 0;
			$failed_count         = 0;
			foreach ( $content_article_list as $article ) {
//				if ($success_count === 3) {
//					break;
//				}
				$ArticleID      = $article['id'];
				$ArticleWPID    = $this->pi_get_post_id_by_djongo_pid( $ArticleID );
				$ArticleWPImage = get_post_meta( $ArticleWPID, 'django_image', true );
				$ArticleWPVideo = get_post_meta( $ArticleWPID, 'django_video', true );
				if ( ! empty( $ArticleWPVideo ) ) {
					update_field( 'pi_article_banner_section_choices', 'video', $ArticleWPID );
					update_field( 'pi_article_video', $ArticleWPVideo, $ArticleWPID );
					$image_url = 'https://performancein.com/assets/' . $ArticleWPImage;
					$this->generate_attachment( $image_url, $ArticleWPID, 'pi_article_video_thumbnail' );
					update_post_meta( $ArticleWPID, 'pi_article_banner_section_choices', 'video' );
					update_post_meta( $ArticleWPID, 'pi_article_video', $ArticleWPVideo );
					WP_CLI::success( 'Update post id = ' . $ArticleWPID );
				} else {
					WP_CLI::warning( 'Not Update post id = ' . $ArticleWPID );
				}
				$success_count ++;
			}
			$end_time       = microtime( true );
			$execution_time = ( $end_time - $start_time );
			WP_CLI::success( $execution_time . " Execution time of script in sec." );
		}

		function pi_get_post_id_by_djongo_pid( $ArticleID ) {
			$params   = array(
				'post_type'  => 'post',
				'meta_query' => array(
					array(
						'key'     => 'django_id',
						'value'   => $ArticleID,
						'compare' => '=',
					)
				),
				'fields'     => 'ids'
			);
			$wc_query = new WP_Query( $params );
			$postsIds = $wc_query->posts;
			wp_reset_postdata();
			wp_reset_query();

			return isset( $postsIds[0] ) ? $postsIds[0] : false;
		}

		public function generate_attachment( $image_url, $post_id, $custom_field_key, $pi_image_caption = ' ' ) {

			$newUploadPath = explode( "/", $image_url, - 1 );
			$month         = end( $newUploadPath );
			$year          = prev( $newUploadPath );

			if ( ! empty( $year ) && ! empty( $month ) ) {
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
				$wp_filetype = wp_check_filetype( $filename, null );
				$attachment  = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title'     => sanitize_file_name( $filename ),
					'post_excerpt'   => $pi_image_caption,
					'post_content'   => $pi_image_caption,
					'post_status'    => 'inherit'
				);
				$attach_id   = wp_insert_attachment( $attachment, $file, $post_id );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
				$res1        = wp_update_attachment_metadata( $attach_id, $attach_data );
				$res2        = set_post_thumbnail( $post_id, $attach_id );
				update_field( $custom_field_key, $attach_id, $post_id );
			}
		}

		public function pi_get_all_article_patch() {
			$start_time = microtime( true );
			global $wpdb;
			$success_count = 0;
			$WpIDs         = $this->pi_get_article_id_by_djongo_pid();
			foreach ( $WpIDs as $aid ) {
//				if( $success_count === 10 ) {
//					break;
//				}
				$aid                             = $aid->ID;
				$getPrimaryCategory              = get_post_meta( $aid, 'django_primary_category_id', true );
				$getSponcered                    = get_post_meta( $aid, 'pi_sponsored', true );
				$getDjangoID                     = get_post_meta( $aid, 'django_id', true );
				$getResourceID                   = $wpdb->prepare( "SELECT `related_resource_id` FROM `content_article` WHERE id= %d", $getDjangoID );
				$get_resources_id                = $wpdb->get_var( $getResourceID );
				$pi_table_get_resource_term      = $wpdb->prepare( "SELECT term_id FROM `wp_termmeta` WHERE meta_value = %d and meta_key = 'pi_category_id'", $getPrimaryCategory );
				$pi_table_get_resources_terms_id = $wpdb->get_var( $pi_table_get_resource_term );
				update_field( 'pi_primary_category', $pi_table_get_resources_terms_id, $aid );
				if ( 'enable' === $getSponcered ) {
					update_field( 'pi_sponsored', array( 'sponsored' ), $aid );
				}
				$pi_django_resource_id = $wpdb->prepare( "SELECT post_id FROM `wp_postmeta` WHERE meta_value = %d and meta_key = 'pi_django_resource_id'", $get_resources_id );
				$resource_post_id      = $wpdb->get_var( $pi_django_resource_id );
				update_field( 'pi_related_resources', $resource_post_id, $aid );
				update_post_meta( $aid, 'pi_artical_script_run', 'yes' );
				WP_CLI::success( 'Update post id = ' . $aid );
				$success_count ++;
			}

			$end_time       = microtime( true );
			$execution_time = ( $end_time - $start_time );

			WP_CLI::success( $execution_time . " Execution time of script in sec." );
		}

		public function pi_set_primary_cat_article() {
			$start_time = microtime( true );
			global $wpdb;
			$success_count = 0;
			$postIDs       = $this->get_all_primary_categories();
			foreach ( $postIDs as $postID ) {
				$categories          = wp_get_post_categories( $postID );
				$getPrimaryCategory  = get_post_meta( $postID, 'pi_primary_category', true );
				$pi_primary_category = array(
					$getPrimaryCategory
				);
				if ( ! empty( $pi_primary_category ) ) {
					$categories = array_filter( array_merge( $pi_primary_category, $categories ) );
					$categories = array_unique( $categories );
				}
				wp_set_post_terms( $postID, $categories, 'category', true );
				WP_CLI::success( 'Update post id = ' . $postID );
			}

		}

		public function get_article_date() {
			global $wpdb;
			$getAllPostsIDS = $this->get_all_posts_no_meta_key();
			foreach ( $getAllPostsIDS as $getAllPostsID ) {
				$getDjangoPostID   = get_post_meta( $getAllPostsID, 'django_id', true );
				$articleDateQuery  = 'SELECT scheduled_time FROM  content_article WHERE id=' . $getDjangoPostID;
				$articleDateResult = $wpdb->get_results( $articleDateQuery );
				$articleDate       = $articleDateResult->scheduled_time;
				echo '<pre>';
				print_r( $articleDate );
				echo '</pre>';
				if ( ! empty( $articleDateResult ) ) {

				}

			}
		}

		public function set_excerpt(){
			global $wpdb;
			$getAllPostsIDS = $this->get_all_posts_no_meta_key();
			foreach ( $getAllPostsIDS as $getAllPostsID ) {
				$getDjangoPostID         = get_post_meta( $getAllPostsID, 'django_id', true );
				$djangoIntroQuery = 'SELECT intro FROM content_article WHERE id='.$getDjangoPostID;
				$djangoIntroRes = $wpdb->get_results($djangoIntroQuery);
				if(! empty($djangoIntroRes[0]->intro)){
					$djangoIntro = $djangoIntroRes[0]->intro;
					wp_update_post(
						array(
							'ID'        => $getAllPostsID,
							'post_excerpt' => $djangoIntro
						)
					);
				}

				WP_CLI::success( 'Update post id = ' . $getAllPostsID );

				/*if(! empty($djangoIntroQuery))*/
			}
		}

		public function remove_all_categories() {
			$getAllPostsIDS = $this->get_all_posts_for_cats();
			foreach ( $getAllPostsIDS as $getAllPostsID ) {
				$allCategories = wp_get_post_categories( $getAllPostsID );
				update_post_meta( $getAllPostsID, 'removable_category', 'yes' );
				wp_remove_object_terms( $getAllPostsID, $allCategories, 'category' );
				WP_CLI::success( 'Update post id = ' . $getAllPostsID );


			}
		}

		public function select_while_no_cats_set_affiliate() {
			global $wpdb;
			$getCatsQuery    = 'SELECT id from content_article T1 WHERE NOT EXISTS (SELECT * FROM content_globalcategoryarticle T2 WHERE T1.id = T2.article_id)';
			$getCatsQueryRes = $wpdb->get_results( $getCatsQuery, ARRAY_A );
			foreach ( $getCatsQueryRes as $djanoIDRes ) {
				$djanoID      = $djanoIDRes['id'];
				$getWPIDQuery = $wpdb->prepare( "SELECT post_id FROM `wp_postmeta` WHERE meta_value = %d and meta_key = 'django_id'", $djanoID );
				$getWPID      = $wpdb->get_var( $getWPIDQuery );
				$getPrimaryWpCat = get_post_meta($getWPID,'pi_primary_category', true);
				if( empty($getPrimaryWpCat)) {
					wp_set_post_terms( $getWPID, 104, 'category', true );
				}

			}
		}

		public function select_noraml_categories() {
			global $wpdb;
			$getAllPostsIDS = $this->get_all_posts_for_cats();
			foreach ( $getAllPostsIDS as $getAllPostsID ) {
				$getDjangoPostID         = get_post_meta( $getAllPostsID, 'django_id', true );
				$getPrimaryCategoryQuery = $wpdb->get_results( $wpdb->prepare( 'SELECT primary_category_id FROM `content_article` WHERE `id` = %d', $getDjangoPostID ) );
				$get_all_category_list   = $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM `content_globalcategoryarticle` WHERE `article_id` = %d', $getDjangoPostID ) );
				$Primary_category_array  = array();
				foreach ( $getPrimaryCategoryQuery as $category ) {
					$pi_table_get_term        = $wpdb->prepare( "SELECT term_id FROM `wp_termmeta` WHERE meta_value = %d and meta_key = 'pi_category_id'", $category->primary_category_id );
					$pi_table_get_terms_id    = $wpdb->get_var( $pi_table_get_term );
					$Primary_category_array[] = $pi_table_get_terms_id;
				}

				$Primary_category_array = array_filter( $Primary_category_array );
				$category_array         = array();
				foreach ( $get_all_category_list as $category ) {
					$pi_table_get_resource_term      = $wpdb->prepare( "SELECT term_id FROM `wp_termmeta` WHERE meta_value = %d and meta_key = 'pi_category_id'", $category->globalcategory_id );
					$pi_table_get_resources_terms_id = $wpdb->get_var( $pi_table_get_resource_term );
					$category_array[]                = $pi_table_get_resources_terms_id;
				}
				$allCategories = array_merge( $Primary_category_array, $category_array );
				$allCategories = array_unique( $allCategories );
				update_post_meta( $getAllPostsID, 'removable_category', 'yes' );
				wp_set_post_terms( $getAllPostsID, $allCategories, 'category', true );
				WP_CLI::success( 'Update post id = ' . $getAllPostsID );

			}
		}

		public function pi_changed_trashed_url() {
			$start_time = microtime( true );
			global $wpdb;
			$success_count = 0;
			$postIDs       = $this->get_all_posts_no_meta_key();
			foreach ( $postIDs as $postID ) {
				/*if( $success_count === 2 ) {
					break;
					WP_CLI::success( 'Total Updated post = ' . $success_count );
				}*/
				$post_slug  = get_post_field( 'post_name', $postID );
				$trashedURL = str_replace( '__trashed', '', $post_slug );
				/*$wpdb->query( $wpdb->prepare(
					"UPDATE $wpdb->posts SET post_name =" . $trashedURL . " WHERE ID = %d",
					$postID
				)
				);*/
				wp_update_post(
					array(
						'ID'        => $postID,
						'post_name' => $trashedURL
					)
				);
				WP_CLI::success( 'Update post id = ' . $postID );
				$success_count ++;
			}
			WP_CLI::success( 'Total Updated post = ' . $success_count );
		}

		protected function get_all_posts_no_meta_key() {
			$args     = array(
				'post_type'      => 'post',
				'posts_per_page' => - 1,
				'fields'         => 'ids',
			);
			$wc_query = new WP_Query( $args );
			$postsIds = $wc_query->posts;
			wp_reset_postdata();
			wp_reset_query();

			return isset( $postsIds ) ? $postsIds : false;
		}

		protected function get_all_posts_for_cats() {
			$args     = array(
				'post_type'      => 'post',
				'posts_per_page' => - 1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'     => 'removable_category',
						'compare' => 'NOT EXISTS'
					)
				)
			);
			$wc_query = new WP_Query( $args );
			$postsIds = $wc_query->posts;
			wp_reset_postdata();
			wp_reset_query();

			return isset( $postsIds ) ? $postsIds : false;
		}

		protected function get_all_primary_categories() {
			$args     = array(
				'post_type'      => 'post',
				'posts_per_page' => - 1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'     => 'pi_primary_category',
						'value'   => '',
						'compare' => '!='
					)
				)
			);
			$wc_query = new WP_Query( $args );
			$postsIds = $wc_query->posts;
			wp_reset_postdata();
			wp_reset_query();

			return isset( $postsIds ) ? $postsIds : false;
		}

		function patch_gallery_artical_update() {
			$start_time = microtime( true );
			global $wpdb;
			$params          = array(
				'post_type'      => 'post',
				'meta_query'     => array(
					array(
						'key' => 'django_id',
					)
				),
				'posts_per_page' => '-1',
				'fields'         => 'ids'
			);
			$get_all_article = get_posts( $params );
			$postsIds        = $get_all_article;
			foreach ( $postsIds as $articles ) {
				$getScriptStatus = get_post_meta( $articles, 'pi_gallery_script_run', true );
				if ( empty( $getScriptStatus ) ) {
					$data_test         = get_post_meta( $articles, 'django_id', true );
					$pi_gallery_id     = $wpdb->prepare( "SELECT image, caption FROM `content_galleryitem` WHERE article_id = %d", $data_test );
					$pi_gallery_images = $wpdb->get_results( $pi_gallery_id );
					if ( is_array( $pi_gallery_images ) && ! empty( $pi_gallery_images ) ) {
						$image_counter     = 0;
						$pi_gallery_allids = array();
						foreach ( $pi_gallery_images as $pi_gallery_image ) {
							if ( isset( $pi_gallery_image->image ) && ! empty( $pi_gallery_image->image ) ) {
								if ( $image_counter === 0 ) {
									$front_image_gallery           = get_post_meta( $articles, 'django_image', true );
									$front_image_gallery_image_url = 'https://performancein.com/assets/' . $front_image_gallery;
									$this->generate_attachment( $front_image_gallery_image_url, $articles, 'pi_article_image_gallery_thumbnail', '' );
								}
								$image_url     = 'https://performancein.com/assets/' . $pi_gallery_image->image;
								$image_caption = $pi_gallery_image->caption;
								if ( ! isset( $image_caption ) && empty( $image_caption ) ) {
									$image_caption = ' ';
								}
								$pi_gallery_allids[] = $this->gallery_generate_attachment( $image_url, $image_caption, $articles );
								$image_counter ++;
							}
						}
						update_field( 'pi_article_banner_section_choices', 'gallery', $articles );
						update_field( 'pi_article_image_galleries', $pi_gallery_allids, $articles );
						update_post_meta( $articles, 'pi_article_banner_section_choices', 'gallery' );
						update_post_meta( $articles, 'pi_article_image_galleries', $pi_gallery_allids );
						update_post_meta( $articles, 'pi_gallery_script_run', 'yes' );
						WP_CLI::success( "Artical gallery Updated : " . $articles );
					}
				}

			}
			$end_time       = microtime( true );
			$execution_time = ( $end_time - $start_time );

			WP_CLI::success( $execution_time . " Execution time of script in sec." );

		}

		function pi_get_gallery_all_images() {
			global $wpdb;
			$start_time           = microtime( true );
			$pi_gallery_id        = 'SELECT article_id, image, caption FROM `content_galleryitem` WHERE id BETWEEN 1188 AND 1209';
			$pi_gallery_imagesRes = $wpdb->get_results( $pi_gallery_id, ARRAY_A );
			$image_counter        = 0;
			$pi_gallery_allids    = array();
			$piMainGallery        = array();
			$tes1                 = 0;
			$test                 = 0;
			$demo                 = array();
			foreach ( $pi_gallery_imagesRes as $pi_gallery_image ) {
				if ( $tes1 === 0 && $test === 0 || $test === $pi_gallery_image['article_id'] ) {
					$tes1   = 1;
					$test   = $pi_gallery_image['article_id'];
					$demo[] = $pi_gallery_image['article_id'];

				} else {
					$tes1              = 0;
					$test              = 0;
					$pi_gallery_allids = array();
				}

				$getDjangoID                   = $pi_gallery_image['article_id'];
				$getDjangoImage                = $pi_gallery_image['image'];
				$getDjangoCaption              = $pi_gallery_image['caption'];
				$getWPIDQuery                  = $wpdb->prepare( "SELECT post_id FROM `wp_postmeta` WHERE meta_value = %d and meta_key = 'django_id'", $getDjangoID );
				$getWPID                       = $wpdb->get_var( $getWPIDQuery );
				$front_image_gallery           = get_post_meta( $getWPID, 'django_image', true );
				$front_image_gallery_image_url = 'https://performancein.com/assets/' . $front_image_gallery;
				$this->generate_attachment( $front_image_gallery_image_url, $getWPID, 'pi_article_image_gallery_thumbnail', '' );
				$image_url           = 'https://performancein.com/assets/' . $getDjangoImage;
				$image_caption       = $getDjangoCaption;
				$pi_gallery_allids[] = $this->gallery_generate_attachment( $image_url, $image_caption, $getWPID );
				update_field( 'pi_article_banner_section_choices', 'gallery', $getWPID );
				update_field( 'pi_article_image_galleries', $pi_gallery_allids, $getWPID );
				update_post_meta( $getWPID, 'pi_article_banner_section_choices', 'gallery' );
				update_post_meta( $getWPID, 'pi_article_image_galleries', $pi_gallery_allids );
				update_post_meta( $getWPID, 'pi_gallery_script_run', 'yes' );

				WP_CLI::success( "Artical gallery Updated : " . $getWPID );


			}


			$end_time       = microtime( true );
			$execution_time = ( $end_time - $start_time );

			WP_CLI::success( $execution_time . " Execution time of script in sec." );
		}

		public function gallery_generate_attachment( $image_url, $pi_image_caption, $post_id ) {

			$newUploadPath = explode( "/", $image_url, - 1 );
			$month         = end( $newUploadPath );
			$year          = prev( $newUploadPath );

			if ( ! empty( $year ) && ! empty( $month ) ) {
				$upload_dir = wp_upload_dir( $year . '/' . $month );
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
					'post_excerpt'   => $pi_image_caption,
					'post_content'   => $pi_image_caption,
					'post_status'    => 'inherit'
				);
				$attach_id   = wp_insert_attachment( $attachment, $file );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

				return $attach_id;
			}
		}

		protected function pi_get_article_id_by_djongo_pid() {

			$params   = array(
				'post_status'    => 'publish',
				'post_type'      => 'post',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'relation' => 'OR',
						array(
							'key'     => 'pi_artical_script_run',
							'value'   => 'yes',
							'compare' => '!=',
						),
						array(
							'key'     => 'pi_artical_script_run',
							'compare' => 'NOT EXISTS',
						)
					)
				)
			);
			$wc_query = new WP_Query( $params );
			$postsIds = $wc_query->posts;
			wp_reset_postdata();
			wp_reset_query();

			return isset( $postsIds ) ? $postsIds : false;
		}

		public function pi_set_partner_profile_article() {
			$start_time = microtime( true );
			global $wpdb;
			$success_count = 0;
			$WpIDs         = $this->pi_get_partner_article_id_by_djongo_pid();
			foreach ( $WpIDs as $aid ) {
				/*if ( $success_count === 10 ) {
					break;
				}*/
				$aid           = $aid->ID;
				$getDjangoID   = get_post_meta( $aid, 'django_id', true );
				$getPartnerObj = $wpdb->prepare( "SELECT `partner_profile_id` FROM `content_article` WHERE id= %d", $getDjangoID );
				$getPartnerIDs = $wpdb->get_var( $getPartnerObj );
				/*update_post_meta($aid,'django_partner_profile',$getPartnerIDs);*/
				$piPartnerWPObj = $wpdb->prepare( "SELECT post_id FROM `wp_postmeta` WHERE meta_value = %d and meta_key = 'django_supplier_id'", $getPartnerIDs );
				$PartnerId      = $wpdb->get_var( $piPartnerWPObj );
				if ( ! empty( $PartnerId ) && isset( $PartnerId ) ) {
					update_field( 'pi_partners', $PartnerId, $aid );
				}
				update_post_meta( $aid, 'pi_artical_partners_script_run', 'yes' );
				$success_count ++;
				WP_CLI::success( 'Update post id = ' . $aid );
			}
			WP_CLI::success( 'total count = ' . $success_count );

			$end_time       = microtime( true );
			$execution_time = ( $end_time - $start_time );

			WP_CLI::success( $execution_time . " Execution time of script in sec." );
			die();
		}

		public function pi_update_artcle_author() {
			global $wpdb;
			$start_time    = microtime( true );
			$success_count = 0;

			/*$args = array(
				'role' => '',
				'role__in' => array('customer'),
				'role__not_in' => array(),
				'meta_key' => 'django_user_id',
				'meta_value' => '',
				'meta_compare' => '',
				'meta_query'     => array(
					'relation' => 'AND',
					array(

						array(
							'key'     => 'post_author_updated',
							'value'   => 'yes',
							'compare' => '!=',
						),

					)
				),
				'date_query' => array(),
				'include' => array(),
				'exclude' => array(),
				'orderby' => 'login',
				'order' => 'ASC',
				'offset' => '',
				'search' => '',
				'number' => '',
				'count_total' => false,
				'fields' => 'all',
				'who' => ''
			);*/

//			$args = array(
//				'role' => '',
//				'role__in' => array('customer'),
//				'role__not_in' => array(),
//				'fields' => 'ids',
//				'meta_query'        => array(
//					array(
//						'key'     => 'post_author_updated',
//						'compare' => 'NOT EXISTS',
//					)
//				)
//
//			);
//
//
//
//			$user = get_users($args);
//
//			global $wpdb;

//			$count = $wpdb->get_var( $wpdb->prepare(
//				"SELECT COUNT(*) FROM $wpdb->users
//		LEFT JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id
//		WHERE meta_key  NOT EXISTS 'post_author_updated'
//		"));
			$args1 = array(
				'role__in'     => array( 'author' ),
				'meta_key'     => 'post_author_updated',
				'meta_compare' => 'NOT EXISTS', // exact match only
				'number'       => 1100,
			);

			$user_query = new WP_User_Query( $args1 );
			$users      = (array) $user_query->get_results(); // return: Array of WP_User objects
			// return: 15
//			foreach($users as $user_id){
//			echo '<br/>'.'<pre>'.$user_id->ID.'</pre>';
//				update_user_meta($user_id->ID,'post_author_updated','yes');
//
//			}

//			echo $user_query->get_total(); // return: 86
//
//		//	echo print_r( $user_query,true );
//			die();

//			$user = get_users(
//				array(
//					'meta_key'    => 'django_user_id',
//					'role' => 'customer',
//					'count_total' => false,
//					'query_id' => 'authors_with_posts',
//					'compare' => 'EXISTS',
//					'number' => 8000,
//					'meta_query'     => array(
//						'relation' => 'AND',
//						array(
//							'relation' => 'OR',
//							array(
//								'key'     => 'post_author_updated',
//								'value'   => 'yes',
//								'compare' => '!=',
//							),
//							array(
//								'key'     => 'post_author_updated',
//								'compare' => 'NOT EXISTS',
//							)
//						)
//					)
//				) );
			foreach ( $users as $user_id ) {
//				$rolesArray = $users->roles;
//				$roles      = $rolesArray[0];
//				$user_data  = $users->data;
				$defaults = array(
					'author'    => $user_id->ID,
					'post_type' => 'post',
					'fields'    => 'ids'
				);
				//if ( 'customer' === $roles ) {


				$jango_id = get_user_meta( $user_id->ID, 'django_user_id', true );


				$getPosts = get_posts( $defaults );
				echo '<pre>';
				print_r( "Posts: " . $getPosts );
				echo '</pre>';

				$getPostAuthirObj = $this->pi_get_article_post_author( $jango_id );
				if ( ! empty( $getPosts ) && isset( $getPosts ) ) {
					foreach ( $getPosts as $getPost ) {

						if ( $getPostAuthirObj ) {
							/*$arg = array(
								'ID' => $getPost,
								'post_author' => $getPostAuthirObj,
							);
							wp_update_post( $arg );*/
							/*$wpdb->query(
								$wpdb->prepare(
									"UPDATE $wpdb->posts SET post_author =" . $getPostAuthirObj . " WHERE ID = %d",
									$getPost
								)
							);*/
						}
						/*update_user_meta($getPostAuthirObj,'post_author_updated','yes');*/
						WP_CLI::success( 'Update post id = ' . $getPost );
						WP_CLI::success( 'Update Userid = ' . $getPostAuthirObj );
					}
					WP_CLI::success( 'Update Users id = ' . $user_id->ID );
				} else {
					/*update_user_meta($getPostAuthirObj,'post_author_updated','yes');*/
				}

				//}
				$success_count ++;

			}
			exit;
			WP_CLI::success( 'total count = ' . $success_count );

			$end_time       = microtime( true );
			$execution_time = ( $end_time - $start_time );

			WP_CLI::success( $execution_time . " Execution time of script in sec." );
			die();
		}

		protected function pi_get_article_post_author( $account_user_django_id ) {
			$user = get_users(
				array(
					'meta_key'   => 'django_user_id',
					'meta_value' => $account_user_django_id,
					'number'     => 100
				) );

			return isset( $user ) ? $user[0]->ID : false;
		}

		protected function pi_get_partner_article_id_by_djongo_pid() {

			$params   = array(
				'post_status'    => 'publish',
				'post_type'      => 'post',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'relation' => 'OR',
						array(
							'key'     => 'pi_artical_partners_script_run',
							'value'   => 'yes',
							'compare' => '!=',
						),
						array(
							'key'     => 'pi_artical_partners_script_run',
							'compare' => 'NOT EXISTS',
						)
					)
				)
			);
			$wc_query = new WP_Query( $params );
			$postsIds = $wc_query->posts;
			wp_reset_postdata();
			wp_reset_query();

			return isset( $postsIds ) ? $postsIds : false;
		}

		public function default_category_update() {
			$args             = array(
				'post_type'      => 'post',
				'cat'            => '225',
				'posts_per_page' => '-1',
				'fields'         => 'ids'
			);
			$data_posts       = new WP_Query( $args );
			$postsCategoryIds = $data_posts->posts;
			foreach ( $postsCategoryIds as $postCatId ) {
				$primary_cat = get_field( 'pi_primary_category', $postCatId, true );
				if ( isset( $primary_cat ) && ! empty( $primary_cat ) ) {
					WP_CLI::warning( $postCatId . " Primary Category There!!" );
				} else {
					wp_remove_object_terms( $postCatId, 'performanceIN', 'category' );
					wp_set_post_categories( $postCatId, array( 122 ), false );
					WP_CLI::success( $postCatId . " Updated Category!!" );
				}
			}
		}

		public function image_option_update() {
			$params             = array(
				'post_type'      => 'post',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => 'pi_article_banner_section_choices',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key' => 'django_id',
					),
					array(
						'key'     => 'pi_artical_partners_script_run',
						'compare' => 'NOT EXISTS',
					),
				),
				'fields'         => 'ids'
			);
			$artical_images_ids = get_posts( $params );
			foreach ( $artical_images_ids as $artical_id ) {
				update_field( 'pi_article_banner_section_choices', 'image', $artical_id );
				update_post_meta( $artical_id, 'pi_article_banner_section_choices', 'image' );
				WP_CLI::success( 'Update id = ' . $artical_id );

			}
		}

		public function image_update_for_path() {
			$start_time    = microtime( true );
			$success_count = 0;
			global $wpdb;
			$params             = array(
				'post_type'      => 'post',
				'posts_per_page' => - 1,
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'key'     => 'pi_article_banner_section_choices',
						'compare' => 'NOT EXISTS',
					),
					array(
						'key' => 'django_id',
					),
					array(
						'key'     => 'pi_artical_partners_script_run',
						'compare' => 'NOT EXISTS',
					),
				),
				'fields'         => 'ids'
			);
			$artical_images_ids = get_posts( $params );
			$articlePosts       = $artical_images_ids;
			foreach ( $articlePosts as $artical_id ) {
				$getUserMeta        = get_post_meta( $artical_id, 'django_id', true );
				$article_image      = 'SELECT image FROM `content_article` WHERE video IS NULL AND id=' . $getUserMeta;
				$article_image_list = $wpdb->get_var( $article_image );
				if ( ! empty( $article_image_list ) ) {
					$image_url = 'https://performancein.com/assets/' . $article_image_list;
					$this->artical_attachment( $image_url, $artical_id, 'pi_article_image' );
					$success_count ++;
					update_post_meta( $artical_id, 'pi_artical_partners_script_run', 'yes' );
				}
				WP_CLI::success( "Update Post Image ID : " . $artical_id );
			}
			$end_time       = microtime( true );
			$execution_time = ( $end_time - $start_time );

			WP_CLI::success( "Total Post updated. " . $success_count );
			WP_CLI::success( " Execution time of script in sec." . $execution_time );
		}

		function artical_attachment( $image_url, $post_id, $custom_field_key ) {

			$newUploadPath = explode( "/", $image_url, - 1 );
			$month         = end( $newUploadPath );
			$year          = prev( $newUploadPath );

			if ( ! empty( $year ) && ! empty( $month ) ) {
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

				$wp_filetype = wp_check_filetype( $filename, null );
				$attachment  = array(
					'post_mime_type' => $wp_filetype['type'],
					'post_title'     => sanitize_file_name( $filename ),
					'post_status'    => 'inherit'
				);
				$attach_id   = wp_insert_attachment( $attachment, $file, $post_id );
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
				$res1        = wp_update_attachment_metadata( $attach_id, $attach_data );
				$res2        = set_post_thumbnail( $post_id, $attach_id );
				update_field( $custom_field_key, $attach_id, $post_id );
			}
		}

		function pi_author_selection_in_posts() {
			global $wpdb;
			$getAllArticles = "SELECT `id`, `author_id` FROM `content_article`";
			$getAllArticles = $wpdb->get_results( $getAllArticles, ARRAY_A );
			foreach ( $getAllArticles as $getAllArticle ) {
				$ArticleID         = $getAllArticle['id'];
				$ArticleAuthorID   = $getAllArticle['author_id'];
				$ArticleWPID       = $this->pi_get_post_id_by_djongo_pid( $ArticleID );
				$getAuthordjangoID = $this->pi_get_article_post_author( $ArticleAuthorID );
				echo $ArticleWPID .
				     /*echo $ArticleAuthorID. " === ".$getAuthordjangoID."\n";*/
				     $wpdb->query( $wpdb->prepare(
					     "UPDATE $wpdb->posts SET post_author =" . $getAuthordjangoID . " WHERE ID = %d",
					     $ArticleWPID
				     )
				     );

				update_post_meta( $ArticleWPID, 'pi_article_author_script', 'yes' );
				WP_CLI::success( $ArticleWPID . " Updated post]!!" );
				WP_CLI::success( $getAuthordjangoID . " Updated userMeta!!" );

			}


		}

		function pi_post_inactive_status_change() {
			$getInactivePosts = $this->get_pi_get_inactive_posts();;
			foreach ( $getInactivePosts as $getInactivePostId ) {
				wp_update_post(
					array(
						'ID'          => $getInactivePostId,
						'post_status' => 'draft'
					)
				);
				WP_CLI::success( $getInactivePostId . " Updated post]!!" );
			}

		}

		protected function get_pi_get_inactive_posts() {
			$args     = array(
				'post_type'      => 'post',
				'posts_per_page' => - 1,
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'   => 'django_active',
						'value' => 0,

					)
				)
			);
			$wc_query = new WP_Query( $args );
			$postsIds = $wc_query->posts;
			wp_reset_postdata();
			wp_reset_query();

			return isset( $postsIds ) ? $postsIds : false;
		}

		public function delete_article() {

			for ( $i = 23870; $i < 23906; $i ++ ) {
				wp_delete_post( $i, true );
				WP_CLI::success( 'deleted id = ' . $i );
			}


		}

	}

	WP_CLI::add_command( 'performance', 'ImportArticleCLI' );

}


