<?php

namespace WPMedia\Cloudflare;

use stdClass;

/**
 * @since  3.5
 */
class APIClient {
	const CLOUDFLARE_API = 'https://api.cloudflare.com/client/v4/';

	/**
	 * Email address for API authentication.
	 *
	 * @var string
	 */
	public $email;

	/**
	 * API key for API authentication.
	 *
	 * @var string
	 */
	public $api_key;

	/**
	 * Zone ID.
	 *
	 * @var string
	 */
	protected $zone_id;

	/**
	 * An array of curl options.
	 *
	 * @var array
	 */
	public $curl_options = [];

	/**
	 * APIClient constructor.
	 *
	 * @param string $useragent The user agent for this plugin or package. For example, "wp-rocket/3.5".
	 */
	public function __construct( $useragent ) {
		$this->curl_options[ CURLOPT_USERAGENT ] = $useragent;
	}

	/**
	 * Sets up the API credentials.
	 *
	 * @since  3.5
	 *
	 * @param string $email   The email associated with the Cloudflare account.
	 * @param string $api_key The API key for the associated Cloudflare account.
	 * @param string $zone_id The zone ID.
	 */
	public function set_api_credentials( $email, $api_key, $zone_id ) {
		$this->email   = $email;
		$this->api_key = $api_key;
		$this->zone_id = $zone_id;
	}

	/**
	 * Get zone data.
	 *
	 * @since  3.5
	 *
	 * @return stdClass Cloudflare response packet.
	 */
	public function get_zones() {
		return $this->get( "zones/{$this->zone_id}" );
	}

	/**
	 * Get the zone's page rules.
	 *
	 * @since  3.5
	 *
	 * @return stdClass Cloudflare response packet.
	 */
	public function list_pagerules() {
		return $this->get(
			"zones/{$this->zone_id}/pagerules",
			[
				'status'    => 'active',
				'order'     => null,
				'direction' => null,
				'match'     => null,
			]
		);
	}

	/**
	 * Purges the cache.
	 *
	 * @since  3.5
	 *
	 * @return stdClass Cloudflare response packet.
	 */
	public function purge() {
		return $this->delete( "zones/{$this->zone_id}/purge_cache", [ 'purge_everything' => true ] );
	}

	/**
	 * Purges the given URLs.
	 *
	 * @since  3.5
	 *
	 * @param array|null $urls   An array of URLs that should be removed from cache.
	 * @param array|null $tags   Any assets served with a Cache-Tag header that matches one of the provided values will
	 *                           be purged from the CloudFlare cache.
	 *
	 * @return stdClass Cloudflare response packet.
	 */
	public function purge_files( array $urls, array $tags = null ) {
		return $this->delete(
			"zones/{$this->zone_id}/purge_cache",
			[
				'files' => $urls,
				'tags'  => $tags,
			]
		);
	}

	/**
	 * Changes the zone's browser cache TTL setting.
	 *
	 * @since  3.5
	 *
	 * @param string $value New setting's value.
	 *
	 * @return stdClass Cloudflare response packet.
	 */
	public function change_browser_cache_ttl( $value ) {
		return $this->change_setting( 'browser_cache_ttl', $value );
	}

	/**
	 * Changes the zone's rocket loader setting.
	 *
	 * @since  3.5
	 *
	 * @param string $value New setting's value.
	 *
	 * @return stdClass Cloudflare response packet.
	 */
	public function change_rocket_loader( $value ) {
		return $this->change_setting( 'rocket_loader', $value );
	}

	/**
	 * Changes the zone's minify setting.
	 *
	 * @since  3.5
	 *
	 * @param string $value New setting's value.
	 *
	 * @return stdClass Cloudflare response packet.
	 */
	public function change_minify( $value ) {
		return $this->change_setting( 'minify', $value );
	}

	/**
	 * Changes the zone's cache level.
	 *
	 * @since  3.5
	 *
	 * @param string $value New setting's value.
	 *
	 * @return stdClass Cloudflare response packet.
	 */
	public function change_cache_level( $value ) {
		return $this->change_setting( 'cache_level', $value );
	}

	/**
	 * Changes the zone's development mode.
	 *
	 * @since  3.5
	 *
	 * @param string $value New setting's value.
	 *
	 * @return stdClass Cloudflare response packet.
	 */
	public function change_development_mode( $value ) {
		return $this->change_setting( 'development_mode', $value );
	}

	/**
	 * Changes the given setting.
	 *
	 * @since  3.5
	 *
	 * @param string $setting Name of the setting to change.
	 * @param string $value   New setting's value.
	 *
	 * @return stdClass Cloudflare response packet.
	 */
	protected function change_setting( $setting, $value ) {
		return $this->patch( "zones/{$this->zone_id}/settings/{$setting}", [ 'value' => $value ] );
	}

