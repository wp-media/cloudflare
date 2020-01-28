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

		self::$cf      = new Cloudflare( self::$options, self::$cf_facade );
		$has_page_rule = self::$cf->has_page_rule( 'cache_everything' );

		$this->assertTrue( is_wp_error( $has_page_rule ) );
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

		add_filter('site_url', function() { return self::$site_url; } );

		self::$cf      = new Cloudflare( self::$options, self::$cf_facade );
		$has_page_rule = self::$cf->has_page_rule( 'cache_everything' );

		$this->assertEquals( 0, $has_page_rule );
		remove_filter('site_url', function() { return self::$site_url; } );
	}
}
