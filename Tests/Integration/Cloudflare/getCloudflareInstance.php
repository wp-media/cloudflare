<?php

namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::get_cloudflare_instance
 * @covers WPMedia\Cloudflare\Cloudflare::is_api_keys_valid
 * @group  Cloudflare
 * @group  CloudflareManager
 */
class Test_GetCloudflareInstance extends TestCase {

	public function testWithCloudflareDisabled() {
		$this->setInvalidApiCredentials( false );

		delete_transient( 'rocket_cloudflare_is_api_keys_valid' );

		new Cloudflare( self::$options, self::$api );
		$is_api_keys_valid_cf = get_transient( 'rocket_cloudflare_is_api_keys_valid' );

		$this->assertFalse( $is_api_keys_valid_cf );
	}

	public function testShouldSetCloudflareApiKeyTransientWhenCFCredentialsAreNull() {
		$this->setInvalidApiCredentials();

		new Cloudflare( self::$options, self::$api );
		$is_api_keys_valid_cf = get_transient( 'rocket_cloudflare_is_api_keys_valid' );

		$this->assertTrue( is_wp_error( $is_api_keys_valid_cf ) );
	}

	public function testShouldSetCloudflareTransientWhenCFCredentialsAreWrong() {
		$data = [
			'cloudflare_email'   => 'test@example.com',
			'cloudflare_api_key' => 'someAuthKey',
			'cloudflare_zone_id' => 'zone1',
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		new Cloudflare( self::$options, self::$api );
		$is_api_keys_valid_cf = get_transient( 'rocket_cloudflare_is_api_keys_valid' );

		$this->assertTrue( is_wp_error( $is_api_keys_valid_cf ) );
	}

	public function testShouldValidateCredentialsButEmptyZoneId() {
		$data = [
			'cloudflare_email'   => 'test@example.com',
			'cloudflare_api_key' => 'someAuthKey',
			'cloudflare_zone_id' => null,
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		new Cloudflare( self::$options, self::$api );
		$is_api_keys_valid_cf = get_transient( 'rocket_cloudflare_is_api_keys_valid' );

		$this->assertTrue( is_wp_error( $is_api_keys_valid_cf ) );
		$this->assertSame( 'cloudflare_no_zone_id', $is_api_keys_valid_cf->get_error_code() );
	}

	public function testShouldValidateCredentialsButWrongZoneId() {
		$data = [
			'cloudflare_email'   => self::$email,
			'cloudflare_api_key' => self::$api_key,
			'cloudflare_zone_id' => 'zone_id',
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		new Cloudflare( self::$options, self::$api );
		$is_api_keys_valid_cf = get_transient( 'rocket_cloudflare_is_api_keys_valid' );

		$this->assertTrue( is_wp_error( $is_api_keys_valid_cf ) );
		$this->assertSame( 'cloudflare_invalid_auth', $is_api_keys_valid_cf->get_error_code() );
	}

	public function testShouldValidateCredentialsButFailsAtDomainMatch() {
		$data = [
			'cloudflare_email'   => self::$email,
			'cloudflare_api_key' => self::$api_key,
			'cloudflare_zone_id' => self::$zone_id,
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		$callback = function() {
			return 'https://example.org';
		};
		add_filter( 'site_url', $callback );

		new Cloudflare( self::$options, self::$api );
		$is_api_keys_valid_cf = get_transient( 'rocket_cloudflare_is_api_keys_valid' );

		$this->assertTrue( is_wp_error( $is_api_keys_valid_cf ) );
		$this->assertSame( 'cloudflare_wrong_zone_id', $is_api_keys_valid_cf->get_error_code() );

		remove_filter( 'site_url', $callback );
	}

	public function testShouldValidateCredentials() {
		$data = [
			'cloudflare_email'   => self::$email,
			'cloudflare_api_key' => self::$api_key,
			'cloudflare_zone_id' => self::$zone_id,
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		$callback = function() {
			return self::$site_url;
		};
		add_filter( 'site_url', $callback );

		new Cloudflare( self::$options, self::$api );
		$is_api_keys_valid_cf = get_transient( 'rocket_cloudflare_is_api_keys_valid' );

		$this->assertTrue( $is_api_keys_valid_cf );
		remove_filter( 'site_url', $callback );
	}
}
