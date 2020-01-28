<?php
namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::purge_by_url
 * @group  Cloudflare
 */
class Test_PurgeByUrl extends TestCase {

	public function testPurgeByUrlWithAPIError() {
		$data = [
			'cloudflare_email'   => null,
			'cloudflare_api_key' => null,
			'cloudflare_zone_id' => null,
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		self::$cf         = new Cloudflare( self::$options, self::$cf_facade );
		$purge_urls       = [
			'/',
			'/hello-world'
		];
		$purge_cloudflare = self::$cf->purge_by_url( null, $purge_urls, null );

		$this->assertTrue( is_wp_error( $purge_cloudflare ) );
	}

	public function testPurgeByUrlWithPurgeError() {
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

		self::$cf         = new Cloudflare( self::$options, self::$cf_facade );
		$purge_urls       = [
			'/',
			'/hello-world'
		];
		$purge_cloudflare = self::$cf->purge_by_url( null, $purge_urls, null );

		$this->assertTrue( is_wp_error( $purge_cloudflare ) );
		$this->assertSame( 'cloudflare_purge_failed', $purge_cloudflare->get_error_code() );
		remove_filter('site_url', $callback );
	}
}
