<?php

namespace WPMedia\Cloudflare\Tests\Integration\CloudflareSubscriber;

use WPMedia\Cloudflare\Tests\Integration\TestCase;

/**
 * @covers WP_Rocket\Subscriber\Addons\Cloudflare\CloudflareSubscriber::set_varnish_localhost
 * @group  Cloudflare
 * @group  CloudflareSubscriber
 */
class Test_SetVarnishLocalhost extends TestCase {

	public function testShouldReturnDefaultWhenCloudflareDisabled() {
		add_filter( 'pre_get_rocket_option_do_cloudflare', '__return_false' );

		$this->assertSame( [], apply_filters( 'rocket_varnish_ip', [] ) );

		remove_filter( 'pre_get_rocket_option_do_cloudflare', '__return_false' );
	}

	public function testShouldReturnDefaultWhenVarnishDisabled() {
		add_filter( 'pre_get_rocket_option_do_cloudflare', '__return_true' );
		add_filter( 'pre_get_rocket_option_varnish_auto_purge', '__return_false' );

		$this->assertSame( [], apply_filters( 'rocket_varnish_ip', [] ) );

		remove_filter( 'pre_get_rocket_option_do_cloudflare', '__return_true' );
		remove_filter( 'pre_get_rocket_option_varnish_auto_purge', '__return_false' );
	}

	public function testShouldReturnLocalhostWhenVarnishEnabled() {
		add_filter( 'pre_get_rocket_option_do_cloudflare', '__return_true' );
		add_filter( 'pre_get_rocket_option_varnish_auto_purge', '__return_true' );

		$this->assertSame( [ 'localhost' ], apply_filters( 'rocket_varnish_ip', [] ) );

		remove_filter( 'pre_get_rocket_option_do_cloudflare', '__return_true' );
		remove_filter( 'pre_get_rocket_option_varnish_auto_purge', '__return_true' );
	}

	public function testShouldReturnLocalhostWhenFilterTrue() {
		add_filter( 'pre_get_rocket_option_do_cloudflare', '__return_true' );
		add_filter( 'do_rocket_varnish_http_purge', '__return_true' );

		$this->assertSame( [ 'localhost' ], apply_filters( 'rocket_varnish_ip', [] ) );

		remove_filter( 'pre_get_rocket_option_do_cloudflare', '__return_true' );
		remove_filter( 'do_rocket_varnish_http_purge', '__return_true' );
	}
}
