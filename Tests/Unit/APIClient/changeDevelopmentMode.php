<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use WPMedia\Cloudflare\APIClient;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::change_development_mode
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_ChangeDevelopmentMode extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		$api = new APIClient( 'cloudflare/3.5' );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$api->change_development_mode( 'on' );
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( 'zones/zone1234/settings/development_mode', [ 'value' => 'invalid' ], 'patch' )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'  => null,
				    'success' => false,
				    'errors'  => [
					    (object) [
						    'message' => 'Invalid value for zone setting development_mode',
					    ],
				    ],
			    ];
		    } );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->change_development_mode( 'invalid' );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting development_mode', $error->message );
	}

	public function testShouldSucceedWhenValidSettingGiven() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( 'zones/zone1234/settings/development_mode', [ 'value' => 'off' ], 'patch' )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'  => (object) [
					    'id'    => 'development_mode',
					    'value' => 'off',
				    ],
				    'success' => true,
				    'errors'  => [],
			    ];
		    } );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->change_development_mode( 'off' );
		$this->assertTrue( $response->success );
		$this->assertSame( 'development_mode', $response->result->id );
		$this->assertSame( 'off', $response->result->value );
	}
}
