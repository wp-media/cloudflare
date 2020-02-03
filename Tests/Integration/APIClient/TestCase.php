<?php

namespace WPMedia\Cloudflare\Tests\Integration\APIClient;

use WPMedia\Cloudflare\Tests\Integration\TestCase as BaseTestCase;
use function WPMedia\Cloudflare\Tests\Integration\getFactory;

abstract class TestCase extends BaseTestCase {
	protected static $api;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$api = getFactory()->getContainer( 'cloudflare_api' );
	}

	protected function getSetting( $setting ) {
		$method = $this->get_reflective_method( 'get', self::$api );
		$response = $method->invoke( self::$api, 'zones/' . self::$zone_id . "/settings/{$setting}" );

		if ( $response->success ) {
			return $response->result->value;
		}
	}
}
