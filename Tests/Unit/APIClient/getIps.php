<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use WPMedia\Cloudflare\APIClient;

/**
 * @covers WPMedia\Cloudflare\APIClient::get_ips
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_GetIps extends TestCase {
	private $api;

	protected function setUp() {
		parent::setUp();

		$this->api = $this->getAPIMock();
		$this->api->shouldReceive( 'request' )
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
	}

	public function testShouldReturnIpsWhenNoCredentials() {
		$response = $this->api->get_ips();
		$this->assertTrue( $response->success );
		$this->assertContains( '173.245.48.0/20', $response->result->ipv4_cidrs );
		$this->assertContains( '2400:cb00::/32', $response->result->ipv6_cidrs );
	}

	public function testShouldReturnIpsWhenCredentialsSet() {
		$this->api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$response = $this->api->get_ips();
		$this->assertTrue( $response->success );
		$this->assertContains( '173.245.48.0/20', $response->result->ipv4_cidrs );
		$this->assertContains( '2400:cb00::/32', $response->result->ipv6_cidrs );
	}
}
