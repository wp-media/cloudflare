<?php
namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::purge_by_url
 * @group  Cloudflare
 */
class Test_PurgeByUrl extends TestCase {

	public function testPurgeByUrlWithAPIError() {
		$this->setInvalidApiCredentials();

		$cf   = new Cloudflare( self::$options, self::$api );
		$purge_urls = [
			'/',
			'/hello-world'
		];
		$response   = $cf->purge_by_url( null, $purge_urls, null );

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function testPurgeByUrlWithPurgeError() {
		$purge_urls = [
			'/',
			'/hello-world'
		];
		$response   = self::$cf->purge_by_url( null, $purge_urls, null );

		$this->assertTrue( is_wp_error( $response ) );
		$this->assertSame( 'cloudflare_purge_failed', $response->get_error_code() );
	}
}
