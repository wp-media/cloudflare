<?php
namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::has_page_rule
 * @group  Cloudflare
 */
class Test_HasPageRule extends TestCase {

	public function testHasPageRuleWithAPIError() {
		$data = [
			'cloudflare_email'   => null,
			'cloudflare_api_key' => null,
			'cloudflare_zone_id' => null,
			'do_cloudflare'      => true,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		self::$cf = new Cloudflare( self::$options, self::$cf_facade );
		$response = self::$cf->has_page_rule( 'cache_everything' );

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function testHasPageRuleWithSuccessButNoPageRule() {
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
		$response = self::$cf->has_page_rule( 'cache_everything' );

		$this->assertEquals( 0, $response );
		remove_filter('site_url', $callback );
	}
}
