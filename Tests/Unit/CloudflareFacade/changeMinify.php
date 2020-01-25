<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::change_minify
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_ChangeMinify extends TestCase {
	protected $newSetting = [
		'css'  => 'on',
		'html' => 'on',
		'js'   => 'on',
	];

	public function testShouldThrowErrorWhenInvalidCredentials() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings', false );

		$settings->shouldReceive( 'change_minify' )
		         ->once()
		         ->with( null, $this->newSetting )
		         ->andReturnUsing( function() {
			         throw new AuthenticationException( 'Authentication information must be provided' );
		         } );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$cf->change_minify( $this->newSetting );
	}

	public function testShouldFailWhenInvalidSettingGiven() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'change_minify' )
		         ->once()
		         ->with( 'zone1234', [ 'css' => 'invalid' ] )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'  => [],
				         'success' => false,
				         'errors'  => [
					         (object) [
						         'message' => 'Invalid value for zone setting minify',
					         ],
				         ],
			         ];
		         } );

		$response = $cf->change_minify( [ 'css' => 'invalid' ] );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting minify', $error->message );
	}

	public function testShouldChangeMinifyWhenSettingGiven() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'change_minify' )
		         ->once()
		         ->with( 'zone1234', $this->newSetting )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'  => (object) [
					         'value' => $this->newSetting,
				         ],
				         'success' => true,
				         'errors'  => [],
			         ];
		         } );

		$response = $cf->change_minify( $this->newSetting );
		$this->assertTrue( $response->success );
		$this->assertSame( $this->newSetting, $response->result->value );
	}
}
