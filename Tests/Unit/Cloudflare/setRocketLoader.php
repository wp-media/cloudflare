<?php

namespace WPMedia\Cloudflare\Tests\Unit\Cloudflare;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::set_rocket_loader
 * @group  Cloudflare
 * @group  CloudflareManager
 */
class Test_SetRocketLoader extends TestCase {

	public function testSetRocketLoaderWithInvalidCredentials() {
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
			$cloudflare->set_rocket_loader( 'off' )
		);
	}

	public function testSetRocketLoaderWithException() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$api->shouldReceive( 'change_rocket_loader' )->once()->with( 'off' )->andThrow( new \Exception() );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->set_rocket_loader( 'off' )
		);
	}

	public function testSetRocketLoaderWithNoSuccess() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		Functions\when( 'wp_sprintf_l' )->justReturn( '' );
		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$cf_reply   = json_decode( '{"success":false,"errors":[{"code":1007,"message":"Invalid value for zone setting rocket_loader"}],"messages":[],"result":null}' );
		$api->shouldReceive( 'change_rocket_loader' )->once()->with( 'off' )->andReturn( $cf_reply );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->set_rocket_loader( 'off' )
		);
	}

	public function testSetRocketLoaderWithSuccess() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$cf_reply   = json_decode( '{"result":{"id":"rocket_loader","value":"off","modified_on":"","editable":true},"success":true,"errors":[],"messages":[]}' );
		$api->shouldReceive( 'change_rocket_loader' )->once()->with( 'off' )->andReturn( $cf_reply );

		$this->assertEquals(
			'off',
			$cloudflare->set_rocket_loader( 'off' )
		);
	}
}
