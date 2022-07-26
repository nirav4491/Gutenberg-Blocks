<?php

/**
 * Class PI_SignIn_With_Google
 */
class PI_SignIn_With_Google {
	/**
	 * @param $client_id
	 * @param $client_secret_key
	 * @param $client_redirect_url
	 * @param $code
	 *
	 * @return bool|mixed
	 */
	public function getAccessToken( $client_id, $client_secret_key, $client_redirect_url, $code ) {
		$post_vars = array(
			'code'          => $code,
			'client_id'     => $client_id,
			'client_secret' => $client_secret_key,
			'redirect_uri'  => $client_redirect_url,
			'grant_type'    => 'authorization_code'
		);
		$result    = wp_remote_post( 'https://accounts.google.com/o/oauth2/token', array(
			'timeout' => 30,
			'method'  => 'POST',
			'body'    => $post_vars
		) );
		if ( ! is_wp_error( $result ) ) {
			return json_decode( $result['body'], true );
		}

		return false;
	}

	/**
	 * @param $access_token
	 *
	 * @return bool|mixed
	 */
	public function getUserInfo( $access_token ) {
		$url    = 'https://www.googleapis.com/oauth2/v2/userinfo?fields=name,email,gender,id,picture,verified_email,given_name,family_name';
		$result = wp_remote_post( $url, array(
			'timeout'   => 30,
			'sslverify' => false,
			'method'    => 'GET',
			'headers'   => array(
				'Authorization' => 'Bearer' . $access_token,
			),
			'body'      => array()
		) );

		if ( ! is_wp_error( $result ) ) {
			return json_decode( $result['body'], true );
		}

		return false;
	}
}