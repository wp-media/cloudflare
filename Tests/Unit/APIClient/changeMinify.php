<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use WPMedia\Cloudflare\APIClient;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::change_minify
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_ChangeMinify extends TestCase {
	protected $newSetting = [
		'css'  => 'on',
		'html' => 'on',
		'js'   => 'on',
	];

	public function testShouldThrowErrorWhenInvalidCredentials() {
		$api = new APIClient( 'cloudflare/3.5' );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$api->change_minify( $this->newSetting );
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( 'zones/zone1234/settings/minify', [ 'value' => [ 'css' => 'invalid' ] ], 'patch' )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'  => [],
				    'success' => false,
				    'errors'  => [
					    (object) [
						    'message' => 'Invalid value for zone setting minify',
					    ],
				    ],
			    ];
		    } );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->change_minify( [ 'css' => 'invalid' ] );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting minify', $error->message );
	}

	public function testShouldChangeMinifyWhenSettingGiven() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( 'zones/zone1234/settings/minify', [ 'value' => $this->newSetting ], 'patch' )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'  => (object) [
					    'value' => $this->newSetting,
				    ],
				    'success' => true,
				    'errors'  => [],
			    ];
		    } );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->change_minify( $this->newSetting );
		$this->assertTrue( $response->success );
		$this->assertSame( $this->newSetting, $response->result->value );
	}
}
