<?php

namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::get_settings
 * @group  Cloudflare
 */
class Test_GetSettings extends TestCase {

	public function testGetSettingsWithAPIError() {
		$this->setInvalidApiCredentials();
		$cf       = new Cloudflare( self::$options, self::$cf_facade );
		$response = $cf->get_settings();

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function testGetSettingsWithSuccess() {
		$response = self::$cf->get_settings();

		$this->assertSame( [ 'cache_level', 'minify', 'rocket_loader', 'browser_cache_ttl' ], array_keys( $response ) );
		$this->assertTrue( 'on' === $response['minify'] || 'off' === $response['minify'] );
		$this->assertContains( $response['browser_cache_ttl'], $this->getTTLValidValues() );
	}
}
