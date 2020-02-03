<?php

namespace WPMedia\Cloudflare\Tests\Unit\Cloudflare;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::is_api_keys_valid
 * @group  Cloudflare
 * @group  CloudflareManager
 */
class Test_IsApiKeyValid extends TestCase {

	public function testApiKeysWithEmptyValues() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		// Set rocket_cloudflare_is_api_keys_valid transient to WP_error for constructor.
		Functions\when( 'get_transient' )->justReturn( $mocks['wp_error'] );

		$cloudflare = new Cloudflare( $mocks['options'], $api );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->is_api_keys_valid( '', '', '' )
		);
	}

	public function testApiKeysWithNullValues() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		// Set rocket_cloudflare_is_api_keys_valid transient to WP_error for constructor.
		Functions\when( 'get_transient' )->justReturn( $mocks['wp_error'] );

		$cloudflare = new Cloudflare( $mocks['options'], $api );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->is_api_keys_valid( null, null, null )
		);
	}

	public function testApiKeysWithEmptyZoneValue() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		// Set rocket_cloudflare_is_api_keys_valid transient to WP_error for constructor.
		Functions\when( 'get_transient' )->justReturn( $mocks['wp_error'] );

		$cloudflare = new Cloudflare( $mocks['options'], $api );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->is_api_keys_valid( 'test@test.com', 'API_KEY', '' )
		);
	}

	/**
	 * Test Cloudflare API valid keys with wrong credentials
	 */
	public function testApiKeysWithWrongCredentialsExceptionThrown() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		// Set rocket_cloudflare_is_api_keys_valid transient to WP_error for constructor.
		Functions\when( 'get_transient' )->justReturn( $mocks['wp_error'] );

		$api->shouldReceive( 'set_api_credentials' )->andThrow( new \Exception() );
		$cloudflare = new Cloudflare( $mocks['options'], $api );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->is_api_keys_valid( 'test@test.com', 'API_KEY', 'ZONE_ID' )
		);
	}

	/**
	 * Test Cloudflare API valid keys with wrong zone id, correct credentials.
	 */
	public function testApiKeysWithWrongZoneId() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		// Set rocket_cloudflare_is_api_keys_valid transient to WP_error for constructor.
		Functions\when( 'get_transient' )->justReturn( $mocks['wp_error'] );

		$api->shouldReceive( 'set_api_credentials' );
		$zone = json_decode( '{"success":false,"errors":[{"code":7003,"message":"Could not route to \/zones\/ZONE_ID, perhaps your object identifier is invalid?"},{"code":7000,"message":"No route for that URI"}],"messages":[],"result":null}' );
		$api->shouldReceive( 'get_zones' )->andReturn( $zone );
		$cloudflare = new Cloudflare( $mocks['options'], $api );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->is_api_keys_valid( 'test@test.com', 'API_KEY', 'ZONE_ID' )
		);
	}

	public function testApiKeysWithWrongDomainMapping() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		// Set rocket_cloudflare_is_api_keys_valid transient to WP_error for constructor.
		Functions\when( 'get_transient' )->justReturn( $mocks['wp_error'] );
		Functions\when( 'get_site_url' )->justReturn( 'another-url.com' );
		Functions\when( 'domain_mapping_siteurl' )->justReturn( 'another-url.com' );
		Functions\when( 'wp_parse_url' )->justReturn( [ 'host' => 'another-url.com' ] );

		$api->shouldReceive( 'set_api_credentials' );
		$zone = json_decode( '{"result":{"id":"","name":"test.com","status":"active","paused":false,"type":"full","development_mode":-1119912,"name_servers":["",""],"original_name_servers":["",""],"original_registrar":null,"original_dnshost":null,"modified_on":"","created_on":"","activated_on":"","meta":{"step":3,"wildcard_proxiable":false,"custom_certificate_quota":0,"page_rule_quota":3,"phishing_detected":false,"multiple_railguns_allowed":false},"owner":{"id":"","type":"user","email":""},"account":{"id":"","name":""},"permissions":["#access:edit","#access:read","#analytics:read","#app:edit","#auditlogs:read","#billing:edit","#billing:read","#cache_purge:edit","#dns_records:edit","#dns_records:read","#lb:edit","#lb:read","#legal:edit","#legal:read","#logs:edit","#logs:read","#member:edit","#member:read","#organization:edit","#organization:read","#ssl:edit","#ssl:read","#stream:edit","#stream:read","#subscription:edit","#subscription:read","#waf:edit","#waf:read","#webhooks:edit","#webhooks:read","#worker:edit","#worker:read","#zone:edit","#zone:read","#zone_settings:edit","#zone_settings:read"],"plan":{"id":"","name":"Free Website","price":0,"currency":"USD","frequency":"","is_subscribed":true,"can_subscribe":false,"legacy_id":"free","legacy_discount":false,"externally_managed":false}},"success":true,"errors":[],"messages":[]}' );
		$api->shouldReceive( 'get_zones' )->andReturn( $zone );
		$cloudflare = new Cloudflare( $mocks['options'], $api );

		$this->assertInstanceOf(
			'WP_Error',
			$cloudflare->is_api_keys_valid( 'test@test.com', 'API_KEY', 'ZONE_ID' )
		);
	}

	public function testApiKeysValid() {
		$mocks = $this->getConstructorMocks();
		$api   = $mocks['api'];

		// Set rocket_cloudflare_is_api_keys_valid transient to WP_error for constructor.
		Functions\when( 'get_transient' )->justReturn( $mocks['wp_error'] );

		Functions\when( 'get_site_url' )->justReturn( 'test.com' );
		Functions\when( 'domain_mapping_siteurl' )->justReturn( 'test.com' );
		Functions\when( 'wp_parse_url' )->justReturn( [ 'host' => 'test.com' ] );

		$api->shouldReceive( 'set_api_credentials' );
		$zone = json_decode( '{"result":{"id":"","name":"test.com","status":"active","paused":false,"type":"full","development_mode":-1119912,"name_servers":["",""],"original_name_servers":["",""],"original_registrar":null,"original_dnshost":null,"modified_on":"","created_on":"","activated_on":"","meta":{"step":3,"wildcard_proxiable":false,"custom_certificate_quota":0,"page_rule_quota":3,"phishing_detected":false,"multiple_railguns_allowed":false},"owner":{"id":"","type":"user","email":""},"account":{"id":"","name":""},"permissions":["#access:edit","#access:read","#analytics:read","#app:edit","#auditlogs:read","#billing:edit","#billing:read","#cache_purge:edit","#dns_records:edit","#dns_records:read","#lb:edit","#lb:read","#legal:edit","#legal:read","#logs:edit","#logs:read","#member:edit","#member:read","#organization:edit","#organization:read","#ssl:edit","#ssl:read","#stream:edit","#stream:read","#subscription:edit","#subscription:read","#waf:edit","#waf:read","#webhooks:edit","#webhooks:read","#worker:edit","#worker:read","#zone:edit","#zone:read","#zone_settings:edit","#zone_settings:read"],"plan":{"id":"","name":"Free Website","price":0,"currency":"USD","frequency":"","is_subscribed":true,"can_subscribe":false,"legacy_id":"free","legacy_discount":false,"externally_managed":false}},"success":true,"errors":[],"messages":[]}' );
		$api->shouldReceive( 'get_zones' )->andReturn( $zone );
		$cloudflare = new Cloudflare( $mocks['options'], $api );

		$this->assertTrue( $cloudflare->is_api_keys_valid( 'test@test.com', 'API_KEY', 'ZONE_ID' ) );
	}
}
