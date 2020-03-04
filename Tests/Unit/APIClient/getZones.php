<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use WPMedia\Cloudflare\APIClient;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::get_zones
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_GetZones extends TestCase {

	public function testShouldFailWhenZoneIdInvalid() {
		$api = new APIClient( 'cloudflare/3.5' );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$api->get_zones();
	}

	public function testShouldFailWhenInvalid() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'do_remote_request' )
		    ->once()
		    ->with( 'zones/invalid', [], 'get' )
		    ->andReturnUsing( function() {
			    $http_result = json_encode( (object) [
				    'result'  => [],
				    'success' => false,
				    'errors'  => [
					    (object) [
						    'code'    => 7003,
						    'message' => 'Could not route to /zones/invalid, perhaps your object identifier is invalid?',
					    ],
				    ],
			    ]
			    );

			    return [ $http_result, '', null, 200 ];
		    } );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'invalid' );

		$response = $api->get_zones();
		$this->assertFalse( $response->success );
		$zone_error = $response->errors[0];
		$this->assertSame( 7003, $zone_error->code );
		$this->assertSame( 'Could not route to /zones/invalid, perhaps your object identifier is invalid?', $zone_error->message );
	}

	public function testShouldFailWhenCurlErrorExists() {
		$curl_error_message = 'cURL error 60: SSL certificate problem: self signed certificate';

		$api = $this->getAPIMock();
		$api->shouldReceive( 'do_remote_request' )
		    ->once()
		    ->with( 'zones/zone1234', [], 'get' )
		    ->andReturnUsing( function() use ( $curl_error_message ) {
			    return [ '', $curl_error_message, null, '' ];
		    } );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->get_zones();
		$this->assertFalse( $response->success );
		$this->assertCount( 1, $response->errors );
		$curl_error = $response->errors[0];
		$this->assertSame( '', $curl_error->code );
		$this->assertSame( $curl_error_message, $curl_error->message );
		$this->assertSame( $curl_error_message, $response->error );
		$this->assertSame( '', $response->http_code );
	}

	public function testShouldSucceedWhenZoneExists() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'do_remote_request' )
		    ->once()
		    ->with( 'zones/zone1234', [], 'get' )
		    ->andReturnUsing( function() {
			    $http_result = json_encode( (object) [
				    'result'  => (object) [
					    'id' => 'zone1234',
				    ],
				    'success' => true,
				    'errors'  => [],
			    ] );

			    return [ $http_result, '', null, 200 ];
		    } );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->get_zones();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
		$this->assertSame( 'zone1234', $response->result->id );
	}
}
