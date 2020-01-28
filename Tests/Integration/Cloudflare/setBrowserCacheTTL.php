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

		self::$cf              = new Cloudflare( self::$options, self::$cf_facade );
		$orig                  = (int) $this->getSetting( 'browser_cache_ttl' );
		$new                   = $orig > 0 ? $orig - 3600 : 3600;
		$set_browser_cache_ttl = self::$cf->set_browser_cache_ttl( $new );

		$this->assertTrue( is_wp_error( $set_browser_cache_ttl ) );
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

		add_filter('site_url', function() { return self::$site_url; } );

		self::$cf              = new Cloudflare( self::$options, self::$cf_facade );
		$new                   = 29;
		//valid values: 0, 30, 60, 300, 1200, 1800, 3600, 7200, 10800, 14400, 18000, 28800, 43200, 57600, 72000, 86400, 172800, 259200, 345600, 432000, 691200, 1382400, 2073600, 2678400, 5356800, 16070400, 31536000
		$set_browser_cache_ttl = self::$cf->set_browser_cache_ttl( $new );

		$this->assertTrue( is_wp_error( $set_browser_cache_ttl ) );
		$this->assertSame( 'cloudflare_browser_cache', $set_browser_cache_ttl->get_error_code() );
		remove_filter('site_url', function() { return self::$site_url; } );
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

		add_filter('site_url', function() { return self::$site_url; } );

		self::$cf              = new Cloudflare( self::$options, self::$cf_facade );
		$orig                  = (int) $this->getSetting( 'browser_cache_ttl' );
		//valid values: 0, 30, 60, 300, 1200, 1800, 3600, 7200, 10800, 14400, 18000, 28800, 43200, 57600, 72000, 86400, 172800, 259200, 345600, 432000, 691200, 1382400, 2073600, 2678400, 5356800, 16070400, 31536000
		$valid_arr             = array_values( array_diff( [ 0, 30, 60, 300, 1200, 1800, 3600, 7200, 10800, 14400, 18000, 28800, 43200, 57600, 72000, 86400, 172800, 259200, 345600, 432000, 691200, 1382400, 2073600, 2678400, 5356800, 16070400, 31536000 ], [ $orig ] ) );
		$new_val               = $valid_arr[ rand( 0, count( $valid_arr ) - 1 ) ];
		$set_browser_cache_ttl = self::$cf->set_browser_cache_ttl( $new_val );
		$new                   = (int) $this->getSetting( 'browser_cache_ttl' );

		$this->assertSame( $new, $set_browser_cache_ttl );
		remove_filter('site_url', function() { return self::$site_url; } );
	}
}
