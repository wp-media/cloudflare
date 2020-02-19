<?php

namespace WPMedia\Cloudflare\Tests\Unit;

use Brain\Monkey\Functions;
use Mockery;
use WPMedia\PHPUnit\Unit\TestCase as WPMediaTestCase;

abstract class TestCase extends WPMediaTestCase {
	protected static $stubPolyfills = true;
	protected static $mockCommonWpFunctionsInSetUp = true;

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();

		rocket_get_constant( 'WP_ROCKET_VERSION', '3.5' );

		Functions\stubs( [ 'sanitize_text_field' ] );
	}

	protected function getAPIMock() {
		return Mockery::mock( 'WPMedia\Cloudflare\APIClient', [ 'cloudflare/1.0' ] )
		              ->makePartial()
		              ->shouldAllowMockingProtectedMethods();
	}
}
