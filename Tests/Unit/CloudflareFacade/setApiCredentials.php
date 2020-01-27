<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::set_api_credentials
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_SetApiCredentials extends TestCase {

	/**
	 * Test should set the email on the API.
	 */
	public function testShouldSetEmail() {
		list( $api, $cf ) = $this->getMocks();

		$api->shouldReceive( 'setEmail' )->with( null );
		$cf->set_api_credentials( null, null, null );

		$api->shouldReceive( 'setEmail' )->with( 'test@example.com' );
		$cf->set_api_credentials( 'test@example.com', null, null );
	}

	/**
	 * Test should set the API key on the API.
	 */
	public function testShouldSetApiKeyWhenGiven() {
		list( $api, $cf ) = $this->getMocks();

		$api->shouldReceive( 'setAuthKey' )->with( null );
		$cf->set_api_credentials( null, null, null );

		$api->shouldReceive( 'setAuthKey' )->with( 'API_KEY' );
		$cf->set_api_credentials( 'test@example.com', 'API_KEY', null );
	}

	/**
	 * Test should set the curl option with the current version Rocket.
	 */
	public function testShouldSetCurlOptionWithCurrentVersionOfRocket() {
		list( $api, $cf ) = $this->getMocks();

		$api->shouldReceive( 'setCurlOption' )
		    ->once()
		    ->with( CURLOPT_USERAGENT, 'wp-rocket/3.5' );

		$cf->set_api_credentials( null, null, null );
	}

	/**
	 * Test should set the API key on the API.
	 */
	public function testShouldSetZoneId() {
		list( $api, $cf ) = $this->getMocks();
		$zone_id = $this->get_reflective_property( 'zone_id', $cf );

		$cf->set_api_credentials( null, null, 'zone1' );
		$this->assertSame( 'zone1', $zone_id->getValue( $cf ) );

		$cf->set_api_credentials( 'test@example.com', '', 'zone10' );
		$this->assertSame( 'zone10', $zone_id->getValue( $cf ) );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );
		$this->assertSame( 'zone1234', $zone_id->getValue( $cf ) );
	}
}
