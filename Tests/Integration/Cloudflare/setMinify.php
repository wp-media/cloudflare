<?php

namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::set_minify
 * @group  Cloudflare
 */
class Test_SetMinify extends TestCase {

	public function testSetMinifyWithAPIError() {
		$this->setInvalidApiCredentials();
		$cf       = new Cloudflare( self::$options, self::$cf_facade );
		$response = $cf->set_minify( 'off' );

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function testSetMinifyWithInvalidValue() {
		$new      = 'invalid';
		$response = self::$cf->set_minify( $new );

		$this->assertTrue( is_wp_error( $response ) );
		$this->assertSame( 'cloudflare_minification', $response->get_error_code() );
	}

	public function testSetMinifyWithSuccess() {
		$orig     = $this->getSetting( 'minify' );
		$new      = 'off' == $orig->js ? 'on' : 'off';
		$response = self::$cf->set_minify( $new );

		$this->assertFalse( is_wp_error( $response ) );
		$this->assertSame( $new, $response );
	}
}
