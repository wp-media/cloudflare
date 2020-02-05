<?php

namespace WPMedia\Cloudflare\Tests\Unit\Cloudflare;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::get_cloudflare_ips
 * @group  Cloudflare
 * @group  CloudflareManager
 */
class Test_GetCloudflareIPS extends TestCase {

	public function testGetCloudflareIPSWithInvalidCredentials() {
		$mocks    = $this->getConstructorMocksWithIps();
		$api      = $mocks['api'];
		$wp_error = $mocks['wp_error'];

		// The Cloudflare constructor run with transient set as WP_Error.
		Functions\when( 'get_transient' )->justReturn( $wp_error );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( true );
		$api->shouldNotReceive( 'set_api_credentials' );

		$cloudflare = new Cloudflare( $mocks['options'], $api );

		Functions\when( 'get_transient' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' );
		$api->shouldReceive( 'ips' )->andThrow( new \Exception() );
		Functions\expect( 'set_transient' )->once();

		$this->assertEquals(
			$mocks['cf_ips'],
			$cloudflare->get_cloudflare_ips()
		);
	}

	public function testGetCloudflareIPSWithInvalidCredentialsButIPSCached() {
		$mocks    = $this->getConstructorMocksWithIps();
		$api      = $mocks['api'];
		$wp_error = $mocks['wp_error'];

		// The Cloudflare constructor run with transient set as WP_Error.
		Functions\when( 'get_transient' )->justReturn( $wp_error );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( true );
		$api->shouldNotReceive( 'set_api_credentials' );

		$cloudflare = new Cloudflare( $mocks['options'], $api );

		Functions\when( 'get_transient' )->justReturn( $mocks['cf_ips'] );
		$api->shouldNotReceive( 'set_api_credentials' );

		$this->assertEquals(
			$mocks['cf_ips'],
			$cloudflare->get_cloudflare_ips()
		);
	}

	public function testGetCloudflareIPSWithValidCredentialsAndNoCachedIPSWithError() {
		$mocks    = $this->getConstructorMocksWithIps();
		$api      = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' );

		$cloudflare = new Cloudflare( $mocks['options'], $api );

		Functions\when( 'get_transient' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' );
		$cf_reply = json_decode( '{"success":false,"errors":[{"code":1007,"message":"Invalid value"}],"messages":[],"result":null}' );
		$api->shouldReceive( 'ips' )->andReturn( $cf_reply );
		Functions\expect( 'set_transient' )->once();

		$this->assertEquals(
			$mocks['cf_ips'],
			$cloudflare->get_cloudflare_ips()
		);
	}

	public function testGetCloudflareIPSWithValidCredentialsAndNoCachedIPSWithSuccess() {
		$mocks    = $this->getConstructorMocksWithIps();
		$api      = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' );

		$cloudflare = new Cloudflare( $mocks['options'], $api );

		Functions\when( 'get_transient' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' );
		$cf_reply = json_decode( '{"result":{"ipv4_cidrs":["173.245.48.0/20","103.21.244.0/22","103.22.200.0/22","103.31.4.0/22","141.101.64.0/18","108.162.192.0/18","190.93.240.0/20","188.114.96.0/20","197.234.240.0/22","198.41.128.0/17","162.158.0.0/15","104.16.0.0/12","172.64.0.0/13","131.0.72.0/22"],"ipv6_cidrs":["2400:cb00::/32","2606:4700::/32","2803:f800::/32","2405:b500::/32","2405:8100::/32","2a06:98c0::/29","2c0f:f248::/32"],"etag":"fb21705459fea38d23b210ee7d67b753"},"success":true,"errors":[],"messages":[]}' );
		$api->shouldReceive( 'ips' )->andReturn( $cf_reply );
		Functions\expect( 'set_transient' )->once();

		$ips = $cloudflare->get_cloudflare_ips();

		$this->assertEquals(
			$mocks['cf_ips']->result->ipv4_cidrs,
			$ips->result->ipv4_cidrs
		);
		$this->assertEquals(
			$mocks['cf_ips']->result->ipv6_cidrs,
			$ips->result->ipv6_cidrs
		);
	}
}
