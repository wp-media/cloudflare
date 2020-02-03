<?php

namespace WPMedia\Cloudflare\Tests\Unit\Cloudflare;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::set_minify
 * @group  Cloudflare
 * @group  CloudflareManager
 */
class Test_SetMinify extends TestCase {

	public function testSetMinifyWithInvalidCredentials() {
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
			$cloudflare->set_minify( 'on' )
		);
	}

	public function testSetMinifyWithException() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$api->shouldReceive( 'change_minify' )
		    ->once()
		    ->with(
			    [
				    'css'  => 'on',
				    'html' => 'on',
				    'js'   => 'on',
			    ]
		    )
		    ->andThrow( new \Exception() );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->set_minify( 'on' )
		);
	}


	/**
	 * Test set minify with no success.
	 */
	public function testSetMinifyWithNoSuccess() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		// The Cloudflare constructor run with transient set as WP_Error.
		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' );

		Functions\when( 'wp_sprintf_l' )->justReturn( '' );
		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$cf_reply   = json_decode( '{"success":false,"errors":[{"code":1007,"message":"Invalid value for zone setting minify"}],"messages":[],"result":null}' );
		$api->shouldReceive( 'change_minify' )
		    ->once()
		    ->with(
			    [
				    'css'  => 'on',
				    'html' => 'on',
				    'js'   => 'on',
			    ]
		    )
		    ->andThrow( $cf_reply );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->set_minify( 'on' )
		);
	}

	/**
	 * Test set minify with success.
	 */
	public function testSetMinifyWithSuccess() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' );

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$cf_reply   = json_decode( '{"result":{"id":"minify","value":{"js":"on","css":"on","html":"on"},"modified_on":"","editable":true},"success":true,"errors":[],"messages":[]}' );
		$api->shouldReceive( 'change_minify' )
		    ->once()
		    ->with(
			    [
				    'css'  => 'on',
				    'html' => 'on',
				    'js'   => 'on',
			    ]
		    )
		    ->andThrow( $cf_reply );

		$this->assertEquals(
			'on',
			$cloudflare->set_minify( 'on' )
		);
	}
}
