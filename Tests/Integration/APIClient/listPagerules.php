<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

use WPMedia\Cloudflare\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\APIClient::list_pagerules
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_ListPagerules extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$api->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$api->list_pagerules();
	}

	public function testShouldGetPageRulesWhenZoneIdIsSet() {
		self::$api->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$api->list_pagerules();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
	}
}
