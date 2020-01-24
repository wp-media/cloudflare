<?php

namespace WPMedia\Cloudflare\Tests\Integration\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::purge_files
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_PurgeFiles extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		self::$cf->set_api_credentials( null, null, null );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		self::$cf->purge_files( [ '/purge-url' ] );
	}

	public function testShouldFailWhenUrlInvalid() {
		self::$cf->set_api_credentials( self::$email, self::$api_key, self::$zone_id );

		$response = self::$cf->purge_files( ['/invalid/URL'] );
		$this->assertFalse( $response->success );
		$this->assertSame( 'Unable to purge /invalid/URL, which is an invalid URL.', $response->errors[0]->message );
	}
}
