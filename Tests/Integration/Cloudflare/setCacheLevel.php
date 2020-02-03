<?php

namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::set_cache_level
 * @group  Cloudflare
 */
class Test_SetCacheLevel extends TestCase {

	public function testSetCacheLevelWithAPIError() {
		$this->setInvalidApiCredentials();
		$cf       = new Cloudflare( self::$options, self::$cf_facade );
		$response = $cf->set_cache_level( 'basic' );

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function testSetCacheLevelWithInvalidValue() {
		$mode     = 'invalid';
		$response = self::$cf->set_cache_level( $mode );

		$this->assertTrue( is_wp_error( $response ) );
		$this->assertSame( 'cloudflare_cache_level', $response->get_error_code() );
	}

	public function testSetCacheLevelWithSuccess() {
		//valid values: aggressive, basic, simplified
		$orig     = $this->getSetting( 'cache_level' );
		$new_val  = $this->getNewCacheLevel( $orig );
		$response = self::$cf->set_cache_level( $new_val );

		$this->assertFalse( is_wp_error( $response ) );
		$this->assertSame( $new_val, $response );
	}
}
