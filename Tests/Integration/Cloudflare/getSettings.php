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

		self::$cf     = new Cloudflare( self::$options, self::$cf_facade );
		$get_settings = self::$cf->get_settings();

		$this->assertTrue( is_wp_error( $get_settings ) );
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

		add_filter('site_url', function() { return self::$site_url; } );

		self::$cf        = new Cloudflare( self::$options, self::$cf_facade );
		$minify          = $this->getSetting( 'minify' );
		$cf_minify_value = 'on';

		if ( 'off' === $minify->js || 'off' === $minify->css || 'off' === $minify->html ) {
			$cf_minify_value = 'off';
		}

		$orig_cf_settings_array = [
			'cache_level'       => $this->getSetting( 'cache_level' ),
			'minify'            => $cf_minify_value,
			'rocket_loader'     => $this->getSetting( 'rocket_loader' ),
			'browser_cache_ttl' => $this->getSetting( 'browser_cache_ttl' ),
		];

		$get_settings = self::$cf->get_settings();

		$this->assertSame( $orig_cf_settings_array, $get_settings );
		remove_filter('site_url', function() { return self::$site_url; } );
	}
}
