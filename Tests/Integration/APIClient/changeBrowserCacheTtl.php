<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

use Exception;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::change_browser_cache_ttl
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_ChangeBrowserCacheTtl extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$api->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$api->change_browser_cache_ttl( 31536000 );
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Invalid value for zone setting browser_cache_ttl' );

		$response = self::$api->change_browser_cache_ttl( 'invalid' );
	}

	public function testShouldChangeBrowserCacheTtlWhenSettingGiven() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$orig    = (int) $this->getSetting( 'browser_cache_ttl' );
		$new_ttl = $this->getNewTTL( $orig );

		$response = self::$api->change_browser_cache_ttl( $new_ttl );
		$this->assertTrue( $response->success );
		$this->assertSame( $new_ttl, $response->result->value );
		$this->assertNotSame( $orig, $response->result->value );
	}
}
