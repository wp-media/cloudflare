<?php
namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::get_cloudflare_ips
 * @group  Cloudflare
 */
class Test_GetCloudflareIPS extends TestCase {

	public function testGetCloudflareIPSWithAPIError() {
		$data = [
			'cloudflare_email'   => null,
			'cloudflare_api_key' => null,
			'cloudflare_zone_id' => null,
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		$orig_cf_ips        = get_transient( 'rocket_cloudflare_ips' );
		self::$cf           = new Cloudflare( self::$options, self::$cf_facade );
		$get_cloudflare_ips = self::$cf->get_cloudflare_ips();
		$new_cf_ips         = get_transient( 'rocket_cloudflare_ips' );

		$this->assertFalse( $orig_cf_ips );
		$this->assertEquals( $get_cloudflare_ips, $new_cf_ips );
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

		$callback = function() { return self::$site_url; };
		add_filter('site_url', $callback );

		$orig_cf_ips        = get_transient( 'rocket_cloudflare_ips' );
		self::$cf           = new Cloudflare( self::$options, self::$cf_facade );
		$get_cloudflare_ips = self::$cf->get_cloudflare_ips();
		$new_cf_ips         = get_transient( 'rocket_cloudflare_ips' );

		$this->assertEquals( $get_cloudflare_ips, $new_cf_ips );
		remove_filter('site_url', $callback );
	}

}
