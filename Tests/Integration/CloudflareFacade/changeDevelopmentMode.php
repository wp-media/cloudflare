<?php

namespace WPMedia\Cloudflare\Tests\Integration\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::change_development_mode
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_ChangeDevelopmentMode extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$cf->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$cf->change_development_mode( 'on' );
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$cf->change_development_mode( 'invalid' );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting development_mode', $error->message );
	}

	public function testShouldSucceedWhenValidSettingGiven() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$cf->change_development_mode( 'off' );
		$this->assertTrue( $response->success );
		$this->assertSame( 'development_mode', $response->result->id );
		$this->assertSame( 'off', $response->result->value );
	}
}
