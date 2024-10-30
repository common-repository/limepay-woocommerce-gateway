<?php
/**
 * Limepay_API class.
 *
 */
class Limepay_API {

	/**
	 * Limepay API Endpoint
	 */
	const ENDPOINT_LIVE     = 'https://api.limepay.com.au/';
	const ENDPOINT_SANDBOX	= 'https://api.sandbox.limepay.com.au/';
	const ENDPOINT_TST      = 'https://api.tst.limep.net/';
	const ENDPOINT_DEV      = 'https://api.dev.limep.net/';

	const ENDPOINT_LIVE_LEGACY     = 'https://www.limepay.com.au/api/v1/';
	const ENDPOINT_SANDBOX_LEGACY	 = 'https://www.sandbox.limepay.com.au/api/v1/';
	const ENDPOINT_TST_LEGACY      = 'https://www.tst.limepay.com.au/api/v1/';
	const ENDPOINT_DEV_LEGACY      = 'https://www.dev.limepay.com.au/api/v1/';

	/**
	 * Secret API Key.
	 *
	 * @var string
	 */
	private static $secret_key = '';

	/**
	 * Set secret API Key.
	 *
	 * @param string $key
	 */
	public static function set_secret_key( $secret_key ) {
		self::$secret_key = $secret_key;
	}

	/**
	 * Get Limepay environment [live, sandbox, tst, dev]
	 *
	 * @return string
	 */
	private static function get_limepay_env() {
		$pkExp = explode( '_', self::$secret_key );
		$env = $pkExp[0];
		return $env;
	}


	/**
	 * Get API endpoint url.
	 *
	 * @return string
	 */
	private static function get_api_end_point( $is_legacy ) {
		$env = self::get_limepay_env();
		$url = '';

		switch($env) {
			case 'dev':
				$url = $is_legacy ? self::ENDPOINT_DEV_LEGACY : self::ENDPOINT_DEV;
				break;
			case 'tst':
				$url = $is_legacy ? self::ENDPOINT_TST_LEGACY : self::ENDPOINT_TST;
				break;
			case 'test':
			case 'sandbox':
				$url = $is_legacy ? self::ENDPOINT_SANDBOX_LEGACY : self::ENDPOINT_SANDBOX;
				break;
			default:
				$url = $is_legacy ? self::ENDPOINT_LIVE_LEGACY : self::ENDPOINT_LIVE;
		}

		return $url;
	}

	/**
	 * Generates the headers to pass to API request.
	 *
	 * @version 3.1.0
	 */
	public static function get_headers( $data_string, $is_legacy ) {

		$http_headers = array(
				"Content-Type: application/json",
				"Content-Length: " . strlen( $data_string )
		);

		if ( $is_legacy ) {
				$http_headers['Authorization'] = 'Basic ' . base64_encode( self::$secret_key . ':' );
		} else {
				$http_headers['Limepay-SecretKey'] = self::$secret_key;
		}

		return $http_headers;
	}

	/**
	 * Limepay API request
	 *
	 * @version 3.1.0
	 * @param array  $request
	 * @param string $api
	 * @param string $method
	 * @param bool   $with_headers To get the response with headers.
	 * @return stdClass|array
	 * @throws Limepay_Exception
	 */

	public static function request( $request, $api, $method = 'POST', $is_legacy = false ) {

		$req_string = json_encode( $request );
		$endpoint = self::get_api_end_point( $is_legacy ) . $api;

		Limepay_Helper::write_log( $method . ' ' . $endpoint );

		$response = wp_safe_remote_post( $endpoint,
			[
				'sslverify' => true,
				'method'  => $method,
				'headers' => self::get_headers( $req_string, $is_legacy ),
				'body'    => $req_string,
				'timeout' => 60,
			]
		);

		if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
			throw new Exception( 'Limepay API call failed' );
		}

		return $response;
	}

}
