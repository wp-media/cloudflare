<?php

namespace WPMedia\Cloudflare\Tests\Integration\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::change_browser_cache_ttl
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_ChangeBrowserCacheTtl extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$cf->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$cf->change_browser_cache_ttl( 31536000 );
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$cf->change_browser_cache_ttl( 'invalid' );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting browser_cache_ttl', $error->message );
	}

	public function testShouldChangeBrowserCacheTtlWhenSettingGiven() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$orig      = (int) $this->getSetting( 'browser_cache_ttl' );
		$new_ttl  = $this->getNewTTL( $orig );

		$response  = self::$cf->change_browser_cache_ttl( $new_ttl );
		$this->assertTrue( $response->success );
		$this->assertSame( $new_ttl, $response->result->value );
		$this->assertNotSame( $orig, $response->result->value );
	}
}
