<?php

namespace WPMedia\Cloudflare\Tests\Integration\CloudflareSubscriber;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Tests\Integration\TestCase;
use function WPMedia\Cloudflare\Tests\Integration\getFactory;

/**
 * @covers WPMedia\Cloudflare\CloudflareSubscriber::auto_purge
 * @group  Subscriber
 */
class Test_AutoPurge extends TestCase {
	private static $options;
	private static $cf;

	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();

		self::$cf      = getFactory()->getContainer( 'cloudflare' );
		self::$options = getFactory()->getContainer( 'options' );
	}

	public function testShouldBailoutWhenCFAddonOff() {
		$data = [ 'do_cloudflare' => 0 ];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		Functions\expect( 'current_user_can' )->with( 'rocket_purge_cloudflare_cache' )->never();
		Functions\expect( 'is_wp_error' )->never();

		do_action( 'after_rocket_clean_domain' );
	}

	public function testShouldBailoutWhenUserCantPurgeCF() {
		$data = [ 'do_cloudflare' => 1 ];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		$user = $this->factory->user->create( [ 'role' => 'contributor' ] );
		wp_set_current_user( $user );

		Functions\expect( 'is_wp_error' )->never();

		do_action( 'after_rocket_clean_domain' );
	}

	public function testShouldBailoutWhenNoPageRule() {
		$data = [
			'cloudflare_email'   => self::$email,
			'cloudflare_api_key' => self::$api_key,
			'cloudflare_zone_id' => self::$zone_id,
			'do_cloudflare'      => 1,
		];
		update_option( 'wp_rocket_settings', $data );
		self::$options->set_values( $data );

		add_filter( 'site_url', [ $this, 'setSiteUrl' ] );

		$admin = get_role( 'administrator' );
		$admin->add_cap( 'rocket_purge_cloudflare_cache' );
		$user = $this->factory->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user );

		Functions\expect( 'is_wp_error' )
			->ordered()
			->once()
			->with( null )
			->andAlsoExpectIt()
			->once()
			->with( 0 );

		do_action( 'after_rocket_clean_domain' );

		remove_filter( 'site_url', [ $this, 'setSiteUrl' ] );
	}

	public function setSiteUrl() {
		return self::$site_url;
	}
}
