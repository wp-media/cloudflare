<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

use Exception;
use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::get_zones
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_GetZones extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$api->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$api->get_zones();
	}

	public function testShouldFailWhenInvalid() {
		self::$api->set_api_credentials( self::$email, self::$api_key, 'ZONE_ID' );

		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Incorrect Cloudflare Zone ID. Read the <a href="https://docs.wp-rocket.me/article/18-using-wp-rocket-with-cloudflare/?utm_source=wp_plugin&#038;utm_medium=wp_rocket#add-on" rel="noopener noreferrer" target="_blank">documentation</a> for further guidance.' );

		$response = self::$api->get_zones();
	}

	public function testShouldSucceedWhenZoneExists() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$api->get_zones();
		$this->assertTrue( $response->success );
		$this->assertSame( self::$zone_id, $response->result->id );
	}
}
