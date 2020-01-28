<?php

namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Tests\Integration\TestCase as BaseTestCase;
use function WPMedia\Cloudflare\Tests\Integration\getFactory;

abstract class TestCase extends BaseTestCase {
	protected static $api;
	protected static $cf_facade;
	protected static $cf;
	protected static $options;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$api       = getFactory()->getContainer( 'cloudflare_api' );
		self::$cf_facade = getFactory()->getContainer( 'cloudflare_facade' );
		self::$cf        = getFactory()->getContainer( 'cloudflare' );
		self::$options   = getFactory()->getContainer( 'options' );
	}

	protected function getSetting( $setting ) {
		$response = self::$api->get( 'zones/' . self::$zone_id . '/settings/' . $setting );

		if ( $response->success ) {
			return $response->result->value;
		}
	}
}
