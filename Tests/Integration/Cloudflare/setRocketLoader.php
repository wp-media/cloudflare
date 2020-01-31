<?php
namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::set_rocket_loader
 * @group  Cloudflare
 */
class Test_SetRocketLoader extends TestCase {

	public function testSetRocketLoaderWithAPIError() {
		$this->setInvalidApiCredentials();
		$cf = new Cloudflare( self::$options, self::$cf_facade );
		$response = $cf->set_rocket_loader( 'off' );

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function testSetRocketLoaderWithSuccess() {
		$orig     = $this->getSetting( 'rocket_loader' );
		$new      = 'off' == $orig ? 'on' : 'off';
		$response = self::$cf->set_rocket_loader( $new );

		$this->assertFalse( is_wp_error( $response ) );
		$this->assertSame( $new, $response );
	}
}
