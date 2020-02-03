<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

/**
 * @covers WPMedia\Cloudflare\APIClient::set_api_credentials
 * @group  Cloudflare
 * @group  CloudflareAPI
 */
class Test_SetApiCredentials extends TestCase {

	public function testShouldSetEmail() {
		self::$api->set_api_credentials( null, null, null );
		$this->assertNull( $this->getPropertyValue( 'email' ) );

		self::$api->set_api_credentials( 'test@example.com', null, null );
		$this->assertSame( 'test@example.com', $this->getPropertyValue( 'email' ) );
	}

	public function testShouldSetApiKey() {
		self::$api->set_api_credentials( null, null, null );
		$this->assertNull( $this->getPropertyValue( 'api_key' ) );

		self::$api->set_api_credentials( null, 'someAuthKey', null );
		$this->assertSame( 'someAuthKey', $this->getPropertyValue( 'api_key' ) );
	}

	protected function getPropertyValue( $property ) {
		$property = self::get_reflective_property( $property, self::$api );

		return $property->getValue( self::$api );
	}
}
