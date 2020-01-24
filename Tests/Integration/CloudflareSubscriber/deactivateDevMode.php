<?php

namespace WPMedia\Cloudflare\Tests\Integration\CloudflareSubscriber;

use WPMedia\Cloudflare\Tests\Integration\TestCase;
use function WPMedia\Cloudflare\Tests\Integration\getFactory;

/**
 * @covers WPMedia\Cloudflare\CloudflareSubscriber::deactivate_devmode
 * @group  Subscriber
 */
class Test_DeactivateDevMode extends TestCase {

	/**
	 * Test should not deactivate cloudflare dev mode when cloudflare addon is off.
	 */
	public function testShouldNotDeactivateDevMode() {
		$data = [
			'do_cloudflare'      => 0,
			'cloudflare_devmode' => 'on',
		];
		update_option( 'wp_rocket_settings', $data );
		$options = getFactory()->getContainer( 'options' );
		$options->set_values( $data );

		do_action( 'rocket_cron_deactivate_cloudflare_devmode' );

		$settings = get_option( 'wp_rocket_settings' );
		$this->assertSame( 'on', $settings['cloudflare_devmode'] );
	}

	/**
	 * Test should deactivate cloudflare dev mode.
	 */
	public function testShouldDeactivateDevMode() {
		$data = [
			'do_cloudflare'      => 1,
			'cloudflare_devmode' => 'on',
		];
		update_option( 'wp_rocket_settings', $data );
		$options = getFactory()->getContainer( 'options' );
		$options->set_values( $data );

		do_action( 'rocket_cron_deactivate_cloudflare_devmode' );

		$this->assertSame( 'off', $options->get( 'cloudflare_devmode' ) );
		$settings = get_option( 'wp_rocket_settings' );
		$this->assertSame( 'off', $settings['cloudflare_devmode'] );
	}
}
