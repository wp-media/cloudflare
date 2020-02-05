<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::purge
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_Purge extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$api->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$api->purge();
	}

	public function testShouldPurgeCacheWhenZoneIdIsSet() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$api->purge();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
	}
}
