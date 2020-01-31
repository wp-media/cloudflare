<?php

namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::set_devmode
 * @group  Cloudflare
 */
class Test_SetDevMode extends TestCase {

	public function testSetDevModeWithAPIError() {
		$this->setInvalidApiCredentials();
		$cf       = new Cloudflare( self::$options, self::$cf_facade );
		$response = $cf->set_devmode( false );

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function testSetDevModeWithInvalidValue() {
		$new      = 'invalid';
		$response = self::$cf->set_devmode( $new );

		$this->assertFalse( is_wp_error( $response ) );
		$this->assertSame( 'off', $response );
	}

	public function testSetDevModeWithSuccess() {
		$orig     = $this->getSetting( 'development_mode' );
		$new      = 'off' === $orig ? true : false;
		$response = self::$cf->set_devmode( $new );

		$this->assertFalse( is_wp_error( $response ) );
		$this->assertSame( $new, 'off' === $response ? false : true );
	}
}
