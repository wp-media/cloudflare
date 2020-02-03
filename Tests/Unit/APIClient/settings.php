<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use WPMedia\Cloudflare\APIClient;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::settings
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_Settings extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		$api = new APIClient( 'cloudflare/3.5' );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$api->settings();
	}

	public function testShouldReturnSettingsWhenZoneIdIsSet() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( 'zones/zone1234/settings', [], 'get' )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'  => [
					         // List of settings
					         (object) [],
					         (object) [],
					         (object) [],
					         (object) [],
					         (object) [],
				         ],
				         'success' => true,
				         'errors'  => [],
			         ];
		         } );
		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->settings();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
		$this->assertGreaterThan( 4, $response->result );
	}
}
