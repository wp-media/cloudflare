<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use WPMedia\Cloudflare\APIClient;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::change_cache_level
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_ChangeCacheLevel extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		$api = new APIClient( 'cloudflare/3.5' );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$api->change_cache_level( 'aggressive' );
	}

	public function testShouldFailWhenInvalidLevelGiven() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( 'zones/zone1234/settings/cache_level', [ 'value' => 'invalid' ], 'patch' )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'  => [],
				    'success' => false,
				    'errors'  => [
					    (object) [
						    'message' => 'Invalid value for zone setting change_cache_level',
					    ],
				    ],
			    ];
		    } );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->change_cache_level( 'invalid' );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting change_cache_level', $error->message );
	}

	public function testShouldChangeCacheLevelWhenLevelGiven() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( 'zones/zone1234/settings/cache_level', [ 'value' => 'simplified' ], 'patch' )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'   => (object) [
					    'value' => 'simplified',
				    ],
				    'success'  => true,
				    'errors'   => [],
				    'messages' => [],
			    ];
		    } );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->change_cache_level( 'simplified' );
		$this->assertTrue( $response->success );
		$this->assertSame( 'simplified', $response->result->value );
	}
}
