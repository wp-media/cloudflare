<?php
namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::set_rocket_loader
 * @group  Cloudflare
 */
class Test_SetRocketLoader extends TestCase {

	public function testSetRocketLoaderWithAPIError() {
		$data = [
			'cloudflare_email'   => null,
			'cloudflare_api_key' => null,
			'cloudflare_zone_id' => null,
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		self::$cf = new Cloudflare( self::$options, self::$cf_facade );
		$mode     = 'off';
		$response = self::$cf->set_rocket_loader( $mode );

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function testSetRocketLoaderWithSuccess() {
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
		$orig     = $this->getSetting( 'rocket_loader' );
		$new      = 'off' == $orig ? 'on' : 'off';
		$response = self::$cf->set_rocket_loader( $new );

		$this->assertFalse( is_wp_error( $response ) );
		$this->assertSame( $new, $response );

		remove_filter('site_url', $callback );
	}
}
