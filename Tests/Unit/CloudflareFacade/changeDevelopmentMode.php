<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::change_development_mode
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_ChangeDevelopmentMode extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings', false );

		$settings->shouldReceive( 'change_development_mode' )
		         ->once()
		         ->with( null, 'on' )
		         ->andReturnUsing( function() {
			         throw new AuthenticationException( 'Authentication information must be provided' );
		         } );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$cf->change_development_mode( 'on' );
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'change_development_mode' )
		         ->once()
		         ->with( 'zone1234', 'invalid' )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'  => null,
				         'success' => false,
				         'errors'  => [
					         (object) [
						         'message' => 'Invalid value for zone setting development_mode',
					         ],
				         ],
			         ];
		         } );

		$response = $cf->change_development_mode( 'invalid' );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting development_mode', $error->message );
	}

	public function testShouldSucceedWhenValidSettingGiven() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'change_development_mode' )
		         ->once()
		         ->with( 'zone1234', 'invalid' )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'  => (object) [
					         'id'    => 'development_mode',
					         'value' => 'off',
				         ],
				         'success' => true,
				         'errors'  => [],
			         ];
		         } );

		$response = $cf->change_development_mode( 'invalid' );
		$this->assertTrue( $response->success );
		$this->assertSame( 'development_mode', $response->result->id );
		$this->assertSame( 'off', $response->result->value );
	}
}
