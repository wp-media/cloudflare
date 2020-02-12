<?php

namespace WPMedia\Cloudflare\Tests\Unit\APIClient;

use WPMedia\Cloudflare\APIClient;

/**
 * @covers WPMedia\Cloudflare\APIClient::set_api_credentials
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_SetApiCredentials extends TestCase {

	public function testShouldSetEmail() {
		$api = new APIClient( 'cloudflare/1.0' );
		$email = $this->get_reflective_property( 'email', $api );

		$api->set_api_credentials( '', null, null );
		$this->assertSame( '', $email->getValue( $api ) );

		$api->set_api_credentials( 'test@example.com', null, null );
		$this->assertSame( 'test@example.com', $email->getValue( $api ) );
	}

	public function testShouldSetApiKeyWhenGiven() {
		$api = new APIClient( 'cloudflare/1.0' );
		$api_key = $this->get_reflective_property( 'api_key', $api );

		$api->set_api_credentials( null, '', null );
		$this->assertSame( '', $api_key->getValue( $api ) );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', null );
		$this->assertSame( 'API_KEY', $api_key->getValue( $api ) );
	}

	public function testShouldSetZoneId() {
		$api = new APIClient( 'cloudflare/1.0' );
		$zone_id = $this->get_reflective_property( 'zone_id', $api );

		$api->set_api_credentials( null, null, 'zone1' );
		$this->assertSame( 'zone1', $zone_id->getValue( $api ) );

		$api->set_api_credentials( 'test@example.com', '', 'zone10' );
		$this->assertSame( 'zone10', $zone_id->getValue( $api ) );

		$api->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );
		$this->assertSame( 'zone1234', $zone_id->getValue( $api ) );
	}
}
