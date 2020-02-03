<?php

namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Cloudflare;

/**
 * @covers WPMedia\Cloudflare\Cloudflare::purge_cloudflare
 * @group  Cloudflare
 */
class Test_PurgeCloudflare extends TestCase {

	public function testPurgeCloudflareWithAPIError() {
		$this->setInvalidApiCredentials();
		$cf       = new Cloudflare( self::$options, self::$cf_facade );
		$response = $cf->purge_cloudflare();

		$this->assertTrue( is_wp_error( $response ) );
	}

	public function testPurgeCloudflareWithSuccess() {
		$this->assertTrue( self::$cf->purge_cloudflare() );
	}
}
