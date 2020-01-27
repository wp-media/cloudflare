<?php

namespace WPMedia\Cloudflare\Tests\Unit\CloudflareFacade;

use Cloudflare\Exception\AuthenticationException;

/**
 * @covers WPMedia\Cloudflare\CloudflareFacade::change_cache_level
 * @group  Cloudflare
 * @group  CloudflareFacade
 */
class Test_ChangeCacheLevel extends TestCase {

	public function testShouldThrowErrorWhenInvalidCredentials() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings', false );

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

	public function testShouldFailWhenInvalidLevelGiven() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'change_cache_level' )
		         ->once()
		         ->with( 'zone1234', 'invalid' )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'  => [],
				         'success' => false,
				         'errors'  => [
					         (object) [
						         'message' => 'Invalid value for zone setting change_cache_level',
					         ],
				         ],
			         ];
		         } );

		$response = $cf->change_cache_level( 'invalid' );
		$this->assertFalse( $response->success );
		$error = $response->errors[0];
		$this->assertSame( 'Invalid value for zone setting change_cache_level', $error->message );
	}

	public function testShouldReturnIps() {
		list( $cf, $settings ) = $this->getMocksWithDep( 'settings' );

		$cf->set_api_credentials( 'test@example.com', 'API_KEY', 'zone1234' );

		$settings->shouldReceive( 'change_cache_level' )
		         ->once()
		         ->with( 'zone1234', 'simplified' )
		         ->andReturnUsing( function() {
			         return (object) [
				         'result'   => (object) [
					         'value' => 'simplified',
				         ],
				         'success'  => true,
				         'errors'   => [],
				         'messages' => [],
			         ];
		         } );

		$response = $cf->change_cache_level( 'simplified' );
		$this->assertTrue( $response->success );
		$this->assertSame( 'simplified', $response->result->value );
	}
}
