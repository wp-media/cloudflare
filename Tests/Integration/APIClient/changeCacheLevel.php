<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

use Exception;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::change_cache_level
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_ChangeCacheLevel extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$api->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$api->change_cache_level( 'aggressive' );
	}

	public function testShouldFailWhenInvalidLevelGiven() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Invalid value for zone setting cache_level' );

		$response = self::$api->change_cache_level( 'invalid' );
	}

	public function testShouldChangeCacheLevelWhenLevelGiven() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$orig     = $this->getSetting( 'cache_level' );
		$new      = $this->getNewCacheLevel( $orig );
		$response = self::$api->change_cache_level( $new );
		$this->assertTrue( $response->success );
		$this->assertSame( $new, $response->result->value );
		$this->assertNotSame( $orig, $response->result->value );
	}
}
