<?php

namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::get_settings
 * @group  Cloudflare
 */
class Test_GetSettings extends TestCase {

	public function testGetSettingsWithAPIError() {
		$data = [
			'cloudflare_email'   => null,
			'cloudflare_api_key' => null,
			'cloudflare_zone_id' => null,
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		self::$cf = new Cloudflare( self::$options, self::$cf_facade );
		$response = self::$cf->get_settings();

		$this->assertTrue( is_wp_error( $response ) );
	}


	public function testGetSettingsWithSuccess() {
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
		$response = self::$cf->get_settings();

		$this->assertSame( [ 'cache_level', 'minify', 'rocket_loader', 'browser_cache_ttl' ], array_keys( $response ) );
		$this->assertTrue( 'on' === $response['minify'] || 'off' === $response['minify'] );
		$this->assertContains( $response['browser_cache_ttl'], $this->getTTLValidValues() );

		remove_filter( 'site_url', $callback );
	}
}
