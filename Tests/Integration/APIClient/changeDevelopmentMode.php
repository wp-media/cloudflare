<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

use Exception;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::change_development_mode
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_ChangeDevelopmentMode extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$api->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$api->change_development_mode( 'on' );
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Invalid value for zone setting development_mode' );

		$response = self::$api->change_development_mode( 'invalid' );
	}

	public function testShouldSucceedWhenValidSettingGiven() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$api->change_development_mode( 'off' );
		$this->assertTrue( $response->success );
		$this->assertSame( 'development_mode', $response->result->id );
		$this->assertSame( 'off', $response->result->value );
	}
}
