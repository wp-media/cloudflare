<?php

namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::set_browser_cache_ttl
 * @group  Cloudflare
 */
class Test_SetBrowserCacheTTL extends TestCase {

	public function testSetBrowserCacheTTLWithAPIError() {
		$this->setInvalidApiCredentials();
		$cf       = new Cloudflare( self::$options, self::$cf_facade );
		$response = $cf->set_browser_cache_ttl( 3600 );

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function testSetBrowserCacheTTLWithInvalidValue() {
		$response = self::$cf->set_browser_cache_ttl( 29 );

		$this->assertTrue( is_wp_error( $response ) );
		$this->assertSame( 'cloudflare_browser_cache', $response->get_error_code() );
	}

	public function testSetBrowserCacheTTLWithValidValue() {
		$orig     = (int) $this->getSetting( 'browser_cache_ttl' );
		$new_ttl  = $this->getNewTTL( $orig );
		$response = self::$cf->set_browser_cache_ttl( $new_ttl );

		$this->assertFalse( is_wp_error( $response ) );
		$this->assertSame( $new_ttl, $response );
	}
}
