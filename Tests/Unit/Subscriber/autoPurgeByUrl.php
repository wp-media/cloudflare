<?php

namespace WPMedia\Cloudflare\Tests\Unit\Subscriber;

use Brain\Monkey\Functions;
use Mockery;
use WPMedia\Cloudflare\Cloudflare;
use WPMedia\Cloudflare\Subscriber;
use WP_Error;

/**
 * @covers WPMedia\Cloudflare\Subscriber::auto_purge_by_url
 * @group  Cloudflare
 * @group  CloudflareSubscriber
 */
class Test_AutoPurgeByUrl extends TestCase {

	public function testShouldNotAutoPurgeByUrlWhenNoUserPermissions() {
		$mocks = $this->getConstructorMocks();
		Functions\when( 'current_user_can' )->justReturn( false );

		$cloudflare = Mockery::mock( Cloudflare::class );
		$cloudflare->shouldNotReceive( 'has_page_rule' );
		$cloudflare->shouldNotReceive( 'purge_by_url' );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );

		$cloudflare_subscriber->auto_purge_by_url( 1, [ '/hello-world' ], '' );
	}

	public function testShouldNotAutoPurgeByUrlWhenNoCacheEverything() {
		$mocks = $this->getConstructorMocks();
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'is_wp_error' )->justReturn( true );
		$wp_error   = Mockery::mock( WP_Error::class );
		$cloudflare = Mockery::mock( Cloudflare::class );
		$cloudflare->shouldReceive( 'has_page_rule' )
		           ->andReturn( $wp_error );
		$cloudflare->shouldNotReceive( 'purge_by_url' );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->auto_purge_by_url( 1, [ '/hello-world' ], '' );
	}

	public function testShouldAutoPurgeByUrlWhenCacheEverythingButReturnError() {
		$mocks = $this->getConstructorMocks();
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'get_current_user_id' )->justReturn( 1 );
		Functions\expect( 'get_rocket_i18n_home_url' )->once()->with( '' )->andReturn( 'http://example.org/' );
		Functions\expect( 'get_feed_link' )->twice()->andReturn( 'http://example.org/feed/', 'http://example.org/feed/comments' );

		$wp_error = Mockery::mock( WP_Error::class );
		$wp_error->shouldReceive( 'get_error_message' )->andReturn( 'Error!' );

		$cloudflare = Mockery::mock( Cloudflare::class );
		$cloudflare->shouldReceive( 'has_page_rule' )->with( 'cache_everything' )->andReturn( true );
		$cloudflare->shouldReceive( 'purge_by_url' )
		           ->with( 1, [
			           '/hello-world',
			           'http://example.org/',
			           'http://example.org/feed/',
			           'http://example.org/feed/comments',
		           ], '' )
		           ->andReturn( $wp_error );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->auto_purge_by_url( 1, [ '/hello-world' ], '' );
	}

	public function testShouldAutoPurgeByUrlWhenCacheEverything() {
		$mocks = $this->getConstructorMocks();
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'get_current_user_id' )->justReturn( 1 );
		Functions\when( 'is_wp_error' )->justReturn( false );
		Functions\expect( 'get_rocket_i18n_home_url' )->once()->with( '' )->andReturn( 'http://example.org/' );
		Functions\expect( 'get_feed_link' )->twice()->andReturn( 'http://example.org/feed/', 'http://example.org/feed/comments' );

		$cloudflare = Mockery::mock( Cloudflare::class );
		$cloudflare->shouldReceive( 'has_page_rule' )->with( 'cache_everything' )->andReturn( true );
		$cloudflare->shouldReceive( 'purge_by_url' )
		           ->with( 1, [
			           '/hello-world',
			           'http://example.org/',
			           'http://example.org/feed/',
			           'http://example.org/feed/comments',
		           ], '' )
		           ->andReturn( true );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->auto_purge_by_url( 1, [ '/hello-world' ], '' );
	}
}
