<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::get_zones
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_GetZones extends TestCase {

	public function testShouldFailWhenZoneIdInvalid() {
		list( $api, $cf ) = $this->getMocks();

		$api->shouldReceive( 'get' )
		    ->once()
		    ->with( 'zones/' )
		    ->andReturnUsing( function() {
			    throw new AuthenticationException( 'Authentication information must be provided' );
		    } );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$cf->get_zones();
	}

	public function testShouldFailWhenInvalid() {
		list( $api, $cf ) = $this->getMocks();

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'invalid' );

		$api->shouldReceive( 'get' )
		    ->once()
		    ->with( 'zones/invalid' )
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

		$response = $cf->get_zones();
		$this->assertFalse( $response->success );
		$zone_error = $response->errors[0];
		$this->assertSame( 7003, $zone_error->code );
		$this->assertSame( 'Could not route to /zones/invalid, perhaps your object identifier is invalid?', $zone_error->message );
	}

	public function testShouldSucceedWhenZoneExists() {
		list( $api, $cf ) = $this->getMocks();

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$api->shouldReceive( 'get' )
		    ->once()
		    ->with( 'zones/zone1234' )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'  => (object) [
					    'id' => 'zone1234',
				    ],
				    'success' => true,
				    'errors'  => [],
			    ];
		    } );

		$response = $cf->get_zones();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
		$this->assertSame( 'zone1234', $response->result->id );
	}
}
