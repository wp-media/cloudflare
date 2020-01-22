<?php
namespace WPMedia\Cloudflare\Tests\Integration\CloudflareSubscriber;

use WPMedia\Cloudflare\Tests\Integration\TestCase;
use WPMedia\Cloudflare\CloudflareSubscriber;
use WPMedia\Cloudflare\Cloudflare;
use WPMedia\Cloudflare\CloudflareFacade;
use WP_Rocket\Admin\Options;
use WP_Rocket\Admin\Options_Data;

class Test_SetVarnishPurgeRequestHost extends TestCase {

	public function testShouldReturnDefaultWhenCloudflareDisabled() {
		update_option(
			'wp_rocket_settings',
			[
				'do_cloudflare' => 0,
			]
		);

		$cloudflare_facade = new CloudflareFacade();
		$cf_subscriber     = new CloudflareSubscriber( new Cloudflare( new Options_Data( (new Options( 'wp_rocket_'))->get( 'settings' ) ), $cloudflare_facade ), new Options_Data( (new Options( 'wp_rocket_'))->get( 'settings' ) ), new Options() );

		$this->assertSame(
			'example.org',
			$cf_subscriber->set_varnish_purge_request_host( 'example.org' )
		);
	}

	public function testShouldReturnDefaultWhenVarnishDisabled() {
		update_option(
			'wp_rocket_settings',
			[
				'do_cloudflare' => 1,
				'varnish_auto_purge' => 0,
			]
		);

		$cloudflare_facade = new CloudflareFacade();
		$cf_subscriber     = new CloudflareSubscriber( new Cloudflare( new Options_Data( (new Options( 'wp_rocket_'))->get( 'settings' ) ), $cloudflare_facade ), new Options_Data( (new Options( 'wp_rocket_'))->get( 'settings' ) ), new Options() );

		$this->assertSame(
			'example.org',
			$cf_subscriber->set_varnish_purge_request_host( 'example.org' )
		);
	}

	public function testShouldReturnCurrentHostWhenVarnishEnabled() {
		update_option(
			'wp_rocket_settings',
			[
				'do_cloudflare' => 1,
				'varnish_auto_purge' => 1,
			]
		);

		$cloudflare_facade = new CloudflareFacade();
		$cf_subscriber     = new CloudflareSubscriber( new Cloudflare( new Options_Data( (new Options( 'wp_rocket_'))->get( 'settings' ) ), $cloudflare_facade ), new Options_Data( (new Options( 'wp_rocket_'))->get( 'settings' ) ), new Options() );

		$this->assertSame(
			'example.org',
			$cf_subscriber->set_varnish_purge_request_host( 'test.local' )
		);
	}

	public function testShouldReturnCurrentHostWhenFilterTrue() {
		update_option(
			'wp_rocket_settings',
			[
				'do_cloudflare' => 1,
				'varnish_auto_purge' => 0,
			]
		);

		add_filter( 'do_rocket_varnish_http_purge', '__return_true' );

		$cloudflare_facade = new CloudflareFacade();
		$cf_subscriber     = new CloudflareSubscriber( new Cloudflare( new Options_Data( (new Options( 'wp_rocket_'))->get( 'settings' ) ), $cloudflare_facade ), new Options_Data( (new Options( 'wp_rocket_'))->get( 'settings' ) ), new Options() );

		$this->assertSame(
			'example.org',
			$cf_subscriber->set_varnish_purge_request_host( 'test.local' )
		);

		remove_filter( 'do_rocket_varnish_http_purge', '__return_true' );
	}
}
