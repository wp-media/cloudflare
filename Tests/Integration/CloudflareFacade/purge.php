<?php

namespace WPMedia\Cloudflare\Tests\Integration\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::purge
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_Purge extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$cf->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$cf->purge();
	}

	public function testShouldPurgeCacheWhenZoneIdIsSet() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$cf->purge();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
	}
}
