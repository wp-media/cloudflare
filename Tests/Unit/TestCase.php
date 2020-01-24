<?php

namespace WPMedia\Cloudflare\Tests\Unit;

use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use WPMedia\Cloudflare\Tests\TestCaseTrait;

abstract class TestCase extends PHPUnitTestCase {
	use MockeryPHPUnitIntegration;
	use TestCaseTrait;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::stubRocketFunctions();
	}

	/**
	 * Prepares the test environment before each test.
	 */
	protected function setUp() {
		parent::setUp();
		Monkey\setUp();

		$this->mockCommonWpFunctions();

		rocket_get_constant( 'WP_ROCKET_VERSION', '3.5' );
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	protected function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Mock common WP functions.
	 */
	protected function mockCommonWpFunctions() {
		Monkey\Functions\stubs(
			[
				'__',
				'esc_attr__',
				'esc_html__',
				'_x',
				'esc_attr_x',
				'esc_html_x',
				'_n',
				'_nx',
				'esc_attr',
				'esc_html',
				'esc_textarea',
				'esc_url',
			]
		);

		$functions = [
			'_e',
			'esc_attr_e',
			'esc_html_e',
			'_ex',
		];

		foreach ( $functions as $function ) {
			Monkey\Functions\when( $function )->echoArg();
		}
	}
}
