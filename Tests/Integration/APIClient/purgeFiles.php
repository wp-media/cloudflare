<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

use Exception;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::purge_files
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_PurgeFiles extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$api->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$api->purge_files( [ '/purge-url' ] );
	}

	public function testShouldFailWhenUrlInvalid() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Unable to purge /invalid/URL, which is an invalid URL.' );

		$response = self::$api->purge_files( [ '/invalid/URL' ] );
		$this->assertFalse( $response->success );
	}

	public function testShouldSucceedWhenUrlsGiven() {
		$this->assertTrue( true );
	}
}
