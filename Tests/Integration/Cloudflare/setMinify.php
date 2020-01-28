<?php
namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::set_minify
 * @group  Cloudflare
 */
class Test_SetMinify extends TestCase {

	public function testSetMinifyWithAPIError() {
		$data = [
			'cloudflare_email'   => null,
			'cloudflare_api_key' => null,
			'cloudflare_zone_id' => null,
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		self::$cf   = new Cloudflare( self::$options, self::$cf_facade );
		$mode       = 'off';
		$set_minify = self::$cf->set_minify( $mode );

		$this->assertTrue( is_wp_error( $set_minify ) );
	}

	public function testSetMinifyWithInvalidValue() {
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

		self::$cf   = new Cloudflare( self::$options, self::$cf_facade );
		$new        = 'invalid';
		$set_minify = self::$cf->set_minify( $new );

		$this->assertTrue( is_wp_error( $set_minify ) );
		$this->assertSame( 'cloudflare_minification', $set_minify->get_error_code() );
		remove_filter('site_url', $callback );
	}

	public function testSetMinifyWithSuccess() {
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

		self::$cf   = new Cloudflare( self::$options, self::$cf_facade );
		$orig       = $this->getSetting( 'minify' );
		$new        = 'off' == $orig->js ? 'on' : 'off';
		$set_minify = self::$cf->set_minify( $new );
		$new_val    = $this->getSetting( 'minify' );

		$this->assertSame( $new, $new_val->js );
		remove_filter('site_url', $callback );
	}
}
