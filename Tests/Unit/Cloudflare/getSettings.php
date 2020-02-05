<?php

namespace WPMedia\Cloudflare\Tests\Unit\Cloudflare;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::get_settings
 * @group  Cloudflare
 * @group  CloudflareManager
 */
class Test_GetSettings extends TestCase {

	public function testGetSettingsWithInvalidCredentials() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( $mocks['wp_error'] );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( true );
		$api->shouldNotReceive( 'set_api_credentials' );

		$cloudflare = new Cloudflare( $mocks['options'], $api );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->get_settings()
		);
	}

	public function testGetSettingsWithException() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' );

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$api->shouldReceive( 'change_development_mode' )->andThrow( new \Exception() );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->get_settings()
		);
	}

	public function testGetSettingsWithNoSuccess() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' );

		Functions\when( 'wp_sprintf_l' )->justReturn( '' );
		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$cf_reply   = json_decode( '{"success":false,"errors":[{"code":1007,"message":"Invalid value for zone setting minify"}],"messages":[],"result":null}' );
		$api->shouldReceive( 'get_settings' )->once()->with()->andReturn( $cf_reply );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->get_settings()
		);
	}

	public function testGetSettingsWithSuccess() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		Functions\when( 'get_transient' )->justReturn( true );
		Functions\expect( 'set_transient' )->never();
		Functions\when( 'is_wp_error' )->justReturn( false );
		$api->shouldReceive( 'set_api_credentials' );

		$cloudflare = new Cloudflare( $mocks['options'], $api );
		$cf_reply   = json_decode( '{"result":[{"id":"0rtt","value":"off","modified_on":null,"editable":true},{"id":"advanced_ddos","value":"on","modified_on":null,"editable":false},{"id":"always_online","value":"on","modified_on":"","editable":true},{"id":"always_use_https","value":"off","modified_on":null,"editable":true},{"id":"automatic_https_rewrites","value":"on","modified_on":"","editable":true},{"id":"brotli","value":"on","modified_on":null,"editable":true},{"id":"browser_cache_ttl","value":31536000,"modified_on":"","editable":true},{"id":"browser_check","value":"on","modified_on":null,"editable":true},{"id":"cache_level","value":"aggressive","modified_on":"","editable":true},{"id":"challenge_ttl","value":1800,"modified_on":null,"editable":true},{"id":"ciphers","value":[],"modified_on":null,"editable":true},{"id":"cname_flattening","value":"flatten_at_root","modified_on":null,"editable":false},{"id":"development_mode","value":"off","modified_on":"","time_remaining":0,"editable":true},{"id":"edge_cache_ttl","value":7200,"modified_on":null,"editable":true},{"id":"email_obfuscation","value":"on","modified_on":"","editable":true},{"id":"hotlink_protection","modified_on":"","value":"off","editable":true},{"id":"http2","value":"on","modified_on":null,"editable":false},{"id":"http3","value":"off","modified_on":null,"editable":true},{"id":"ip_geolocation","value":"on","modified_on":"","editable":true},{"id":"ipv6","value":"off","modified_on":"","editable":true},{"id":"max_upload","value":100,"modified_on":null,"editable":true},{"id":"min_tls_version","value":"1.0","modified_on":null,"editable":true},{"id":"minify","value":{"js":"on","css":"on","html":"on"},"modified_on":"","editable":true},{"id":"mirage","value":"off","modified_on":null,"editable":false},{"id":"mobile_redirect","value":{"status":"off","mobile_subdomain":null,"strip_uri":false},"modified_on":null,"editable":true},{"id":"opportunistic_encryption","value":"on","modified_on":null,"editable":true},{"id":"opportunistic_onion","value":"on","modified_on":null,"editable":true},{"id":"origin_error_page_pass_thru","value":"off","modified_on":null,"editable":false},{"id":"polish","value":"off","modified_on":null,"editable":false},{"id":"prefetch_preload","value":"off","modified_on":null,"editable":false},{"id":"privacy_pass","value":"on","modified_on":null,"editable":true},{"id":"pseudo_ipv4","value":"off","modified_on":null,"editable":true},{"id":"response_buffering","value":"off","modified_on":null,"editable":false},{"id":"rocket_loader","value":"off","modified_on":"","editable":true},{"id":"security_header","modified_on":null,"value":{"strict_transport_security":{"enabled":false,"max_age":0,"include_subdomains":false,"preload":false,"nosniff":false}},"editable":true},{"id":"security_level","value":"medium","modified_on":"","editable":true},{"id":"server_side_exclude","value":"on","modified_on":"","editable":true},{"id":"sort_query_string_for_cache","value":"off","modified_on":null,"editable":false},{"id":"ssl","value":"flexible","modified_on":"","certificate_status":"active","validation_errors":[],"editable":true},{"id":"tls_1_2_only","value":"off","modified_on":null,"editable":true},{"id":"tls_1_3","value":"on","modified_on":null,"editable":true},{"id":"tls_client_auth","value":"off","modified_on":null,"editable":true},{"id":"true_client_ip_header","value":"off","modified_on":null,"editable":false},{"id":"waf","value":"off","modified_on":null,"editable":false},{"id":"webp","value":"off","modified_on":null,"editable":false},{"id":"websockets","value":"on","modified_on":"","editable":true}],"success":true,"errors":[],"messages":[]}' );
		$api->shouldReceive( 'get_settings' )->once()->with()->andReturn( $cf_reply );

		$this->assertEquals(
			[
				'cache_level'       => 'aggressive',
				'minify'            => 'on',
				'rocket_loader'     => 'off',
				'browser_cache_ttl' => 31536000,
			],
			$cloudflare->get_settings()
		);
	}
}
