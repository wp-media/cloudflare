<?php

namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::set_browser_cache_ttl
 * @group  Cloudflare
 */
class Test_SetBrowserCacheTTL extends TestCase {

	public function testSetBrowserCacheTTLWithAPIError() {
		$data = [
			'cloudflare_email'   => null,
			'cloudflare_api_key' => null,
			'cloudflare_zone_id' => null,
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		self::$cf = new Cloudflare( self::$options, self::$cf_facade );
		$response = self::$cf->set_browser_cache_ttl( 3600 );

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function testSetBrowserCacheTTLWithInvalidValue() {
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

		self::$cf = new Cloudflare( self::$options, self::$cf_facade );
		$response = self::$cf->set_browser_cache_ttl( 29 );

		$this->assertTrue( is_wp_error( $response ) );
		$this->assertSame( 'cloudflare_browser_cache', $response->get_error_code() );

		remove_filter( 'site_url', $callback );
	}

	public function testSetBrowserCacheTTLWithValidValue() {
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

		self::$cf = new Cloudflare( self::$options, self::$cf_facade );
		$orig     = (int) $this->getSetting( 'browser_cache_ttl' );
		$new_ttl  = $this->getNewTTL( $orig );
		$response = self::$cf->set_browser_cache_ttl( $new_ttl );

		$this->assertFalse( is_wp_error( $response ) );
		$this->assertSame( $new_ttl, $response );

		remove_filter( 'site_url', $callback );
	}
}
