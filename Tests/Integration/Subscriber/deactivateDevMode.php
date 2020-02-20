<?php

namespace WPMedia\Cloudflare\Tests\Integration\Subscriber;

use WPMedia\Cloudflare\Tests\Integration\TestCase;
use function WPMedia\Cloudflare\Tests\Integration\getFactory;

/**
 * @covers WPMedia\Cloudflare\CloudflareSubscriber::deactivate_devmode
 * @group  Cloudflare
 * @group  Subscriber
 */
class Test_DeactivateDevMode extends TestCase {
	private static $options;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$options = getFactory()->getContainer( 'options' );
	}

	public function testShouldDeactivateDevMode() {
		$this->setOptions( [ 'cloudflare_devmode' => 'on' ] );

		do_action( 'rocket_cron_deactivate_cloudflare_devmode' );

		$this->assertSame( 'off', self::$options->get( 'cloudflare_devmode' ) );
		$settings = get_option( 'wp_rocket_settings' );
		$this->assertSame( 'off', $settings['cloudflare_devmode'] );
	}

	public function testShouldNotActivateDevMode() {
		$this->setOptions( [ 'cloudflare_devmode' => 'off' ] );

		do_action( 'rocket_cron_deactivate_cloudflare_devmode' );

		$this->assertSame( 'off', self::$options->get( 'cloudflare_devmode' ) );
		$settings = get_option( 'wp_rocket_settings' );
		$this->assertSame( 'off', $settings['cloudflare_devmode'] );
	}

	private function setOptions( $data ) {
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );
	}
}
