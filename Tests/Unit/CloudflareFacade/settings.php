<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::settings
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_Settings extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings', false );

		$settings->shouldReceive( 'settings' )
		         ->once()
		         ->with( null )
		         ->andReturnUsing( function() {
			         throw new AuthenticationException( 'Authentication information must be provided' );
		         } );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$cf->settings();
	}

	public function testShouldReturnSettingsWhenZoneIdIsSet() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'settings' )
		         ->once()
		         ->with( 'zone1234' )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'  => [
					         // List of settings
					         (object) [],
					         (object) [],
					         (object) [],
					         (object) [],
					         (object) [],
				         ],
				         'success' => true,
				         'errors'  => [],
			         ];
		         } );

		$response = $cf->settings();
		$this->assertTrue( $response->success );
		$this->assertEmpty( $response->errors );
		$this->assertGreaterThan( 4, $response->result );
	}
}
