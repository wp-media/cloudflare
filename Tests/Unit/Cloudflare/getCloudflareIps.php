<?php

namespace WPMedia\Cloudflare\Tests\Unit\Cloudflare;

use Brain\Monkey\Functions;
use Mockery;
use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::get_cloudflare_ips
 * @group  Cloudflare
 * @group  CloudflareManager
 */
class Test_GetCloudflareIps extends TestCase {

	private function mockGetCloudflareInstance( $api, $wp_error = false ) {
		if ( false === $wp_error ) {
			Functions\expect( 'get_transient' )->once()->with( 'rocket_cloudflare_is_api_keys_valid' )->andReturn( true );
			$api->shouldReceive( 'set_api_credentials' );
		} else {
			Functions\expect( 'get_transient' )
				->once()
				->with( 'rocket_cloudflare_is_api_keys_valid' )
				->andReturn( $wp_error );
			$api->shouldNotReceive( 'set_api_credentials' );
		}
		Functions\when( 'is_wp_error' )->justReturn( true );
	}

	public function testShouldReturnIpsFromCloudflareWhenInvalidCredentials() {
		$mocks    = $this->getConstructorMocksWithIps();
		$api      = $mocks['api'];
		$expected = $mocks['cf_ips'];
		$this->mockGetCloudflareInstance( $api, $mocks['wp_error'] );

		$cloudflare = new Cloudflare( $mocks['options'], $api );

		Functions\expect( 'get_transient' )
			->once()
			->with( 'rocket_cloudflare_ips' )
			->andReturn( false );
		$api->shouldReceive( 'get_ips' )->andReturn( $expected );
		Functions\expect( 'set_transient' )
			->once()
			->with( 'rocket_cloudflare_ips', $expected, Mockery::type( 'integer' ) )
			->andReturnNull();

		$actual = $cloudflare->get_cloudflare_ips();
		$this->assertTrue( $actual->success );
		$this->assertEquals( $expected, $actual );
	}

	public function testShouldReturnCachedIpsWhenInvalidCredentials() {
		$mocks    = $this->getConstructorMocksWithIps();
		$api      = $mocks['api'];
		$expected = $mocks['cf_ips'];
		$this->mockGetCloudflareInstance( $api, $mocks['wp_error'] );

		$cloudflare = new Cloudflare( $mocks['options'], $api );

		Functions\expect( 'get_transient' )
			->once()
			->with( 'rocket_cloudflare_ips' )
			->andReturn( $expected );
		$api->shouldNotReceive( 'get_ips' );

		$actual = $cloudflare->get_cloudflare_ips();
		$this->assertTrue( $actual->success );
		$this->assertEquals( $expected, $actual );
	}

	public function testShouldReturnDefaultIpsWhenErrorReceivedFromCloudflare() {
		$mocks    = $this->getConstructorMocksWithIps();
		$api      = $mocks['api'];
		$expected = $mocks['cf_ips'];
		$this->mockGetCloudflareInstance( $api );

		$cloudflare = new Cloudflare( $mocks['options'], $api );

		Functions\expect( 'get_transient' )
			->once()
			->with( 'rocket_cloudflare_ips' )
			->andReturn( false );
		Functions\expect( 'set_transient' )
			->once()
			->with( 'rocket_cloudflare_ips', Mockery::type( 'stdClass' ), Mockery::type( 'integer' ) )
			->andReturnNull();

		// Mock the failed response from Cloudflare.
		$api_response = json_decode( '{"success":false,"errors":[{"code":1007,"message":"Invalid value"}],"messages":[],"result":null}' );
		$this->assertFalse( $api_response->success );
		$api->shouldReceive( 'get_ips' )->andReturn( $api_response );

		// Run it and check.
		$actual           = $cloudflare->get_cloudflare_ips();
		$this->assertTrue( $actual->success );
		$this->assertEquals( $expected, $actual );
	}

	public function testShouldReturnIpsFromCloudflareWhenValidCredentialsAndNoCachedIps() {
		$mocks    = $this->getConstructorMocksWithIps();
		$api      = $mocks['api'];
		$expected = $mocks['cf_ips'];
		$this->mockGetCloudflareInstance( $api );

		$cloudflare = new Cloudflare( $mocks['options'], $api );

		Functions\expect( 'get_transient' )
			->once()
			->with( 'rocket_cloudflare_ips' )
			->andReturn( $expected );
		$api->shouldNotReceive( 'get_ips' );

		// Run it and check.
		$actual = $cloudflare->get_cloudflare_ips();
		$this->assertTrue( $actual->success );
		$this->assertEquals( $expected, $actual );
	}
}
