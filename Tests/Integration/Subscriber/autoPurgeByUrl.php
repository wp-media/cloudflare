<?php

namespace WPMedia\Cloudflare\Tests\Integration\Subscriber;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Tests\Integration\TestCase;
use function WPMedia\Cloudflare\Tests\Integration\getFactory;

/**
 * @covers WPMedia\Cloudflare\Subscriber::auto_purge_by_url
 * @group  Cloudflare
 * @group  Subscriber
 */
class Test_AutoPurgeByUrl extends TestCase {
	private static $options;
	private static $cf;
	private static $post_id;

	public static function wpSetUpBeforeClass( $factory ) {
		self::$cf      = getFactory()->getContainer( 'cloudflare' );
		self::$options = getFactory()->getContainer( 'options' );
		self::$post_id = $factory->post->create();
	}

	public function testShouldBailoutWhenUserCantPurgeCF() {
		$user = $this->factory->user->create( [ 'role' => 'contributor' ] );
		wp_set_current_user( $user );

		Functions\expect( 'get_rocket_i18n_home_url' )->never();

		do_action( 'after_rocket_clean_post', self::$post_id, [], 'en' );
	}

	public function testShouldBailoutWhenNoPageRule() {
		// Set the user who can purge Cloudflare.
		$admin = get_role( 'administrator' );
		$admin->add_cap( 'rocket_purge_cloudflare_cache' );
		$user = $this->factory->user->create( [ 'role' => 'administrator' ] );
		wp_set_current_user( $user );
		$this->assertTrue( current_user_can( 'rocket_purge_cloudflare_cache' ) );

		// Why? Because our test site doesn't have page rules.
		Functions\expect( 'get_rocket_i18n_home_url' )->never();

		do_action( 'after_rocket_clean_post', self::$post_id, [], 'en' );
	}
}
