<?php
namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::has_page_rule
 * @group  Cloudflare
 */
class Test_HasPageRule extends TestCase {

	public function testHasPageRuleWithAPIError() {
		$this->setInvalidApiCredentials();
		$cf = new Cloudflare( self::$options, self::$cf_facade );
		$response = $cf->has_page_rule( 'cache_everything' );

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function testHasPageRuleWithSuccessButNoPageRule() {
		$this->assertEquals( 0, self::$cf->has_page_rule( 'cache_everything' ) );
	}
}
