<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::get_settings
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_GetSettings extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$api->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$api->get_settings();
	}

	public function testShouldReturnSettingsWhenZoneIdIsSet() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$api->get_settings();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
		$this->assertGreaterThan( 4, $response->result );
	}
}
