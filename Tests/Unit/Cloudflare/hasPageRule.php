<?php

namespace WPMedia\Cloudflare\Tests\Unit\Cloudflare;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::has_page_rule
 * @group  Cloudflare
 * @group  CloudflareManager
 */
class Test_HasPageRule extends TestCase {

	public function testHasRuleWithInvalidCredentials() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		// The Cloudflare constructor run with transient set as WP_Error.
		Functions\when( 'get_transient' )->justReturn( $mocks['wp_error'] );
		$api->shouldNotReceive( 'is_api_keys_valid' );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( true );
		$api->shouldNotReceive( 'set_api_credentials' );

		$cloudflare    = new Cloudflare( $mocks['options'], $api );
		$has_page_rule = $cloudflare->has_page_rule( 'cache_everything' );

		$this->assertEquals(
			$mocks['wp_error'],
			$has_page_rule
		);
	}

	public function testHasRuleWithException() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		$api->shouldNotReceive( 'is_api_keys_valid' );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$api->shouldReceive( 'list_pagerules' )->once()->with()->andThrow( new \Exception() );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->has_page_rule( 'cache_everything' )
		);
	}

	public function testHasRuleWithNoSuccess() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		Functions\when( 'wp_sprintf_l' )->justReturn( '' );
		$cloudflare   = new Cloudflare( $mocks['options'], $api );
		$cf_page_rule = json_decode( '{"success":false,"errors":[{"code":7003,"message":"Could not route to \/zones\/ZONE_ID, perhaps your object identifier is invalid?"},{"code":7000,"message":"No route for that URI"}],"messages":[],"result":null}' );
		$api->shouldReceive( 'list_pagerules' )->once()->with()->andReturn( $cf_page_rule );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->has_page_rule( 'cache_everything' )
		);
	}

	public function testHasRuleWithSuccessButNoPageRule() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );

		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		$cloudflare   = new Cloudflare( $mocks['options'], $api );
		$cf_page_rule = json_decode( '{"result":[{"id":"","targets":[{"target":"url","constraint":{"operator":"matches","value":""}}],"actions":[{"id":"cache_level","value":"bypass"}],"priority":3,"status":"active","created_on":"","modified_on":""},{"id":"","targets":[{"target":"url","constraint":{"operator":"matches","value":""}}],"actions":[{"id":"cache_level","value":""}],"priority":2,"status":"active","created_on":"","modified_on":""}],"success":true,"errors":[],"messages":[]}' );
		Functions\when( 'wp_json_encode' )->justReturn( json_encode( $cf_page_rule ) );
		$api->shouldReceive( 'list_pagerules' )->once()->with()->andReturn( $cf_page_rule );

		$this->assertEquals( 0, $cloudflare->has_page_rule( 'cache_everything' ) );
	}

	public function testHasRuleWithSuccessAndPageRule() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		$api->shouldNotReceive( 'is_api_keys_valid' );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' )->once()->andReturn();

		$cloudflare   = new Cloudflare( $mocks['options'], $api );
		$cf_page_rule = json_decode( '{"result":[{"id":"","targets":[{"target":"url","constraint":{"operator":"matches","value":""}}],"actions":[{"id":"cache_level","value":"bypass"}],"priority":3,"status":"active","created_on":"","modified_on":""},{"id":"","targets":[{"target":"url","constraint":{"operator":"matches","value":""}}],"actions":[{"id":"cache_level","value":"cache_everything"}],"priority":2,"status":"active","created_on":"","modified_on":""}],"success":true,"errors":[],"messages":[]}' );
		Functions\when( 'wp_json_encode' )->justReturn( json_encode( $cf_page_rule ) );
		$api->shouldReceive( 'list_pagerules' )->once()->with()->andReturn( $cf_page_rule );

		$this->assertEquals( 1, $cloudflare->has_page_rule( 'cache_everything' ) );
	}
}
