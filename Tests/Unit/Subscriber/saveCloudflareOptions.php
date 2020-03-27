<?php

namespace WPMedia\Cloudflare\Tests\Unit\Subscriber;

use Brain\Monkey\Functions;
use WPMedia\Cloudflare\Subscriber;

/**
 * @covers WPMedia\Cloudflare\Subscriber::save_cloudflare_options
 * @group  Cloudflare
 * @group  CloudflareSubscriber
 */
class Tes_SaveCloudflareOptions extends TestCase {

	public function testShouldBailOutWhenCurrentUserCant() {
		$mocks = $this->getConstructorMocks();

		Functions\expect( 'current_user_can' )->once()->andReturn( false );
		Functions\expect( 'get_current_user_id' )->never();
		Functions\expect( 'delete_transient' )->never();
		Functions\expect( 'is_wp_error' )->never();
		Functions\expect( 'set_transient' )->never();

		$cloudflare_subscriber = new Subscriber( $mocks['cloudflare'], $mocks['options_data'], $mocks['options'] );
		$cloudflare_subscriber->save_cloudflare_options( [], [] );
	}

	public function testShouldSaveAndRevalidateCloudflareCredentialsWithError() {
		$mocks      = $this->getConstructorMocks();
		$cloudflare = $mocks['cloudflare'];
		$wp_error   = $mocks['wp_error'];

		Functions\expect( 'current_user_can' )->once()->andReturn( true );
		Functions\when( 'is_wp_error' )->justReturn( true );
		Functions\expect( 'get_transient' )->once()->with( 'rocket_cloudflare_is_api_keys_valid' )->andReturn( $wp_error );
		$wp_error->shouldReceive( 'get_error_message' )->andReturn( 'Error!' );
		$cloudflare->shouldReceive( 'is_api_keys_valid' )->andReturn( $wp_error );

		Functions\when( 'get_current_user_id' )->justReturn( 1 );

		Functions\expect( 'delete_transient' )->once()->with( 'rocket_cloudflare_is_api_keys_valid' );
		Functions\expect( 'set_transient' )->once();
		Functions\expect( 'add_settings_error' )->once();
		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );

