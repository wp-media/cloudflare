<?php

namespace WPMedia\Cloudflare\Tests;

/**
 * Initialize the test suite.
 *
 * @param string $test_suite Directory name of the test suite. Default is 'Unit'.
 */
function init_test_suite( $test_suite = 'Unit' ) {
	check_readiness();

	init_constants( $test_suite );

	// Load the Composer autoloader.
	require_once CLOUDFLARE_PLUGIN_ROOT . '/vendor/autoload.php';
	require_once __DIR__ . '/TestCaseTrait.php';

	// Load Patchwork before everything else in order to allow us to redefine WordPress, 3rd party, or any other non-native PHP functions.
	require_once CLOUDFLARE_PLUGIN_ROOT . '/vendor/antecedent/patchwork/Patchwork.php';
}

/**
 * Check the system's readiness to run the tests.
 */
function check_readiness() {
	if ( version_compare( phpversion(), '5.6.0', '<' ) ) {
		trigger_error( 'Unit Tests require PHP 5.6 or higher.', E_USER_ERROR ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Valid use case for our testing suite.
	}

	if ( ! file_exists( dirname( __DIR__ ) . '/vendor/autoload.php' ) ) {
		trigger_error( 'Whoops, we need Composer before we start running tests.  Please type: `composer install`.  When done, try running `phpunit` again.', E_USER_ERROR ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Valid use case for our testing suite.
	}
}

/**
 * Initialize the constants.
 *
 * @param string $test_suite_folder Directory name of the test suite.
 */
function init_constants( $test_suite_folder ) {
	define( 'CLOUDFLARE_PLUGIN_ROOT', dirname( __DIR__ ) . DIRECTORY_SEPARATOR );
	define( 'CLOUDFLARE_TESTS_DIR', __DIR__ . DIRECTORY_SEPARATOR );
	define( 'CLOUDFLARE_PLUGIN_TESTS_ROOT', CLOUDFLARE_TESTS_DIR . $test_suite_folder );

	if ( 'Unit' === $test_suite_folder && ! defined( 'ABSPATH' ) ) {
		define( 'ABSPATH', CLOUDFLARE_PLUGIN_ROOT );
	}
}
