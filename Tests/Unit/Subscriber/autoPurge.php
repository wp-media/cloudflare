<?php

namespace WPMedia\Cloudflare\Tests\Unit\Subscriber;

use Brain\Monkey\Functions;
use Mockery;
use WPMedia\Cloudflare\Cloudflare;
use WPMedia\Cloudflare\Subscriber;
use WP_Error;

/**
 * @covers WPMedia\Cloudflare\Subscriber::auto_purge
 * @group  Cloudflare
 * @group  CloudflareSubscriber
 */
class Test_AutoPurge extends TestCase {

	public function testShouldNotAutoPurgeWhenNoUserPermissions() {
		$mocks = $this->getConstructorMocks();
		Functions\when( 'current_user_can' )->justReturn( false );

		$cloudflare = Mockery::mock( Cloudflare::class );
		$cloudflare->shouldNotReceive( 'has_page_rule' );
		$cloudflare->shouldNotReceive( 'purge_cloudflare' );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->auto_purge();
	}

	public function testShouldNotAutoPurgeWhenNoCacheEverything() {
		$mocks = $this->getConstructorMocks();
		Functions\when( 'current_user_can' )->justReturn( true );

		$wp_error   = \Mockery::mock( WP_Error::class );
		$cloudflare = Mockery::mock( Cloudflare::class );
		$cloudflare->shouldReceive( 'has_page_rule' )->andReturn( $wp_error );
		$cloudflare->shouldNotReceive( 'purge_cloudflare' );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->auto_purge();
	}

	public function testShouldAutoPurgeWhenCacheEverythingButReturnError() {
		$mocks = $this->getConstructorMocks();
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'get_current_user_id' )->justReturn( 1 );

		$wp_error = Mockery::mock( WP_Error::class );
		$wp_error->shouldReceive( 'get_error_message' )->andReturn( 'Error!' );

		$cloudflare = Mockery::mock( Cloudflare::class );
		$cloudflare->shouldReceive( 'has_page_rule' )->andReturn( true );
		$cloudflare->shouldReceive( 'purge_cloudflare' )->andReturn( $wp_error );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->auto_purge();
	}

	public function testShouldAutoPurgeWhenCacheEverything() {
		$mocks = $this->getConstructorMocks();
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'get_current_user_id' )->justReturn( 1 );

		$wp_error   = Mockery::mock( WP_Error::class );
		$cloudflare = Mockery::mock( Cloudflare::class );
		$cloudflare->shouldReceive( 'has_page_rule' )->andReturn( true );
		$cloudflare->shouldReceive( 'purge_cloudflare' )->andReturn( true );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->auto_purge();
	}
}
