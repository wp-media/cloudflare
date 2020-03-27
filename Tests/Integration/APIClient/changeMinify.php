<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

use Exception;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::change_minify
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_ChangeMinify extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$api->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$api->change_minify(
			[
				'css'  => 'on',
				'html' => 'on',
				'js'   => 'on',
			]
		);
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Invalid value for zone setting minify' );
		$response = self::$api->change_minify( [ 'css' => 'invalid' ] );
	}

	public function testShouldChangeMinifyWhenSettingGiven() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$orig     = (array) $this->getSetting( 'minify' );
		$new      = $this->getNewSetting( $orig );
		$response = self::$api->change_minify( $new );

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
