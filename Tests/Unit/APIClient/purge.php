<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use WPMedia\Cloudflare\APIClient;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::purge
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_Purge extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		$api = new APIClient( 'cloudflare/3.5' );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$api->purge();
	}

	public function testShouldPurgeCacheWhenZoneIdIsSet() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( 'zones/zone1234/purge_cache', [ 'purge_everything' => true ], 'delete' )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'  => [],
				    'success' => true,
				    'errors'  => [],
			    ];
		    } );
		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->purge();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
	}
}
