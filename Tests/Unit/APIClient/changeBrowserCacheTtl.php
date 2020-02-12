<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use WPMedia\Cloudflare\APIClient;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::change_browser_cache_ttl
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_ChangeBrowserCacheTtl extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		$api = new APIClient( 'cloudflare/1.0' );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$api->change_browser_cache_ttl( 31536000 );
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( 'zones/zone1234/settings/browser_cache_ttl', [ 'value' => -3600 ], 'patch' )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'  => [],
				    'success' => false,
				    'errors'  => [
					    (object) [
						    'message' => 'Invalid value for zone setting browser_cache_ttl',
					    ],
				    ],
			    ];
		    } );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->change_browser_cache_ttl( -3600 );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting browser_cache_ttl', $error->message );
	}

	public function testShouldChangeBrowserCacheTtlWhenTTLGiven() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( 'zones/zone1234/settings/browser_cache_ttl', [ 'value' => 3600 ], 'patch' )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'   => (object) [
					    'value' => 3600,
				    ],
				    'success'  => true,
				    'errors'   => [],
				    'messages' => [],
			    ];
		    } );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->change_browser_cache_ttl( 3600 );
		$this->assertTrue( $response->success );
		$this->assertSame( 3600, $response->result->value );
	}
}
