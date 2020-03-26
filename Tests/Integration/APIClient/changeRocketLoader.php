<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

use Exception;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::change_rocket_loader
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_ChangeRocketLoader extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$api->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$api->change_rocket_loader( 'off' );
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Invalid value for zone setting rocket_loader' );
		$response = self::$api->change_rocket_loader( 'invalid' );
	}

	public function testShouldChangeRocketLoaderWhenSettingGiven() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$orig     = $this->getSetting( 'rocket_loader' );
		$new      = 'off' == $orig ? 'on' : 'off';
		$response = self::$api->change_rocket_loader( $new );

		$this->assertTrue( $response->success );
		$this->assertSame( $new, $response->result->value );
		$this->assertNotSame( $orig, $response->result->value );
	}
}
