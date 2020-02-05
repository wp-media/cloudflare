<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use WPMedia\Cloudflare\APIClient;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::change_rocket_loader
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_ChangeRocketLoader extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		$api = new APIClient( 'cloudflare/3.5' );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$api->change_rocket_loader( 'off' );
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( 'zones/zone1234/settings/rocket_loader', [ 'value' => 'invalid' ], 'patch' )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'  => [],
				    'success' => false,
				    'errors'  => [
					    (object) [
						    'message' => 'Invalid value for zone setting rocket_loader',
					    ],
				    ],
			    ];
		    } );
		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->change_rocket_loader( 'invalid' );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting rocket_loader', $error->message );
	}

	public function testShouldChangeRocketLoaderWhenSettingGiven() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( 'zones/zone1234/settings/rocket_loader', [ 'value' => 'on' ], 'patch' )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'  => (object) [
					         'value' => 'on',
				         ],
				         'success' => true,
				         'errors'  => [],
			         ];
		         } );
		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->change_rocket_loader( 'on' );
		$this->assertTrue( $response->success );
		$this->assertSame( 'on', $response->result->value );
	}
}
