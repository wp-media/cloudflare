<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

/**
 * @covers WPMedia\Cloudflare\APIClient::get_ips
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_GetIps extends TestCase {

	public function testShouldReturnIpsWhenNoCredentials() {
		self::$api->set_api_credentials( null, null, null );

		$response = self::$api->get_ips();
		$this->assertTrue( $response->success );
		$this->assertContains( '173.245.48.0/20', $response->result->ipv4_cidrs );
		$this->assertContains( '2400:cb00::/32', $response->result->ipv6_cidrs );
	}

	public function testShouldReturnIpsWhenCredentialsSet() {
		self::$api->set_api_credentials( self::$email, self::$api_key, null );

		$response = self::$api->get_ips();
		$this->assertTrue( $response->success );
		$this->assertContains( '173.245.48.0/20', $response->result->ipv4_cidrs );
		$this->assertContains( '2400:cb00::/32', $response->result->ipv6_cidrs );
	}
}
