<?php
namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::set_devmode
 * @group  Cloudflare
 */
class Test_SetDevMode extends TestCase {

	public function testSetDevModeWithAPIError() {
		$data = [
			'cloudflare_email'   => null,
			'cloudflare_api_key' => null,
			'cloudflare_zone_id' => null,
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		self::$cf = new Cloudflare( self::$options, self::$cf_facade );
		$response = self::$cf->set_devmode( false );

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function testSetDevModeWithInvalidValue() {
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
		$new      = 'invalid';
		$response = self::$cf->set_devmode( $new );

		$this->assertFalse( is_wp_error( $response ) );
		$this->assertSame( 'off', $response );

		remove_filter('site_url', $callback );
	}

	public function testSetDevModeWithSuccess() {
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
		$orig     = $this->getSetting( 'development_mode' );
		$new      = 'off' === $orig ? true : false;
		$response = self::$cf->set_devmode( $new );

		$this->assertFalse( is_wp_error( $response ) );
		$this->assertSame( $new, 'off' === $response ? false : true );

		remove_filter('site_url', $callback );
	}
}