		$old_value = [
			'cloudflare_email'   => '',
			'cloudflare_api_key' => '',
			'cloudflare_zone_id' => '',
		];
		$value     = [
			'cloudflare_email'   => 'test@test.com',
			'cloudflare_api_key' => '',
			'cloudflare_zone_id' => '',
		];
		$cloudflare_subscriber->save_cloudflare_options( $old_value, $value );
	}

	public function testShouldSaveAndRevalidateCloudflareCredentialsWithSuccess() {
		$mocks      = $this->getConstructorMocks();
		$cloudflare = $mocks['cloudflare'];

		Functions\expect( 'current_user_can' )->once()->andReturn( true );
		$cloudflare->shouldReceive( 'is_api_keys_valid' )->andReturn( true );

		Functions\expect( 'delete_transient' )->once()->with( 'rocket_cloudflare_is_api_keys_valid' );
		Functions\expect( 'set_transient' )->once();
		Functions\expect('is_wp_error')
			->once()
			->andReturn( false );
		Functions\expect( 'add_settings_error' )->never();

		Functions\when( 'get_current_user_id' )->justReturn( 1 );

		Functions\expect( 'get_transient' )->once()->with( 'rocket_cloudflare_is_api_keys_valid' )->andReturn( true );
		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );

		$old_value = [
			'cloudflare_email'   => '',
			'cloudflare_api_key' => '',
			'cloudflare_zone_id' => '',
		];
		$value     = [
			'cloudflare_email'   => 'test@test.com',
			'cloudflare_api_key' => '',
			'cloudflare_zone_id' => '',
		];
		$cloudflare_subscriber->save_cloudflare_options( $old_value, $value );
	}

	public function testShouldSetDevModeWhenError() {
		$mocks      = $this->getConstructorMocks();
		$cloudflare = $mocks['cloudflare'];
		$wp_error   = $mocks['wp_error'];

		Functions\expect( 'current_user_can' )->once()->andReturn( true );
		Functions\expect('is_wp_error')
			->twice()
			->andReturn( false, true );

		Functions\expect( 'get_transient' )->once()->with( 'rocket_cloudflare_is_api_keys_valid' )->andReturn( true );

		$wp_error->shouldReceive( 'get_error_message' )->andReturn( 'Error!' );
		$cloudflare->shouldReceive( 'set_devmode' )->andReturn( $wp_error );

		$cloudflare_update_result   = [];
		$cloudflare_update_result[] = [
			'result'  => 'error',
			'message' => '<strong>WP Rocket: </strong>Cloudflare development mode error: Error!',
		];

		Functions\expect( 'set_transient' )
			->once()
			->with( '1_cloudflare_update_settings', $cloudflare_update_result );

		Functions\when( 'get_current_user_id' )->justReturn( 1 );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );

		$old_value = [ 'cloudflare_devmode' => 0 ];
		$value     = [ 'cloudflare_devmode' => 1 ];
		$cloudflare_subscriber->save_cloudflare_options( $old_value, $value );
	}

	public function testShouldSetDevModeWhenSuccess() {
		$mocks                    = $this->getConstructorMocks();
		$cloudflare               = $mocks['cloudflare'];
		$cloudflare_update_result = [];

		Functions\expect( 'get_transient' )->once()->with( 'rocket_cloudflare_is_api_keys_valid' )->andReturn( true );
		Functions\expect('is_wp_error')->andReturn( false );

		// Set up the set_devmode mocks.
		$cloudflare->shouldReceive( 'set_devmode' )->andReturn( 'on' );
		$cloudflare_update_result[] = [
			'result'  => 'success',
			'message' => '<strong>WP Rocket: </strong>Cloudflare development mode on',
		];

		Functions\expect( 'current_user_can' )->once()->with( 'rocket_manage_options' )->andReturn( true );
		Functions\expect( 'delete_transient' )->never();
		Functions\expect( 'get_current_user_id' )->once()->andReturn( 1 );
		Functions\expect( 'set_transient' )
			->once()
			->with( '1_cloudflare_update_settings', $cloudflare_update_result );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );

		$old_value = [ 'cloudflare_devmode' => 0 ];
		$value     = [ 'cloudflare_devmode' => 1 ];
		$cloudflare_subscriber->save_cloudflare_options( $old_value, $value );
	}

	public function testShouldSetSettingsWhenError() {
		$mocks      = $this->getConstructorMocks();
		$cloudflare = $mocks['cloudflare'];
		$wp_error   = $mocks['wp_error'];

		Functions\expect( 'current_user_can' )->once()->andReturn( true );
		Functions\expect('is_wp_error')
			->andReturn( false, true, true, true, true );

		Functions\expect( 'get_transient' )->once()->with( 'rocket_cloudflare_is_api_keys_valid' )->andReturn( true );

		$wp_error->shouldReceive( 'get_error_message' )->andReturn( 'Error!' );

		$cloudflare->shouldReceive( 'set_cache_level' )->andReturn( $wp_error );
		$cloudflare->shouldReceive( 'set_minify' )->andReturn( $wp_error );
		$cloudflare->shouldReceive( 'set_rocket_loader' )->andReturn( $wp_error );
		$cloudflare->shouldReceive( 'set_browser_cache_ttl' )->andReturn( $wp_error );

		$cloudflare_update_result   = [];
		$cloudflare_update_result[] = [
			'result'  => 'error',
			'message' => '<strong>WP Rocket: </strong>Cloudflare cache level error: Error!',
		];
		$cloudflare_update_result[] = [
			'result'  => 'error',
			'message' => '<strong>WP Rocket: </strong>Cloudflare minification error: Error!',
		];
		$cloudflare_update_result[] = [
			'result'  => 'error',
			'message' => '<strong>WP Rocket: </strong>Cloudflare rocket loader error: Error!',
		];
		$cloudflare_update_result[] = [
			'result'  => 'error',
			'message' => '<strong>WP Rocket: </strong>Cloudflare browser cache error: Error!',
		];

		Functions\expect( 'get_current_user_id' )->once()->andReturn( 1 );
		Functions\expect( 'set_transient' )
			->once()
			->with( '1_cloudflare_update_settings', $cloudflare_update_result );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );
		unset( $GLOBALS['tonya'] );

		$old_value = [
			'cloudflare_auto_settings' => 0,
			'cloudflare_old_settings'  => 'on,on,off,1',
		];
		$value     = [
			'cloudflare_auto_settings' => 1,
			'cloudflare_old_settings'  => 'on,on,off,1',
		];
		$cloudflare_subscriber->save_cloudflare_options( $old_value, $value );
	}

	public function testShouldSetSettingsWhenSuccess() {
		$mocks      = $this->getConstructorMocks();
		$cloudflare = $mocks['cloudflare'];

		Functions\expect( 'current_user_can' )->once()->andReturn( true );

		Functions\expect( 'get_transient' )->once()->with( 'rocket_cloudflare_is_api_keys_valid' )->andReturn( true );
		Functions\expect('is_wp_error')
			->andReturn( false, false, false, false, false );

		$cloudflare->shouldReceive( 'set_cache_level' )->andReturn( 'aggressive' );
		$cloudflare->shouldReceive( 'set_minify' )->andReturn( 'on' );
		$cloudflare->shouldReceive( 'set_rocket_loader' )->andReturn( 'off' );
		$cloudflare->shouldReceive( 'set_browser_cache_ttl' )->andReturn( '31536000' );

		$cloudflare_update_result   = [];
		$cloudflare_update_result[] = [
			'result'  => 'success',
			'message' => '<strong>WP Rocket: </strong>Cloudflare cache level set to Standard',
		];
		$cloudflare_update_result[] = [
			'result'  => 'success',
			'message' => '<strong>WP Rocket: </strong>Cloudflare minification on',
		];
		$cloudflare_update_result[] = [
			'result'  => 'success',
			'message' => '<strong>WP Rocket: </strong>Cloudflare rocket loader off',
		];
		$cloudflare_update_result[] = [
			'result'  => 'success',
			'message' => '<strong>WP Rocket: </strong>Cloudflare browser cache set to 31536000 seconds',
		];

		Functions\expect( 'get_current_user_id' )->once()->andReturn( 1 );
		Functions\expect( 'set_transient' )
			->once()
			->with( '1_cloudflare_update_settings', $cloudflare_update_result );

		$cloudflare_subscriber = new Subscriber( $cloudflare, $mocks['options_data'], $mocks['options'] );

		$old_value = [
			'cloudflare_auto_settings' => 0,
			'cloudflare_old_settings'  => 'on,on,off,1',
		];
		$value     = [
			'cloudflare_auto_settings' => 1,
			'cloudflare_old_settings'  => 'on,on,off,1',
		];
		$cloudflare_subscriber->save_cloudflare_options( $old_value, $value );
	}
}
