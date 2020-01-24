<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Api;
use Cloudflare\Exception\AuthenticationException;
use Cloudflare\Zone\Settings;
use Mockery;
use WPMedia\Cloudflare\Tests\Unit\TestCase;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::change_cache_level
 * @group  Facade
 */
class Test_ChangeCacheLevel extends TestCase {

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

		$settings->shouldReceive( 'change_cache_level' )
		         ->once()
		         ->with( null, 'aggressive' )
		         ->andReturnUsing( function() {
			         throw new AuthenticationException( 'Authentication information must be provided' );
		         } );

		$this->expectException( AuthenticationException::class );
		$this->expectExceptionMessage( 'Authentication information must be provided' );
		$cf->change_cache_level( 'aggressive' );
	}

	public function testShouldReturnIps() {
		list( $cf, $settings ) = $this->getMocks();

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'change_cache_level' )
		         ->once()
		         ->with( 'zone1234', 'aggressive' )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'   => (object) [
					         'value' => 'aggressive',
				         ],
				         'success'  => true,
				         'errors'   => [],
				         'messages' => [],
			         ];
		         } );

		$response = $cf->change_cache_level( 'aggressive' );
		$this->assertTrue( $response->success );
		$this->assertSame( 'aggressive', $response->result->value );
	}
}
