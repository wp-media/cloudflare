<?php

namespace WPMedia\Cloudflare\Tests\Unit\Cloudflare;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::set_cache_level
 * @group  Cloudflare
 * @group  CloudflareManager

 */
class Test_SetCacheLevel extends TestCase {

	public function testSetCacheLevelWithInvalidCredentials() {
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
			$cloudflare->set_cache_level( 'aggressive' )
		);
	}

	public function testSetCacheLevelWithException() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		// The Cloudflare constructor run with transient set as WP_Error.
		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$api->shouldReceive( 'change_cache_level' )->once()->with( 'aggressive' )->andThrow( new \Exception() );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->set_cache_level( 'aggressive' )
		);
	}

	public function testSetCacheLevelWithSuccess() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		// The Cloudflare constructor run with transient set as WP_Error.
		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$cf_reply   = json_decode( '{"result":{"id":"cache_level","value":"aggressive","modified_on":"","editable":true},"success":true,"errors":[],"messages":[]}' );
		$api->shouldReceive( 'change_cache_level' )->once()->with( 'aggressive' )->andThrow( $cf_reply );

		$this->assertEquals(
			'aggressive',
			$cloudflare->set_cache_level( 'aggressive' )
		);
	}
}
