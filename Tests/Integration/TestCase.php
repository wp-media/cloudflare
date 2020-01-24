<?php

namespace WPMedia\Cloudflare\Tests\Integration;

use Brain\Monkey;
use WPMedia\Cloudflare\Tests\TestCaseTrait;
use WP_UnitTestCase;

abstract class TestCase extends WP_UnitTestCase {
	use TestCaseTrait;

	protected static $api_key;
	protected static $email;
	protected static $zone_id;

	/**
	 * Name of the API credentials config file, if applicable. Set in the test or new TestCase.
	 *
	 * For example: rocketcdn.php or cloudflare.php.
	 *
	 * @var string
	 */
	protected static $api_credentials_config_file;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		// Set up the Cloudflare API's credentials.
		self::$api_credentials_config_file = 'cloudflare.php';
		self::$email                       = self::getApiCredential( 'ROCKET_CLOUDFLARE_EMAIL' );
		self::$api_key                     = self::getApiCredential( 'ROCKET_CLOUDFLARE_API_KEY' );
		self::$zone_id                     = self::getApiCredential( 'ROCKET_CLOUDFLARE_ZONE_ID' );
	}

	/**
	 * Prepares the test environment before each test.
	 */
	public function setUp() {
		parent::setUp();
		Monkey\setUp();

		getFactory()->restoreState();
	}

	/**
	 * Cleans up the test environment after each test.
	 */
	public function tearDown() {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Gets the credential's value from either an environment variable (stored locally on the machine or CI) or from a
	 * local constant defined in `tests/env/local/cloudflare.php`.
	 *
	 * @param string $name Name of the environment variable or constant to find.
	 *
	 * @return string returns the value if available; else an empty string.
	 */
	protected static function getApiCredential( $name ) {
		$var = getenv( $name );
		if ( ! empty( $var ) ) {
			return $var;
		}

		if ( ! self::$api_credentials_config_file ) {
			return '';
		}

		$config_file = dirname( __DIR__ ) . '/env/local/cloudflare.php';
		if ( ! is_readable( $config_file ) ) {
			return '';
		}

		// This file is local to the developer's machine and not stored in the repo.
		require_once $config_file;

		return rocket_get_constant( $name, '' );
	}
}
