<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use WPMedia\Cloudflare\APIClient;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::get_ips
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_GetIps extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		$api = new APIClient( 'cloudflare/1.0' );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$api->get_ips();
	}

	public function testShouldReturnIps() {
		$api = $this->getAPIMock();
		$api->shouldReceive( 'request' )
		    ->once()
		    ->with( '/ips', [], 'get' )
		    ->andReturnUsing( function() {
			    return (object) [
				    'result'   => (object) [
					    'ipv4_cidrs' => [ '173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22' ],
					    'ipv6_cidrs' => [ '2400:cb00::/32', '2606:4700::/32', '2803:f800::/32' ],
				    ],
				    'success'  => true,
				    'errors'   => [],
			    ];
		    } );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $api->get_ips();
		$this->assertTrue( $response->success );
		$this->assertContains( '173.245.48.0/20', $response->result->ipv4_cidrs );
		$this->assertContains( '2400:cb00::/32', $response->result->ipv6_cidrs );
	}
}
