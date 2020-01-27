<?php

namespace WPMedia\Cloudflare\Tests\Integration\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;
use Cloudflare\IPs;
use Cloudflare\Zone\Cache;
use Cloudflare\Zone\Pagerules;
use Cloudflare\Zone\Settings;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::set_api_credentials
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_SetApiCredentials extends TestCase {
	private static $version;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$version = rocket_get_constant( 'WP_ROCKET_VERSION', '3.5' );
	}

	public function testShouldSetEmail() {
		self::$cf->set_api_credentials( null, null, null );
		$this->assertNull( self::$api->email );

		self::$cf->set_api_credentials( 'test@example.com', null, null );
		$this->assertSame( 'test@example.com', self::$api->email );
	}

	public function testShouldSetApiKey() {
		self::$cf->set_api_credentials( null, null, null );
		$this->assertNull( self::$api->auth_key );

		self::$cf->set_api_credentials( null, 'someAuthKey', null );
		$this->assertSame( 'someAuthKey', self::$api->auth_key );
	}

	public function testShouldSetCurlOption() {
		self::$api->curl_options = null;
		self::$cf->set_api_credentials( 'test@example.com', 'someAuthKey', 'zone1' );
		$this->assertArrayHasKey( CURLOPT_USERAGENT, self::$api->curl_options );
		$this->assertSame( 'wp-rocket/' . self::$version, self::$api->curl_options[ CURLOPT_USERAGENT ] );
	}

	public function testShouldSetPageRules() {
		self::$cf->set_api_credentials( 'test@example.com', 'someAuthKey', 'zone1' );
		$page_rules = $this->get_reflective_property( 'page_rules', self::$cf );
		$this->assertInstanceOf( Pagerules::class, $page_rules->getValue( self::$cf ) );
	}

	public function testShouldSetCache() {
		self::$cf->set_api_credentials( 'test@example.com', 'someAuthKey', 'zone1' );
		$cache = $this->get_reflective_property( 'cache', self::$cf );
		$this->assertInstanceOf( Cache::class, $cache->getValue( self::$cf ) );
	}

	public function testShouldSetSettings() {
		self::$cf->set_api_credentials( 'test@example.com', 'someAuthKey', 'zone1' );
		$settings = $this->get_reflective_property( 'settings', self::$cf );
		$this->assertInstanceOf( Settings::class, $settings->getValue( self::$cf ) );
	}

	public function testShouldSetIps() {
		self::$cf->set_api_credentials( 'test@example.com', 'someAuthKey', 'zone1' );
		$ips = $this->get_reflective_property( 'ips', self::$cf );
		$this->assertInstanceOf( IPs::class, $ips->getValue( self::$cf ) );
	}

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$cf->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$api->get( 'test' );
	}

	public function testShouldGetResponseWhenCredentialsAreValid() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, null );

		$response = self::$api->get( 'test' );
		$this->assertSame( 'get', $response->method );
	}
}
