<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use WPMedia\Cloudflare\APIClient;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::purge_files
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_PurgeFiles extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		$api = new APIClient( 'cloudflare/3.5' );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$api->purge_files( [ '/purge-url' ] );
	}

	public function testShouldFailWhenUrlInvalid() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with(
			    'zones/zone1234/purge_cache',
			    [ 'files' => [ '/invalid/URL' ] ],
			    'delete'
		    )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'  => [],
				    'success' => false,
				    'errors'  => [
					    (object) [
						    'message' => 'Unable to purge /invalid/URL, which is an invalid URL.',
					    ],
				    ],
			    ];
		    } );
		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->purge_files( [ '/invalid/URL' ] );
		$this->assertFalse( $response->success );
		$this->assertSame( 'Unable to purge /invalid/URL, which is an invalid URL.', $response->errors[0]->message );
	}

	public function testShouldSucceedWhenUrlsGiven() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with(
			    'zones/zone1234/purge_cache',
			    [ 'files' => [ '/about', '/contact' ] ],
			    'delete'
		    )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'  => [],
				    'success' => true,
				    'errors'  => [],
			    ];
		    } );
		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->purge_files( [ '/about', '/contact' ] );
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
	}
}
