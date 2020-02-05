<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

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

		$response = self::$api->change_cache_level( 'invalid' );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting cache_level', $error->message );
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
