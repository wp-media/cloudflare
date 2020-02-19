<?php
namespace WPMedia\Cloudflare\Tests\Unit\Subscriber;

use Brain\Monkey\Functions;
use Mockery;
use WPMedia\Cloudflare\Cloudflare;
use WPMedia\Cloudflare\Subscriber;

/**
 * @covers WPMedia\Cloudflare\Subscriber::purge_cache
 * @group  Cloudflare
 * @group  CloudflareSubscriber
 */
class Test_PurgeCache extends TestCase {

	public function testShouldNotPurgeWhenNoUserPermission() {
		$mocks = $this->getConstructorMocks();

		$cloudflare = Mockery::mock( Cloudflare::class);
		$cloudflare->shouldNotReceive('purge_cloudflare');

		$_GET['_wpnonce'] = '';
		Functions\when( 'wp_verify_nonce' )->justReturn( true );
		Functions\when( 'sanitize_key' )->justReturn( '' );
		Functions\when( 'current_user_can' )->justReturn( false );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->purge_cache_no_die();
	}

	public function testShouldPurgeWithError() {
		$mocks = $this->getConstructorMocks();

		Functions\when( 'is_wp_error' )->justReturn( true );

		$wp_error   = Mockery::mock( \WP_Error::class );
		$wp_error->shouldReceive('get_error_message')->andReturn( 'Error!' );

		$cloudflare = Mockery::mock( Cloudflare::class);
		$cloudflare->shouldReceive('purge_cloudflare')->andReturn( $wp_error );

		$_GET['_wpnonce'] = '';
		Functions\when( 'wp_verify_nonce' )->justReturn( true );
		Functions\when( 'sanitize_key' )->justReturn( '' );
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'get_current_user_id' )->justReturn( 1 );

		$cf_purge_result = [
			'result'  => 'error',
			// translators: %s = CloudFare API return message.
			'message' => sprintf( __( '<strong>WP Rocket:</strong> %s', 'rocket' ), 'Error!' ),
		];

		Functions\expect( 'set_transient' )
			->once()
			->with('1_cloudflare_purge_result', $cf_purge_result );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );

		$cloudflare_subscriber->purge_cache_no_die();
	}

	public function testShouldPurgeWithSuccess() {
		$mocks = $this->getConstructorMocks();

		$cloudflare = Mockery::mock( Cloudflare::class);
		$cloudflare->shouldReceive('purge_cloudflare')->andReturn( true );

		$_GET['_wpnonce'] = '';
		Functions\when( 'wp_verify_nonce' )->justReturn( true );
		Functions\when( 'sanitize_key' )->justReturn( '' );
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'get_current_user_id' )->justReturn( 1 );

		$cf_purge_result = [
			'result'  => 'success',
			'message' => __( '<strong>WP Rocket:</strong> Cloudflare cache successfully purged.', 'rocket' ),
		];

		Functions\expect( 'set_transient' )
			->once()
			->with('1_cloudflare_purge_result', $cf_purge_result );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );

		$cloudflare_subscriber->purge_cache_no_die();
	}
}
