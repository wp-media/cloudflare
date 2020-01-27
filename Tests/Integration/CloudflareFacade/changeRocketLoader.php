<?php

namespace WPMedia\Cloudflare\Tests\Integration\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::change_rocket_loader
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_ChangeRocketLoader extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$cf->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$cf->change_rocket_loader( 'off' );
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$cf->change_rocket_loader( 'invalid' );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting rocket_loader', $error->message );
	}

	public function testShouldChangeRocketLoaderWhenSettingGiven() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$orig     = $this->getSetting( 'rocket_loader' );
		$new      = 'off' == $orig ? 'on' : 'off';
		$response = self::$cf->change_rocket_loader( $new );

		$this->assertTrue( $response->success );
		$this->assertSame( $new, $response->result->value );
		$this->assertNotSame( $orig, $response->result->value );
	}
}
