<?php

namespace WPMedia\Cloudflare\Tests\Integration\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::settings
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_Settings extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$cf->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$cf->settings();
	}

	public function testShouldReturnSettingsWhenZoneIdIsSet() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$cf->settings();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
		$settings = array_column( $response->result, 'id' );

		$this->assertContains( 'browser_cache_ttl', $settings );
		$this->assertContains( 'cache_level', $settings );
	}
}
