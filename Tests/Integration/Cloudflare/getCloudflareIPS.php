<?php

namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::get_cloudflare_ips
 * @group  Cloudflare
 * @group  CloudflareManager
 */
class Test_GetCloudflareIPS extends TestCase {

	public function testGetCloudflareIPSWithAPIError() {
		$this->setInvalidApiCredentials();

		$orig_cf_ips = get_transient( 'rocket_cloudflare_ips' );
		$cf          = new Cloudflare( self::$options, self::$api );
		$response    = $cf->get_cloudflare_ips();
		$new_cf_ips  = get_transient( 'rocket_cloudflare_ips' );

		$this->assertFalse( $orig_cf_ips );
		$this->assertEquals( $response, $new_cf_ips );
	}

	public function testGetSettingsWithSuccess() {
		$response  = self::$cf->get_cloudflare_ips();
		$transient = get_transient( 'rocket_cloudflare_ips' );

		$this->assertTrue( $transient->success );
		$this->assertTrue( $response->success );
		$this->assertSame( $transient->result->ipv4_cidrs, $response->result->ipv4_cidrs );
		$this->assertSame( $transient->result->ipv6_cidrs, $response->result->ipv6_cidrs );
	}
}
