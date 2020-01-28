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
		//valid values: 0, 30, 60, 300, 1200, 1800, 3600, 7200, 10800, 14400, 18000, 28800, 43200, 57600, 72000, 86400, 172800, 259200, 345600, 432000, 691200, 1382400, 2073600, 2678400, 5356800, 16070400, 31536000
		$valid_arr = array_values( array_diff( [ 0, 30, 60, 300, 1200, 1800, 3600, 7200, 10800, 14400, 18000, 28800, 43200, 57600, 72000, 86400, 172800, 259200, 345600, 432000, 691200, 1382400, 2073600, 2678400, 5356800, 16070400, 31536000 ], [ $orig ] ) );
		$new       = $valid_arr[ rand( 0, count( $valid_arr ) - 1 ) ];

		$response  = self::$cf->change_browser_cache_ttl( $new );
		$this->assertTrue( $response->success );
		$this->assertSame( $new, $response->result->value );
		$this->assertNotSame( $orig, $response->result->value );
	}
}
