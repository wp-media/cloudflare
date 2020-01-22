<?php
namespace WPMedia\Cloudflare\Tests\Integration\CloudflareSubscriber;

use WPMedia\Cloudflare\Tests\Integration\TestCase;
use WPMedia\Cloudflare\CloudflareSubscriber;
use WPMedia\Cloudflare\Cloudflare;
use WPMedia\Cloudflare\CloudflareFacade;
use WP_Rocket\Admin\Options;
use WP_Rocket\Admin\Options_Data;

class Test_DeactivateDevMode extends TestCase {

	/**
	 * Test should not deactivate cloudflare dev mode when cloudflare addon is off.
	 */
	public function testShouldNotDeactivateDevMode() {
		$options      = new Options( 'wp_rocket_');
		$options_data = new Options_Data( $options->get( 'settings' ) );
		$settings     = [
			'do_cloudflare'      => 0,
			'cloudflare_devmode' => 'on',
		];
		$options_data->set_values( $settings );
		$options->set( 'settings', $options_data->get_options() );

		$cloudflare_facade = new CloudflareFacade();
		$cf_subscriber     = new CloudflareSubscriber( new Cloudflare( $options_data, $cloudflare_facade ), $options_data, $options );
		$cf_subscriber->deactivate_devmode();

		$this->assertSame(
			'on',
			$options_data->get( 'cloudflare_devmode' )
		);
	}

	/**
	 * Test should deactivate cloudflare dev mode.
	 */
	public function testShouldDeactivateDevMode() {
		$options      = new Options( 'wp_rocket_');
		$options_data = new Options_Data( $options->get( 'settings' ) );
		$settings     = [
			'do_cloudflare'      => 1,
			'cloudflare_devmode' => 'on',
		];
		$options_data->set_values( $settings );
		$options->set( 'settings', $options_data->get_options() );

		$cloudflare_facade = new CloudflareFacade();
		$cf_subscriber     = new CloudflareSubscriber( new Cloudflare( $options_data, $cloudflare_facade ), $options_data, $options );
		$cf_subscriber->deactivate_devmode();

		$this->assertSame(
			'off',
			$options_data->get( 'cloudflare_devmode' )
		);
	}
}
