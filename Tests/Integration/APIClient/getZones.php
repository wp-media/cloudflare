<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

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

		$response = self::$api->get_zones();
		$this->assertFalse( $response->success );
		$this->assertCount( 2, $response->errors );
		$zone_error = $response->errors[0];
		$this->assertSame( 7003, $zone_error->code );
		$this->assertSame( 'Could not route to /zones/ZONE_ID, perhaps your object identifier is invalid?', $zone_error->message );
	}

	public function testShouldSucceedWhenZoneExists() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$api->get_zones();
		$this->assertTrue( $response->success );
		$this->assertSame( self::$zone_id, $response->result->id );
	}
}
