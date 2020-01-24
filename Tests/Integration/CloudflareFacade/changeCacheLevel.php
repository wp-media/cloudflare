<?php

namespace WPMedia\Cloudflare\Tests\Integration\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::change_cache_level
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_ChangeCacheLevel extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$cf->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$cf->change_cache_level( 'aggressive' );
	}

	public function testShouldFailWhenInvalidLevelGiven() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$cf->change_cache_level( 'invalid' );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting cache_level', $error->message );
	}

	public function testShouldChangeCacheLevelWhenLevelGiven() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$orig     = $this->getSetting( 'cache_level' );
		$new      = $this->getNewCacheLevel( $orig );
		$response = self::$cf->change_cache_level( $new );
		$this->assertTrue( $response->success );
		$this->assertSame( $new, $response->result->value );
		$this->assertNotSame( $orig, $response->result->value );
	}

	private function getNewCacheLevel( $cache_level ) {
		foreach ( [ 'aggressive', 'basic', 'simplified' ] as $level ) {
			if ( $level !== $cache_level ) {
				return $level;
			}
		}
	}
}
