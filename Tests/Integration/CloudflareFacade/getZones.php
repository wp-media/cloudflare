<?php

namespace WPMedia\Cloudflare\Tests\Integration\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::get_zones
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_GetZones extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$cf->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$cf->get_zones();
	}

	public function testShouldFailWhenInvalid() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, 'ZONE_ID' );

		$response = self::$cf->get_zones();
		$this->assertFalse( $response->success );
		$this->assertCount( 2, $response->errors );
		$zone_error = $response->errors[0];
		$this->assertSame( 7003, $zone_error->code );
		$this->assertSame( 'Could not route to /zones/ZONE_ID, perhaps your object identifier is invalid?', $zone_error->message );
	}

	public function testShouldSucceedWhenZoneExists() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$cf->get_zones();
		$this->assertTrue( $response->success );
		$this->assertSame( self::$zone_id, $response->result->id );
	}
}
