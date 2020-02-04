<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::get_ips
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_GetIps extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$api->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$api->get_ips();
	}

	public function testShouldReturnIps() {
		self::$api->set_api_credentials( self::$email, self::$api_key, null );

		$response = self::$api->get_ips();
		$this->assertTrue( $response->success );
		$this->assertContains( '173.245.48.0/20', $response->result->ipv4_cidrs );
		$this->assertContains( '2400:cb00::/32', $response->result->ipv6_cidrs );
	}
}
