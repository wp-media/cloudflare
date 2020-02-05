<?php

namespace WPMedia\Cloudflare\Tests\Unit\Cloudflare;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::set_browser_cache_ttl
 * @group  Cloudflare
 * @group  CloudflareManager
 */
class Test_SetBrowserCacheTTL extends TestCase {

	public function testSetBrowserCacheTTLWithInvalidCredentials() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		// The Cloudflare constructor run with transient set as WP_Error.
		Functions\when( 'get_transient' )->justReturn( $mocks['wp_error'] );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( true );
		$api->shouldNotReceive( 'set_api_credentials' );

		$cloudflare = new Cloudflare( $mocks['options'], $api );

		$this->assertEquals(
			$mocks['wp_error'],
			$cloudflare->set_browser_cache_ttl( 31536000 )
		);
	}

	public function testSetBrowserCacheTTLWithException() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$api->shouldReceive( 'change_browser_cache_ttl' )->once()->with( 31536000 )->andThrow( new \Exception() );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->set_browser_cache_ttl( 31536000 )
		);
	}

	public function testSetBrowserCacheTTLWithNoSuccess() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		Functions\when( 'wp_sprintf_l' )->justReturn( '' );
		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$cf_reply   = json_decode( '{"success":false,"errors":[{"code":1007,"message":"Invalid value for zone setting browser_cache_ttl"}],"messages":[],"result":null}' );
		$api->shouldReceive( 'change_browser_cache_ttl' )->once()->with( 31536000 )->andReturn( $cf_reply );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->set_browser_cache_ttl( 31536000 )
		);
	}

	public function testSetBrowserCacheTTLWithSuccess() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$cf_reply   = json_decode( '{"result":{"id":"browser_cache_ttl","value":31536000,"modified_on":"","editable":true},"success":true,"errors":[],"messages":[]}' );
		$api->shouldReceive( 'change_browser_cache_ttl' )->once()->with( 31536000 )->andReturn( $cf_reply );

		$this->assertEquals(
			31536000,
			$cloudflare->set_browser_cache_ttl( 31536000 )
		);
	}
}
