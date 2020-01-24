<?php

namespace WPMedia\Cloudflare\Tests\Integration\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::change_minify
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_ChangeMinify extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$cf->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$cf->change_minify(
			[
				'css'  => 'on',
				'html' => 'on',
				'js'   => 'on',
			]
		);
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$cf->change_minify( [ 'css' => 'invalid' ] );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting minify', $error->message );
	}

	public function testShouldChangeMinifyWhenSettingGiven() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$orig     = (array) $this->getSetting( 'minify' );
		$new      = $this->getNewSetting( $orig );
		$response = self::$cf->change_minify( $new );

		$this->assertTrue( $response->success );
		$actual = (array) $response->result->value;
		$this->assertSame( $new, $actual );
		$this->assertNotSame( $orig, $actual );
	}

	private function getNewSetting( $orig ) {
		foreach ( $orig as $key => $value ) {
			$orig[ $key ] = 'on' === $value ? 'off' : 'on';
		}

		return $orig;
	}
}
