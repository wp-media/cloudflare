<?php

namespace WPMedia\Cloudflare\Tests\Integration\Subscriber;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Tests\Integration\TestCase;
use function WPMedia\Cloudflare\Tests\Integration\getFactory;

/**
 * @covers WPMedia\Cloudflare\CloudflareSubscriber::auto_purge
 * @group  Cloudflare
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

	public function testShouldBailoutWhenUserCantPurgeCF() {
		$user = $this->factory->user->create( [ 'role' => 'contributor' ] );
		wp_set_current_user( $user );

		Functions\expect( 'is_wp_error' )->never();

		do_action( 'after_rocket_clean_domain' );
	}

	public function testShouldBailoutWhenNoPageRule() {
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
	}
}
