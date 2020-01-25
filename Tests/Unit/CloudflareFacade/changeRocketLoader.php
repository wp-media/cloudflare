<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::change_rocket_loader
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_ChangeRocketLoader extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings', false );

		$settings->shouldReceive( 'change_rocket_loader' )
		         ->once()
		         ->with( null, 'off' )
		         ->andReturnUsing( function() {
			         throw new AuthenticationException( 'Authentication information must be provided' );
		         } );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$cf->change_rocket_loader( 'off' );
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'change_rocket_loader' )
		         ->once()
		         ->with( 'zone1234', 'invalid' )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'  => [],
				         'success' => false,
				         'errors'  => [
					         (object) [
						         'message' => 'Invalid value for zone setting rocket_loader',
					         ],
				         ],
			         ];
		         } );

		$response = $cf->change_rocket_loader( 'invalid' );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting rocket_loader', $error->message );
	}

	public function testShouldChangeRocketLoaderWhenSettingGiven() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'change_rocket_loader' )
		    ->once()
			->with( 'zone1234', 'on' )
			->andReturnUsing( function() {
				return (object) [
					'result'  => (object) [
						'value' => 'on',
					],
					'success' => true,
					'errors'   => [],
				];
			} );

		$response = $cf->change_rocket_loader( 'on' );
		$this->assertTrue( $response->success );
		$this->assertSame( 'on', $response->result->value );
	}
}
