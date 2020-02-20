<?php

namespace WPMedia\Cloudflare\Tests\Unit\Subscriber;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Subscriber;

/**
 * @covers WPMedia\Cloudflare\Subscriber::save_cloudflare_old_settings
 * @group  Cloudflare
 * @group  CloudflareSubscriber
 */
class Test_SaveOldSettings extends TestCase {

	public function testShouldNotSaveOldSetting() {
		$mocks      = $this->getConstructorMocks();
		$cloudflare = $mocks['cloudflare'];

		Functions\expect( 'current_user_can' )->once()->andReturn( true );
		$cloudflare->shouldNotReceive( 'get_settings' );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );

		$old_value = [
			'do_cloudflare'            => 1,
			'cloudflare_auto_settings' => 1,
			'cloudflare_old_settings'  => 'on,on,off,1',
		];
		$value     = [
			'do_cloudflare'            => 1,
			'cloudflare_auto_settings' => 1,
		];
		$this->assertSame(
			$value,
			$cloudflare_subscriber->save_cloudflare_old_settings( $value, $old_value )
		);
	}

	public function testShouldSaveOldSetting() {
		$mocks      = $this->getConstructorMocks();
		$cloudflare = $mocks['cloudflare'];

		Functions\expect( 'current_user_can' )->once()->andReturn( true );
		Functions\when( 'is_wp_error' )->justReturn( false );

		$cloudflare->shouldReceive( 'get_settings' )->andReturn(
			[
				'cache_level'       => 'aggressive',
				'minify'            => 'on',
				'rocket_loader'     => 'off',
				'browser_cache_ttl' => '31536000',
			]
		);

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );

		$old_value = [
			'do_cloudflare'            => 1,
			'cloudflare_auto_settings' => 0,
			'cloudflare_old_settings'  => 'on,on,off,1',
		];
		$value     = [
			'do_cloudflare'            => 1,
			'cloudflare_auto_settings' => 1,
		];
		$this->assertNotEquals(
			$value,
			$cloudflare_subscriber->save_cloudflare_old_settings( $value, $old_value )
		);
	}
}
