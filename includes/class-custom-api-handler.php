<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Custom_API_Handler {

	/**
	 * Fetch data from the custom API.
	 *
	 * @param string $elements Comma-separated list of elements.
	 * @return array The fetched data.
	 */
	public static function get_api_data( $elements ) {
		if ( empty( $elements ) ) {
			return array();
		}

		$cache_key = 'custom_api_data_' . md5( $elements );
		$cached_data = get_transient( $cache_key );

		if ( $cached_data !== false ) {
			return $cached_data;
		}

		$response = wp_remote_post(
			'https://httpbin.org/post',
			array(
				'body'    => array( 'elements' => explode( ',', $elements ) ),
				'timeout' => 15,
			)
		);

		if ( is_wp_error( $response ) ) {
			// Log the error message for debugging purposes
			error_log( 'Custom API Error: ' . $response->get_error_message() );
			return array();
		}

		$body = wp_remote_retrieve_body( $response );
		if ( empty( $body ) ) {
			return array();
		}

		$data = json_decode( $body, true );
		if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $data ) ) {
			// Log JSON decode error for debugging purposes
			error_log( 'Custom API JSON Decode Error: ' . json_last_error_msg() );
			return array();
		}

		$return_data = array();
		if ( isset( $data['json']['elements'] ) && is_array( $data['json']['elements'] ) ) {
			$return_data = $data['json']['elements'];
		}

		// Cache the data for 5 minutes.
		set_transient( $cache_key, $return_data, 5 * MINUTE_IN_SECONDS );

		return $return_data;
	}
}
?>
