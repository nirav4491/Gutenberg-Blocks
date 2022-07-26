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

	class test_ImportPartners {

		public function __construct() {

			// example constructor called when plugin loads

		}

		public function test_cli() {
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
				$userIDArray = explode( ",", $user_list['uID'] );
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
								$GetEmail         = $newEmail;


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
						if ( isset( $get_author_info_rsult[0]['image'] ) && ! empty( $get_author_info_rsult[0]['image'] ) ) {

							$this->generate_attachment( 'https://performancein.com/assets/' . $get_author_info_rsult[0]['image'], $new_user_id, 'author_avtar_image' );
						}

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
				echo '<pre>';
				print_r($UserEmailArray);
				echo '</pre>';

			}
		}


	}


	WP_CLI::add_command( 'performance', 'test_ImportPartners' );

}
