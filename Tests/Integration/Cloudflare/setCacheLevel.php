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

		self::$cf        = new Cloudflare( self::$options, self::$cf_facade );
		$mode            = 'basic';
		$set_cache_level = self::$cf->set_cache_level( $mode );

		$this->assertTrue( is_wp_error( $set_cache_level ) );
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

		add_filter('site_url', function() { return self::$site_url; } );

		self::$cf        = new Cloudflare( self::$options, self::$cf_facade );
		$mode            = 'invalid';
		$set_cache_level = self::$cf->set_cache_level( $mode );

		$this->assertTrue( is_wp_error( $set_cache_level ) );
		$this->assertSame( 'cloudflare_cache_level', $set_cache_level->get_error_code() );
		remove_filter('site_url', function() { return self::$site_url; } );
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

		add_filter('site_url', function() { return self::$site_url; } );

		self::$cf        = new Cloudflare( self::$options, self::$cf_facade );
		//valid values: aggressive, basic, simplified
		$orig            = $this->getSetting( 'cache_level' );
		$valid_arr       = array_values( array_diff( [ 'aggressive', 'basic', 'simplified' ], [ $orig ] ) );
		$new_val         = $valid_arr[ rand( 0, count( $valid_arr ) - 1 ) ];
		$set_cache_level = self::$cf->set_cache_level( $new_val );
		$new             = $this->getSetting( 'cache_level' );

		$this->assertSame( $new, $set_cache_level );
		remove_filter('site_url', function() { return self::$site_url; } );
	}
}
