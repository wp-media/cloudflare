<?php

namespace WPMedia\Cloudflare\Tests\Unit\Cloudflare;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::purge_by_url
 * @group  Cloudflare
 * @group  CloudflareManager
 */
class Test_PurgeByUrl extends TestCase {

	public function testPurgeCloudflareByUrlWithInvalidCredentials() {
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
			$cloudflare->purge_by_url( null, [ '/purge-url' ], null )
		);
	}

	public function testPurgeCloudflareByUrlWithException() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$api->shouldReceive( 'purge_files' )->once()->with( [ '/purge-url' ] )->andThrow( new \Exception() );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->purge_by_url( null, [ '/purge-url' ], null )
		);
	}

	public function testPurgeCloudflareByUrlWithSuccess() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$cf_purge   = json_decode( '{"success": true,"errors": [],"messages": [],"result": {"id": ""}}' );
		$api->shouldReceive( 'purge_files' )->once()->with( [ '/purge-url' ] )->andReturn( $cf_purge );

		$this->assertTrue( $cloudflare->purge_by_url( null, [ '/purge-url' ], null ) );
	}
}
