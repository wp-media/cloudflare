<?php
namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::set_cache_level
 * @group  Cloudflare
 */
class Test_SetCacheLevel extends TestCase {

	public function testSetCacheLevelWithAPIError() {
		$data = [
			'cloudflare_email'   => null,
			'cloudflare_api_key' => null,
			'cloudflare_zone_id' => null,
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		self::$cf = new Cloudflare( self::$options, self::$cf_facade );
		$mode     = 'basic';
		$response = self::$cf->set_cache_level( $mode );

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function testSetCacheLevelWithInvalidValue() {
		$data = [
			'cloudflare_email'   => self::$email,
			'cloudflare_api_key' => self::$api_key,
			'cloudflare_zone_id' => self::$zone_id,
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		$callback = function() { return self::$site_url; };
		add_filter('site_url', $callback );

		self::$cf = new Cloudflare( self::$options, self::$cf_facade );
		$mode     = 'invalid';
		$response = self::$cf->set_cache_level( $mode );

		$this->assertTrue( is_wp_error( $response ) );
		$this->assertSame( 'cloudflare_cache_level', $response->get_error_code() );
		remove_filter('site_url', $callback );
	}

	public function testSetCacheLevelWithSuccess() {
		$data = [
			'cloudflare_email'   => self::$email,
			'cloudflare_api_key' => self::$api_key,
			'cloudflare_zone_id' => self::$zone_id,
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		$callback = function() { return self::$site_url; };
		add_filter('site_url', $callback );

		self::$cf        = new Cloudflare( self::$options, self::$cf_facade );
		//valid values: aggressive, basic, simplified
		$orig     = $this->getSetting( 'cache_level' );
		$new_val  = $this->getNewCacheLevel( $orig );
		$response = self::$cf->set_cache_level( $new_val );

		$this->assertFalse( is_wp_error( $response ) );
		$this->assertSame( $new_val, $response );

		remove_filter('site_url', $callback );
	}
}
