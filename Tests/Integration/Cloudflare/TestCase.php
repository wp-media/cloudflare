<?php

namespace WPMedia\Cloudflare\Tests\Integration\Cloudflare;

use WPMedia\Cloudflare\Tests\Integration\TestCase as BaseTestCase;
use function WPMedia\Cloudflare\Tests\Integration\getFactory;

abstract class TestCase extends BaseTestCase {
	protected static $cf;
	protected static $options;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$cf      = getFactory()->getContainer( 'cloudflare' );
		self::$options = getFactory()->getContainer( 'options' );
	}

	public function tearDown() {
		parent::tearDown();

		getFactory()->resetToOriginalState();
	}

	protected function setInvalidApiCredentials( $do_cloudflare = true ) {
		$data = [
			'cloudflare_email'   => null,
			'cloudflare_api_key' => null,
			'cloudflare_zone_id' => null,
			'do_cloudflare'      => $do_cloudflare,
		];

		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );
	}
}
