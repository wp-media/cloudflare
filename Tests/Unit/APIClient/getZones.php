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
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( 'zones/invalid', [], 'get' )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'  => [],
				    'success' => false,
				    'errors'  => [
					    (object) [
						    'code'    => 7003,
						    'message' => 'Could not route to /zones/invalid, perhaps your object identifier is invalid?',
					    ],
				    ],
			    ];
		    } );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'invalid' );

		$response = $api->get_zones();
		$this->assertFalse( $response->success );
		$zone_error = $response->errors[0];
		$this->assertSame( 7003, $zone_error->code );
		$this->assertSame( 'Could not route to /zones/invalid, perhaps your object identifier is invalid?', $zone_error->message );
	}

	public function testShouldSucceedWhenZoneExists() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( 'zones/zone1234', [], 'get' )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'  => (object) [
					    'id' => 'zone1234',
				    ],
				    'success' => true,
				    'errors'  => [],
			    ];
		    } );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->get_zones();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
		$this->assertSame( 'zone1234', $response->result->id );
	}
}
