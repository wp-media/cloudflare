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

		self::$cf          = new Cloudflare( self::$options, self::$cf_facade );
		$mode              = 'off';
		$set_rocket_loader = self::$cf->set_rocket_loader( $mode );

		$this->assertTrue( is_wp_error( $set_rocket_loader ) );
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

		add_filter('site_url', function() { return self::$site_url; } );

		self::$cf          = new Cloudflare( self::$options, self::$cf_facade );
		$orig              = $this->getSetting( 'rocket_loader' );
		$new               = 'off' == $orig ? 'on' : 'off';
		$set_rocket_loader = self::$cf->set_rocket_loader( $new );
		$new_val           = $this->getSetting( 'rocket_loader' );

		$this->assertSame( $new, $new_val );
		remove_filter('site_url', function() { return self::$site_url; } );
	}
}
