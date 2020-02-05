<?php

namespace WPMedia\Cloudflare\Tests\Unit\Cloudflare;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::get_instance
 * @group  Cloudflare
 * @group  CloudflareManager
 */
class Test_GetInstance extends TestCase {

	public function testGetInstanceWithInvalidCFCredentialsNoTransient() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( false );
		Functions\expect( 'set_transient' )->once();
		Functions\when( 'is_wp_error' )->justReturn( true );
		$api->shouldNotReceive( 'set_api_credentials' );

		new Cloudflare( $mocks['options'], $api );
	}

	public function testGetInstanceWithValidCFCredentialsNoTransient() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( false );
		Functions\expect( 'set_transient' )->once();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' );

		new Cloudflare( $mocks['options'], $api );
	}

	public function testGetInstanceWithInValidCFCredentialsAndTransient() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( $mocks['wp_error'] );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( true );
		$api->shouldNotReceive( 'set_api_credentials' );

		new Cloudflare( $mocks['options'], $api );
	}

	public function testGetInstanceWithValidCFCredentialsAndTransient() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( true );
		$api->shouldNotReceive( 'set_api_credentials' );

		new Cloudflare( $mocks['options'], $api );
	}
}
