<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareSubscriber;

use Brain\Monkey\Functions;
use Mockery;
use WPMedia\Cloudflare\Cloudflare;
use WPMedia\Cloudflare\CloudflareSubscriber;
use WPMedia\Cloudflare\Tests\Unit\TestCase;
use WP_Error;

/**
 * @covers WPMedia\Cloudflare\CloudflareSubscriber::auto_purge
 * @group  Cloudflare
 * @group  CloudflareSubscriber
 */
class Test_AutoPurge extends TestCase {

	/**
	 * Test should not AutoPurge Cloudflare if Cloudflare Addon is disabled.
	 */
	public function testShouldNotAutoPurgeWhenCloudflareIsDisabled() {
		$mocks = $this->getConstructorMocks( 0, '', '', '' );

		$cloudflare = Mockery::mock( Cloudflare::class );
		$cloudflare->shouldNotReceive( 'has_page_rule' );
		$cloudflare->shouldNotReceive( 'purge_cloudflare' );

		$cloudflare_subscriber = new CloudflareSubscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->auto_purge();
	}

	/**
	 * Test should not AutoPurge Cloudflare if Cloudflare Addon is enabled but user has no permissions.
	 */
	public function testShouldNotAutoPurgeWhenCloudflareIsEnabledButNoUserPermissions() {
		$mocks = $this->getConstructorMocks( 1, '', '', '' );
		Functions\when( 'current_user_can' )->justReturn( false );

		$cloudflare = Mockery::mock( Cloudflare::class );
		$cloudflare->shouldNotReceive( 'has_page_rule' );
		$cloudflare->shouldNotReceive( 'purge_cloudflare' );

		$cloudflare_subscriber = new CloudflareSubscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->auto_purge();
	}

	/**
	 * Test should not AutoPurge Cloudflare if Cache Everything Page Rule is not set.
	 */
	public function testShouldNotAutoPurgeWhenNoCacheEverything() {
		$mocks = $this->getConstructorMocks( 1, '', '', '' );
		Functions\when( 'current_user_can' )->justReturn( true );

		$wp_error   = \Mockery::mock( WP_Error::class );
		$cloudflare = Mockery::mock( Cloudflare::class );
		$cloudflare->shouldReceive( 'has_page_rule' )->andReturn( $wp_error );
		$cloudflare->shouldNotReceive( 'purge_cloudflare' );

		$cloudflare_subscriber = new CloudflareSubscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->auto_purge();
	}

	/**
	 * Test should AutoPurge Cloudflare if Cache Everything Page Rule is set but receives an error.
	 */
	public function testShouldAutoPurgeWhenCacheEverythingButReturnError() {
		$mocks = $this->getConstructorMocks( 1, '', '', '' );
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'get_current_user_id' )->justReturn( 1 );

		$wp_error = Mockery::mock( WP_Error::class );
		$wp_error->shouldReceive( 'get_error_message' )->andReturn( 'Error!' );

		$cloudflare = Mockery::mock( Cloudflare::class );
		$cloudflare->shouldReceive( 'has_page_rule' )->andReturn( true );
		$cloudflare->shouldReceive( 'purge_cloudflare' )->andReturn( $wp_error );

		$cloudflare_subscriber = new CloudflareSubscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->auto_purge();
	}

	/**
	 * Test should AutoPurge Cloudflare if Cache Everything Page Rule is set.
	 */
	public function testShouldAutoPurgeWhenCacheEverything() {
		$mocks = $this->getConstructorMocks( 1, '', '', '' );
		Functions\when( 'current_user_can' )->justReturn( true );
		Functions\when( 'get_current_user_id' )->justReturn( 1 );

		$wp_error   = Mockery::mock( WP_Error::class );
		$cloudflare = Mockery::mock( Cloudflare::class );
		$cloudflare->shouldReceive( 'has_page_rule' )->andReturn( true );
		$cloudflare->shouldReceive( 'purge_cloudflare' )->andReturn( true );

		$cloudflare_subscriber = new CloudflareSubscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->auto_purge();
	}

	/**
	 * Get the mocks required by Cloudflare’s constructor.
	 *
	 * @since  1.0
	 * @author Soponar Cristina
	 * @access private
	 *
	 * @param integer $do_cloudflare      - Value to return for $options->get( 'do_cloudflare' ).
	 * @param string  $cloudflare_email   - Value to return for $options->get( 'cloudflare_email' ).
	 * @param string  $cloudflare_api_key - Value to return for $options->get( 'cloudflare_api_key' ).
	 * @param string  $cloudflare_zone_id - Value to return for $options->get( 'cloudflare_zone_id' ).
	 *
	 * @return array                      - Array of Mocks
	 */
	private function getConstructorMocks( $do_cloudflare = 1, $cloudflare_email = '', $cloudflare_api_key = '', $cloudflare_zone_id = '' ) {
		$options      = $this->createMock( 'WP_Rocket\Admin\Options' );
		$options_data = $this->createMock( 'WP_Rocket\Admin\Options_Data' );
		$map          = [
			[
				'do_cloudflare',
				'',
				$do_cloudflare,
			],
			[
				'cloudflare_email',
				null,
				$cloudflare_email,
			],
			[
				'cloudflare_api_key',
				null,
				$cloudflare_api_key,
			],
			[
				'cloudflare_zone_id',
				null,
				$cloudflare_zone_id,
			],
		];
		$options_data->method( 'get' )->will( $this->returnValueMap( $map ) );

		$mocks = [
			'options_data' => $options_data,
			'options'      => $options,
		];

		return $mocks;
	}
}
