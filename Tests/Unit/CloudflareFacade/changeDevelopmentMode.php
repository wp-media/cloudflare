<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Api;
use Cloudflare\Exception\AuthenticationException;
use Cloudflare\Zone\Settings;
use Mockery;
use WPMedia\Cloudflare\Tests\Unit\TestCase;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::change_development_mode
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_ChangeDevelopmentMode extends TestCase {

	private function getMocks( $setApiExpects = true ) {
		if ( $setApiExpects ) {
			$api = Mockery::mock( Api::class, [
				'setEmail'      => null,
				'setAuthKey'    => null,
				'setCurlOption' => null,
			] );
		} else {
			$api = Mockery::mock( Api::class );
		}

		$cf       = $this->getFacade( $api );
		$settings = Mockery::mock( Settings::class, [ $api ] );
		$this->set_reflective_property( $settings, 'settings', $cf );

		return [ $cf, $settings ];
	}

	public function testShouldThrowErrorWhenInvalidCredentials() {
		list( $cf, $settings ) = $this->getMocks( false );

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
		list( $cf, $settings ) = $this->getMocks();

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'change_development_mode' )
		         ->once()
		         ->with( 'zone1234', 'invalid' )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'   => null,
				         'success'  => false,
				         'errors'   => [
					         (object) [
						         'message' => 'Invalid value for zone setting development_mode',
					         ],
				         ],
				         'messages' => [],
			         ];
		         } );

		$response = $cf->change_development_mode( 'invalid' );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting development_mode', $error->message );
	}

	public function testShouldSucceedWhenValidSettingGiven() {
		list( $cf, $settings ) = $this->getMocks();

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'change_development_mode' )
		         ->once()
		         ->with( 'zone1234', 'invalid' )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'   => (object) [
					         'id'    => 'development_mode',
					         'value' => 'off',
				         ],
				         'success'  => true,
				         'errors'   => [],
				         'messages' => [],
			         ];
		         } );

		$response = $cf->change_development_mode( 'invalid' );
		$this->assertTrue( $response->success );
		$this->assertSame( 'development_mode', $response->result->id );
		$this->assertSame( 'off', $response->result->value );
	}
}
