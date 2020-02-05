<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use WPMedia\Cloudflare\APIClient;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::get_settings
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_GetSettings extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		$api = new APIClient( 'cloudflare/3.5' );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$api->get_settings();
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

		$response = $api->get_settings();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
		$this->assertGreaterThan( 4, $response->result );
	}
}
