<?php

namespace WPMedia\Cloudflare\Tests\Unit;

use Brain\Monkey;
use Mockery;
use WPMedia\PHPUnit\Unit\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase {

	protected function setUp() {
		parent::setUp();
		Monkey\setUp();

		rocket_get_constant( 'WP_ROCKET_VERSION', '3.5' );
	}

	protected function getAPIMock() {
		return Mockery::mock( 'WPMedia\Cloudflare\APIClient', [ 'cloudflare/3.5' ] )
		              ->makePartial()
		              ->shouldAllowMockingProtectedMethods();
	}
}
