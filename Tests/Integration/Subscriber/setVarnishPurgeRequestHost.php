<?php

namespace WPMedia\Cloudflare\Tests\Integration\Subscriber;

use WPMedia\Cloudflare\Tests\Integration\TestCase;
use function WPMedia\Cloudflare\Tests\Integration\getFactory;

/**
 * @covers WPMedia\Cloudflare\CloudflareSubscriber::set_varnish_purge_request_host
 * @group  Cloudflare
 * @group  Subscriber
 */
class Test_SetVarnishPurgeRequestHost extends TestCase {

	public function testShouldReturnDefaultWhenVarnishDisabled() {
		$options = getFactory()->getContainer( 'options' );
		$options->set_values( [ 'varnish_auto_purge' => 0 ] );

		$this->assertSame(
			'example.org',
			apply_filters( 'rocket_varnish_purge_request_host', 'example.org' )
		);
	}

	public function testShouldReturnCurrentHostWhenVarnishEnabled() {
		$options = getFactory()->getContainer( 'options' );
		$options->set_values( [  'varnish_auto_purge' => 1 ] );

		$this->assertSame(
			'example.org',
			apply_filters( 'rocket_varnish_purge_request_host', 'test.local' )
		);
	}

	public function testShouldReturnCurrentHostWhenFilterTrue() {
		$options = getFactory()->getContainer( 'options' );
		$options->set_values( [ 'varnish_auto_purge' => 0 ] );

		add_filter( 'do_rocket_varnish_http_purge', '__return_true' );

		$this->assertSame(
			'example.org',
			apply_filters( 'rocket_varnish_purge_request_host', 'test.local' )
		);

		remove_filter( 'do_rocket_varnish_http_purge', '__return_true' );
	}
}
