<?php

namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::get_cloudflare_ips
 * @group  Cloudflare
 * @group  CloudflareManager
 */
class Test_GetCloudflareIps extends TestCase {

	public function testShouldReturnIpsFromCloudflareWhenInvalidCredentials() {
		$this->setInvalidApiCredentials();

		$orig_cf_ips = get_transient( 'rocket_cloudflare_ips' );
		$cf          = new Cloudflare( self::$options, self::$api );
		$response    = $cf->get_cloudflare_ips();
		$new_cf_ips  = get_transient( 'rocket_cloudflare_ips' );

		$this->assertFalse( $orig_cf_ips );
		$this->assertEquals( $response, $new_cf_ips );
	}

	public function testShouldReturnCachedIpsWhenInvalidCredentials() {
		delete_transient( 'rocket_cloudflare_ips' );
		$cf = new Cloudflare( self::$options, self::$api );

		// Run it once to cache the response.
		$cf->get_cloudflare_ips();
		$cf_ips = get_transient( 'rocket_cloudflare_ips' );

		// Run it again to check that it pulled from the transient.
		Functions\expect( 'set_transient' )->never();
		$response = $cf->get_cloudflare_ips();

		$this->assertTrue( $cf_ips->success );
		$this->assertTrue( $response->success );
		$this->assertSame( $cf_ips->result->ipv4_cidrs, $response->result->ipv4_cidrs );
		$this->assertSame( $cf_ips->result->ipv6_cidrs, $response->result->ipv6_cidrs );
	}

	public function testShouldReturnIpsFromCloudflareWhenValidCredentialsAndNoCachedIps() {
		$this->setInvalidApiCredentials();

		delete_transient( 'rocket_cloudflare_ips' );
		$response  = self::$cf->get_cloudflare_ips();
		$transient = get_transient( 'rocket_cloudflare_ips' );

		$this->assertTrue( $transient->success );
		$this->assertTrue( $response->success );
		$this->assertSame( $transient->result->ipv4_cidrs, $response->result->ipv4_cidrs );
		$this->assertSame( $transient->result->ipv6_cidrs, $response->result->ipv6_cidrs );
	}
}
