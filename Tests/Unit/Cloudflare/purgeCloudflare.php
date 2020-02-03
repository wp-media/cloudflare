<?php

namespace WPMedia\Cloudflare\Tests\Unit\Cloudflare;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::purge_cloudflare
 * @group  Cloudflare
 * @group  CloudflareManager
 */
class Test_PurgeCloudflare extends TestCase {

	public function testPurgeCloudflareWithInvalidCredentials() {
		$mocks    = $this->getConstructorMocks();
		$api      = $mocks['api'];
		$wp_error = $mocks['wp_error'];

		// The Cloudflare constructor run with transient set as WP_Error.
		Functions\when( 'get_transient' )->justReturn( $wp_error );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( true );
		$api->shouldNotReceive( 'set_api_credentials' );

		$cloudflare = new Cloudflare( $mocks['options'], $api );

		$this->assertEquals(
			$wp_error,
			$cloudflare->purge_cloudflare()
		);
	}

	public function testPurgeCloudflareWithException() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$api->shouldReceive( 'purge' )->once()->andThrow( new \Exception() );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->purge_cloudflare()
		);
	}


	/**
	 * Test purge Cloudflare with no success.
	 */
	public function testPurgeCloudflareWithNoSuccess() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		// The Cloudflare constructor run with transient set as WP_Error.
		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		Functions\when( 'wp_sprintf_l' )->justReturn( '' );
		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$cf_purge   = json_decode( '{"success":false,"errors":[{"code":7001,"message":"Method GET not available for that URI."}],"messages":[],"result":null}' );
		$api->shouldReceive( 'purge' )->once()->andReturn( $cf_purge );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->purge_cloudflare()
		);
	}

	public function testPurgeCloudflareWithSuccess() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$cf_purge   = json_decode( '{"success": true,"errors": [],"messages": [],"result": {"id": ""}}' );
		$api->shouldReceive( 'purge' )->once()->andReturn( $cf_purge );

		$this->assertTrue( $cloudflare->purge_cloudflare() );
	}
}
