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

	class ExamplePluginWPCLI {

		public function __construct() {

			// example constructor called when plugin loads

		}

		public function pf_user_recruiter_role_insert() {

			global $wpdb;

			$start = microtime( true );

			$get_all_user  = 'SELECT auth_user.*, auth_user_groups.user_id, auth_user_groups.group_id, auth_group.name as role FROM `auth_user`
INNER JOIN auth_user_groups ON auth_user.id=auth_user_groups.user_id
INNER JOIN auth_group ON auth_user_groups.group_id=auth_group.id WHERE auth_group.name = "Recruiter"';
			$user_lists    = $wpdb->get_results( $get_all_user );
			$success_count = 0;
			$failed_count  = 0;
			foreach ( $user_lists as $per_user ) {
				$user_role = $per_user->role;
				if ( "1" == $per_user->is_superuser ) {
					$user_role = 'administrator';
				} elseif ( $per_user->role === 'Authors' ) {
					$user_role = 'author';
				} elseif ( $per_user->role === 'Recruiter' ) {
					$user_role = 'customer';

				} elseif ( $per_user->role === 'Sales' || $per_user->role === 'Content Team' ) {
					$user_role = 'editor';
				}
				if ( ! empty( $per_user->email ) ) {
					if ( email_exists( $per_user->email ) ) {
						$ExistingID = email_exists( $per_user->email );
						$user_meta  = get_userdata( $ExistingID );
						$user_roles = $user_meta->roles;
						$userEmail  = get_user_by( 'email', 'user@example.com' );
						if ( 'customer' === $user_roles[0] || 'account' === $user_roles[0] ) {
							$new_uname         = $per_user->username;
							$lastNameconcatstr = "wordpress";
							$emailString       = "performancein.com";
							$newEmail          = sprintf( '%1$s.%2$s@%3$s', $new_uname, $lastNameconcatstr, $emailString );
							$newEmail          = strtolower( $newEmail );
							$per_user->email   = $newEmail;
						} else {
							WP_CLI::warning( 'not inserted author id = ' . $per_user->username );
						}

					}
				}
				$userdata = array(
					'user_pass'     => '',
					//(string) The plain-text user password.
					'user_login'    => $per_user->username,
					//(string) The user's login username.
					'user_nicename' => $per_user->username,
					//(string) The URL-friendly user name.
					'user_email'    => $per_user->email,
					//(string) The user email address.
					'display_name'  => $per_user->username,
					//(string) The user's display name. Default is the user's username.
					'nickname'      => $per_user->username,
					//(string) The user's nickname. Default is the user's username.
					'first_name'    => $per_user->first_name,
					//(string) The user's first name. For new users, will be used to build the first part of the user's display name if $display_name is not specified.
					'last_name'     => $per_user->last_name,
					//(string) The user's last name. For new users, will be used to build the second part of the user's display name if $display_name is not specified.
					'role'          => $user_role,
					//(string) User's role.
				);

				$get_author_info       = 'SELECT * FROM content_authorprofile WHERE user_id =' . $per_user->id;
				$get_author_info_rsult = $wpdb->get_results( $get_author_info, ARRAY_A );
				$new_user_id           = wp_insert_user( $userdata );
				if ( ! empty( $get_author_info_rsult[0]['twitter'] ) ) {
					update_user_meta( $new_user_id, 'pi_twitter_url', $get_author_info_rsult[0]['twitter'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['lin'] ) ) {
					update_user_meta( $new_user_id, 'pi_linkedin_url', $get_author_info_rsult[0]['lin'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['bio'] ) ) {
					update_user_meta( $new_user_id, 'pi_user_bio', $get_author_info_rsult[0]['bio'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['name'] ) ) {
					update_user_meta( $new_user_id, 'pi_user_name', $get_author_info_rsult[0]['name'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['slug'] ) ) {
					update_user_meta( $new_user_id, 'pi_user_slug', $get_author_info_rsult[0]['slug'] );
				}


				update_user_meta( $new_user_id, 'django_user_id', $per_user->id );
				update_user_meta( $new_user_id, 'is_confirm', 1 );
				update_user_meta( $new_user_id, 'pi_staff_status', $per_user->is_staff );
				update_user_meta( $new_user_id, 'pi_super_user_status', $per_user->is_superuser );
				update_user_meta( $new_user_id, 'pi_is_active', $per_user->is_active );
				update_user_meta( $new_user_id, 'pi_group_id', $per_user->group_id );
				if ( isset( $get_author_info_rsult[0]['image'] ) && ! empty( $get_author_info_rsult[0]['image'] ) ) {

					$this->generate_attachment( 'https://performancein.com/assets/' . $get_author_info_rsult[0]['image'], $new_user_id, 'author_avtar_image' );
				}

				if ( ! is_wp_error( $new_user_id ) ) {

					$update_sql               = $wpdb->prepare( "UPDATE wp_users SET user_pass = %s WHERE ID =%d", $per_user->password, $new_user_id );
					$results_password_updated = $wpdb->query( $update_sql );

					WP_CLI::success( 'Inserted = ' . $new_user_id );
					$success_count ++;
				} else {

					echo $new_user_id->get_error_message();

					WP_CLI::success( 'Not Inserted = ' . $per_user->id );
					$failed_count ++;
				}

			}

			$time_elapsed_secs = microtime( true ) - $start;

			WP_CLI::success( 'executed time = ' . $time_elapsed_secs );

		}

		public function pf_user_sales_role_insert() {

			global $wpdb;

			$start = microtime( true );

			$get_all_user  = 'SELECT auth_user.*, auth_user_groups.user_id, auth_user_groups.group_id, auth_group.name as role FROM `auth_user`
INNER JOIN auth_user_groups ON auth_user.id=auth_user_groups.user_id
INNER JOIN auth_group ON auth_user_groups.group_id=auth_group.id WHERE auth_group.name = "Sales"';
			$user_lists    = $wpdb->get_results( $get_all_user );
			$success_count = 0;
			$failed_count  = 0;
			foreach ( $user_lists as $per_user ) {
				$user_role = $per_user->role;
				if ( "1" == $per_user->is_superuser ) {
					$user_role = 'administrator';
				} elseif ( $per_user->role === 'Authors' ) {
					$user_role = 'author';
				} elseif ( $per_user->role === 'Recruiter' ) {
					$user_role = 'customer';

				} elseif ( $per_user->role === 'Sales' || $per_user->role === 'Content Team' ) {
					$user_role = 'editor';
				}
				if ( ! empty( $per_user->email ) ) {
					if ( email_exists( $per_user->email ) ) {
						$ExistingID = email_exists( $per_user->email );
						$user_meta  = get_userdata( $ExistingID );
						$user_roles = $user_meta->roles;
						$userEmail  = get_user_by( 'email', 'user@example.com' );
						if ( 'customer' === $user_roles[0] || 'account' === $user_roles[0] ) {
							$new_uname         = $per_user->username;
							$lastNameconcatstr = "wordpress";
							$emailString       = "performancein.com";
							$newEmail          = sprintf( '%1$s.%2$s@%3$s', $new_uname, $lastNameconcatstr, $emailString );
							$newEmail          = strtolower( $newEmail );
							$per_user->email   = $newEmail;
						} else {
							WP_CLI::warning( 'not inserted author id = ' . $per_user->username );
						}

					}
				}

				$userdata = array(
					'user_pass'     => '',
					//(string) The plain-text user password.
					'user_login'    => $per_user->username,
					//(string) The user's login username.
					'user_nicename' => $per_user->username,
					//(string) The URL-friendly user name.
					'user_email'    => $per_user->email,
					//(string) The user email address.
					'display_name'  => $per_user->username,
					//(string) The user's display name. Default is the user's username.
					'nickname'      => $per_user->username,
					//(string) The user's nickname. Default is the user's username.
					'first_name'    => $per_user->first_name,
					//(string) The user's first name. For new users, will be used to build the first part of the user's display name if $display_name is not specified.
					'last_name'     => $per_user->last_name,
					//(string) The user's last name. For new users, will be used to build the second part of the user's display name if $display_name is not specified.
					'role'          => $user_role,
					//(string) User's role.
				);

				$get_author_info       = 'SELECT *  FROM `content_authorprofile` WHERE `user_id` =' . $per_user->id;
				$get_author_info_rsult = $wpdb->get_results( $get_author_info, ARRAY_A );
				$new_user_id           = wp_insert_user( $userdata );
				if ( ! empty( $get_author_info_rsult[0]['twitter'] ) ) {
					update_user_meta( $new_user_id, 'pi_twitter_url', $get_author_info_rsult[0]['twitter'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['lin'] ) ) {
					update_user_meta( $new_user_id, 'pi_linkedin_url', $get_author_info_rsult[0]['lin'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['bio'] ) ) {
					update_user_meta( $new_user_id, 'pi_user_bio', $get_author_info_rsult[0]['bio'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['name'] ) ) {
					update_user_meta( $new_user_id, 'pi_user_name', $get_author_info_rsult[0]['name'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['slug'] ) ) {
					update_user_meta( $new_user_id, 'pi_user_slug', $get_author_info_rsult[0]['slug'] );
				}
				update_user_meta( $new_user_id, 'django_user_id', $per_user->id );
				update_user_meta( $new_user_id, 'is_confirm', 1 );
				update_user_meta( $new_user_id, 'pi_staff_status', $per_user->is_staff );
				update_user_meta( $new_user_id, 'pi_super_user_status', $per_user->is_superuser );
				update_user_meta( $new_user_id, 'pi_is_active', $per_user->is_active );
				update_user_meta( $new_user_id, 'pi_group_id', $per_user->group_id );
				if ( isset( $get_author_info_rsult[0]['image'] ) && ! empty( $get_author_info_rsult[0]['image'] ) ) {
					$this->generate_attachment( 'https://performancein.com/assets/' . $get_author_info_rsult[0]['image'], $new_user_id, 'author_avtar_image' );
				}

				if ( ! is_wp_error( $new_user_id ) ) {

					$update_sql               = $wpdb->prepare( "UPDATE wp_users SET user_pass = %s WHERE ID =%d", $per_user->password, $new_user_id );
					$results_password_updated = $wpdb->query( $update_sql );

					WP_CLI::success( 'Inserted = ' . $new_user_id );
					$success_count ++;
				} else {

					echo $new_user_id->get_error_message();

					WP_CLI::success( 'Not Inserted = ' . $per_user->id );
					$failed_count ++;
				}

			}

			$time_elapsed_secs = microtime( true ) - $start;

			WP_CLI::success( 'executed time = ' . $time_elapsed_secs );

		}

		public function pf_user_author_role_insert() {

			global $wpdb;

			$start = microtime( true );

			$get_all_user  = 'SELECT auth_user.*, auth_user_groups.user_id, auth_user_groups.group_id, auth_group.name as role FROM `auth_user`
INNER JOIN auth_user_groups ON auth_user.id=auth_user_groups.user_id
INNER JOIN auth_group ON auth_user_groups.group_id=auth_group.id WHERE auth_group.name = "Authors"';
			$user_lists    = $wpdb->get_results( $get_all_user );
			$success_count = 0;
			$failed_count  = 0;
			foreach ( $user_lists as $per_user ) {
				$user_role = $per_user->role;
				if ( "1" == $per_user->is_superuser ) {
					$user_role = 'administrator';
				} elseif ( $per_user->role === 'Authors' ) {
					$user_role = 'author';
				} elseif ( $per_user->role === 'Recruiter' ) {
					$user_role = 'customer';

				} elseif ( $per_user->role === 'Sales' || $per_user->role === 'Content Team' ) {
					$user_role = 'editor';
				}
				if ( ! empty( $per_user->email ) ) {
					if ( email_exists( $per_user->email ) ) {
						$ExistingID = email_exists( $per_user->email );
						$user_meta  = get_userdata( $ExistingID );
						$user_roles = $user_meta->roles;
						$userEmail  = get_user_by( 'email', 'user@example.com' );
						if ( 'customer' === $user_roles[0] || 'account' === $user_roles[0] ) {
							$new_uname         = $per_user->username;
							$lastNameconcatstr = "wordpress";
							$emailString       = "performancein.com";
							$newEmail          = sprintf( '%1$s.%2$s@%3$s', $new_uname, $lastNameconcatstr, $emailString );
							$newEmail          = strtolower( $newEmail );
							$per_user->email   = $newEmail;
						} else {
							WP_CLI::warning( 'not inserted author id = ' . $per_user->username );
						}

					}
				}

				$userdata = array(
					'user_pass'     => '',
					//(string) The plain-text user password.
					'user_login'    => $per_user->username,
					//(string) The user's login username.
					'user_nicename' => $per_user->username,
					//(string) The URL-friendly user name.
					'user_email'    => $per_user->email,
					//(string) The user email address.
					'display_name'  => $per_user->username,
					//(string) The user's display name. Default is the user's username.
					'nickname'      => $per_user->username,
					//(string) The user's nickname. Default is the user's username.
					'first_name'    => $per_user->first_name,
					//(string) The user's first name. For new users, will be used to build the first part of the user's display name if $display_name is not specified.
					'last_name'     => $per_user->last_name,
					//(string) The user's last name. For new users, will be used to build the second part of the user's display name if $display_name is not specified.
					'role'          => $user_role,
					//(string) User's role.
				);

				$get_author_info       = 'SELECT * FROM content_authorprofile WHERE user_id =' . $per_user->id;
				$get_author_info_rsult = $wpdb->get_results( $get_author_info, ARRAY_A );
				$new_user_id           = wp_insert_user( $userdata );
				if ( ! empty( $get_author_info_rsult[0]['twitter'] ) ) {
					update_user_meta( $new_user_id, 'pi_twitter_url', $get_author_info_rsult[0]['twitter'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['lin'] ) ) {
					update_user_meta( $new_user_id, 'pi_linkedin_url', $get_author_info_rsult[0]['lin'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['bio'] ) ) {
					update_user_meta( $new_user_id, 'pi_user_bio', $get_author_info_rsult[0]['bio'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['name'] ) ) {
					update_user_meta( $new_user_id, 'pi_user_name', $get_author_info_rsult[0]['name'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['slug'] ) ) {
					update_user_meta( $new_user_id, 'pi_user_slug', $get_author_info_rsult[0]['slug'] );
				}


				update_user_meta( $new_user_id, 'django_user_id', $per_user->id );
				update_user_meta( $new_user_id, 'is_confirm', 1 );
				update_user_meta( $new_user_id, 'pi_staff_status', $per_user->is_staff );
				update_user_meta( $new_user_id, 'pi_super_user_status', $per_user->is_superuser );
				update_user_meta( $new_user_id, 'pi_is_active', $per_user->is_active );
				update_user_meta( $new_user_id, 'pi_group_id', $per_user->group_id );
				/*if ( isset( $get_author_info_rsult[0]['image'] ) && ! empty( $get_author_info_rsult[0]['image'] ) ) {

					$this->generate_attachment( 'https://performancein.com/assets/' . $get_author_info_rsult[0]['image'], $new_user_id, 'author_avtar_image' );
				}*/

				if ( ! is_wp_error( $new_user_id ) ) {

					$update_sql               = $wpdb->prepare( "UPDATE wp_users SET user_pass = %s WHERE ID =%d", $per_user->password, $new_user_id );
					$results_password_updated = $wpdb->query( $update_sql );

					WP_CLI::success( 'Inserted = ' . $new_user_id );
					$success_count ++;
				} else {

					echo $new_user_id->get_error_message();

					WP_CLI::success( 'Not Inserted = ' . $per_user->id );
					$failed_count ++;
				}

			}

			$time_elapsed_secs = microtime( true ) - $start;

			WP_CLI::success( 'executed time = ' . $time_elapsed_secs );

		}

		public function pf_user_no_role_insert() {

			global $wpdb;

			$start = microtime( true );

			$get_all_user  = 'SELECT * from auth_user T1 WHERE NOT EXISTS (SELECT user_id FROM auth_user_groups T2 WHERE T1.id = T2.user_id)';
			$user_lists    = $wpdb->get_results( $get_all_user );
			$success_count = 0;
			$failed_count  = 0;
			foreach ( $user_lists as $per_user ) {
				$user_role = 'author';
				if ( ! empty( $per_user->email ) ) {
					if ( email_exists( $per_user->email ) ) {
						$ExistingID        = email_exists( $per_user->email );
						$user_meta         = get_userdata( $ExistingID );
						$user_roles        = $user_meta->roles;
						$userEmail         = get_user_by( 'email', 'user@example.com' );
						$new_uname         = $per_user->username;
						$lastNameconcatstr = "wordpress";
						$emailString       = "performancein.com";
						$newEmail          = sprintf( '%1$s.%2$s@%3$s', $new_uname, $lastNameconcatstr, $emailString );
						$newEmail          = strtolower( $newEmail );
						$per_user->email   = $newEmail;


					}
				}

				$userdata = array(
					'user_pass'     => '',
					//(string) The plain-text user password.
					'user_login'    => $per_user->username,
					//(string) The user's login username.
					'user_nicename' => $per_user->username,
					//(string) The URL-friendly user name.
					'user_email'    => $per_user->email,
					//(string) The user email address.
					'display_name'  => $per_user->username,
					//(string) The user's display name. Default is the user's username.
					'nickname'      => $per_user->username,
					//(string) The user's nickname. Default is the user's username.
					'first_name'    => $per_user->first_name,
					//(string) The user's first name. For new users, will be used to build the first part of the user's display name if $display_name is not specified.
					'last_name'     => $per_user->last_name,
					//(string) The user's last name. For new users, will be used to build the second part of the user's display name if $display_name is not specified.
					'role'          => $user_role,
					//(string) User's role.
				);

				$get_author_info       = 'SELECT * FROM content_authorprofile WHERE user_id =' . $per_user->id;
				$get_author_info_rsult = $wpdb->get_results( $get_author_info, ARRAY_A );
				$new_user_id           = wp_insert_user( $userdata );
				if ( ! empty( $get_author_info_rsult[0]['twitter'] ) ) {
					update_user_meta( $new_user_id, 'pi_twitter_url', $get_author_info_rsult[0]['twitter'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['lin'] ) ) {
					update_user_meta( $new_user_id, 'pi_linkedin_url', $get_author_info_rsult[0]['lin'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['bio'] ) ) {
					update_user_meta( $new_user_id, 'pi_user_bio', $get_author_info_rsult[0]['bio'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['name'] ) ) {
					update_user_meta( $new_user_id, 'pi_user_name', $get_author_info_rsult[0]['name'] );
				}
				if ( ! empty( $get_author_info_rsult[0]['slug'] ) ) {
					update_user_meta( $new_user_id, 'pi_user_slug', $get_author_info_rsult[0]['slug'] );
				}


				update_user_meta( $new_user_id, 'django_user_id', $per_user->id );
				update_user_meta( $new_user_id, 'is_confirm', 1 );
				update_user_meta( $new_user_id, 'pi_staff_status', $per_user->is_staff );
				update_user_meta( $new_user_id, 'pi_super_user_status', $per_user->is_superuser );
				update_user_meta( $new_user_id, 'pi_is_active', $per_user->is_active );
				update_user_meta( $new_user_id, 'pi_group_id', $per_user->group_id );
				if ( isset( $get_author_info_rsult[0]['image'] ) && ! empty( $get_author_info_rsult[0]['image'] ) ) {

					$this->generate_attachment( 'https://performancein.com/assets/' . $get_author_info_rsult[0]['image'], $new_user_id, 'author_avtar_image' );
				}

				if ( ! is_wp_error( $new_user_id ) ) {

					$update_sql               = $wpdb->prepare( "UPDATE wp_users SET user_pass = %s WHERE ID =%d", $per_user->password, $new_user_id );
					$results_password_updated = $wpdb->query( $update_sql );

					WP_CLI::success( 'Inserted = ' . $new_user_id );
					$success_count ++;
				} else {

					echo $new_user_id->get_error_message();

					WP_CLI::success( 'Not Inserted = ' . $per_user->id );
					$failed_count ++;
				}

			}

			$time_elapsed_secs = microtime( true ) - $start;

			WP_CLI::success( 'executed time = ' . $time_elapsed_secs );

		}


		public function pf_account_user_insert() {

			global $wpdb;

			//$get_account_user   = 'SELECT * FROM `monetisation_account`';

			$get_account_user = 'SELECT `monetisation_account`.`id`,`monetisation_account`.`password`,`monetisation_account`.`last_login`,`monetisation_account`.`email`,`monetisation_account`.`first_name`,`monetisation_account`.`last_name`,`monetisation_account`.`is_confirmed`,`monetisation_account`.`analytics_uuid`,`monetisation_account`.`has_logged_in`,`monetisation_account`.`last_date_viewed_jobs`,`monetisation_account`.`stripe_customer_id`,`monetisation_accountpreferences`.`company_name`,`monetisation_accountpreferences`.`demographic`,`monetisation_accountpreferences`.`id` as `accountpreferences_id`, `monetisation_accountpreferences`.`job_title` FROM `monetisation_account` LEFT JOIN `monetisation_accountpreferences`
ON `monetisation_account`.`id` = `monetisation_accountpreferences`.`account_id`';

			$account_user_lists = $wpdb->get_results( $get_account_user );
			/*echo '<pre>';
			print_r( count( $account_user_lists ) );
			echo '</pre>';
			exit;*/


			$success_count = 0;
			$failed_count  = 0;

			//echo "<pre>";print_r($account_user_lists); echo "</pre>";exit;

			foreach ( $account_user_lists as $per_user ) {

//				if ( $success_count === 10 ) {
//					break;
//				}

				if ( email_exists( $per_user->email ) ) {
					$old_uname       = strstr( $per_user->email, '@', true );
					$new_uname       = $old_uname . '_MD_' . wp_rand( 999, 99999 );
					$new_email       = str_replace( $old_uname, $new_uname, $per_user->email );
					$per_user->email = $new_email;
					WP_CLI::success( 'EMAIL ALREADY EXIST = ' . $per_user->email );
				}
				if ( ! empty( $per_user->username ) ) {
					if ( username_exists( $per_user->username ) ) {
						$newUname           = $per_user->email;
						$per_user->username = $newUname;
					}
				}


				$userdata              = array(
					'user_pass'     => '',
					//(string) The plain-text user password.
					'user_login'    => $per_user->email,
					//(string) The user's login username.
					'user_nicename' => $per_user->email,
					//(string) The URL-friendly user name.
					'user_email'    => $per_user->email,
					//(string) The user email address.
					'display_name'  => $per_user->email,
					//(string) The user's display name. Default is the user's username.
					'nickname'      => $per_user->email,
					//(string) The user's nickname. Default is the user's username.
					'first_name'    => $per_user->first_name,
					//(string) The user's first name. For new users, will be used to build the first part of the user's display name if $display_name is not specified.
					'last_name'     => $per_user->last_name,
					//(string) The user's last name. For new users, will be used to build the second part of the user's display name if $display_name is not specified.
					'role'          => 'customer',
					//(string) User's role.
				);
				$userID                = $per_user->id;
				$new_user_id           = wp_insert_user( $userdata );
				$getAllOrdersUserWise  = 'SELECT * FROM `monetisation_order` LEFT JOIN monetisation_jobproductorderitem ON monetisation_order.uuid = monetisation_jobproductorderitem.order_id WHERE account_id=' . $userID;
				$order_lists           = $wpdb->get_results( $getAllOrdersUserWise );
				$pi_product_temp_array = array();
				if ( ! empty( $order_lists ) ) {
					foreach ( $order_lists as $pi_djongo_pid ) {

						$product_id = isset( $pi_djongo_pid->job_product_id ) ? $pi_djongo_pid->job_product_id : '';
						if ( ! isset( $pi_product_temp_array[ $product_id ] ) && ! empty( $product_id ) ) {
							$pi_product_temp_array[ $product_id ] = 1;
						} elseif ( isset( $pi_product_temp_array[ $product_id ] ) && ! empty( $product_id ) ) {
							$pi_product_temp_array[ $product_id ] ++;
						}


					}
				}
				/**
				 * posted job
				 */
				$userwiseJobsProductID   = 'SELECT monetisation_job.product_id FROM monetisation_job WHERE account_id =' . $userID;
				$userwiseJobsProductists = $wpdb->get_results( $userwiseJobsProductID );
				$pi_product_temp_arr     = array();
				if ( ! empty( $userwiseJobsProductists ) ) {
					$totalJobPost = count( $userwiseJobsProductists );
					foreach ( $userwiseJobsProductists as $pi_djongo_pid ) {
						$product_id = isset( $pi_djongo_pid->product_id ) ? $pi_djongo_pid->product_id : '';
						if ( ! isset( $pi_product_temp_arr[ $product_id ] ) && ! empty( $product_id ) ) {
							$pi_product_temp_arr[ $product_id ] = 1;
						} elseif ( isset( $pi_product_temp_arr[ $product_id ] ) && ! empty( $product_id ) ) {
							$pi_product_temp_arr[ $product_id ] ++;
						}
					}
				}
				foreach ( $pi_product_temp_arr as $key => $values ) {
					$product                   = $this->pi_get_product_id_by_djongo_pid( $key );
					$pendingCredit[ $product ] = absint( $pi_product_temp_array[ $key ] - $pi_product_temp_arr[ $key ] );
				}
				update_user_meta( $new_user_id, 'pi_credit_package', wp_json_encode( $pendingCredit ) );
				update_user_meta( $new_user_id, 'django_user_account_id', $per_user->id );
				/*update_user_meta( $new_user_id, 'pi_twitter_url', $per_user->id );
				update_user_meta( $new_user_id, 'pi_linkedin_url', $per_user->id );*/ // No need to set for the account(recruiter) user
				update_user_meta( $new_user_id, 'is_confirm', $per_user->is_confirmed );
				update_user_meta( $new_user_id, 'pi_company_name', $per_user->company_name );
				update_user_meta( $new_user_id, 'pi_demographic', $per_user->demographic );
				update_user_meta( $new_user_id, 'pi_analytics_uuid', $per_user->analytics_uuid );
				update_user_meta( $new_user_id, 'has_logged_in', $per_user->has_logged_in );
				update_user_meta( $new_user_id, 'stripe_customer_id', $per_user->stripe_customer_id );
				update_user_meta( $new_user_id, 'last_date_viewed_jobs', $per_user->last_date_viewed_jobs );
				update_user_meta( $new_user_id, 'pi_accountpreferences_id', $per_user->accountpreferences_id );
				update_user_meta( $new_user_id, 'pi_job_title', $per_user->job_title );

				$recuruiterLogoQuery          = 'SELECT * FROM monetisation_recruiterinformation WHERE account_id =' . $per_user->id;
				$recuruiterLogoQueryResult    = $wpdb->get_results( $recuruiterLogoQuery, ARRAY_A );
				$recuruiterTwitterQuery       = 'SELECT * FROM monetisation_twitteraccount WHERE account_id =' . $per_user->id;
				$recuruiterTwitterQueryResult = $wpdb->get_results( $recuruiterTwitterQuery, ARRAY_A );

				if ( ! empty( $recuruiterLogoQueryResult[0]['recruiter_name'] ) ) {
					update_user_meta( $new_user_id, 'pi_recruiter_company_name', $recuruiterLogoQueryResult[0]['recruiter_name'] );
				}
				if ( isset( $recuruiterLogoQueryResult[0]['image'] ) && ! empty( $recuruiterLogoQueryResult[0]['image'] ) ) {

					$this->generate_attachment( 'https://performancein.com/assets/' . $recuruiterLogoQueryResult[0]['image'], $new_user_id, 'pi_recruiter_logo' );
				}
				if ( ! empty( $recuruiterTwitterQueryResult[0] ) ) {
					update_user_meta( $new_user_id, 'pi_twitter_token', $recuruiterTwitterQueryResult[0]['token'] );
					update_user_meta( $new_user_id, 'pi_twitter_secret', $recuruiterTwitterQueryResult[0]['secret'] );
					update_user_meta( $new_user_id, 'pi_twitter_username', $recuruiterTwitterQueryResult[0]['username'] );
					update_user_meta( $new_user_id, 'pi_twitter_user_id', $recuruiterTwitterQueryResult[0]['user_id'] );
					update_user_meta( $new_user_id, 'pi_twitter_id', $recuruiterTwitterQueryResult[0]['id'] );
				}

				if ( isset( $per_user->accountpreferences_id ) && ! empty( $per_user->accountpreferences_id ) ) {
					$regionsIds   = array();
					$verticalsIds = array();

					$regionsQuery       = 'SELECT * FROM monetisation_accountpreferences_regions WHERE accountpreferences_id =' . $per_user->accountpreferences_id;
					$regionsQueryResult = $wpdb->get_results( $regionsQuery, ARRAY_A );
					foreach ( $regionsQueryResult as $regionsResult ) {
						if ( '1' === $regionsResult['region_id'] ) {
							$regionalForData = 'usa';
						} elseif ( '2' === $regionsResult['region_id'] ) {
							$regionalForData = 'europe';
						} else {
							$regionalForData = 'global';
						}
						$regionsIds[] = $regionalForData;
					}
					if ( ! empty( $regionsIds ) && isset( $regionsIds ) ) {
						update_user_meta( $new_user_id, 'pi_regions_of_interest', $regionsIds );
					}

					$verticalsQuery       = 'SELECT * FROM monetisation_accountpreferences_verticals WHERE accountpreferences_id =' . $per_user->accountpreferences_id;
					$verticalsQueryResult = $wpdb->get_results( $verticalsQuery, ARRAY_A );
					foreach ( $verticalsQueryResult as $verticalsResult ) {
						if ( '1' === $verticalsResult['vertical_id'] ) {
							$verticalalForData = 'finance';
						} elseif ( '2' === $verticalsResult['vertical_id'] ) {
							$verticalalForData = 'travel';
						} elseif ( '3' === $verticalsResult['vertical_id'] ) {
							$verticalalForData = 'telecoms';
						} elseif ( '4' === $verticalsResult['vertical_id'] ) {
							$verticalalForData = 'retail';
						} elseif ( '5' === $verticalsResult['vertical_id'] ) {
							$verticalalForData = 'automotive';
						} elseif ( '6' === $verticalsResult['vertical_id'] ) {
							$verticalalForData = 'electrical';
						} elseif ( '7' === $verticalsResult['vertical_id'] ) {
							$verticalalForData = 'fashion';
						}
						$verticalsIds[] = $verticalalForData;
					}
					if ( ! empty( $verticalsIds ) && isset( $verticalsIds ) ) {
						update_user_meta( $new_user_id, 'pi_verticals', $verticalsIds );
					}

					$topicsQuery       = 'SELECT * FROM monetisation_accountpreferences_topics WHERE accountpreferences_id =' . $per_user->accountpreferences_id;
					$topicsQueryResult = $wpdb->get_results( $topicsQuery, ARRAY_A );
					foreach ( $topicsQueryResult as $topicsResult ) {
						if ( '1' === $topicsResult['topic_id'] ) {
							$topicalForData = 'affiliate';
						} elseif ( '2' === $topicsResult['topic_id'] ) {
							$topicalForData = 'social';
						} elseif ( '3' === $topicsResult['topic_id'] ) {
							$topicalForData = 'search';
						} elseif ( '4' === $topicsResult['topic_id'] ) {
							$topicalForData = 'email';
						} elseif ( '5' === $topicsResult['topic_id'] ) {
							$topicalForData = 'display';
						} elseif ( '6' === $topicsResult['topic_id'] ) {
							$topicalForData = 'lead generation';
						} elseif ( '7' === $topicsResult['topic_id'] ) {
							$topicalForData = 'mobile';
						}
						$topicsIds[] = $topicalForData;
					}
					if ( ! empty( $topicsIds ) && isset( $topicsIds ) ) {
						update_user_meta( $new_user_id, 'pi_topics', $topicsIds );
					}
				}


				if ( ! is_wp_error( $new_user_id ) ) {

					$update_sql               = $wpdb->prepare( "UPDATE wp_users SET user_pass = %s WHERE ID =%d", $per_user->password, $new_user_id );
					$results_password_updated = $wpdb->query( $update_sql );

					WP_CLI::success( 'Inserted = ' . $new_user_id );
					$success_count ++;
				} else {

					echo $new_user_id->get_error_message();

					WP_CLI::success( 'Not Inserted = ' . $per_user->id );
					$failed_count ++;
				}

			}

			//WP_CLI::success( 'result = '. $str );

		}

		function pi_get_user_md() {

			global $wpdb;
			$start_time           = microtime( true );
			$success_count        = 0;
			$AllUsers             = get_users();
			$get_all_users_md     = "SELECT *  FROM `wp_users` WHERE `user_email` LIKE '%_MD_%'";
			$content_article_list = $wpdb->get_results( $get_all_users_md );
			foreach ( $content_article_list as $userObj ) {
				if ( $success_count === 100 ) {
					break;
				}
				$userID    = $userObj->ID;
				$userEmail = $userObj->user_email;
				if ( strpos( $userEmail, '_MD_' ) !== false ) {
					$getDjangoUserID    = get_user_meta( $userID, 'django_user_id', true );
					$get_account_user   = 'SELECT `monetisation_account`.`id`,`monetisation_account`.`password`,`monetisation_account`.`last_login`,`monetisation_account`.`email`,`monetisation_account`.`first_name`,`monetisation_account`.`last_name`,`monetisation_account`.`is_confirmed`,`monetisation_account`.`analytics_uuid`,`monetisation_account`.`has_logged_in`,`monetisation_account`.`last_date_viewed_jobs`,`monetisation_account`.`stripe_customer_id`,`monetisation_accountpreferences`.`company_name`,`monetisation_accountpreferences`.`demographic`,`monetisation_accountpreferences`.`id` as `accountpreferences_id`, `monetisation_accountpreferences`.`job_title` FROM `monetisation_account` LEFT JOIN `monetisation_accountpreferences`
ON `monetisation_account`.`id` = `monetisation_accountpreferences`.`account_id` WHERE `monetisation_account`.`id` =' . $getDjangoUserID;
					$account_user_lists = $wpdb->get_results( $get_account_user, ARRAY_A );
					$firstName          = $account_user_lists[0]['first_name'];
					$lastName           = $account_user_lists[0]['last_name'];
					$lastNameconcatstr  = "wordpress";
					$emailString        = "performancein.com";
					if ( ! empty( $firstName ) || ! empty( $lastName ) ) {
						$newEmail = sprintf( '%1$s%2$s.%3$s@%4$s', $firstName, $lastName, $lastNameconcatstr, $emailString );
						$newEmail = strtolower( $newEmail );
					} else {
						$newEmail = $userEmail;
					}

					update_user_meta( $userID, 'billing_email', $newEmail );
					wp_update_user( array(
						'ID'            => $userID,
						'user_login'    => $newEmail,
						'user_email'    => $newEmail,
						'user_nicename' => $newEmail,
						'display_name'  => $newEmail,
						'nickname'      => $newEmail,
					) );
					WP_CLI::success( 'Update user email id = ' . $newEmail );
				}
				$success_count ++;
			}
			$end_time       = microtime( true );
			$execution_time = ( $end_time - $start_time );

			WP_CLI::success( $execution_time . " Execution time of script in sec." );
		}

		public function generate_attachment( $image_url, $post_id, $custom_field_key ) {
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
			update_user_meta( $post_id, $custom_field_key, $attach_id );
		}

		function pf_import_article() {
			global $wpdb;

			$get_all_article      = 'SELECT * FROM `content_article`';
			$content_article_list = $wpdb->get_results( $get_all_article );

			$success_count = 0;
			$failed_count  = 0;

			foreach ( $content_article_list as $article ) {


//				if ( $success_count === 1 ) {
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

				$defaults = array(
					'post_author'  => $user_data->ID,
					'post_content' => $article->content,
					'post_title'   => $article->title,
					'post_excerpt' => $article->summary,
					'post_status'  => 'draft',
					'post_type'    => 'post',
				);

				wp_insert_post( $defaults );

				$success_count ++;


				if ( ! empty( $new_user_id ) ) {
					$success_count ++;
				} else {
					$failed_count ++;
				}
			}


		}

		public function pi_remaining_authors() {
			global $wpdb;
			$start         = microtime( true );
			$success_count = 0;
			$failed_count  = 0;
			$get_all_user  = 'SELECT GROUP_CONCAT(id) as uID,GROUP_CONCAT(username) as uName,  email, COUNT(email) repeat_count
FROM auth_user
GROUP BY email
HAVING repeat_count > 1';
			$user_lists    = $wpdb->get_results( $get_all_user, ARRAY_A );
			foreach ( $user_lists as $user_list ) {
				$userIDArray    = explode( ",", $user_list['uID'] );
				$UserEmailArray = array();
				foreach ( $userIDArray as $singleUserId ) {
					$user_select = $wpdb->get_results( "select user_id from $wpdb->usermeta where meta_key = 'django_user_id' AND meta_value = '" . $singleUserId . "'", ARRAY_A );
					if ( ! empty( $user_select ) && is_array( $user_select ) ) {
						WP_CLI::success( 'Already Added = ' . $singleUserId );
					} else {
						$getUserData       = "SELECT * FROM `auth_user` WHERE id=" . $singleUserId;
						$getUserDataResult = $wpdb->get_results( $getUserData, ARRAY_A );
						$GetUserName       = $getUserDataResult[0]['username'];
						$GetFirstName      = $getUserDataResult[0]['first_name'];
						$GetFirstName      = ! empty( $GetFirstName ) ? $GetFirstName : '';
						$GetLastName       = $getUserDataResult[0]['last_name'];
						$GetLastName       = ! empty( $GetLastName ) ? $GetLastName : '';
						$GetEmail          = $getUserDataResult[0]['email'];
						$GetStaff          = $getUserDataResult[0]['is_staff'];
						$GetActive         = $getUserDataResult[0]['is_active'];
						$GetSuperuser      = $getUserDataResult[0]['is_superuser'];
						$GetPassword       = $getUserDataResult[0]['password'];
						$user_role         = 'author';
						if ( ! empty( $GetEmail ) ) {
							if ( email_exists( $GetEmail ) ) {
								$new_uname         = $GetUserName;
								$lastNameconcatstr = "wordpress";
								$emailString       = "performancein.com";
								$newEmail          = sprintf( '%1$s.%2$s@%3$s', $new_uname, $lastNameconcatstr, $emailString );
								$newEmail          = strtolower( $newEmail );
								$GetEmail          = $newEmail;


							}
						}

						$UserEmailArray[] = $GetEmail;

						$userdata = array(
							'user_pass'     => '',
							//(string) The plain-text user password.
							'user_login'    => $GetUserName,
							//(string) The user's login username.
							'user_nicename' => $GetUserName,
							//(string) The URL-friendly user name.
							'user_email'    => $GetEmail,
							//(string) The user email address.
							'display_name'  => $GetUserName,
							//(string) The user's display name. Default is the user's username.
							'nickname'      => $GetUserName,
							//(string) The user's nickname. Default is the user's username.
							'first_name'    => $GetFirstName,
							//(string) The user's first name. For new users, will be used to build the first part of the user's display name if $display_name is not specified.
							'last_name'     => $GetLastName,
							//(string) The user's last name. For new users, will be used to build the second part of the user's display name if $display_name is not specified.
							'role'          => $user_role,
							//(string) User's role.
						);

						$get_author_info       = 'SELECT * FROM content_authorprofile WHERE user_id =' . $singleUserId;
						$get_author_info_rsult = $wpdb->get_results( $get_author_info, ARRAY_A );
						$new_user_id           = wp_insert_user( $userdata );
						if ( ! empty( $get_author_info_rsult[0]['twitter'] ) ) {
							update_user_meta( $new_user_id, 'pi_twitter_url', $get_author_info_rsult[0]['twitter'] );
						}
						if ( ! empty( $get_author_info_rsult[0]['lin'] ) ) {
							update_user_meta( $new_user_id, 'pi_linkedin_url', $get_author_info_rsult[0]['lin'] );
						}
						if ( ! empty( $get_author_info_rsult[0]['bio'] ) ) {
							update_user_meta( $new_user_id, 'pi_user_bio', $get_author_info_rsult[0]['bio'] );
						}
						if ( ! empty( $get_author_info_rsult[0]['name'] ) ) {
							update_user_meta( $new_user_id, 'pi_user_name', $get_author_info_rsult[0]['name'] );
						}
						if ( ! empty( $get_author_info_rsult[0]['slug'] ) ) {
							update_user_meta( $new_user_id, 'pi_user_slug', $get_author_info_rsult[0]['slug'] );
						}


						update_user_meta( $new_user_id, 'django_user_id', $singleUserId );
						update_user_meta( $new_user_id, 'is_confirm', 1 );
						update_user_meta( $new_user_id, 'pi_staff_status', $GetStaff );
						update_user_meta( $new_user_id, 'pi_super_user_status', $GetSuperuser );
						update_user_meta( $new_user_id, 'pi_is_active', $GetActive );
						/*if ( isset( $get_author_info_rsult[0]['image'] ) && ! empty( $get_author_info_rsult[0]['image'] ) ) {

							$this->generate_attachment( 'https://performancein.com/assets/' . $get_author_info_rsult[0]['image'], $new_user_id, 'author_avtar_image' );
						}*/

						if ( ! is_wp_error( $new_user_id ) ) {

							$update_sql               = $wpdb->prepare( "UPDATE wp_users SET user_pass = %s WHERE ID =%d", $GetPassword, $new_user_id );
							$results_password_updated = $wpdb->query( $update_sql );

							WP_CLI::success( 'Inserted = ' . $new_user_id );
							$success_count ++;
						} else {

							echo $new_user_id->get_error_message();

							WP_CLI::success( 'Not Inserted = ' . $singleUserId );
							$failed_count ++;
						}


					}
				}


			}
		}

		public function pi_user_description_update() {
			$start         = microtime( true );
			$success_count = 0;
			$failed_count  = 0;
			$args1         = array(
				'role'   => 'editor',
				'number' => - 1,
				'fields' => 'ID'

			);
			$authorsArray  = get_users( $args1 );
			foreach ( $authorsArray as $authorID ) {
				$getUserDescription = get_user_meta( $authorID, 'pi_user_bio', true );
				update_user_meta( $authorID, 'description', $getUserDescription );
				WP_CLI::success( 'Description updated!!! = ' . $authorID );
			}


		}

		public function pf_delete_user() {
			global $wpdb;
			$args      = array(
				'role'   => array( 'author' ),
				'fields' => 'ids'
			);
			$user_list = get_users( $args );
			if ( ! empty( $user_list ) ) {
				foreach ( $user_list as $user ) {
					if ( wp_delete_user( $user ) ) {
						WP_CLI::success( 'deleted id = ' . $user );
					} else {
						WP_CLI::success( 'USer not deleted. id = ' . $user );
					}
				}


			}


		}

		protected function pi_get_product_id_by_djongo_pid( $pi_djongo_pid ) {
			$params   = array(
				'post_type'  => 'product',
				'meta_query' => array(
					array(
						'key'     => 'pi_product_uuid',
						'value'   => $pi_djongo_pid,
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

		protected function pi_get_author_id() {
			$args1        = array(
				'role'   => 'editor',
				'number' => -1,
				'fields' => 'ID'

			);
			$authorsArray = get_users( $args1 );

			return isset( $authorsArray ) ? $authorsArray : false;
		}
		public function pi_user_admin_username_update() {
			global $wpdb;
			$start         = microtime( true );
			$success_count = 0;
			$failed_count  = 0;
			$args1         = array(
				'role'   => 'administrator',
				'number' => - 1,
				'fields' => 'ID'

			);
			$authorsArray  = get_users( $args1 );
			foreach ( $authorsArray as $authorID ) {
				$dJangoID             = get_user_meta( $authorID, 'django_user_id', true );
				if(! empty($dJangoID)) {
					$getAdminUserObj      = 'SELECT username FROM auth_user WHERE id=' . $dJangoID;
					$getAdminUserObjRsult = $wpdb->get_results( $getAdminUserObj, ARRAY_A );
					$getAdminUserObjRsult = $getAdminUserObjRsult[0];
					$getAdminUserObjRsult = $getAdminUserObjRsult['username'];
					$update_sql               = $wpdb->prepare( "UPDATE wp_users SET user_login = %s WHERE ID =%d",$getAdminUserObjRsult, $authorID );
					$results_password_updated = $wpdb->query( $update_sql );
				}


				WP_CLI::success( 'username updated!!! = ' . $authorID );
			}


		}

		public function get_author_images() {
			global $wpdb;
			$start         = microtime( true );
			$success_count = 0;
			$failed_count  = 0;
			$getAuthorIDs = $this->pi_get_author_id();
			foreach ($getAuthorIDs as $getAuthorID){
				$dJangoUserID = get_user_meta($getAuthorID,'django_user_id',true);
				$scriptStatus = get_user_meta($getAuthorID,'pi_author_image_added',true);
				/*var_dump($scriptStatus."\n");*/
				if( empty($scriptStatus)) {
					$dJangoQuery = 'SELECT image FROM `content_authorprofile` WHERE user_id='.$dJangoUserID;
					$dJangoQueryRes  = $wpdb->get_results( $dJangoQuery, ARRAY_A );
					$dJangoQueryImagePath = $dJangoQueryRes[0];
					$dJangoQueryImagePath = $dJangoQueryImagePath['image'];
					if(! empty($dJangoQueryImagePath) && isset($dJangoQueryImagePath)) {
						$this->generate_attachment( 'https://performancein.com/assets/' .$dJangoQueryImagePath, $getAuthorID, 'author_avtar_image' );

					}
					update_user_meta( $getAuthorID, 'pi_author_image_added','yes' );
					WP_CLI::success( 'Author Image updated!!! = ' . $getAuthorID );
				}


			}
			WP_CLI::success( 'Author Image updated!!! = ' . $getAuthorID );


		}

		public function pi_update_job_credit_zero() {
			global $wpdb;
			$start         = microtime( true );
			$success_count = 0;
			$failed_count  = 0;
			$args1         = array(
				'role'   => 'customer',
				'number' => - 1,
				'fields' => 'ID'

			);
			$userIDs       = get_users( $args1 );
			foreach ( $userIDs as $userID ) {
				$dJangoUserID = get_user_meta( $userID, 'django_user_account_id', true );
				if ( ! empty( $dJangoUserID ) ) {
					$getAllOrdersUserWise  = 'SELECT * FROM `monetisation_order` LEFT JOIN monetisation_jobproductorderitem ON monetisation_order.uuid = monetisation_jobproductorderitem.order_id WHERE account_id=' . $dJangoUserID;
					$order_lists           = $wpdb->get_results( $getAllOrdersUserWise );
					$pi_product_temp_array = array();
					if ( ! empty( $order_lists ) ) {
						foreach ( $order_lists as $pi_djongo_pid ) {

							$product_id = isset( $pi_djongo_pid->job_product_id ) ? $pi_djongo_pid->job_product_id : '';
							if ( ! isset( $pi_product_temp_array[ $product_id ] ) && ! empty( $product_id ) ) {
								$pi_product_temp_array[ $product_id ] = 1;
							} elseif ( isset( $pi_product_temp_array[ $product_id ] ) && ! empty( $product_id ) ) {
								$pi_product_temp_array[ $product_id ] ++;
							}


						}
					}

					$userwiseJobsProductID   = 'SELECT monetisation_job.product_id FROM monetisation_job WHERE account_id =' . $dJangoUserID;
					$userwiseJobsProductists = $wpdb->get_results( $userwiseJobsProductID );
					$pi_product_temp_arr     = array();
					if ( ! empty( $userwiseJobsProductists ) ) {
						$totalJobPost = count( $userwiseJobsProductists );
						foreach ( $userwiseJobsProductists as $pi_djongo_pid ) {
							$product_id = isset( $pi_djongo_pid->product_id ) ? $pi_djongo_pid->product_id : '';
							if ( ! isset( $pi_product_temp_arr[ $product_id ] ) && ! empty( $product_id ) ) {
								$pi_product_temp_arr[ $product_id ] = 1;
							} elseif ( isset( $pi_product_temp_arr[ $product_id ] ) && ! empty( $product_id ) ) {
								$pi_product_temp_arr[ $product_id ] ++;
							}
						}
					}
					$pendingCredit = array();
					if ( ! empty( $pi_product_temp_arr ) ) {
						foreach ( $pi_product_temp_arr as $key => $values ) {
							$product                   = $this->pi_get_product_id_by_djongo_pid( $key );
							$pendingCredit[ $product ] = absint( $pi_product_temp_array[ $key ] - $pi_product_temp_arr[ $key ] );

						}
					} else {
						foreach ( $pi_product_temp_array as $key => $values ) {
							$product                   = $this->pi_get_product_id_by_djongo_pid( $key );
							$pendingCredit[ $product ] = absint( $pi_product_temp_array[ $key ] - $pi_product_temp_arr[ $key ] );

						}
					}
					update_user_meta( $userID, 'pi_credit_package', wp_json_encode( $pendingCredit ) );
					WP_CLI::success( 'Updated User ID = ' . $userID );
				}

			}

		}

		/**
		 * @throws Exception
		 * Function to set customer in oreders
		 */
		public function get_orders_customer_id(){
			$query = new WC_Order_Query( array(
				'limit' => -1,
				'orderby' => 'date',
				'order' => 'DESC',
				'return' => 'ids',
			) );
			$orders = $query->get_orders();
			foreach ($orders as $orderID){
				$dJangoOrderID = get_post_meta($orderID,'_pi_order_account_id',true);
				if(! empty($dJangoOrderID)) {
					$args1         = array(
					'meta_key'     => 'django_user_account_id',
					'meta_value' => $dJangoOrderID,
					'fields' => 'ID'

				);
				$user_list = get_users( $args1 );
				update_post_meta($orderID, '_customer_user', $user_list[0]);
					WP_CLI::success( 'Updated User ID = ' . $orderID );
					WP_CLI::success( 'Updated User ID = ' . $user_list[0] );
				}


			}
		}
	}

	WP_CLI::add_command( 'performance', 'ExamplePluginWPCLI' );

}