	/**
	 * Gets all of the Cloudflare settings.
	 *
	 * @since  3.5
	 *
	 * @return stdClass Cloudflare response packet.
	 */
	public function settings() {
		return $this->get( "zones/{$this->zone_id}/settings" );
	}

	/**
	 * Get the Cloudflare IPs.
	 *
	 * @since  3.5
	 *
	 * @return stdClass Cloudflare response packet.
	 */
	public function ips() {
		return $this->get( '/ips' );
	}

	/**
	 * API call method for sending requests using GET
	 *
	 * @param string $path Path of the endpoint
	 * @param array  $data Data to be sent along with the request
	 *
	 * @return stdClass Cloudflare response packet.
	 */
	protected function get( $path, array $data = [] ) {
		return $this->request( $path, $data, 'get' );
	}

	/**
	 * API call method for sending requests using POST
	 *
	 * @param string $path Path of the endpoint
	 * @param array  $data Data to be sent along with the request
	 *
	 * @return mixed
	 */
	protected function post( $path, array $data = [] ) {
		return $this->request( $path, $data, 'post' );
	}

	/**
	 * API call method for sending requests using PUT
	 *
	 * @param string $path Path of the endpoint
	 * @param array  $data Data to be sent along with the request
	 *
	 * @return mixed
	 */
	protected function put( $path, array $data = [] ) {
		return $this->request( $path, $data, 'put' );
	}

	/**
	 * API call method for sending requests using DELETE
	 *
	 * @param string $path Path of the endpoint
	 * @param array  $data Data to be sent along with the request
	 *
	 * @return mixed
	 */
	protected function delete( $path, array $data = [] ) {
		return $this->request( $path, $data, 'delete' );
	}

	/**
	 * API call method for sending requests using PATCH
	 *
	 * @param string $path Path of the endpoint
	 * @param array  $data Data to be sent along with the request
	 *
	 * @return mixed
	 */
	protected function patch( $path, array $data = [] ) {
		return $this->request( $path, $data, 'patch' );
	}

	/**
	 * API call method for sending requests using GET, POST, PUT, DELETE OR PATCH
	 *
	 * @param string $path   Path of the endpoint
	 * @param array  $data   Data to be sent along with the request
	 * @param string $method Type of method that should be used ('GET', 'POST', 'PUT', 'DELETE', 'PATCH')
	 *
	 * @return mixed
	 */
	protected function request( $path, array $data = [], $method = 'get' ) {
		if ( ! isset( $this->email, $this->api_key ) || false === filter_var( $this->email, FILTER_VALIDATE_EMAIL ) ) {
			throw new AuthenticationException( __( 'Authentication information must be provided', 'cloudflare' ) );
		}

		//Removes null entries
		$data = array_filter( $data, function( $val ) {
			return ! is_null( $val );
		} );

		$url = 'https://api.cloudflare.com/client/v4/' . $path;

		$default_curl_options = [
			CURLOPT_VERBOSE        => false,
			CURLOPT_FORBID_REUSE   => true,
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_HEADER         => false,
			CURLOPT_TIMEOUT        => 30,
			CURLOPT_SSL_VERIFYPEER => true,
		];

		$curl_options = $default_curl_options;
		if ( isset( $this->curl_options ) && is_array( $this->curl_options ) ) {
			$curl_options = array_replace( $default_curl_options, $this->curl_options );
		}

		$user_agent = __FILE__;
		$headers    = [
			"X-Auth-Email: {$this->email}",
			"X-Auth-Key: {$this->api_key}",
			"User-Agent: {$user_agent}",
			'Content-type: application/json',
		];

		$ch = curl_init();
		curl_setopt_array( $ch, $curl_options );

		$json_data = json_encode( $data );

		if ( $method === 'post' ) {
			curl_setopt( $ch, CURLOPT_POST, true );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data );
		} elseif ( $method === 'put' ) {
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data );
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
		} elseif ( $method === 'delete' ) {
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data );
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'DELETE' );
		} elseif ( $method === 'patch' ) {
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data );
			curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PATCH' );
		} else {
			$url .= '?' . http_build_query( $data );
		}

		curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch, CURLOPT_URL, $url );

		$http_result = curl_exec( $ch );
		$error       = curl_error( $ch );
		$information = curl_getinfo( $ch );
		$http_code   = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

		if ( in_array( $http_code, [ 401, 403 ] ) ) {
			throw new UnauthorizedException( __( 'You do not have permission to perform this request,', 'cloudflare' ) );
		}

		$response = json_decode( $http_result );
		if ( ! $response ) {
			$response          = new stdClass();
			$response->success = false;
		}

		if ( true !== $response->success ) {
			$response->error       = $error;
			$response->http_code   = $http_code;
			$response->method      = $method;
			$response->information = $information;
		}

		return $response;
	}
}
