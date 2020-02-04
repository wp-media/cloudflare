<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use WPMedia\Cloudflare\APIClient;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::list_pagerules
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_ListPagerules extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		$api = new APIClient( 'cloudflare/3.5' );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$api->list_pagerules();
	}

	public function testShouldGetPageRulesWhenZoneIdIsSet() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with(
			    'zones/zone1234/pagerules',
			    [ 'status' => 'active' ],
			    'get'
		    )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'  => [],
				    'success' => true,
				    'errors'  => [],
			    ];
		    } );
		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->list_pagerules();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
	}
}
